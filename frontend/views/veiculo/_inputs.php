<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use common\models\Escola;
use kartik\date\DatePicker;
use common\models\Veiculo;
use common\models\Condutor;
use common\models\Modelo;

use kartik\widgets\FileInput;
use common\models\NecessidadesEspeciais;

$ano=date('Y')+1;
$selectAnos = [];
while($ano >= 1990)
{
    $selectAnos[$ano] = $ano;
    $ano -= 1;
}

?>  
  <div class="row">
        <div class="col-md-2">
           
        <?php   
              echo $form->field($model, 'idModelo')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Modelo::find()->all(), 'id','nome','marca.nome'),
                'value' =>  $model->idModelo,
                'language' => 'pt',
                'options' => ['placeholder' => 'Selecione o modelo', 'class' => 'form-control', 'id' => 'modelo'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                    'initialize' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-md-2">
        <?php   
              echo $form->field($model, 'tipoVeiculo')->widget(Select2::classname(), [
                'data' => Veiculo::ARRAY_TIPO_VEICULO,
                'value' =>  $model->tipoVeiculo,
                'language' => 'pt',
                'options' => ['placeholder' => 'Selecione o tipo do veículo', 'class' => 'form-control', 'id' => 'tipoVeiculo'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                    'initialize' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4">
            <?php   
              echo $form->field($model, 'idCondutor')->widget(Select2::classname(), [
                // 'data' => ArrayHelper::map(Condutor::disponivelVeiculo($model->idCondutor), 'id', 'nome'),
				//alteracao feita para exibir apenas condutores ativos
                'data' => ArrayHelper::map(Condutor::find()->andWhere('status = 1')->all(), 'id', 'nome'),
                'value' =>  $model->idCondutor,
                'language' => 'pt',
                'options' => ['placeholder' => 'Selecione o condutor', 'class' => 'form-control', 'id' => 'condutor'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                    'initialize' => true,
                ],
            ]);
            ?>
        </div>
        <div class="col-md-2">
            <?=    

            $form->field($model, 'anoFabricacao')->dropDownList($selectAnos, ['prompt' => 'SELECIONE']);
            ?>
        </div>
        <div class="col-md-2">
            <?=    

            $form->field($model, 'anoModelo')->dropDownList($selectAnos, ['prompt' => 'SELECIONE']);
            ?>
        </div>
    </div>
    <div class="row">

        <div class="col-md-1">
            <?= $form->field($model, 'placa')->textInput(['maxlength' => 7]) ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'capacidade')->textInput(['maxlength' => true, 'type' => 'number' ]) ?>
        </div>
        <div class="col-md-2">
            <?php   
              echo $form->field($model, 'alocacao')->widget(Select2::classname(), [
                'data' => Veiculo::ARRAY_ALOCACAO,
                'value' =>  $model->alocacao,
                'language' => 'pt',
                'options' => ['placeholder' => 'Selecione a alocação', 'class' => 'form-control', 'id' => 'alocacao'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => false,
                    'initialize' => true,
                ],
            ]);
            ?>
        </div>

        <div class="col-md-2">
           <?=  $form->field($model, 'adaptado')->dropDownList(Veiculo::ARRAY_ADAPTADO) ?>
        </div>
        <div class="col-md-3">
           <?=  $form->field($model, 'combustivel')->dropDownList(Veiculo::ARRAY_TIPO) ?>
        </div>
        <div class="col-md-3">
        <?php 
                echo $form->field($model, 'dataVencimentoCRLV')->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'value' =>  $model->dataVistoriaMunicipal,
                    'options' => ['placeholder' => 'Data'],
                    'pluginOptions' => [
                        'orientation' => 'bottom left',
                        'autoclose'=>true,
                        'format' => 'dd/mm/yyyy',
                        // 'endDate' => 'today',
                    ]
                ]);
            ?>
        </div>
    </div>
    <div class="row">

         <div class="col-md-3">
             <?php 
                echo $form->field($model, 'dataVistoriaMunicipal')->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'value' =>  $model->dataVistoriaMunicipal,
                    'options' => ['placeholder' => 'Data'],
                    'pluginOptions' => [
                        'orientation' => 'bottom left',
                        'autoclose'=>true,
                        'format' => 'dd/mm/yyyy',
                        'endDate' => 'today',
                    ]
                ]);
            ?>
         </div>
         <div class="col-md-3">
             <?php 
                echo $form->field($model, 'dataVistoriaEstadual')->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'value' =>  $model->dataVistoriaEstadual,
                    'options' => ['placeholder' => 'Data'],
                    'pluginOptions' => [
                        'orientation' => 'bottom left',
                        'autoclose'=>true,
                        'format' => 'dd/mm/yyyy',
                        // 'endDate' => 'today',
                    ]
                ]);
            ?>
         </div>
        <div class="col-md-3">
            <?= $form->field($model, 'numApolice')->textInput(['maxlength' => true, 'type' => 'number' ]) ?>
            
        </div>
         <div class="col-md-3">
             
             <?php 
                echo $form->field($model, 'dataVencimentoSeguro')->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'value' =>  $model->dataVencimentoSeguro,
                    'options' => ['placeholder' => 'Data'],
                    'pluginOptions' => [
                        'orientation' => 'bottom left',
                        'autoclose'=>true,
                        'format' => 'dd/mm/yyyy',
                        'startDate' => 'today',
                    ]
                ]);
            ?>
         </div>
    </div>
    <div class="row">
        <div class="col-md-6">
             <div class="template-fileinput <?php if(empty($model->docApolice)) print 'without-files'; ?>">
             <?php
              echo $form->field($model, 'documentoApoliceSeguro[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'showPreview' => false, 'showUpload' => false, 'initialPreview' => [],  'language' => Yii::$app->language,'showUpload' => false]
            ]); ?>
                <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>

            </div>
        </div>
      <div class="col-md-6">
             <div class="template-fileinput <?php if(empty($model->docCRLV)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'documentoCRLV[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'showPreview' => false, 'showUpload' => false, 'initialPreview' => [], 'language' => Yii::$app->language, 'showUpload' => false]
            ]); ?>
                <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
            </div>
        </div>

    </div>
    <div class="row">
         <div class="col-md-6">
         <div class="template-fileinput <?php if(empty($model->docVistoriaMunicipal)) print 'without-files'; ?>">
                <?php
              echo $form->field($model, 'documentoVistoriaMunicipal[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'showPreview' => false, 'showUpload' => false, 'initialPreview' => [], 'language' => Yii::$app->language, 'showUpload' => false]
            ]); ?>
                <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="template-fileinput <?php if(empty($model->docVistoriaEstadual)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'documentoVistoriaEstadual[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'showPreview' => false, 'showUpload' => false, 'initialPreview' => [], 'language' => Yii::$app->language, 'showUpload' => false]
            ]); ?>
                <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="template-fileinput <?php if(empty($model->docDpvat)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'documentoDPVAT[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'showPreview' => false, 'showUpload' => false, 'initialPreview' => [], 'language' => Yii::$app->language, 'showUpload' => false]
            ]); ?>
                <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="template-fileinput <?php if(empty($model->fotoVeiculo)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'anexoFotoVeiculo[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>false],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','jpeg'],  'showPreview' => false, 'showUpload' => false, 'initialPreview' => [], 'language' => Yii::$app->language, 'showUpload' => false]
            ]); ?>
                <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>

            </div>
        </div>
    </div>
    <div class="row">
         <div class="col-md-6">
            <div class="template-fileinput <?php if(empty($model->fotoPlaca)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'anexoFotoPlaca[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>false],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','jpeg'],  'showPreview' => false, 'showUpload' => false, 'initialPreview' => [], 'language' => Yii::$app->language, 'showUpload' => false]
            ]); ?>
                <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>

            </div>        
    </div>