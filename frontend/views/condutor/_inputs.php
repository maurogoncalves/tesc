<?php
use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Condutor;
use common\models\Usuario;
use yii\helpers\Html; 
use kartik\select2\Select2;
use common\models\TipoLogradouro;


/* @var $this yii\web\View */
/* @var $model common\models\Planoconta */
/* @var $form yii\widgets\ActiveForm */
?> 
<style type="text/css">

.input-group {
  width: 100%;
}

.field-condutor-endereco  li {
  padding: 3px 20px;
  margin: 0;
}

.field-condutor-endereco  li:hover{
  background: #7FDFFF;
  border-color: #7FDFFF;
}

.geocoder-control-selected{
  background: #7FDFFF;
  border-color: #7FDFFF;
}

.field-condutor-endereco  ul li {
  list-style-type: none;
}
</style>

<?php if (in_array(Yii::$app->user->identity->idPerfil, [
              Usuario::PERFIL_SUPER_ADMIN,
              Usuario::PERFIL_SECRETARIO,
              Usuario::PERFIL_DIRETOR,
              Usuario::PERFIL_DRE
            ])) { ?>
    <div class="box-header with-border">
      <h4>Informações do condutor</h4>
    </div>

    <div class="row">
      <div class="col-md-2">
        <?= $form->field($model, 'status')->dropDownList(Condutor::ARRAY_STATUS) ?>
      </div>

      <div class="col-md-10">
        <?= $form->field($model, 'pendencias')->textArea(['rows' => 2, 'maxlength' => 300]) ?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-3">
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
      
      <div class="col-md-3">
        <?php 
          echo $form->field($model, 'dataNascimento')->widget(DatePicker::classname(), [
              'type' => DatePicker::TYPE_COMPONENT_APPEND,
              'value' =>  $model->dataNascimento,
              'options' => ['placeholder' => 'Data'],
              'pluginOptions' => [
                  'orientation' => 'bottom left',
                  'autoclose'=>true,
                  'format' => 'dd/mm/yyyy',
                  'endDate' => '-18y',
                  'startDate' => '-100y',
              ]
          ]);
        ?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <?= $form->field($model, 'nit')->textInput([
                'onBlur'=>'MascaraNIT(this);',
                'onKeyPress'=>'MascaraNIT(this);',
                'maxlength'=>'15',
        ]) ?>
      </div>
      <div class="col-md-3">
        <?= $form->field($model, 'alvara')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-3">
        <?= $form->field($model, 'inscricaoMunicipal')->textInput() ?>
      </div>
      <div class="col-md-3">
        <?php
          foreach ($model->regioes as $es)
              $model->inputRegiao[] = $es->regiao; 
        ?>
        <?= $form->field($model, 'inputRegiao')->widget(Select2::classname(), [
                'data' => Condutor::ARRAY_REGIAO,
                'language' => 'pt',
                'options' => [
                        'placeholder' => 'Selecione as regiões',
                        'class' => 'form-control',
                        'id' => 'condutorRegioes'
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'multiple' => true,
                    'initialize' => true,
                ],
            ]);
        ?>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-2">
        <?=  $form->field($model, 'cep')->textInput(['id' => 'cep', 'maxlength' => 9, 'autocomplete' => 'off']);?>
      </div>
      <div class="col-md-2">
        <?= $form->field($model, 'tipoLogradouro')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(TipoLogradouro::find()->all(), 'TIPO', 'TIPO'),
            'value' => '',
            'language' => 'pt', 
            'options' => ['placeholder' => 'Selecione', 'class' => 'form-control', 'id' => 'tipo-logradouro'],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple' => false,
                'initialize' => true,
            ],
            'pluginEvents' => [
              "change" => "function() { tipoLogradouro(); }",
            ],
        ]); ?>
      </div>
      <div class="col-md-3">
        <div class="mapModal">
          <?= $form->field($model, 'endereco')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

                <!-- <?= $form->field($model, 'endereco', [
            'template' => '{label}<div class="input-group"><div class="address-input">{input}</div>
            <span class="input-group-btn"><button class="btn btn-default pickLocation" type="button" ><i class="fa fa-map" aria-hidden="true"></i></button></span></div>{error}{hint}'
            ]); ?> -->
            <?= $form->field($model, 'lat', ['options' => ['class' => 'lat']])->hiddenInput(['maxlength' => true])->label(false); ?>
            <?= $form->field($model, 'lng', ['options' => ['class' => 'lng']])->hiddenInput(['maxlength' => true])->label(false); ?>
        </div>
      </div>
      <div class="col-md-1"> 
        <?=  $form->field($model, 'numeroResidencia')->textInput(['type' => 'number']);?>
      </div>
      <div class="col-md-2"> 
        <?=  $form->field($model, 'bairro')->textInput();?>
      </div>  
      <div class="col-md-2">
        <?=  $form->field($model, 'complementoResidencia')->textInput(['autocomplete' => 'off']);?>
      </div>
    </div>
    <div class="row">
      <div id="tabelaEndereco"></div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div id="location-map">
          <div id="mapUser" style="min-height:400px;display:none;"></div>
        </div>
      </div>
    </div>
    <!-- <div class="row" id="map-modal-ajax" style="display: none;">
        <div class="col-md-12">
          <div  id="location-map-ajax">
            <div id="map_canvas_ajax"></div>
          </div>
        </div>
    </div>  -->
    <div class="row">
      <div class="col-md-3">
        <?= $form->field($model, 'rg')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-3">
        <?= $form->field($model, 'orgaoEmissor')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-3">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">
        <?= $form->field($model, 'cnhRegistro')->textInput(['maxlength' => true, 'type' => 'number']) ?>
      </div>
      <div class="col-md-3">
        <?php 
          echo $form->field($model, 'cnhValidade')->widget(DatePicker::classname(), [
              'type' => DatePicker::TYPE_COMPONENT_APPEND,
              'value' =>  $model->cnhValidade,
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
      <div class="col-md-6">
        <?php 
          echo '<label class="control-label">Período do contrato</label>';
          echo DatePicker::widget([
              'model' => $model,
              'attribute' => 'dataInicioContrato',
              'attribute2' => 'dataFimContrato',

              'options' => ['placeholder' => 'Início do contrato'],
              'options2' => ['placeholder' => 'Fim do contrato'],
              'type' => DatePicker::TYPE_RANGE,
              'form' => $form,
                    'separator' => 'até',
              'pluginOptions' => [
                  'autocomplete' => 'off',
                  'format' => 'dd/mm/yyyy',
                  'autoclose' => true,
                  'orientation' => 'bottom left',


              ]
          ]);       
        ?>
      </div>  
    </div>
    
    <div class="row"> 
      <div class="col-md-3">
        <?= $form->field($model, 'tipoContrato')->dropDownList(Condutor::ARRAY_TIPO,[
            'prompt' => 'Selecione'
        ]) ?>
      </div>
      <div class="col-md-3">
        <?=  $form->field($model, 'valorPagoKmViagem')->textInput(['maxlength' => true, 'class' => 'form-control money']);?>
      </div>
      <div class="col-md-3" id="minKmDia" style="display: none;">
        <?=  $form->field($model, 'minKmDia')->textInput(['maxlength' => true, 'class' => 'form-control', 'type' => 'number']);?>
      </div>
      <div class="col-md-3" id="maxKmDia" style="display: none;">
        <?=  $form->field($model, 'maxKmDia')->textInput(['maxlength' => true, 'class' => 'form-control', 'type' => 'number']);?>
      </div>
      <div class="col-md-3" id="maxViagensDia" style="display: none;">
        <?=  $form->field($model, 'maxViagensDia')->textInput(['maxlength' => true, 'class' => 'form-control', 'type' => 'number']);?>
      </div>
    </div>

    <div class="row"> 
      <div class="col-md-3">
        <?=  $form->field($model, 'kmViagemAtual')->textInput(['maxlength' => true, 'class' => 'form-control', 'type' => 'number']);?>
      </div>
      <div class="col-md-3">
        <?=  $form->field($model, 'kmViagemSabadoLetivo')->textInput(['maxlength' => true, 'class' => 'form-control', 'type' => 'number']);?>
      </div>
      <div class="col-md-3">
        <?=  $form->field($model, 'valorAF')->textInput(['maxlength' => true, 'class' => 'form-control money']);?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="template-fileinput <?php if(empty($model->docCnhCondutor)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'documentoCNHCondutor[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
            ]);  ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-6">
        <div class="template-fileinput <?php if(empty($model->docCRLV)) print 'without-files'; ?>">
          <?php
          echo $form->field($model, 'documentoCRLV[]')->widget(FileInput::classname(), [
            'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
            'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
            ]); ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>      
      </div>
      <div class="col-md-6">
        <div class="template-fileinput <?php if(empty($model->docApoliceSeguro)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'documentoApoliceSeguro[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
              ]);  ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div div class="template-fileinput <?php if(empty($model->docAutorizacaoEscolar)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'documentoAutorizacaoEscolar[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
              ]);  ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
        <div class="template-fileinput <?php if(empty($model->docProntuarioCNH)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'documentoProntuarioCNH[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
              ]);  ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>
      <div class="col-md-6">
        <div class="template-fileinput <?php if(empty($model->fotoMotorista)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'anexoFotoMotorista[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'image/*', 'multiple'=>false],
              'pluginOptions'=>[
                  'allowedFileExtensions'=>['jpg','gif','png','jpeg'],  
                  'language' => Yii::$app->language, 
                  'showPreview' => true, // function () { return (!empty($model->fotoMotorista) ? true : false); }, 
                  'initialPreview' => (($model->fotoMotorista != '') ? [\Yii::getAlias('@web').'/'.$model->fotoMotorista] : []), 
                  'initialPreviewConfig'=> [ ['url' => \Yii::getAlias('@web').'/index.php?r=condutor/remover-foto-motorista&id='.$model->id] ],
                  'showUpload' => false,
                  'overwriteInitial'=>false,
                  'showRemove' => true,
                  'initialPreviewAsData'=> true, // function () { return (($model->fotoMotorista) ? true : false); },
                  ]
              ]);  ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>
    </div>
    <div class="row">
      <div class="col-md-3">     
        <?= $form->field($model, 'telefone', [
            'template' => '{label}<div class="input-group"><span class="input-group-addon"><i class="fab fa-whatsapp icon-whatsapp-input" aria-hidden="true" ></i><input type="checkbox" aria-label="..." class=""  name="Condutor[telefoneWhatsapp]"'.$model->telefoneWhatsapp.' ></span> {input}</div>{error}{hint}'
          ])->textInput(
              [
                  'onBlur'=>'MascaraTelefone(this);',
                  'onKeyPress'=>'MascaraTelefone(this);',
                  'maxlength'=>'15',
          ]);
        ?>
      </div>
      <div class="col-md-3">     
         <?= $form->field($model, 'telefone2', [
            'template' => '{label}<div class="input-group"><span class="input-group-addon"><i class="fab fa-whatsapp icon-whatsapp-input" aria-hidden="true" ></i><input type="checkbox" aria-label="..." class=""  name="Condutor[telefoneWhatsapp2]"'.$model->telefoneWhatsapp2.' ></span> {input}</div>{error}{hint}'
            ])->textInput(
                [
                    'onBlur'=>'MascaraTelefone(this);',
                    'onKeyPress'=>'MascaraTelefone(this);',
                    'maxlength'=>'15',
            ]); ?>
      </div>
      <div class="col-md-3">     
         <?= $form->field($model, 'celular', [
            'template' => '{label}<div class="input-group"><span class="input-group-addon"><i class="fab fa-whatsapp icon-whatsapp-input" aria-hidden="true" ></i><input type="checkbox" aria-label="..." class=""  name="Condutor[celularWhatsapp]"'.$model->celularWhatsapp.' ></span> {input}</div>{error}{hint}'
            ])->textInput(
                [
                    'onBlur'=>'MascaraCelular(this);',
                    'onKeyPress'=>'MascaraCelular(this);',
                    'maxlength'=>'15',
            ]); ?>
      </div>
      <div class="col-md-3">     
         <?= $form->field($model, 'celular2', [
            'template' => '{label}<div class="input-group"><span class="input-group-addon"><i class="fab fa-whatsapp icon-whatsapp-input" aria-hidden="true" ></i><input type="checkbox" aria-label="..." class=""  name="Condutor[celularWhatsapp2]"'.$model->celularWhatsapp2.' ></span> {input}</div>{error}{hint}'
            ])->textInput(
                [
                    'onBlur'=>'MascaraCelular(this);',
                    'onKeyPress'=>'MascaraCelular(this);',
                    'maxlength'=>'15',
            ]); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <?= $form->field($model, 'folhaPonto')->textInput(['maxlength' => true, 'placeholder' => 'http://www.google.com']) ?>
      </div>
      <div class="col-md-6">
        <?= $form->field($model, 'pesquisaRota')->textInput(['maxlength' => true]) ?>
      </div>
    </div>

    <div class="box-header with-border">
      <h4>Informações do monitor</h4>
    </div>
    <div class="row">
      <div class="col-md-4">
        <?= $form->field($model, 'nomeMonitor')->textInput(['maxlength' => true]) ?>
      </div>
      <div class="col-md-4">
        <?= $form->field($model, 'rgMonitor')->textInput(['maxlength' => true]) ?>
      </div>
  
      <div class="col-md-4">
        <?php
            echo $form->field($model, 'cpfMonitor')->textInput(
          [
              'onBlur'=>'ValidarCPF(this);',
              'onKeyPress'=>'MascaraCPF(this);',
              'maxlength'=>'14'
          ])
        ?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">     
        <?= $form->field($model, 'telefoneMonitor', [
          'template' => '{label}<div class="input-group"><span class="input-group-addon"><i class="fab fa-whatsapp icon-whatsapp-input" aria-hidden="true" ></i><input type="checkbox" aria-label="..." class=""  name="Condutor[telefoneMonitorWhatsapp]"'.$model->telefoneMonitorWhatsapp.' ></span> {input}</div>{error}{hint}'
          ])->textInput(
              [  
                  'onBlur'=>'MascaraTelefone(this);',
                  'onKeyPress'=>'MascaraTelefone(this);',
                  'maxlength'=>'15',
          ]); ?>
      </div>
      <div class="col-md-4">     
            <?= $form->field($model, 'celularMonitor', [
              'template' => '{label}<div class="input-group"><span class="input-group-addon"><i class="fab fa-whatsapp icon-whatsapp-input" aria-hidden="true" ></i><input type="checkbox" aria-label="..." class=""  name="Condutor[celularMonitorWhatsapp]"'.$model->celularMonitorWhatsapp.' ></span> {input}</div>{error}{hint}'
              ])->textInput(
                  [
                      'onBlur'=>'MascaraCelular(this);',
                      'onKeyPress'=>'MascaraCelular(this);',
                      'maxlength'=>'15',
              ]); ?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="template-fileinput <?php if(empty($model->docRgMonitor)) print 'without-files'; ?>">
            <?php
          echo $form->field($model, 'documentoMonitorRG[]')->widget(FileInput::classname(), [
          'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
          'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
          ]);  ?>
              <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="template-fileinput <?php if(empty($model->docContratoTrabalho)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'documentoMonitorContratoTrabalho[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
            ]);  ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>
      <div class="col-md-6">
        <div class="template-fileinput <?php if(empty($model->docCertidaoAntecedentesCriminais)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'documentoMonitorCertidaoAntecedentesCriminais[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>[
                'allowedFileExtensions'=>['jpg','gif','png','pdf'],  
                'language' => Yii::$app->language, 
                'showPreview' => false, 
                'initialPreview' => [], 
                'showUpload' => false
              ]
              ]);  ?>
        <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
      </div>   
    </div>
  <?php }
  else 
  { ?>

    <div class="row">
      <div class="col-md-4">
        <div class="template-fileinput <?php if(empty($model->fotoMotorista)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'anexoFotoMotorista[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'image/*', 'multiple'=>false],
              'pluginOptions'=>[
                  'allowedFileExtensions'=>['jpg','gif','png','jpeg'],  
                  'language' => Yii::$app->language, 
                  'showPreview' => true, // function () { return (!empty($model->fotoMotorista) ? true : false); }, 
                  'initialPreview' => (($model->fotoMotorista != '') ? [\Yii::getAlias('@web').'/'.$model->fotoMotorista] : []), 
                  'initialPreviewConfig'=> [ ['url' => \Yii::getAlias('@web').'/index.php?r=condutor/remover-foto-motorista&id='.$model->id] ],
                  'showUpload' => false,
                  'overwriteInitial'=>false,
                  'showRemove' => true,
                  'initialPreviewAsData'=> true, // function () { return (($model->fotoMotorista) ? true : false); },
                  ]
              ]);  ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>
      <div class="col-md-8">
        <div class="col-md-6">
          <div class="template-fileinput <?php if(empty($model->docCnhCondutor)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'documentoCNHCondutor[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
              ]);  ?>
            <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
          </div>
          <div class="template-fileinput <?php if(empty($model->docCRLV)) print 'without-files'; ?>">
            <?php
            echo $form->field($model, 'documentoCRLV[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
              ]); ?>
            <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
          </div> 
  
          <div class="template-fileinput <?php if(empty($model->docApoliceSeguro)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'documentoApoliceSeguro[]')->widget(FileInput::classname(), [
                'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
                'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
                ]);  ?>
            <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
          </div>
        </div>

        <div class="col-md-6">
          <div div class="template-fileinput <?php if(empty($model->docAutorizacaoEscolar)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'documentoAutorizacaoEscolar[]')->widget(FileInput::classname(), [
                'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
                'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
                ]);  ?>
            <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
          </div>   
          <div class="template-fileinput <?php if(empty($model->docProntuarioCNH)) print 'without-files'; ?>">
            <?php
              echo $form->field($model, 'documentoProntuarioCNH[]')->widget(FileInput::classname(), [
                'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
                'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
                ]);  ?>
            <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
          </div> 
        </div>

      </div>
    </div>
    
    <div class="box-header with-border">
      <h4>Documentos do monitor</h4>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="template-fileinput <?php if(empty($model->docRgMonitor)) print 'without-files'; ?>">
            <?php
          echo $form->field($model, 'documentoMonitorRG[]')->widget(FileInput::classname(), [
          'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
          'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
          ]);  ?>
              <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>

      <div class="col-md-4">
        <div class="template-fileinput <?php if(empty($model->docContratoTrabalho)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'documentoMonitorContratoTrabalho[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png','pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
            ]);  ?>
          <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
        </div>   
      </div>
      <div class="col-md-4">
        <div class="template-fileinput <?php if(empty($model->docCertidaoAntecedentesCriminais)) print 'without-files'; ?>">
          <?php
            echo $form->field($model, 'documentoMonitorCertidaoAntecedentesCriminais[]')->widget(FileInput::classname(), [
              'options'=>['accept'=>'application/pdf, image/*', 'multiple'=>true],
              'pluginOptions'=>[
                'allowedFileExtensions'=>['jpg','gif','png','pdf'],  
                'language' => Yii::$app->language, 
                'showPreview' => false, 
                'initialPreview' => [], 
                'showUpload' => false
              ]
              ]);  ?>
        <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
      </div>   
    </div>


  <?php } ?>



