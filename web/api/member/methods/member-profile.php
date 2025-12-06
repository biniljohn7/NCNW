<?php
if (isset($_GET['memberId'])) {
    $memberId = esc($_GET['memberId']);

    if ($memberId) {
        if (preg_match('/^gp_(\d+)$/', $memberId, $matches)) {
            $groupId = (int)$matches[1];
            $group = $pixdb->getRow(
                'message_groups',
                ['id' => $groupId],
                'title,
                createdOn'
            );
            if ($group) {
                $r->status = 'ok';
                $group->profileImage =  null;
                $group->name = $group->title;
                $group->email = null;
                $group->createdOn = $group->createdOn ? date('d M Y', strtotime($group->createdOn)) : null;

                $r->success = 1;
                $r->message = 'Viewed Successfully!';
                $r->data = (object)[
                    'profileData' => $group,
                    'visible' => []
                ];
            }
        } else {
            $member = $pixdb->get(
                'members',
                [
                    'id' => $memberId,
                    'single' => 1
                ],
                'firstName,
                lastName,
                email,
                avatar'
            );

            if ($member) {
                $r->status = 'ok';
                $member->profileImage = $member->avatar ? $pix->domain . 'uploads/avatars/' . $pix->thumb($member->avatar, '150x150') : null;
                $member->name = html_entity_decode($member->firstName . ' ' . $member->lastName, ENT_QUOTES, 'UTF-8');

                $r->success = 1;
                $r->message = 'Viewed Successfully!';
                $r->data = (object)[
                    'profileData' => $member,
                    'visible' => []
                ];
            }
        }
    }
}
