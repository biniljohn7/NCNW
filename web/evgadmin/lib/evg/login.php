<?php
global $pix, $pixdb, $date;

$data = (object)[
    'memberId' => null,
    'prefix' => null,
    'suffix' => 'None',
    'firstName' => null,
    'lastName' => null,
    'email' => null,
    'profileImage' => null,
    'membershipExpireDate' => null,
    'accessToken' => null,
    'memberCode' => null,
    'referralCode' => null,
    'referralPoints' => 0,
    'sectionId' => null,
    'currentChapter' => null,
    'affiliationId' => null,
    'affiliation' => null,
    'profileCreated' => true,
    'notification' => false,
    'roles' => [],
    'membershipStatus' => null
];

if ($memberId) {
    $member = $pixdb->getRow(
        'members',
        ['id' => $memberId]
    );

    if ($member) {
        $data->memberId = $memberId;
        $data->email = $member->email;
        $data->firstName = $member->firstName;
        $data->lastName = $member->lastName;
        $data->roles = $member->role ? explode(',', $member->role) : [];
        $data->profileImage = $this->getAvatar($member->avatar);
        $data->notifications = $member->notifications == 'Y' ? true : false;


        if ($member->memberId == null) {
            $memberCode = $pix->makeMemberId();
            $updated = $pixdb->update(
                'members',
                ['id' => $memberId],
                ['memberId' => $memberCode]
            );
            if ($updated) {
                $data->memberCode = $memberCode;
            }
        } else {
            $data->memberCode = $member->memberId;
        }

        // fetching existing token
        $authToken = $pixdb->get(
            'members_auth',
            [
                'id' => $member->id,
                'single' => 1
            ],
            'token'
        )->token ?? false;

        // creating new token
        if (!$authToken) {
            $authToken = $this->generateAuthToken();

            $ins = $pixdb->insert(
                'members_auth',
                [
                    'id' => $member->id,
                    'token' => $authToken
                ]
            );
            if ($ins) {
                $data->accessToken = $authToken;
            }
        } else {
            $data->accessToken = $authToken;
        }

        $memberInfo = $pixdb->getRow(
            'members_info',
            ['member' => $memberId]
        );

        if ($memberInfo) {
            $data->prefix = $memberInfo->prefix;
            $data->suffix = $memberInfo->suffix;
            $data->referralCode = $memberInfo->refcode;
            $data->referralPoints = $memberInfo->pointsBalance;
            $data->sectionId = $memberInfo->cruntChptr;
            // $data->affiliationId = $memberInfo->affilateOrgzn;

            if ($memberInfo->cruntChptr) {
                $currentChapter = $this->getChapter($memberInfo->cruntChptr, 'name');
                if ($currentChapter) {
                    $data->currentChapter = $currentChapter->name;
                }
            }
            // if ($memberInfo->affilateOrgzn) {
            //     $affiliation = $this->getAffiliation($memberInfo->affilateOrgzn, 'name');
            //     if ($affiliation) {
            //         $data->affiliation = $affiliation->name;
            //     }
            // }
        }

        $mmbrAffiliations = $pixdb->getCol(
            'members_affiliation',
            ['member' => $member->id],
            'affiliation'
        );
        $allAffiliations = [];
        if ($mmbrAffiliations) {
            $affiliation = $this->getAffiliations($mmbrAffiliations, 'id,name');
            foreach ($affiliation as $aff) {
                $allAffiliations[] = $aff;
            }
        }
        $data->affiliation = $allAffiliations;

        $mmbrShps = $pixdb->get(
            'memberships',
            [
                'member' => $member->id,
                'enabled' => 'Y',
                '#SRT' => 'id desc'
            ],
            'enabled,
            expiry'
        )->data;
        foreach ($mmbrShps as $mmbrShp) {
            $data->membershipExpireDate = $mmbrShp->expiry;
            $data->membershipStatus = $mmbrShp->expiry && $date > $mmbrShp->expiry ? 'expired' : 'active';
            if ($mmbrShp->expiry == null || ($mmbrShp->expiry && $date < $mmbrShp->expiry)) {
                $data->membershipExpireDate = $mmbrShp->expiry;
                $data->membershipStatus = 'active';
                break;
            }
        }
    }
}
return (array)$data;
