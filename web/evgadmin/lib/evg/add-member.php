<?php
global $pix, $pixdb, $datetime;

$res = (object)[
    'status' => 'error',
    'message' => ''
];
if (
    isset(
        $args->firstName,
        $args->lastName,
        $args->email,
        $args->zipcode,
        $args->section,
        $args->affiliation,
        $args->createdBy
    )
) {
    $prefix = have($args->prefix);
    $firstName = ucwords($args->firstName);
    $lastName = ucwords($args->lastName);
    $email = $args->email;
    $address = have($args->address);
    $city = have($args->city);
    $state = have($args->state);
    $country = have($args->country);
    $zipcode = esc($args->zipcode);
    $phone = have($args->phone);
    $section = esc($args->section);
    $affiliation = is_array($args->affiliation) ? array_unique(array_filter(array_map('esc', $args->affiliation))) : [];

    if (
        $firstName &&
        $lastName &&
        is_mail($email) &&
        $zipcode &&
        $section &&
        !empty($affiliation)
    ) {
        $mdMembers = loadModule('members');
        $check = $mdMembers->checkMemberExist($email);

        $memberCode = $pix->makeMemberId();

        if (
            $check &&
            !$check->exist
        ) {

            $validSection = $pixdb->get(
                'chapters',
                [
                    'id' => $section,
                    'single' => 1
                ],
                'id, name'
            );

            $validAffiliate = $pixdb->fetchAssoc(
                'affiliates',
                ['id' => $affiliation],
                'id, name'
            );

            if (
                $validSection &&
                !empty($validAffiliate)
            ) {

                $data = [
                    'email' => $email,
                    'firstName'  => $firstName,
                    'lastName' => $lastName,
                    'memberId' => $memberCode,
                    'verified' => 'N',
                    'regOn' => $datetime,
                    'createdBy' => $args->createdBy
                ];

                $mmbrId = $pixdb->insert(
                    'members',
                    $data
                );

                if ($mmbrId) {
                    $memberInfo = [
                        'member' => $mmbrId,
                        'prefix' => $prefix ?? null,
                        'address' => $address ?? null,
                        'city' => $city ?? null,
                        'state' => $state ?? null,
                        'country' => $country ?? null,
                        'zipcode' => $zipcode ?? null,
                        'phone' => $phone ?? null,
                        'cruntChptr' => $section
                    ];

                    $pixdb->insert(
                        'members_info',
                        $memberInfo
                    );

                    $affInsert = $affiliation;
                    if($affInsert) {
                        $valStr = '';
                        foreach($affInsert as $aff) {
                            $valStr .= ($valStr != '' ? ', ' : '') . '(' . $mmbrId . ', ' . $aff . ')';
                        }
                        $qry = 'INSERT INTO `members_affiliation` (`member`, `affiliation`) VALUES ' . $valStr;
                        $pixdb->run($qry);
                    }

                    $affNames = [];
                    foreach($validAffiliate as $aff) {
                        if($aff->name) {
                            $affNames[] = $aff->name;
                        }
                    }

                    $res->data = (object)[
                        'id' => $mmbrId,
                        'name' => $firstName . ' ' . $lastName,
                        'avatarUrl' => '',
                        'section' => $validSection->name,
                        'affiliation' => $affNames,
                        'city' => $city,
                        'zipcode' => $zipcode,
                        'memberId' => $memberCode,
                    ];
                    $res->status = 'ok';
                    $res->message = 'Member created';


                    //send verification
                    loadModule('account-verification')->sendVerification(
                        (object)[
                            'id' => $mmbrId,
                            'email' => $email,
                            'firstName' => "$firstName $lastName"
                        ],
                        null,
                        false
                    );
                }
            }
        } else {
            $res->message = 'Email is already Registered!';
        }
    }
}
return $res;
