<?php
include 'evgadmin/lib/lib.php';

$stripe = loadModule('stripe');

$webhookSecret = 'whsec_r5a0FR79BIftuu6MPdHse5JrqnwYgHLV';
$payload = @file_get_contents('php://input');
$signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

$event = $stripe->verifyPaidResponse($payload, $signature, $webhookSecret);
