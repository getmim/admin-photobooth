<?php

return [
    '__name' => 'admin-photobooth',
    '__version' => '0.1.0',
    '__git' => 'git@github.com:getmim/admin-photobooth.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-photobooth' => ['install','update','remove'],
        'theme/admin/photobooth' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'photobooth' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-pagination' => NULL
            ]
        ],
        'optional' => [
            [
                'admin-photobooth-event' => NULL
            ],
            [
                'admin-photobooth-text' => NULL
            ]
        ]
    ],
    'autoload' => [
        'classes' => [
            'AdminPhotobooth\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-photobooth/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminPhotobooth' => [
                'path' => [
                    'value' => '/photobooth'
                ],
                'method' => 'GET',
                'handler' => 'AdminPhotobooth\\Controller\\Photobooth::index'
            ],
            'adminPhotoboothEdit' => [
                'path' => [
                    'value' => '/photobooth/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminPhotobooth\\Controller\\Photobooth::edit'
            ],
            'adminPhotoboothRemove' => [
                'path' => [
                    'value' => '/photobooth/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminPhotobooth\\Controller\\Photobooth::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'photobooth' => [
                    'label' => 'Photobooth',
                    'icon' => '<i class="fas fa-camera-retro"></i>',
                    'priority' => 0,
                    'route' => ['adminPhotobooth'],
                    'perms' => 'manage_photobooth'
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.photobooth.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ],
            'admin.photobooth.edit' => [
                'fullname' => [
                    'label' => 'Fullname',
                    'type' => 'text',
                    'rules' => [
                        'required' => TRUE
                    ]
                ],
                'phone' => [
                    'label' => 'Phone',
                    'type' => 'tel',
                    'rules' => [
                        'required' => TRUE,
                        'empty' => FALSE,
                        'regex' => '!^(0|62)[0-9]{9,12}$!',
                    ]
                ],
                'images' => [
                    'label' => 'Images',
                    'type' => 'image-list',
                    'form' => 'std-image',
                    'rules' => [
                        'required' => true
                    ]
                ]
            ]
        ]
    ]
];
