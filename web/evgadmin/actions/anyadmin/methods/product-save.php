<?php
if (!$pix->canAccess('paid-plans')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb) {
    $_ = $_POST;
    if (isset($_['name'], $_['code'], $_['type'])) {
        $name = esc($_['name']);
        $code = esc($_['code']);
        $type = esc($_['type']);
        $desc = have($_['desc']);
        $enabled = isset($_['enabled']);
        $id = esc($_['id'] ?? '');

        $new = !$id;
        $amount = null;
        $validity = null;

        $validType = true;
        if (preg_match('/(fee|donation)/', $type)) {
            if ($type == 'fee' && isset($_['amount'], $_['validity'])) {
                $amount = floatval($_['amount']);
                $validity = esc($_['validity']);
                if (!($amount && preg_match('/(fiscal-year|lifelong)/', $validity))) {
                    if ($validity == 'lifelong') {
                        $validity = null;
                    }
                    $validType = false;
                }
            }
        } else {
            $validType = false;
        }
        if ($name && $code && $validType) {
            $dbData = [
                'code' => $code,
                'name' => $name,
                'description' => $desc,
                'amount' => $amount,
                'enabled' => $enabled ? 'Y' : 'N',
                'type' => $type,
                'validity' => $validity
            ];
            if ($new) {
                $iid = $pixdb->insert(
                    'products',
                    $dbData
                );
            } else {
                $iid = $id;
                $pixdb->update(
                    'products',
                    ['id' => $iid],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('Product saved', 1);
                $pix->redirect('?page=payment-categories');
            }
        }
    }
})($pix, $pixdb);
