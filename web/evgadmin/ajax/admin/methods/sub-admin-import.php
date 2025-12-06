<?php
devMode();
(function (
    &$r,
    $pix,
    $pixdb,
    $datetime,
    $evg
) {
    $_ = $_POST;

    if (
        isset($_['records'])
    ) {
        $records = $_['records'];
        if (
            $records &&
            ($records = json_decode($records)) &&
            isset($records->data) &&
            $records->data
        ) {
            if (!is_array($records->data)) {
                $records->data = [];
            }
            list(
                $fullName,
                $email,
                $phone,
                $benefit,
                $events,
                $location,
                $members,
                $membersMod,
                $transactions,
                $career,
                $advocacy,
                $paidPlans,
                $pointRules,
                $messages,
                $cmsPages,
                $contactEnquiries,
                $statistics,
                $helpdesk,
                $developer,
                $projectCoordinators,
                $ncnwTeam,
            ) = $records->data;

            $fullName = ucwords(esc($fullName));
            $email = esc($email);
            $phone = esc($phone);
            $benefit = strtolower(esc($benefit));
            $events = strtolower(esc($events));
            $location = strtolower(esc($location));
            $members = strtolower(esc($members));
            $membersMod = strtolower(esc($membersMod));
            $transactions = strtolower(esc($transactions));
            $career = strtolower(esc($career));
            $advocacy = strtolower(esc($advocacy));
            $paidPlans = strtolower(esc($paidPlans));
            $pointRules = strtolower(esc($pointRules));
            $messages = strtolower(esc($messages));
            $cmsPages = strtolower(esc($cmsPages));
            $contactEnquiries = strtolower(esc($contactEnquiries));
            $statistics = strtolower(esc($statistics));
            $helpdesk = strtolower(esc($helpdesk));
            $developer = strtolower(esc($developer));
            $projectCoordinators = strtolower(esc($projectCoordinators));
            $ncnwTeam = strtolower(esc($ncnwTeam));

            if (
                $fullName &&
                is_mail($email) &&
                $phone
            ) {
                $uName = preg_replace('/[^a-z0-9._]/', '', strtolower(strstr($email, '@', true)));
                $permissions = [];
                //
                if ($benefit == 'yes') {
                    $permissions[] = 'benefit';
                }
                if ($events == 'yes') {
                    $permissions[] = 'events';
                }
                if ($location == 'yes') {
                    $permissions[] = 'location';
                }
                if ($members == 'yes') {
                    $permissions[] = 'members';
                }
                if ($membersMod == 'yes') {
                    $permissions[] = 'members-mod';
                }
                if ($transactions == 'yes') {
                    $permissions[] = 'transactions';
                }
                if ($career == 'yes') {
                    $permissions[] = 'career';
                }
                if ($advocacy == 'yes') {
                    $permissions[] = 'advocacy';
                }
                if ($paidPlans == 'yes') {
                    $permissions[] = 'paid-plans';
                }
                if ($pointRules == 'yes') {
                    $permissions[] = 'point-rules';
                }
                if ($messages == 'yes') {
                    $permissions[] = 'messages';
                }
                if ($cmsPages == 'yes') {
                    $permissions[] = 'cms-pages';
                }
                if ($contactEnquiries == 'yes') {
                    $permissions[] = 'contact-enquiries';
                }
                if ($statistics == 'yes') {
                    $permissions[] = 'statistics';
                }
                if ($helpdesk == 'yes') {
                    $permissions[] = 'helpdesk';
                }
                if ($developer == 'yes') {
                    $permissions[] = 'helpdesk/developer';
                }
                if ($projectCoordinators == 'yes') {
                    $permissions[] = 'helpdesk/project-coordinators';
                }
                if ($ncnwTeam == 'yes') {
                    $permissions[] = 'helpdesk/ncnw-team';
                }
                $password = $pix->makestring(8, 'uln');
                $passwordHash = $pix->encrypt($password);

                $adminData = [
                    'type' => 'sub-admin',
                    'enabled' => 'Y',
                    'name' => $fullName,
                    'username' => $uName,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $passwordHash,
                    'perms' => implode(',', array_unique($permissions)) ?: null

                ];
                $existUser = $pixdb->getRow(
                    'admins',
                    ['email' => $email],
                    'id,
                    type,
                    name,
                    phone'
                );
                $userId = false;
                if ($existUser) {
                    $userId = $existUser->id;
                    $uType = $existUser->type;
                    $upData = [];
                    if ($uType == 'section-president') {
                        $upData['type'] = 'sub-admin';
                        $permissions[] = 'elect';
                    }
                    $upData['name'] = $existUser->name ?: $fullName;
                    $upData['phone'] = $existUser->phone ?: $phone;
                    $upData['perms'] = implode(',', array_unique($permissions)) ?: null;
                    $pixdb->update(
                        'admins',
                        ['id' => $userId],
                        $upData
                    );
                } else {
                    $userId = $pixdb->insert(
                        'admins',
                        $adminData
                    );
                }
                if ($userId) {
                    $r->status = 'ok';
                }
            }
        }
    }
})(
    $r,
    $pix,
    $pixdb,
    $datetime,
    $evg
);
