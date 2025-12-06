<?php
if (!$pix->canAccess('members')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb, $evg) {
    $query = "SELECT 
                t.title,
                t.txnid,
                concat(m.firstName,' ',m.lastName) AS member,
                t.amount,
                t.date
              FROM 
                transactions t
              LEFT JOIN members m ON m.id = t.member
              WHERE 1=1
            ";
    //Transaction id search            
    $txnSearch = esc($_GET['txn_search'] ?? '');
    if ($txnSearch) {
        $qtxn = q("%$txnSearch%");
        $query .= 'AND t.txnid LIKE ' . $qtxn;
    }
    //Member            
    $member = esc($_GET['member'] ?? '');
    if ($member) {
        $query .= 'AND t.member = ' . $member;
    }
    //Member id search            
    $memSearch = esc($_GET['mem_search'] ?? '');
    if ($memSearch) {
        $qmem = q("%$memSearch%");
        $query .= 'AND t.member IN (SELECT m.id FROM members m WHERE m.memberId LIKE ' . $qmem . ')';
    }
    // Section Search
    $shSecSearch = esc($_GET['sh_sec_search'] ?? '');
    if ($shSecSearch) {
        $query .= "AND t.id IN (SELECT ti.txnId FROM txn_items ti WHERE ti.sectionId IN(SELECT c.id FROM chapters c WHERE c.id = '$shSecSearch'))";
    }
    ///State Search
    $stateKey = esc($_GET['st_sort'] ?? '');
    if ($stateKey) {
        $query .= "AND t.id IN (SELECT ti.txnId FROM txn_items ti WHERE ti.sectionId IN(SELECT c.id FROM chapters c WHERE c.state = '$stateKey'))";
    }
    //Affilations / organizations search
    $affliateKey = esc($_GET['af_sort'] ?? '');
    if ($affliateKey) {
        $query .= "AND t.id IN (SELECT ti.txnId FROM txn_items ti WHERE ti.affiliateId= '$affliateKey')";
    }
    //Membership plan
    $memPlanKey = esc($_GET['mp_sort'] ?? '');
    if ($memPlanKey) {
        $query .= "AND t.id IN (SELECT ti.txnId FROM txn_items ti WHERE ti.pdtCode= '$memPlanKey')";
    }
    //Product search
    $prodKey = esc($_GET['prod_sort'] ?? '');
    if ($prodKey) {
        $query .= "AND t.id IN (SELECT ti.txnId FROM txn_items ti WHERE ti.pdtCode= '$prodKey')";
    }
    // date covenrt to yyyy-mm-dd format 
    $formatDate = function ($dateString) {
        return date('Y-m-d', strtotime(str_replace(' / ', '-', $dateString)));
    };

    $shTrStr = esc($_GET['sh_trStr'] ?? '');
    $shTrEnd = esc($_GET['sh_trEnd'] ?? '');
    if (
        $shTrStr != '' &&
        $shTrEnd != ''
    ) {
        $shTrStr = $formatDate($shTrStr);
        $shTrEnd = $formatDate($shTrEnd);

        $query .= 'AND t.date BETWEEN "' . $shTrStr . ' 00:00:00" AND "' . $shTrEnd . ' 23:59:00"';
    } elseif ($shTrStr != '') {
        $shTrStr = $formatDate($shTrStr);
        $query .= 'AND t.date >= "' . $shTrStr . ' 00:00:00"';
    } elseif ($shTrEnd != '') {
        $shTrEnd = $formatDate($shTrEnd);
        $query .= 'AND t.date <= "' . $shTrEnd . ' 23:59:00"';
    }
    // search by status
    $shStatus = esc($_GET['sh_status'] ?? '');
    if ($shStatus == 'success' || $shStatus == 'pending') {
        $query .= 'AND t.status="' . ($shStatus == 'success' ? 'success' : 'pending') . '"';
    }
    $txns = $pixdb->fetchAll($query, PDO::FETCH_NUM);

    $trasactionArray = [];
    foreach ($txns as $obj) {
        $ar = (array) $obj;
        if (!empty($ar['date'])) {
            $ar['date'] = '="' . date('d-m-Y h:i A', strtotime($ar['date'])) . '"';
        }
        if (!empty($ar['amount'])) {
            $ar['amount'] = '$' . $ar['amount'];
        }

        $trasactionArray[] = $ar;
    }

    // CSV Head 
    $data = [
        [
            'Title',
            'Transaction ID',
            'Member',
            'Amount Paid',
            'Payment date'
        ]
    ];

    $data = array_merge($data, $trasactionArray);

    $filename = 'Transactions_Export_' . date('Y_m(F)_d') . '-' . time() . '.csv';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    $output = fopen('php://output', 'w');
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    $pix->remsg();
    exit;
})($pix, $pixdb, $evg);
