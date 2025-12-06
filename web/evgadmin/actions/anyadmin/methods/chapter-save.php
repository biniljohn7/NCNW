<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['name'],
        $_['state'],
    )
) {
    $name = ucwords(esc($_['name']));
    $state = esc($_['state']);
    $firstName = esc($_['firstName']);
    $lastName = esc($_['lastName']);
    $email = esc($_['email']);
    $phone = esc($_['phone']);
    $appliedDate = esc($_['appliedDate']);
    $approvedDate = esc($_['approvedDate']);
    $note = esc($_['note']);
    $status = isset($_['status']) ? 'Y' : 'N';
    $ein = esc($_['ein']);
    $secId = esc($_['secId']);
    $dateChartered = esc($_['dateChartered']);
    $cid = esc($_['cid'] ?? '');
    $new = !$cid;

   $passed = true;

    if ($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $passed = true;
        } else {
            $passed = false;
        }
    } else {
        $passed = true;
    }

    if($passed) {
        if (
            $name &&
            $state
        ) {
            $data = false;
            if ($cid) {
                $data = $pixdb->get(
                    'chapters',
                    [
                        'id' => $cid,
                        'single' => 1
                    ],
                    'id'
                );
            }
            if (
                $new ||
                (
                    !$new &&
                    $data
                )
            ) {
                if (
                    $pixdb->getRow('states', ['id' => $state], 'id')
                ) {
                    $exChapter = $pixdb->getRow(
                        'chapters',
                        ['state' => $state, 'name' => $name],
                        'id'
                    );
                    if (
                        $exChapter &&
                        $exChapter->id == $cid
                    ) {
                        $exChapter = false;
                    }
                    if (!$exChapter) {
                        $dbData = [
                            'state' => $state,
                            'name' => $name,
                            'firstName' => substr($firstName, 0, 60),
                            'lastName' => substr($lastName, 0 ,60),
                            'email' => $email,
                            'phone' => $phone,
                            'appliedDate' => $appliedDate ? date('Y-m-d', strtotime($appliedDate)) : null,
                            'approvedDate' => $approvedDate ? date('Y-m-d', strtotime($approvedDate)) : null,
                            'phone' => $phone,
                            'note' => $note,
                            'ein' => $ein,
                            'secId' => $secId,
                            'dateChartered' => $dateChartered ? date('Y-m-d', strtotime($dateChartered)) : null,
                        ];
                        
                        if ($new) {
                            $dbData['createdAt'] = $datetime;
                            $iid = $pixdb->insert(
                                'chapters',
                                $dbData
                            );
                        } else {
                            $iid = $cid;
                            $dbData['updatedAt'] = $datetime;
                            $pixdb->update(
                                'chapters',
                                [
                                    'id' => $iid
                                ],
                                $dbData
                            );
                            var_dump(basename(__FILE__) . ':' . __LINE__);
                        }
                        if ($iid) {
                            $pix->addmsg('Section saved', 1);
                            $pix->redirect('?page=chapter');
                        }
                    } else {
                        $pix->addmsg('Section already exist');
                    }
                } else {
                    $pix->addmsg('State does not exist');
                }
            }
        }
    } else {
        $pix->addmsg('Email is not valid');
    }
}
