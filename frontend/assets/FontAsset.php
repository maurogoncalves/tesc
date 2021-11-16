<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class FontAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700',
        '//fonts.googleapis.com/css?family=Roboto:300,400,500,600,700',
        '//fonts.googleapis.com/css?family=Lato:300,400,500,600,700'
    ];

    public $cssOptions = [
        'type' => 'text/css',
    ];
}
