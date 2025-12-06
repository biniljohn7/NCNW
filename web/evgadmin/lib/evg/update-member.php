<?php
global $pix, $pixdb, $datetime;

$res = (object)[
    'status' => 'error',
    'message' => ''
];
if(
    isset(
        $args->id,
        $args->firstName,
        $args->lastName,
        $args->email,
        $args->zipcode,
        $args->section,
        $args->affiliation,
        $args->createdBy
    )
) {
    $id = esc($args->id);
    $firstName = ucwords($args->firstName);
    $lastName = ucwords($args->lastName);
    $email = $args->email;
    $address = have($args->address);
    $city = have($args->city);
    $zipcode = esc($args->zipcode);
    $phone = have($args->phone);
    $section = esc($args->section);
    $affiliation = esc($args->affiliation);

    if(
        $id &&
        $firstName &&
        $lastName &&
        is_mail($email) &&
        $zipcode &&
        $section &&
        $affiliation
    ) {
        
        $data = $pixdb->getRow(
            'members',
            ['id' =>  $id],
            'id, email, memberId'
        );

        $mdMembers = loadModule('members');
        $check = $mdMembers->checkMemberExist($email); 

        $isAllowed = false;
        if ($email == $data->email) {
            $isAllowed = true;
        } elseif ($check && $check->exist) {
            $isAllowed = false;
        } else {
            $isAllowed = true;
        }

        if(
            $isAllowed && 
            $data
        ) {
            $validSection = $pixdb->get(
                'chapters',
                [
                    'id' => $section,
                    'single' => 1
                ],
                'id, name'
            );

            $validAffiliate = $pixdb->get(
                'affiliates',
                [
                    'id' => $affiliation,
                    'single' => 1
                ],
                'id, name'
            );

            if(
                $validSection &&
                $validAffiliate
            ) {
                $data = [
                    'email' => $email,
                    'firstName'  => $firstName,
                    'lastName' => $lastName,
                ];

                $pixdb->update(
                    'members',
                    ['id' => $id],
                    $data
                );

                $memberInfo = [
                    'address' => $address ?? null,
                    'city' => $city ?? null,
                    'zipcode' => $zipcode ?? null,
                    'phone' => $phone ?? null,
                    'cruntChptr' => $section,
                    'affilateOrgzn' => $affiliation
                ];

                $pixdb->update(
                    'members_info',
                    ['member' => $id],
                    $memberInfo
                );

                $res->data = (object)[
                    'id' => $id,
                    'name' => $firstName . ' ' . $lastName,
                    'avatarUrl' => '',
                    'section' => $validSection->name,
                    'affiliation' => $validAffiliate->name,
                    'city' => $city,
                    'zipcode' => $zipcode,
                    'memberId' => $data->memberId ?? '',

                ];
                $res->status = 'ok';
                $res->message = 'Member updated';
            }
        } else {
            $res->message = 'Email is already Registered!';
        }

    }
}
return $res;
?>