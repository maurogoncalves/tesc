<?php

use common\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback border-bottom'],
    'inputTemplate' => "<div class='d-flex'><span><img src='img/icon-user.png'></span> <span>{input}</span></div>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback '],
    'inputTemplate' => "<div class='d-flex border-bottom'><span><img src='img/icon-password.png'></span> <span>{input}</span></div>"
];
?>
<link rel="stylesheet" type="text/css" href="css/bootstrap4-bootstrap-utilities.css" >

<style media="screen">
    
  .login-page {
    background-color: white;
  }
  .border {
    border-width: 2px !important;
  }
  .footer {
    position: absolute;
    bottom: 0px;
    width: 100%;
  }
</style>

<div class="login-box bg-white">
    <!-- /.login-logo -->
    <div class="login-box-body border rounded">
        <!-- <p class="login-box-msg">Autentique-se para entrar na plataforma</p> -->

        <div class="d-flex justify-content-center mb-5"> 
          <img src="img/Transporte_Escolar_SJC.png" class="w-75" alt="">
        </div>
        <?=    Alert::widget(); ?>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?=
            $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput([
              'placeholder' => $model->getAttributeLabel('username'),
              'class' => 'form-control border-0'])
        ?>

        <?=
            $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput([
              'placeholder' => $model->getAttributeLabel('password'),
              'class' => 'form-control border-0'])
        ?>

        <div class="row w-100 d-flex justify-content-center ml-1">
            <!-- /.col -->
            <div class="col-xs-4 w-100">
                <?= Html::submitButton('Entrar', [
                  'class' => 'btn btn-flat border-circle-10px w-100 btn-primary btn btn-flat-block btn btn-flat-flat bg-blue',
                   'name' => 'login-button'])
                ?>
                <a class="forgot-password" href="index.php?r=site/request-password-reset">Esqueci minha senha</a><br>
            </div>
            <!-- /.col -->
        </div>

        <div class="m-5 text-center text-primary">
          powered by IPPLAN
        </div>


        <?php ActiveForm::end(); ?>

        <!-- <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-flat btn btn-flat-block btn btn-flat-social btn btn-flat-facebook btn btn-flat-flat"><i class="fa fa-facebook"></i> Sign in
                using Facebook</a>
            <a href="#" class="btn btn-flat btn btn-flat-block btn btn-flat-social btn btn-flat-google-plus btn btn-flat-flat"><i class="fa fa-google-plus"></i> Sign
                in using Google+</a>
        </div> -->
        <!-- /.social-auth-links -->

        <!-- <a href="register.html" class="text-center">Register a new membership</a> -->

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->

<footer class="footer d-flex">
    <div class="text-left">
      <img src="img/Prefeitura-de-SJC.png" class="h-100 p-2" alt="">
    </div>
    <div class="ml-auto text-right">
      <img src="img/IPPLAN_SECUNDÁRIO.png" class="h-100 p-2" alt="">
    </div>
</footer>
