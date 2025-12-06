<?php
require_once(__DIR__ . '/../libs/stripe/vendor/autoload.php');

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;

if (!class_exists('StripeModule')) {
    class StripeModule
    {
        public $connectAccId = '';
        public function __construct()
        {
            loadEnv();
            $secretKey = $_ENV['STRIPE_TEST_SECRET'] ?? '';
            $this->connectAccId = $_ENV['CONNECT_ACC_ID'] ?? '';
            Stripe::setApiKey($secretKey);
        }
        public function createCheckoutSession($items, $txnId, $user, $redirectUrl = null)
        {
            global $pix;

            if (empty($items) || !$txnId || !$user) {
                return false;
            }

            $lineItems = [];
            $totalAmount = 0;
            foreach ($items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => ['name' => $item['name']],
                        'unit_amount' => $item['amount'],
                    ],
                    'quantity' => $item['quantity'],
                ];
                $totalAmount += $item['amount'] * $item['quantity'];
            }

            $itemSummary = implode(',', array_column($items, 'item_id'));

            if (!$user->stripeCusId) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => "$user->firstName $user->lastName",
                ], [
                    'stripe_account' => $this->connectAccId
                ]);

                $this->storeCusId($customer->id, $user->id);
                $user->stripeCusId = $customer->id;
            }

            if ($user->stripeCusId) {
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card', 'us_bank_account'],
                    'line_items' => $lineItems,
                    'mode' => 'payment',
                    'success_url' => $redirectUrl ?? $pix->appDomain . "txn/$txnId/success",
                    'cancel_url' => $redirectUrl ?? $pix->appDomain . "txn/$txnId/cancelled",
                    'payment_method_options' => [
                        'us_bank_account' => [
                            'verification_method' => 'automatic'
                        ]
                    ],
                    'metadata' => [
                        'txn_id' => $txnId,
                        'user_id' => $user->id,
                        'customer_name' => "$user->firstName $user->lastName",
                        'site_name' => 'NCNW',
                        'items' => $itemSummary
                    ],
                    'customer' => $user->stripeCusId,

                    'payment_intent_data' => [
                        'application_fee_amount' => intval(round($totalAmount * 0.015)),
                    ],

                ], [
                    'stripe_account' => $this->connectAccId
                ]);
                return $session;
            }

            return false;
        }

        public function verifyPaidResponse($payload, $signature, $webhookSecret)
        {
            $r = (object)[
                'status' => 'error',
                'message' => ''
            ];

            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload,
                    $signature,
                    $webhookSecret
                );

                $connectedAccountId = $this->connectAccId;

                if ($event->type === 'checkout.session.completed') {
                    $session = $event->data->object;
                    $customerId = $session->customer;
                    $userId = $session->metadata->user_id ?? null;
                    $txnId = $session->metadata->txn_id ?? null;

                    $this->storeCusId($customerId, $userId);

                    $paymentIntent = \Stripe\PaymentIntent::retrieve(
                        $session->payment_intent,
                        ['stripe_account' => $connectedAccountId]
                    );

                    if ($paymentIntent->status === 'succeeded') {
                        $r->status = 'ok';
                        $this->confirmPaymentSession($txnId);
                    } elseif ($paymentIntent->status === 'processing') {
                        $r->status = 'ok';
                    } else {
                        $r->message = "Payment not successful. Status: " . $paymentIntent->status;
                    }
                } elseif ($event->type === 'payment_intent.succeeded') {
                    $paymentIntent = $event->data->object;
                    $txnId = $paymentIntent->metadata->txn_id ?? null;

                    if ($txnId) {
                        $this->confirmPaymentSession($txnId);
                        $r->status = 'ok';
                    }
                } elseif ($event->type === 'invoice.paid') {
                    $invoice = $event->data->object;
                    $subscriptionId = $invoice->subscription;

                    if ($subscriptionId) {
                        $subscription = \Stripe\Subscription::retrieve(
                            $subscriptionId,
                            ['stripe_account' => $connectedAccountId]
                        );

                        $itemId = $subscription->metadata->item_id ?? null;
                        $planType = $subscription->metadata->plan_type ?? null;
                        $this->logRecurringInstallment($itemId, $planType);
                    }
                }
            } catch (\UnexpectedValueException $e) {
                $r->message = 'Invalid payload';
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                $r->message = 'Invalid signature';
            }

            return $r;
        }

        public function createInstllmntSubscription($customerId, $itemName, $amount, $anchorDate, $monthInterval, $cancelDate, $userId, $itemId)
        {
            global $evg;
            $r = (object)[
                'status' => 'error',
                'message' => ''
            ];

            $price = $this->createSubscriptionProduct($itemName, $evg->dollarInCents($amount), $monthInterval);
            if ($price) {
                try {
                    $subscription = \Stripe\Subscription::create([
                        'customer' => $customerId,
                        'items' => [[
                            'price' => $price->id,
                            'quantity' => 1,
                            'metadata' => [
                                'item_id' => $itemId
                            ]
                        ]],
                        'billing_cycle_anchor' => strtotime($anchorDate),
                        'cancel_at' => strtotime($cancelDate),
                        'proration_behavior' => 'none',
                        'application_fee_percent' => 1.5,
                        'metadata' => [
                            'user_id' => $userId,
                            'item_id' => $itemId,
                            'plan_type' => 'installment',
                            'created_by' => 'auto_after_q1'
                        ]
                    ], [
                        'stripe_account' => $this->connectAccId
                    ]);

                    return $subscription;
                } catch (\Exception $e) {
                    $r->message = "Stripe Subscription Error: " . $e->getMessage();
                    return $r;
                }
            }
            return false;
        }
        public function createSubscriptionProduct($pdtName, $amtInCents, $intervalCount)
        {
            try {
                $product = \Stripe\Product::create([
                    'name' => $pdtName
                ], [
                    'stripe_account' => $this->connectAccId
                ]);

                $price = \Stripe\Price::create([
                    'unit_amount' => $amtInCents,
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => 'month',
                        'interval_count' => $intervalCount
                    ],
                    'product' => $product->id
                ], [
                    'stripe_account' => $this->connectAccId
                ]);

                return $price;
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage();
                return false;
            }
        }
        public function confirmPaymentSession($txnId)
        {
            global $pixdb, $evg;
            $txnData = $pixdb->getRow(
                [
                    ['transactions', 't', 'member'],
                    ['members', 'm', 'id']
                ],
                [
                    'txnid' => $txnId,
                    '#QRY' => 'status!="success"'
                ],
                'm.id as memberId,
                m.firstName,
                m.lastName,
                t.amount,
                t.id as txnId'
            );
            if ($txnData) {
                $evg->markPaymentDone($txnData->txnId);

                // notification
                $evg->postNotification(
                    'admin',
                    $txnData->memberId,
                    'new-payment',
                    'New Payment',
                    "New payment of " . dollar($txnData->amount, 1, 1) . " recieved from $txnData->firstName $txnData->lastName.",
                    ['id' => $txnData->txnId]
                );
            }
        }
        public function storeCusId($customerId, $userId)
        {
            global $evg, $pixdb;

            $user = $evg->getMember($userId, 'stripeCusId');
            if ($user && $user->stripeCusId == null) {
                $pixdb->update(
                    'members',
                    ['id' => $userId],
                    ['stripeCusId' => $customerId]
                );
            }
        }
        public function logRecurringInstallment($itemId, $planType)
        {
            global $evg, $pixdb;
            if ($planType == 'installment') {
                $membership = $pixdb->getRow(
                    'memberships',
                    ['id' => $itemId],
                    'id'
                );
                if ($membership) {
                    $id = "mmbrshp$itemId";
                    $list = [$id => (object)['id' => $id]];
                    $txnId = $evg->rosterRenewalReq($list, null, null, null, false);
                    if ($txnId) {
                        $evg->markPaymentDone($txnId);
                    }
                }
            }
        }
        public function getSubscribeInfo($stripeSubscribeId)
        {
            try {
                $subscription = \Stripe\Subscription::retrieve(
                    $stripeSubscribeId,
                    [
                        'stripe_account' => $this->connectAccId
                    ]
                );
                if (!$subscription) {
                    return 'Subscription not found.';
                }

                $statusValue = $subscription->status;
                $start       = $subscription->start_date ?? null;
                $end         = $subscription->cancel_at ?? $subscription->billing_cycle_anchor ?? null;
                $currentEnd  = $subscription->current_period_end ?? $end;

                if ($statusValue === 'active') {
                    return 'Subscription is active (' .
                        ($start ? date('d M', $start) : 'N/A') .
                        ' - ' .
                        ($end ? date('d M Y', $end) : 'N/A') .
                        ')';
                }

                if ($statusValue === 'canceled') {
                    if ($currentEnd && $currentEnd < time()) {
                        return 'Subscription completed its term (ended on ' . date('d M Y', $currentEnd) . ')';
                    }
                    return 'Subscription has been canceled (will end on ' .
                        ($currentEnd ? date('d M Y', $currentEnd) : 'N/A') .
                        ')';
                }

                if (in_array($statusValue, ['past_due', 'unpaid'])) {
                    return 'Subscription is inactive due to payment issues.';
                }

                return $statusValue;
            } catch (\Exception $e) {
                return 'Subscription status unavailable (' . $e->getMessage() . ')';
            }
        }
        public function cancelSubscription($stripeSubscribeId)
        {
            try {
                $subscription = \Stripe\Subscription::retrieve(
                    $stripeSubscribeId,
                    [
                        'stripe_account' => $this->connectAccId
                    ]
                );
                if ($subscription) {
                    $canceledSub = $subscription->cancel();
                    return $canceledSub;
                }
            } catch (\Exception $e) {
                return false;
            }
            return false;
        }
    }
}

return new StripeModule();
