<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\UsuarioSearch;
use common\models\Usuario;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<style type="text/css">
	.select2-container--krajee .select2-selection--single {
    height: 34px !important;
    line-height: 1.8528571429 !important;
    /* padding: 10px 47px 6px 12px !important; */
    padding: 8px 25px 6px 12px;
}
/* .select2-container--krajee .select2-selection--single .select2-selection__clear {
    right: 35px !important; 
    top: 3px !important; 
} */

/* Go horse */
.bootstrap-dialog.type-warning .modal-header {
    background-color: #337ab7 !important;
}
.btn-warning {
    background-color: #337ab7 !important;
    border-color: #337ab7 !important;
}

.table-header-ajax th {
    color: #163783 ;
}
.main-header .sidebar-toggle {
  float: left;
  background-color: transparent;
  background-image: none;
/*  padding: 15px 15px; */  
 font-weight: 900;
  font-family: "Font Awesome 5 Free";
}

</style>


<header class="main-header">

    <?= Html::a('<span class="logo-mini"><img src="img/icone.png" style="width:40px; max-height: 60px; margin-top: 10px;"></span><span class="logo-lg"><img src="img/logo.png" style="width:80%;max-height: 60px; margin: 5px auto;"></span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
    </nav>
</header>


<script type="text/javascript">
  
    // Correção para evitar que o select2 fique desconfigurado na tela
    $(window).on('resize', function(){
            $('.select2-hidden-accessible').each(function (index, value){
                    var el = $(this);
                    settings = el.attr('data-krajee-select2'),
                    id = el.attr('id');
                    settings = window[settings];
                    el.select2(settings);
            });
    });
</script>