<?php
if (!$pix->canAccess('email-templates')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb, $evg) {
    $_ = $_POST;

    if (isset($_['template'])) {
        $template = str2url($_['template']);

        $tmpList = [
            'advocacy' => [
                'body',
                'ftext1',
                'ftext2'
            ],
            'account-verification' => [
                'body',
                'btntext',
                'altlinktext'
            ],
            'leaders-invite-access' => [
                'body',
                'btntext',
                'btnlabel'
            ],
        ];

        if (isset($tmpList[$template])) {
            $tmpData = [];
            foreach ($tmpList[$template] as $inp) {
                $tmpData[$inp] = esc($_[$inp] ?? '');
            }
            $pix->setData(
                "email-templates/$template",
                $tmpData
            );

            $pix->addmsg('Email template saved!', 1);
        }
    }
})($pix, $pixdb, $evg);
