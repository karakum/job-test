<?php
$config = [
    'bootstrap' => ['debug', 'gii'],
    'modules' => [
        'debug' => 'yii\debug\Module',
        'gii' => [
            'class' => 'yii\gii\Module',
            'generators' => [ //here
                'crud' => [ // generator name
                    'class' => 'yii\gii\generators\crud\Generator', // generator class
                    'templates' => [ //setting for out templates
                        'AdminLTE 2' => '@app/templates/crud/adminlte', // template name => path to template
                    ]
                ]
            ],
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
];
return $config;