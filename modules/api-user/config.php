<?php

return [
    '__name' => 'api-user',
    '__version' => '0.2.0',
    '__git' => 'git@github.com:getmim/api-user.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/api-user' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'lib-user' => NULL
            ],
            [
                'api' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-form' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'ApiUser\\Controller' => [
                'type' => 'file',
                'base' => 'modules/api-user/controller'
            ],
            'ApiUser\\Library' => [
                'type' => 'file',
                'base' => 'modules/api-user/library'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'api' => [
            'apiUserIndex' => [
                'path' => [
                    'value' => '/user'
                ],
                'handler' => 'ApiUser\\Controller\\User::index',
                'method' => 'GET'
            ],
            'apiUserCreate' => [
                'path' => [
                    'value' => '/user'
                ],
                'handler' => 'ApiUser\\Controller\\User::create',
                'method' => 'POST'
            ],
            'apiUserSingle' => [
                'path' => [
                    'value' => '/user/(:identity)',
                    'params' => [
                        'identity' => 'any'
                    ]
                ],
                'handler' => 'ApiUser\\Controller\\User::single',
                'method' => 'GET'
            ]
        ]
    ],
    'apiUser' => [
        'formatter' => [
            'remove' => []
        ]
    ],
    'libForm' => [
        'forms' => [
            'api-user.create' => [
                'name' => [
                    'rules' => [
                        'required' => true,
                        'unique' => [
                            'model' => 'LibUser\\Library\\Fetcher',
                            'field' => 'name'
                        ],
                    ]
                ],
                'fullname' => [
                    'rules' => [
                        'required' => true,
                    ]
                ],
                'password' => [
                    'rules' => [
                        'required' => true
                    ]
                ]
            ]
        ]
    ]
];
