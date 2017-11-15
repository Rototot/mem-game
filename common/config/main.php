<?php
$_ENV['yii_config'] = json_decode(file_get_contents(__DIR__ . '/../../.env.json'), true);

return [
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'i18n' => [
            'translations' => [
              '*' => [
                  'class' => 'yii\i18n\PhpMessageSource',
                  'sourceLanguage' => 'ru-RU',
                  'basePath' => '@common/messages',
                    'forceTranslation' => true,
              ]
            ],
        ],
    ],
];
