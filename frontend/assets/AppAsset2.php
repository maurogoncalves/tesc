<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset2 extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/jquery.loadingModal.min.css',
        //'https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css'
        'css/font-awesome5.css',
    ];
    public $js = [
        'js/ajax-modal-popup.js',
        'js/jquery.loadingModal.min.js',
        'js/mascaras.js',
        'js/devell-inputs.js',
        //'https://maps.googleapis.com/maps/api/js?key=AIzaSyCLdXxxtVSN5I0NA2WJ2buip_pEwfF2pW0&libraries=places',
        'js/jquery.geocomplete.js',
        'js/jquery.mask.min.js',
        'js/moneyMask.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
}
