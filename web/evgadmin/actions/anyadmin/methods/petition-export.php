<?php
$_  = $_REQUEST;

if(isset($_['id'])) {
    $id = esc($_['id']);

    $advData = $pixdb->getRow(
        'advocacies',
        ['id' => $id]
    );

    if(
        $id &&
        $advData
    ) {
        $advMembs = $pixdb->get(
            'member_advocacy',
            [
                'advocacy' => $advData->id
            ]
        );

        $membIds = [];
        $membAddr = [];
        $memberInfo = [];
        $stateIds = [];
        $countryIds = [];

        foreach ($advMembs->data as $row) {
            if (!empty($row->member)) {
                $membIds[] = $row->member;
            }
        }

        $membDatas = $evg->getMembers($membIds, 'id, firstName, lastName, memberId');

        if (!empty($membIds)) {
            $membAddr = $pixdb->fetchAssoc(
                'members_info',
                ['member' => $membIds],
                'member, country, state, city, address, address2, zipcode',
                'member'
            );
        }

        foreach ($membAddr as $row) {
            $stateIds[] = $row->state;
            $countryIds[] = $row->country;
        }

        if(!empty($stateIds)) {
            $stateIds = array_filter($stateIds);
            $stName = $pixdb->fetchAssoc(
                'states',
                ['id' => $stateIds],
                'id, name',
                'id'
            );
        }

        if(!empty($countryIds)) {
            $countryIds = array_filter($countryIds);
            $cnName = $pixdb->fetchAssoc(
                'nations',
                ['id' => $countryIds],
                'id, name',
                'id'
            );
        }

        foreach ($membDatas as $row) {
            $addr = isset($membAddr[$row->id]) ? $membAddr[$row->id] : (object)[];

            $memberInfo[$row->id] = [
                'id' => $row->id,
                'name' => trim($row->firstName . ' ' . $row->lastName),
                'address' => $addr->address ?? '',
                'address2' => $addr->address2 ?? '',
                'country' => $addr && isset($addr->country, $cnName[$addr->country]) ? $cnName[$addr->country]->name : '',
                'state' => $addr && isset($addr->state, $stName[$addr->state]) ? $stName[$addr->state]->name : '',
                'city' => $addr->city ?? '',
                'zipcode' => $addr->zipcode ?? ''
            ];
        }

        include $pix->basedir . 'includes/libs/tcpdf/tcpdf.php';

        $pdf = new TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $html = '<p><h2>'.  $advData->title .'</h2></p>
        <hr />
        <br /><br />
        <p>' . $advData->descrptn . '</p>
        <br/>
        <p><h4>Member Details</h4></p>
        <table 
            border="1" 
            cellpadding="6"
            style="width: 100%; text-align: center;"
        >
            <tr style="font-weight: bold; background-color:#ccc;">
                <td width="30%">Name</td>
                <td width="40%">Address</td>
                <td width="30%">Signature</td>
            </tr>
            '. (function ($membs, $info, $pix) {
                $s = '';
                if (!empty($membs) && is_array($membs)) {
                    foreach ($membs as $memb) {
                        $memberId = null;
                        if (is_object($memb) && isset($memb->member)) {
                            $memberId = $memb->member;
                        } elseif (is_array($memb) && isset($memb['member'])) {
                            $memberId = $memb['member'];
                        }
                        if ($memberId === null) {
                            continue;
                        }

                        $inf = $info[$memberId] ?? null;
                        if (empty($inf)) {
                            continue;
                        }

                        $infData = is_array($inf) ? $inf : (is_object($inf) ? (array)$inf : []);

                        $name = $infData['name'] ?? '--';
                        $address = $infData['address'] ?? '--';
                        $address2 = $infData['address2'] ?? '';
                        $city = $infData['city'] ?? '';
                        $state = $infData['state'] ?? '';
                        $country = $infData['country'] ?? '';
                        $zipcode = $infData['zipcode'] ?? '';

                        $addrLines = $address;
                        if ($address2 !== '') {
                            $addrLines .= '<br/>' . $address2;
                        }
                        $cityState = trim($city . ($city && $state ? ', ' : '') . $state);
                        if ($cityState !== '') {
                            $addrLines .= '<br/>' . $cityState;
                        }
                        $countryZip = trim($country . ($country && $zipcode ? ', ' : '') . $zipcode);
                        if ($countryZip !== '') {
                            $addrLines .= '<br/>' . $countryZip;
                        }
                        $s .= '<tr>
                                <td>' . $name . '</td>
                                <td>' . $addrLines . '</td>
                                <td>';

                        if (!empty($memb->signature)) {
                            $signPath = $pix->uploadPath . 'signatures/' . $memb->signature;
                            $s .= '<img src="' . $signPath . '" style="width:80px; height:auto;" />';
                        } else {
                            $s .= '--';
                        }

                        $s .= '</td></tr>';
                    }
                }
                return $s;
            })($advMembs->data ?? [], $memberInfo, $pix) .'
        </table>';

        $pdf->writeHTMLCell(0, 0, 10, 10, $html, 0, 1, 0, true, 'L', true);
        $pdf->Output('Petition_' . $advData->title . '_Export_' . date('Ymd_His') . '.pdf', 'D');
        $pix->remsg();
        exit;
    }
}
?>