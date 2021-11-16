<?php
return [
	'language' => 'pt-br',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
       'distanceMatrix' => [
            'class' => 'common\components\DistanceMatrix',
        ],
        'fileTable' => [
            'class' => 'common\components\FileTable',
        ],
       'arrayPicker' => [
            'class' => 'common\components\ArrayPicker',
        ],
        'selectFactory' => [
            'class' => 'common\components\SelectFactory',
        ],
        'showEntriesToolbar' => [
            'class' => 'common\components\ShowEntriesToolbar',
        ],
        'formatter' => [
            // 'class' => 'yii\i18n\formatter',
            'class' => 'common\components\MyFormatter',
            'thousandSeparator' => '.',
            'decimalSeparator' => ',',
            'currencyCode' => 'R$',
            'nullDisplay' => '',
        ],
        ],
];