<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Confirmar local</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div  id="location-map">
              <div id= "mapUser" style="min-height:500px;"></div>
            </div>
          </div>
        </div> 
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-success pull-right" id="saveLocation">Confirmar</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$('#cep').keypress(function (e) {
  if(e.which == 13 ) {
    $('#cep').blur();
    return false;
  };
});

var marker =  L.marker(); 
var geocoder; 
var address;
var flag = false;
var flagMapa = false;

var selectedDiv;
var currentLocation;
var search;
var enderecoAtual = '<?=   $model->endereco ?>';
var latAtual = <?= print $model->lat; ?>;
var lngAtual = <?= print $model->lng; ?>;
var geocodeService = L.esri.Geocoding.geocodeService();
$(".field-cep").append('<p class="loading"></p>');
$(".field-condutor-endereco").append('<p class="loading"></p>');
$("#cep").change(function() {
  esconderTabela();
  let cep = $("#cep").val();
  if(!cep)
    return null;
  let logradouro = $("#condutor-endereco").val(); 
  let tipo = $("#tipo-logradouro").val();
  $(".field-cep .loading").html('<i class="fas fa-hourglass-half"></i> Buscando informações...');
  $.getJSON( "index.php?r=pesquisa-logradouro/pesquisa-logradouro", {"logradouro": logradouro, "tipo": tipo, "cep": cep})
  .done(function(data) {
    $(".field-cep .loading").html('');

    $("#tabelaEndereco").css("display", "none");
    if(data.status) {
      mostrarTabela(data.enderecos);
      //$('#condutor-endereco').val(data.endereco.TIPO_LOGRADOURO+' '+data.endereco.LOGRADOURO+', '+data.endereco.BAIRRO);
    } else {
      Swal.fire(
            'CEP não encontrado',
            'Confira os números do CEP',
            'warning'
          )
      $("#cep").focus();
      $("#cep").val("");
      $("#condutor-endereco").val("");
      mostrarMapa(); 
    }
      
   });
});
mostrarMapa();
function ocultarMapa(){
  $("#mapUser").css("display", "none");
}
function mostrarMapa(){
  let logradouro = $("#condutor-endereco").val();
  let bairro= $("#condutor-bairro").val();
  let num = $("#condutor-numeroresidencia").val();
    let tipo = $("#tipo-logradouro").val();
  if(logradouro && num){
    $("#mapUser").css("display", "block");
  
      let enderecoCompleto = tipo+` `+logradouro+`, `+num+` `+bairro;
     
      geoSearch(enderecoCompleto);
  }
  else {
    $("#mapUser").css("display", "none");
  }
  flagMapa = true;

}

