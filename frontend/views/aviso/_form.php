<?php

use common\models\Aviso;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\widgets\FileInput;
use yii\helpers\Url;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Aviso */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>
    <div id="row">
         <div class="col-md-2">
         <?= $form->field($model, 'fixado')->widget(Select2::classname(), [
                'data' => Aviso::ARRAY_FIXADO,
                'value' => 2,
                'language' => 'pt',
                'options' => ['placeholder' => 'Selecione', 'class' => 'form-control', 'id' => 'serie'],
                'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false,
                'initialize' => true,
                ],
            ]); ?>
        </div> 
        <div class="col-md-4">
            <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>
        </div> 
        <div class="col-md-3">
            <?= $form->field($model, 'data')->widget(DatePicker::classname(), [
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'value' =>  $model->data,
                'options' => ['placeholder' => 'Data'],
                'pluginOptions' => [
                    'orientation' => 'bottom left',
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy',
                    'startDate' => 'today',
                ]
            ]); ?>
        </div>
        <div class="col-md-3">
            <div class="form-group field-aviso-link ">
            <label class="control-label" for="aviso-link">Link do Vimeo <i class="fa fa-question-circle" aria-hidden="true" data-toggle="tooltip" title="No Vimeo, você deve clicar em 'compartilhar' e copiar apenas o link do vídeo. Cole o Link completo aqui."></i></label>
            <input type="text" id="aviso-link" class="form-control" name="Aviso[link]" placeholder="https://player.vimeo.com/video/<ID_VIDEO>" value="<?= $model->link ?>" aria-invalid="false">

            <div class="help-block"></div>
            </div>
                           

        </div> 
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'mensagem')->textarea(['rows' => 6]) ?>
    </div>

    <?php foreach(['documentoLegislacao','documentoFrete','documentoPasse','documentoOrientacoesSetor','documentoAtualizacaoSistema'] as $doc):
            $metodo = explode('documento', $doc);
            $label = $metodo[1];
            $metodo = 'doc'.$metodo[1];
        ?>
    <div class="row" >
        <hr>
        <div class="col-md-12 ">
            <div class="col-md-6">
                <?php
                echo $form->field($model, $doc.'[]', ['options' => ['class' => 'xx']])->widget(FileInput::classname(), [
                'options' => ['accept' => 'application/pdf', 'multiple' => true],
                'pluginOptions' => ['allowedFileExtensions' => [ 'pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
                ]);
                ?>
            </div>
            <div class="col-md-6">
            <?php if(!$model->isNewRecord){
                    if (!empty($model->$metodo)) {
                        // print_r($model->$metodo);
                        print '<table class="table table-striped table-bordered">';
                        foreach($model->$metodo as $documento){ 
                            $tipo = substr($documento->arquivo, -3);                            
                            echo Yii::$app->fileTable->display($documento,"index.php?r=aviso/delete-doc&id=".$documento->id);
                        
                        }
                        print '</table>';
                    }
                } ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <div class="eow">
        <div class="col-md-12">
            <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
                </div>
            </div> 
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script>    

tinymce.init({
  language: 'pt_BR',
  selector: 'textarea',
  height: 500,
  menubar: false,
  entity_encoding: 'raw',
  plugins: [
    'advlist autolink lists link image charmap print preview anchor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table paste code help wordcount'
  ],
  toolbar: 'undo redo | formatselect | ' +
  ' bold italic backcolor | alignleft aligncenter ' +
  ' alignright alignjustify | bullist numlist outdent indent |' +
  ' removeformat | help',
  content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tiny.cloud/css/codepen.min.css'
  ]
});


</script>

