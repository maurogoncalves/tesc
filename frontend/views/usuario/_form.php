<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Usuario;
use common\models\UsuarioGrupo;
use kartik\select2\Select2;
use common\models\Configuracao;
use common\widgets\Alert;

/* @var $this yii\web\View */
/* @var $model common\models\Usuario */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="box-body">
<div class="usuario-form">

    <?php $form = ActiveForm::begin([
          'encodeErrorSummary' => false,
          'errorSummaryCssClass' => 'help-block',
    ]); ?>
   <?php if($desabilitarPerfil): ;?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning" role="alert">
                     Não é permitido criar condutor/responsável pelo cadastro de usuário. Para condutor utilize o menu <b>Condutores</b> e para responsável utilize o menu de <b>Aluno</b>.
                </div>
        </div>
      </div>
    <?php endif ?>
    <?php if($model->isNewRecord): ;?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-light" role="alert">
                    Atenção: Caso uma senha não seja cadastrada o sistema irá automaticamente atribuir a senha "<b><?= Configuracao::setup()->senhaPadrao ?></b>".
                </div>
        </div>
      </div>
    <?php endif ?>
  <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
        </div>
      <div class="col-md-4">
            <?= $form->field($model, 'username')->textInput(['autocomplete' => 'false']) ?>
        </div>
        <div class="col-md-4">
           <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>

    </div>

  <div class="row">

        <div class="col-md-4">
            <?= $form->field($model, 'idPerfil')->dropDownList(Usuario::ARRAY_PERFIS,['prompt' => 'SELECIONE'] ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'false'])->label('Senha') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'password2')->passwordInput(['autocomplete' => 'false'])?>
        </div>
   
         
    </div>
  <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'status')->dropDownList([1=>'ATIVO', 0=>'INATIVO'] ) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'rg')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?php
                 echo $form->field($model, 'cpf')->textInput(
                [
                    'onBlur'=>'ValidarCPF(this);',
                    'onKeyPress'=>'MascaraCPF(this);',
                    'maxlength'=>'14',
                    'class' => 'form-control cpf'
                    
                ])
             ?>
        </div>
       
    </div>
    <div class="row">
        <div class="col-md-6">
        <?php
        if(Usuario::permissao(Usuario::PERFIL_SUPER_ADMIN)) { 
              foreach ($model->grupos as $es)
                  $model->inputGrupo[] = $es->idGrupo; 
        ?>
        <?=
             $form->field($model, 'inputGrupo')->widget(Select2::classname(), [
                    'data' => UsuarioGrupo::ARRAY_GRUPOS,
                    'language' => 'pt',
                    'options' => [
                            'placeholder' => 'Selecione os grupos',
                            'class' => 'form-control',
                            'id' => 'usuarioGrupos'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                        'initialize' => true,
                    ],
                ]);
         }
        ?>

        </div>
        <div class="col-md-6">
            <?php  if(Usuario::permissao(Usuario::PERFIL_SUPER_ADMIN)) { ?>
                <?= $form->field($model, 'editarDadosProtegidos')->dropDownList([1=>'HABILITADO', 0=>'DESABILITADO'] ) ?>
            <?php } ?>
        </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

</div>

<script type="text/javascript">
    $( document ).ready(function() {
    $('.cpf').mask('000.000.000-00', {reverse: true});
});

</script>