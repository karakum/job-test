<?php
return [
    'bootstrap' => ['log'],
    'components' => [
        'urlManager' => [
//            'baseUrl' => 'http://jobtest.ru/',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
];