$('#condutor-numeroresidencia').change(function(){
  mostrarMapa();
});
$("#condutor-endereco").change(function() {
  esconderTabela();
  mostrarMapa();
  flag = false;
  let logradouro = $("#condutor-endereco").val();
  let tipo = $("#tipo-logradouro").val();
  let cep = $("#cep").val();
    $(".field-condutor-endereco .loading").html('<i class="fas fa-hourglass-half"></i> Buscando informações...');
  $.getJSON( "index.php?r=pesquisa-logradouro/pesquisa-logradouro", {"logradouro": logradouro, "tipo": tipo, "cep": cep})
  .done(function(data) {
    $(".field-condutor-endereco .loading").html('');

    $("#tabelaEndereco").css("display", "none");
    if(data.status) {
      mostrarTabela(data.enderecos);
    } else {
      Swal.fire(
            'Logradouro não encontrado',
            'Digite o CEP ou o nome de um logradouro válido',
            'warning'
          )
      $("#condutor-endereco").focus();
      $("#cep").val("");
      $("#condutor-endereco").val("");
      //mostrarMapa();
    }
      
   });
});

function tipoLogradouro(flag=0){
  
  // console.log("tipoLogradouro()");
  // esconderTabela();
  // let logradouro = $("#condutor-endereco").val();
  // let tipo = $("#tipo-logradouro").val();
  
  // if(logradouro && tipo){
  //   console.log('Logradouro changed');
  //   $('#condutor-endereco').trigger('change');
  // } 
  
  // if(cep && tipo) {
  //   console.log('CEP CHANGED0');
  //   $('#cep').trigger('change');
  // }
}
// $("#condutor-endereco").change(() => {
//   let endereco = $('#condutor-endereco').val();
//   $.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest?text='+endereco+'&maxSuggestions=5&f=json').done((x) => console.log(x));
// })
function esconderTabela(){
  $("#tabelaEndereco").css("display", "none");
}
function mostrarTabela(data){
  let num = $("#condutor-numeroresidencia").val();
  $("#tabelaEndereco").html("");
  if(data.length)
    $("#tabelaEndereco").css("display", "block");
    
    $("#tabelaEndereco").append(`
      <table class="table table-hover table-striped table-bordered" id="" >  
      <thead>
        <tr>
            <td colspan="4" align="center"><b>Selecione o endereço</b></td>
        </tr>
        <tr>
          <td>CEP</td>
          <td>Logradouro</td>
          <td>Bairro</td>
          <td>Cidade</td>
          <td>Selecione</td>
        </tr>
      </thead>
      <tbody id="tabelaEnderecoBody" >
      `);
    for(let i = 0; i <= data.length ; i++){  
      let local = data[i];
      if(local){

        let enderecoCompleto = '';
        if(num){
          enderecoCompleto = local.TIPO_LOGRADOURO+` `+local.LOGRADOURO+`, `+num+` `+local.BAIRRO;
        } else {
          enderecoCompleto = local.TIPO_LOGRADOURO+` `+local.LOGRADOURO+`, `+local.BAIRRO;

        }
        
        console.log(enderecoCompleto);
        $('#tabelaEnderecoBody').append(`<tr><td>`+local.CEP+`</td><td>`+local.TIPO_LOGRADOURO+` `+local.LOGRADOURO+`</td><td>`+local.BAIRRO+`</td><td>`+local.CIDADE+`</td><td algn="center"><a class="btn btn-success" onclick='selecionarEndereco("`+enderecoCompleto+`","`+local.LOGRADOURO+`","`+local.BAIRRO+`","`+local.CEP+`","`+local.TIPO_LOGRADOURO+`")' >Selecionar endereço</a></td></tr>`); 
      }
    }
    $("#tabelaEndereco").append( `
      </tbody>
      </table>
      `);

}
  
