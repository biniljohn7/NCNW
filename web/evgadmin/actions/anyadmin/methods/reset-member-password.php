<?php
$_ = $_POST;

if(
    isset(
        $_['id'],
        $_['resetpass']
    )
) {
    $id = esc($_['id']);
    $resetpass = esc($_['resetpass']);

    if(
        $id &&
        $resetpass &&
        $pix->canAccess('members-mod')
    ) {
        $nwResetPwd = $pix->encrypt($resetpass);

        $pixdb->update(
            'members',
            ['id' => $id],
            ['password' => $nwResetPwd]
        );

        $isAnyLeader = $pixdb->getRow(
            'admins',
            ['memberId' => $id]
        );

        if($isAnyLeader) {
            $pixdb->update(
                'admins',
                ['memberId' => $id],
                ['password' => $nwResetPwd]
            );
        }

        $pix->addmsg('Password reset successfully', 1);
    }
}
?>