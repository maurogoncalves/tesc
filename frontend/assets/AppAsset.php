<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'less/styles.css',
        'css/jquery.loadingModal.min.css',
        //'https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css'
        // 'https://cdn.jsdelivr.net/npm/bootstrap-utilities@4.1.3/bootstrap-utilities.css',
        'css/font-awesome5.css',
        'css/AdminLTE.min.css',
        'css/app.css',
        'css/simplelightbox.css',
        'css/leaflet.css',
		'css/jquery.dataTables.min.css',
      
    ];
    public $js = [
        'js/ajax-modal-popup.js',
        'js/jquery.loadingModal.min.js',
        'js/mascaras.js',
        'js/devell-inputs.js',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyCzPbTbUgQ7dXJ-qFanjJRubYZPH2rVtwA&libraries=places',
        'js/jquery.geocomplete.js',
        'js/jquery.mask.min.js',
        'js/moneyMask.js',
        'js/simple-lightbox.js',
        'js/leaflet.js',
        'js/arcgis-to-geojson.js',
        'js/esri-leaflet.js',
        'js/esri-leaflet-geocoder.js',
        'js/GeosearchInput.js',
        'js/bootstrap-geocoder.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@8',
        'js/jquery.tinymce.min.js',
        'js/tinymce.min.js',
		'js/jquery.dataTables.min.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
}