//$.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest?text=rua+maria+carolina+de+jesus&maxSuggestions=5&f=json').done((x) => console.log(x.suggestions));
//$.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest?text=rua+maria+carolina+de+jesus&maxSuggestions=5&f=json').done((x) => console.log(x));
// // // 

function selecionarEndereco(endereco, logradouro, bairro, cep, tipo){
  flag = true;
  $("#condutor-endereco").val(logradouro);
  $("#condutor-bairro").val(bairro);
  $('#tipo-logradouro').val(tipo).trigger("change");
  $("#cep").val(cep);
  $("#tabelaEndereco").css("display", "none");
  mostrarMapa();
  geoSearch(endereco);
}
function geoSearch(endereco){
  if(!flagMapa)
    return null;
  console.log('geoSearch');
  // $.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?outSr=4326&forStorage=false&outFields=*&maxLocations=20&singleLine='+encodeURI(endereco)+'%2C%20S%C3%83O%20JOS%C3%89%20DOS%20CAMPOS%20-%20SP&f=json')
  // .done(function(data) {
  //       let posicao = data.candidates[0];
  //       addMarker(posicao.location.y, posicao.location.x, endereco);
    
  // });
  geocoder = new google.maps.Geocoder();
  geocoder.geocode({ 'address': endereco + ', São José dos Campos, São Paulo, Brasil', 'region': 'BR' }, function (results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {
              var latitude = results[0].geometry.location.lat();
              var longitude = results[0].geometry.location.lng();
              console.log('GEOCODE', latitude, longitude);
              addMarker(latitude, longitude, endereco);
          }
      }
  });
}
$("#condutor-endereco").attr("autocomplete", "off");

