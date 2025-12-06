<?php
if (!class_exists('HelpDesk')) {
    class HelpDesk
    {
        public const requestStatus = [
            'pending' => 'Pending',
            'estimated' => 'Estimated',
            'est-verified' => 'Estimate Verified',
            'approved' => 'Approved',
            'started' => 'Started',
            'deployed' => 'Deployed',
            'revise-update' => 'Revise Update',
            'resolved' => 'Resolved',
        ];

        public function getStatusName($key)
        {
            return self::requestStatus[$key] ?? ucfirst($key);
        }

        public function sendUpdate($to, $title, $data)
        {
            global $pix, $evg;
            $to = is_array($to) ? $to : array($to);
            $mlArgs = [
                'TITLE' => $title,
                'REQID' => $data->id,
                'REQTYPE' => $evg::reqTypes[$data->reqType],
                'PRIORITY' => $evg::priorities[$data->priority],
                'SUMMARY' => nl2br($data->summary),
                'LINK' =>  $data->link
            ];
            if (
                isset($data->msg)
            ) {
                $mlArgs = array_merge(
                    $mlArgs,
                    [
                        'MESSAGE' => $data->msg
                    ]
                );
            }

            $pix->emailQueue(
                [
                    $to,
                    $title,
                    'helpdesk-update',
                    $mlArgs
                ]
            );
        }
    }
}

return new HelpDesk();
