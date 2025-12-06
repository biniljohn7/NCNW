<?php
if (isset($_GET['id'])) {
    $id = esc($_GET['id']);

    if ($id) {
        $plans = $pixdb->get(
            'membership_plans',
            ['id' => $id],
            'ttlCharge as totalCharges, installments'
        );

        $data = [];

        if (!empty($plans->data)) {
            foreach ($plans->data as $plan) {
                $installments = isset($plan->installments) ? $plan->installments : null;
                $total = (float)$plan->totalCharges;

                $data[] = [
                    'title' => 'One-Time Payment',
                    'installment' => 1,
                    'totalCharges' => $total
                ];

                if (!empty($installments)) {
                    $parts = explode(',', $installments);
                    foreach ($parts as $part) {
                        $val = (int)trim($part);
                        if ($val === 2) {
                            $data[] = [
                                'title' => ' Biannual Installments (Automatic)',
                                'installment' => 2,
                                'totalCharges' => $total
                            ];
                        } elseif ($val === 4) {
                            $data[] = [
                                'title' => 'Quarterly Installments (Automatic)',
                                'installment' => 4,
                                'totalCharges' => $total
                            ];
                        }
                    }
                }
            }
        }

        $r->status = 'ok';
        $r->success = 1;
        $r->data = $data;
        $r->message = 'Data Shown Successfully!';
    }
}