var map = L.map("mapUser", {
    'center': [-23.223701,-45.9009074],
    'zoom' : 15,
    'minZoom': 15,
    'maxZoom': 18
  }); 

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  //this.map.zoomControl.remove();
  map.scrollWheelZoom.disable();

function addMarker(lat,lng,endereco){
    console.log('ADDMARKER ',lat,lng)
    $("#condutor-lat").val(lat);
    $("#condutor-lng").val(lng);
    // if(endereco)
    //   $("#condutor-endereco").val(endereco);
    enderecoAtual = endereco;

    var myIcon = L.icon({ 
      iconUrl:   'img/pin2.png',
      iconSize: [25, 30],
      popupAnchor: [0, -11]
    });
    
    if(marker){
      map.removeLayer(marker); 
    }
     

    marker =  L.marker( L.latLng(lat,lng), {icon:myIcon, draggable: true}).addTo(map);
    console.log(marker);
    var featureGroup = L.featureGroup([marker]);

    map.fitBounds(featureGroup.getBounds());
    map.invalidateSize();
    if(marker){
        marker.on("dragend",function(e){
        var chagedPos = e.target.getLatLng();
        $("#condutor-lat").val(chagedPos.lat);
        $("#condutor-lng").val(chagedPos.lng);
      });
    }
}

$(document).ready(function() {
  if(latAtual && lngAtual && latAtual != 1 && lngAtual != 1)
    addMarker(latAtual, lngAtual, enderecoAtual);
  $( "#condutor-endereco" ).focusout(function() {
    //enableSearch();
  
  });
});


marker.on("dragend",function(e){
  var chagedPos = e.target.getLatLng();
  this.bindPopup(chagedPos.toString()).openPopup();
});
</script>