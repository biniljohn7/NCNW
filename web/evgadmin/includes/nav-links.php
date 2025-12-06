<?php
if ($lgUser) {
    $menus = [
        $pix->canAccess('statistics') ?
            [
                'name' => 'Dashboard',
                'items' => [
                    [
                        '?page=dashboard',
                        'dashboard_customize',
                        'Dashboard'
                    ],
                    [
                        '?page=analytics',
                        'monitoring',
                        'Analytics'
                    ],

                    $isSuperAdmin ?
                        [
                            '?page=notifications',
                            'notifications',
                            'Notifications'
                        ] : null
                ]
            ] : null,

        $pix->canAccess('benefit') ?
            [
                'name' => 'Benefits Management',
                'items' => [
                    [
                        '?page=benefits',
                        'sell',
                        'Benefits'
                    ],
                    [
                        '?page=provider',
                        'loyalty',
                        'Provider'
                    ],
                    [
                        '?page=ctry-benefit',
                        'widgets',
                        'Categories'
                    ],
                ]
            ] : null,

        $pix->canAccess('events') ?
            [
                'name' => 'Manage Events',
                'items' => [
                    [
                        '?page=events',
                        'calendar_month',
                        'All Events'
                    ],
                    [
                        '?page=ctry-event',
                        'category',
                        'Categories'
                    ],
                ]
            ] : null,

        $pix->canAccess('location') ?
            [
                'name' => 'Team Management',
                'items' => [
                    [
                        '?page=nation',
                        'flag',
                        'Country'
                    ],
                    [
                        '?page=region',
                        'explore',
                        'Region'
                    ],
                    [
                        '?page=state',
                        'share_location',
                        'State'
                    ],
                    [
                        '?page=chapter',
                        'assistant_navigation',
                        'Section'
                    ],
                    [
                        '?page=affiliates',
                        'tenancy',
                        'Affiliates'
                    ],
                    [
                        '?page=collegiate-sections',
                        'web',
                        'Collegiate Sections'
                    ]
                ]
            ] : null,
        $pix->canAccess('elect') ?
            [
                'name' => 'Election Management',
                'items' => [
                    [
                        '?page=election',
                        'checklist_rtl',
                        'Election Management'
                    ],
                    $isSuperAdmin ? [
                        '?page=delegates',
                        'assignment_ind',
                        'Delegates'
                    ] : null,
                    $isSuperAdmin ? [
                        '?page=state',
                        'share_location',
                        'State Leaders'
                    ] : null,
                    $isSuperAdmin ? [
                        '?page=chapter',
                        'assistant_navigation',
                        'Section Leaders'
                    ] : null,
                    $isSuperAdmin ? [
                        '?page=affiliates',
                        'tenancy',
                        'Affiliate Leader'
                    ] : null,
                    $isSuperAdmin ? [
                        '?page=collegiate-sections',
                        'web',
                        'Collegiate Liaison'
                    ] : null
                ]
            ] : null,
        $pix->canAccess('members') ?
            [
                'name' => 'Members Management',
                'items' => [
                    [
                        '?page=members',
                        'group',
                        'Members'
                    ]
                ]
            ] : null,
        $pix->canAccess('messages') ?
            [
                'name' => 'Messages Management',
                'items' => [
                    // [
                    //     '?page=messages',
                    //     'mail',
                    //     'Messages'
                    // ],
                    [
                        '?page=groups',
                        'groups_2',
                        'Message Groups'
                    ],
                ]
            ] : null,
        $pix->canAccess('transactions') ?
            [
                'name' => 'Transactions',
                'items' => [
                    [
                        '?page=transactions',
                        'receipt_long',
                        'Recent Transactions'
                    ]
                ]
            ] : null,

        $pix->canAccess('career') ?
            [
                'name' => 'Career Management',
                'items' => [
                    [
                        '?page=career',
                        'school',
                        'Career'
                    ],
                    [
                        '?page=cr-tags',
                        'work',
                        'Career Tag'
                    ],
                ]
            ] : null,

        $pix->canAccess('advocacy') ?
            [
                'name' => 'Advocacy Management',
                'items' => [
                    [
                        '?page=advocacy',
                        'gavel',
                        'Advocacy'
                    ],
                ]
            ] : null,

        $pix->canAccess('contact-enquiries') ?
            [
                'name' => 'Contact-Us Management',
                'items' => [
                    [
                        '?page=enquiries',
                        '3p',
                        'Inquiries'
                    ],
                    [
                        '?page=enq-ques',
                        'live_help',
                        'Questions'
                    ]
                ]
            ] : null,

        $pix->canAccess('cms-pages') ?
            [
                'name' => 'CMS Management',
                'items' => [
                    [
                        '?page=cms',
                        'auto_stories',
                        'CMS Pages'
                    ],
                ]
            ] : null,

        $isSuperAdmin ?
            [
                'name' => 'Videos',
                'items' => [
                    [
                        '?page=videos',
                        'video_settings',
                        'Videos'
                    ],
                ]
            ] : null,

        $pix->canAccess('helpdesk') ?
            [
                'name' => 'Help Desk',
                'items' => [
                    [
                        '?page=requests',
                        'contact_support',
                        'Requests',
                        're/\?page\=requests(?!\&sec\=mod)/i'
                    ],
                    $pix->canAccess('helpdesk/ncnw-team') ?
                        [
                            '?page=requests&sec=mod&id=new',
                            'edit_note',
                            'Create Request'
                        ] : null
                ]
            ] : null,

        $isSuperAdmin ?
            [
                'name' => 'Manage Team',
                'items' => [
                    [
                        '?page=sub-admins',
                        'shield_person',
                        'Sub Admins'
                    ],
                    [
                        '?page=permissions',
                        'visibility_lock',
                        'Permissions'
                    ]
                ]
            ] : null,
        [
            'name' => 'Settings',
            'items' => [
                $pix->canAccess('paid-plans') ?
                    [
                        '?page=member-packages',
                        'card_membership',
                        'Membership Plans'
                    ] : null,
                $pix->canAccess('paid-plans') ?
                    [
                        '?page=payment-categories',
                        'money_bag',
                        'Payment Categories'
                    ] : null,

                $pix->canAccess('point-rules') ?
                    [
                        '?page=points-rules',
                        'developer_guide',
                        'Point Rules'
                    ] : null,

                $pix->canAccess('email-templates') ?
                    [
                        '?page=email-templates',
                        'stacked_email',
                        'Email Templates'
                    ] : null,

                [
                    '?page=change-password',
                    'key',
                    'Change Password'
                ]
            ]
        ],
    ];
}
