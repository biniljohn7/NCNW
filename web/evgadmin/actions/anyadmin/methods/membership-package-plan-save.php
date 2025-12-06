<?php
if (!$pix->canAccess('paid-plans')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb, $evg) {
    $_ = $_POST;
    if (
        isset(
            $_['type'],
            $_['title'],
            $_['natdue'],
            $_['nlatefee'],
            $_['ncapfee'],
            $_['nreinfee'],
            $_['locdue'],
            $_['code']
        )
    ) {
        $type = esc($_['type']);
        $title = esc($_['title']);
        $code = esc($_['code']);
        $visibility = isset($_['visibility']);
        $duration = esc($_['duration']);
        $installment = isset($_['installment']) && is_array($_['installment']) ? $_['installment'] : [];
        $natdue = floatval($_['natdue']);
        $nlatefee = floatval($_['nlatefee']);
        $ncapfee = floatval($_['ncapfee']);
        $nreinfee = floatval($_['nreinfee']);
        $locdue = floatval($_['locdue']);
        $addons = is_array($_['addons']) ? array_map('esc', $_['addons']) : [];
        $id = esc($_['id'] ?? '');
        $new = !$id;

        $installment =  array_unique(array_map('esc', $installment));
        $addons = array_unique(array_filter($addons));

        if (
            $type &&
            $title &&
            ($tpData = $pixdb->getRow('membership_types', ['id' => $type]))
        ) {
            $natTotal = $natdue +
                $nlatefee +
                $ncapfee +
                $nreinfee;

            $dbData = [
                'type' => $type,
                'title' => $title,
                'code' => $code,
                'active' => $visibility ? 'Y' : 'N',
                'duration' => $duration == 1 ? '1 year' : null,
                'installments' => $installment ? implode(',', $installment) : null,
                'nationalDue' => $natdue,
                'natLateFee' => $nlatefee,
                'natCapFee' => $ncapfee,
                'natReinFee' => $nreinfee,
                'localDue' => $locdue,
                'ttlLocChaptChrg' => $natTotal,
                'ttlNatChaptChrg' => $locdue,
                'ttlCharge' => $natTotal + $locdue,
                'addons' => $addons ? json_encode($addons) : null
            ];
            if ($new) {
                $iid = $pixdb->insert(
                    'membership_plans',
                    $dbData
                );
            } else {
                $iid = $id;
                $pixdb->update(
                    'membership_plans',
                    ['id' => $iid],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('Membership plan saved', 1);
                $pix->redirect('?page=member-packages&sec=details&id=' . $type);
            }
        }
    }
})($pix, $pixdb, $evg);
