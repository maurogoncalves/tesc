<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'name' => 'TESC - Transporte Escolar',
    'timeZone' => 'America/Sao_Paulo',
    'language' => 'pt-BR',
    'sourceLanguage'=>'pt-BR',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
            'fileThumb' => [
            'class' => 'common\components\FileThumb',
            'config' => [
                'defaultIcon' => 'img/filethumbs/default.png',
                'jpgIcon' => 'img/filethumbs/jpgIcon.png',
                'pngIcon' => 'img/filethumbs/pngIcon.png',
                'gifIcon' => 'img/filethumbs/gifIcon.png',
                'pdfIcon' => 'img/filethumbs/pdfIcon.png',
                'docIcon' => 'img/filethumbs/docIcon.png',
                'docxIcon' => 'img/filethumbs/docxIcon.png',
                'xlsIcon' => 'img/filethumbs/xlsIcon.png',
                'xlsxIcon' => 'img/filethumbs/xlsxIcon.png',
                'iconWidth' => '80px',
                'iconHeight' => '80px', 
            ]
        ],
      'portalwcf' => [
            'class' => 'mongosoft\soapclient\Client',
            'url' => 'http://portalwcf.ipplan.org.br/Service/IntegracaoExternaService.svc?singleWsdl',
            'options' => [
                'cache_wsdl' => 0,
            ],
        ],

     'assetManager' => [
        'bundles' => [
            'dosamigos\google\maps\MapAsset' => [
                'options' => [
                //api google maps gcloud samuel
                //AIzaSyA5FGajNxey8j_5aBUPwwR-aAeqYShztgA   

                // API GOOGLE MAPS IPPLAN
                //AIzaSyCLdXxxtVSN5I0NA2WJ2buip_pEwfF2pW0
                    'key' => 'AIzaSyCzPbTbUgQ7dXJ-qFanjJRubYZPH2rVtwA',
                    'language' => 'pt-BR',
                    'version' => '3.1.18'
                ]
            ]
        ]
    ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\Usuario',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
            'cookieParams' => ['lifetime' => 7 * 24 *60 * 60]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'email' => [
                    'class' => 'yii\log\EmailTarget',
                    'levels' => ['error', 'warning'],
                    'message' => [
                        'from' => ['email_from@gmail.com'],
                        'to' => ['email_to@gmail.com'],
                        'subject' => 'New example.com log message',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.devell.com.br',
                'username' => 'relay@devell.com.br',
                'password' => '@Bufford2019',
                'port' => '587',
                // 'encryption' => 'none',
            ],
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
   
    ],
    'params' => $params,

    'modules' => [
         'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.*', 'XXX.XXX.XXX.XXX'] // adjust this to your needs
        ],
    ],
];
