<?php
(function ($pixdb, &$r) {
    $_ = $_POST;

    if (isset($_['admin'])) {
        $perms = $_['perms'] ?? [];
        $admin = esc($_['admin']);

        if ($admin) {
            $allowed = [
                'benefit',
                'events',
                'location',
                'members',
                'members-mod',
                'transactions',
                'career',
                'advocacy',
                'paid-plans',
                'point-rules',
                'messages',
                'cms-pages',
                'contact-enquiries',
                'statistics',
                'helpdesk',
                'developer',
                'project-coordinators',
                'ncnw-team',
                'elect'
            ];
            $role = [
                'developer',
                'project-coordinators',
                'ncnw-team'
            ];

            if (!is_array($perms)) {
                $perms = [];
            }

            if (!empty($perms)) {
                $perms = array_map('str2url', $perms);
                $checkPerms = function ($pm) use ($allowed) {
                    return in_array($pm, $allowed);
                };
                $perms = array_filter($perms, $checkPerms);
            }
            if (in_array('members-mod', $perms) && !in_array('members', $perms)) {
                $perms = array_diff($perms, ['members-mod']);
            }

            foreach ($perms as $key => $value) {
                if (in_array($value, $role)) {
                    $perms[$key] = 'helpdesk/' . $value;
                }
            }

            $admData = $pixdb->getRow('admins', ['id' => $admin], 'type');
            if (
                $admData &&
                $admData->type == 'sub-admin'
            ) {
                $pixdb->update(
                    'admins',
                    ['id' => $admin],
                    ['perms' => implode(',', array_unique($perms)) ?: null]
                );

                $r->status = 'ok';
            }
        }
    }
})($pixdb, $r);
