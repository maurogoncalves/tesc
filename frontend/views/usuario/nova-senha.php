<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Nova senha';
$this->params['breadcrumbs'][] = $this->title;
?>
<div style="margin:auto; max-width: 40%;">
    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

        <div class="form-group">
            <?= $form->field($model, 'senhaAntiga')->passwordInput(['autofocus' => true, 'autocomplete' => 'off']) ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'password')->passwordInput(['autofocus' => true, 'autocomplete' => 'off']) ?>
        </div>
        <div class="form-group">
            <?= $form->field($model, 'password2')->passwordInput([ 'autocomplete' => 'off'])->label('Repita a senha') ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Salvar', ['class' => 'btn btn-success pull-right']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    $('#usuario-senhaantiga').blur(function() {
        if ($(this).val())
        {
            $.ajax({
                type: 'POST',
                url: 'index.php?r=usuario%2Fvalida-senha-antiga&idUsuario='+<?= $model->id ?>+'&senhaAntiga='+$(this).val(),
            }).done(function(data) {
                if (!data)
                {
                    Swal.fire(
                        '',
                        'Senha antiga está inválida',
                        'error'
                    )
                    $('#usuario-senhaantiga').val('');
                }
            })
        }
    })

    // $('#usuario-password').blur(function() {
    //     if ($(this).val() != $('#usuario-password').val())
    //     {
    //         Swal.fire(
    //             '',
    //             'AS SENHAS NÃO COINCIDEM',
    //             'error'
    //         );
    //     }

    // })
</script>

