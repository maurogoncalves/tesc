<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use common\models\Escola;
use kartik\date\DatePicker;
use common\models\Aluno;
use kartik\widgets\FileInput;
use common\models\NecessidadesEspeciais;
use common\models\Usuario;
use common\models\TipoLogradouro;


									
?>
<style type="text/css">
 .swal2-popup {
  font-size: 1.0rem !important;
}

  #justificativa-barreira,
  #inputs-barreira {
    display: none;
  }

  .input-group {
    width: 100%;
  }

  .field-aluno-endereco li {
    padding: 3px 20px;
    margin: 0;
  }

  .field-aluno-endereco li:hover {
    background: #7FDFFF;
    border-color: #7FDFFF;
  }

  .geocoder-control-selected {
    background: #7FDFFF;
    border-color: #7FDFFF;
  }

  .field-aluno-endereco ul li {
    list-style-type: none;
  }

  input[type="radio"],
  input[type="checkbox"] {
    margin: 0px !important;
  }

  #tabelaEndereco {
    display: none;
  }

  .datepicker {
    z-index: 1000 !important;
  }

  .swal2-container {
    z-index: 9999 !important;
  }

  .cidade {
    display:none;
  }
</style>
<div class="box-body">
  <!-- <?= Yii::$app->session->getFlash('error'); ?> -->
  <?php $form = ActiveForm::begin([
    'id' => 'formAluno',
    'options' => ['enctype' => 'multipart/form-data'],
    'encodeErrorSummary' => false,
    'errorSummaryCssClass' => 'help-block',
  ]); ?>


  <div class="row">
    <div class="col-md-4">
      <?= $form->field($model, 'nome')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'RA')->textInput(['maxlength' => 9, 'autocomplete' => 'off']) ?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'RAdigito')->textInput(['maxlength' => 1, 'autocomplete' => 'off']) ?>
    </div>
    <div class="col-md-2">
      <?php
      echo $form->field($model, 'cpf')->textInput(
        [
          'onBlur' => 'ValidarCPF(this);',
          'onKeyPress' => 'MascaraCPF(this);',
          'maxlength' => '14'
        ]
      )
      ?>
    </div>
	 <div class="col-md-2">
      <?php
      echo $form->field($model, 'cartaoPasseEscolar')->textInput(
        [
          'maxlength' => '30'
        ]
      )->label('N° Carteira de Passe escolar',['class'=>'label-class'])
      ?>
	  
    </div>
  </div>
  <div class="row">
    <div class="col-md-4"> 
      <?= $form->field($model, 'ensino')->widget(Select2::classname(), [
        'data' => Escola::ARRAY_ENSINO,
        'value' => '',
        'language' => 'pt',
        'options' => ['placeholder' => 'Selecione', 'class' => 'form-control', 'id' => 'ensino'],
        'pluginOptions' => [
          'allowClear' => true,
          'multiple' => false,
          'initialize' => true,
        ],
      ]);
      ?>
    </div>

    <div class="col-md-4">

      <?= $form->field($model, 'serie')->widget(Select2::classname(), [
        'data' => Aluno::ARRAY_SERIES,
        'value' => '',
        'language' => 'pt',
        'options' => ['placeholder' => 'Selecione', 'class' => 'form-control', 'id' => 'serie'],
        'pluginOptions' => [
          'allowClear' => true,
          'multiple' => false,
          'initialize' => true,
        ],
      ]);
      ?>
    </div>
    <div class="col-md-4">
      <?= $form->field($model, 'turma')->widget(Select2::classname(), [
        'data' => Aluno::ARRAY_TURMA,
        'value' => '',
        'language' => 'pt',
        'options' => ['placeholder' => 'Selecione', 'class' => 'form-control', 'id' => 'turma'],
        'pluginOptions' => [
          'allowClear' => true,
          'multiple' => false,
          'initialize' => true,
        ],
      ]);
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <?= $form->field($model, 'horarioEntrada')->input('time') ?>
    </div>
    <div class="col-md-2">
      <?= $form->field($model, 'horarioSaida')->input('time') ?>
    </div>
	<div class="col-md-2">
      <?= $form->field($model, 'turno')->widget(Select2::classname(), [
        'data' => Aluno::ARRAY_TURNO,
        'value' => '',
        'language' => 'pt',
        'options' => ['placeholder' => 'Selecione', 'class' => 'form-control', 'id' => 'turno'],
        'pluginOptions' => [
          'allowClear' => true,
          'multiple' => false,
          'initialize' => true,
        ],
      ]);
      ?>
    </div>
    <div class="col-md-3">
      <?= $form->field($model, 'rg')->textInput(['maxlength' => 10]) ?>
    </div>
    <div class="col-md-3">
      <?php
      echo $form->field($model, 'dataNascimento')->widget(DatePicker::classname(), [
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'value' =>  $model->dataNascimento,
        'options' => ['placeholder' => 'Data', 'style' => 'z-index:1 !important;'],
        'pluginOptions' => [
          'orientation' => 'bottom left',
          'autoclose' => true,
          'format' => 'dd/mm/yyyy',
          // 'startDate' => '-18y',
          'endDate' => '-1y',
        ]
      ])->label('Data de nascimento. Maior de 18 anos: <input type="checkbox" value="" id="checkMaioridade"/>');
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-7">
      <?= $form->field($model, 'nomeMae')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
    </div>
    <div class="col-md-5">
      <?php
      echo $form->field($model, 'cpfResponsavel')->textInput(
        [
          'onBlur' => 'ValidarCPF(this);',
          'onKeyPress' => 'MascaraCPF(this);',
          'maxlength' => '14',
          'autocomplete' => 'off'
        ]
      )
      ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-7">
      <?= $form->field($model, 'nomePai')->textInput(['maxlength' => true, 'autocomplete' => 'off'])->label('Nome do responsável <input type="checkbox" value="" id="checkNomeResponsavel"/> mesmo nome da mãe') ?>
    </div>
    <div class="col-md-5">
      <?php
      echo $form->field($model, 'dataNascimentoResponsavel')->widget(DatePicker::classname(), [
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'value' =>  $model->dataNascimentoResponsavel,
        'options' => ['placeholder' => 'Data'],
        'pluginOptions' => [
          'autocomplete' => 'off',
          'orientation' => 'bottom left',
          'autoclose' => true,
          'format' => 'dd/mm/yyyy',
          'endDate' => '-18y',
          'startDate' => '-100y',
        ]
      ]);
      ?>
    </div>
  </div>

  <div class="row">

    <div class="col-md-6">
      <?php
      $lista = [];
      foreach ($model->alunoCurso as $es) {
        array_push($lista, $es->dia);
      }
      $model->inputCursoLivre = $lista;
      ?>
      <?=
        $form->field($model, 'inputCursoLivre')->widget(Select2::classname(), [
          'data' => Aluno::ARRAY_DIAS_CURSO,
          'value' =>  $model->alunoCurso,
          'language' => 'pt',
          'options' => ['placeholder' => 'Selecione todos os dias da semana', 'class' => 'form-control', 'id' => 'escola'],
          'pluginOptions' => [
            'allowClear' => true,
            'multiple' => true,
            'initialize' => true,
          ],
        ])->label('Dias da semana que realiza curso livre (Cultura inglesa)');
      ?>
    </div>
    <div class="col-md-6">
      <?php
      $lista = [];
      foreach ($model->necessidades as $es) {
        array_push($lista, $es->idNecessidadesEspeciais);
      }
      $model->necessidadesEspeciais = $lista;
      ?>
      <?=
        $form->field($model, 'necessidadesEspeciais')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(NecessidadesEspeciais::find()->all(), 'id', 'nome'),
          'value' => '',
          'language' => 'pt',
          'options' => ['placeholder' => 'Selecione as necessidades', 'class' => 'form-control', 'id' => 'necessidades'],
          'pluginOptions' => [
            'allowClear' => true,
            'multiple' => true,
            'initialize' => true,
          ],
        ])->label('Necessidades especiais');
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
      <?= $form->field($model, 'cep')->textInput(['id' => 'cep', 'maxlength' => 9, 'autocomplete' => 'off']); ?>
    </div>
    <div class="col-md-4">
      <?=
        $form->field($model, 'tipoLogradouro')->widget(Select2::classname(), [
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
        ]);
      ?>
    </div>
    <div class="col-md-6">
      <div class="mapModal">
        <?= $form->field($model, 'endereco')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

        <!-- <?= $form->field($model, 'endereco', [
                'template' => '{label}<div class="input-group"><div class="address-input">{input}</div>
            <span class="input-group-btn"><button class="btn btn-default pickLocation" type="button" ><i class="fa fa-map" aria-hidden="true"></i></button></span></div>{error}{hint}'
              ]); ?> -->
        <?= $form->field($model, 'lat', ['options' => ['class' => 'lat']])->hiddenInput(['maxlength' => true])->label(false); ?>
        <?= $form->field($model, 'lng', ['options' => ['class' => 'lng']])->hiddenInput(['maxlength' => true])->label(false); ?>
		<!-- caso o pedido de redirecionament tenha vindo da pagina de renovacao, esse campo vai ajudar a redirecionar novamente para a pagina de renovacao -->
		<input type="hidden" id='redirect' name="redirect" value="<?php echo $redirect ? $redirect : 0;?>"  aria-invalid="false">				
      </div>
    </div>
  </div>
  <div class="row" id="cidade">
  <div class="col-md-12">
      <?= $form->field($model, 'cidade')->textInput(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-5">
	  
      <?= $form->field($model, 'bairro')->textInput(['readonly' => true]); ?>
    </div>
    <div class="col-md-3">
      <?= $form->field($model, 'numeroResidencia')->textInput(['type' => 'number']); ?>
    </div>
    <div class="col-md-4">
      <?= $form->field($model, 'complementoResidencia')->textInput(['autocomplete' => 'off']); ?>
    </div>
  </div>
  <div class="row">
    <div id="tabelaEndereco">
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div id="location-map">
        <div id="mapUser" style="min-height:400px;display:none;">
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <?=
        $form->field($model, 'idEscola')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Escola::escolasPerfis($model->escola), 'id', 'nome'),
          'value' =>  $model->escola,
          'language' => 'pt',
          'options' => ['placeholder' => 'Selecione a escola', 'class' => 'form-control', 'id' => 'escola-'],
          'pluginOptions' => [
            'allowClear' => true,
            'multiple' => false,
            'initialize' => true,
          ],
        ]);
      ?>
    </div>
    
  </div>
  <div class="row">
    <div id="inputs-barreira">
      <div class="col-md-3">
        <?= $form->field($model, 'barreiraFisica')->dropDownList([1 => 'SIM', 0 => 'NÃO'], ['prompt' => 'SELECIONE']) ?>
      </div>
      <div class="col-md-3">
        <?= $form->field($model, 'distanceEscola')->textInput(['maxlength' => true, 'class' => 'form-control meters']); ?>
      </div>
    </div>
  </div>
  <div class="row" id="justificativa-barreira">
    <div class="col-md-12">
      <?= $form->field($model, 'justificativaBarreiraFisica')->textarea(['rows' => '6', 'placeholder' => '']) ?>
    </div>
  </div>



  <div class="row">
    <div class="col-md-6">
      <div class="template-fileinput <?php if (empty($model->docRgAluno)) print 'without-files'; ?>">
        <?php
        echo $form->field($model, 'documentoRgAluno[]', ['options' => ['class' => 'xx']])->widget(FileInput::classname(), [
          'options' => ['accept' => 'application/pdf, image/*', 'multiple' => true],
          'pluginOptions' => ['allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
        ])->label('RG do aluno');

        ?>
        <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="template-fileinput <?php if (empty($model->docRgResponsavel)) print 'without-files'; ?>">

        <?php
        echo $form->field($model, 'documentoRgResponsavel[]')->widget(FileInput::classname(), [
          'options' => ['accept' => 'application/pdf, image/*', 'multiple' => true, 'id' => 'aluno-rgresponsavel'],
          'pluginOptions' => ['allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'], 'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
        ])->label('RG do responsável'); ?>
        <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-md-3">
      <?php
      echo $form->field($model, 'telefoneResidencial')->textInput(
        [
          'onBlur' => 'MascaraTelefone(this);',
          'onKeyPress' => 'MascaraTelefone(this);',
          'maxlength' => '15'
        ]
      )->label('Telefone 1',['class'=>'label-class'])
      ?>
    </div>
    <div class="col-md-3">
      <?php
      echo $form->field($model, 'telefoneResidencial2')->textInput(
        [
          'onBlur' => 'MascaraTelefone(this);',
          'onKeyPress' => 'MascaraTelefone(this);',
          'maxlength' => '15'
        ]
      )->label('Telefone 2',['class'=>'label-class'])
      ?>
    </div>
    <div class="col-md-3">
      <?php
      echo $form->field($model, 'telefoneCelular')->textInput(
        [
          'onBlur' => 'MascaraTelefone(this);',
          'onKeyPress' => 'MascaraTelefone(this);',
          'maxlength' => '15'
        ]
      )->label('Telefone 3',['class'=>'label-class'])
      ?>

    </div>
    <div class="col-md-3">
      <?php
      echo $form->field($model, 'telefoneCelular2')->textInput(
        [
          'onBlur' => 'MascaraTelefone(this);',
          'onKeyPress' => 'MascaraTelefone(this);',
          'maxlength' => '15'
        ]
      )->label('Telefone 4',['class'=>'label-class'])
      ?>
    </div>
  </div>
  <div class="form-group">
    <span id="erro-telefones" style="color:#f00; padding-left:15px"></span> 
	<?php if($model->isNewRecord == 'Salvar'){ ?>
		<?= Html::submitButton('Salvar', ['class' => 'btn btn-success pull-right'] ) ?>
	<?php }else{ ?>	
		<?= Html::button('Atualizar',['class' => 'btn btn-success pull-right','id' => 'salvarAluno'] ) ?>
		
	<?php } ?>
	
  </div>
  
  
  <?php ActiveForm::end(); ?>

</div>
<!-- <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
</div> -->




<!-- Validação do endereço no maps -->
<script type="text/javascript">

 var redirect = $("#redirect").val();	
 
  var perfilUsuario = <?php print Yii::$app->user->identity->idPerfil; ?>;
  if(perfilUsuario == 1){
	  $("#salvarAluno").on('click', function(event) {
			$("#formAluno").submit();
		 });	
  }else{
	  if(redirect != 0 ){	
		$("#salvarAluno").on('click', function(event) {
			$("#formAluno").submit();
		 });	
  }else{
		$("#salvarAluno").on('click', function(event) {
		var dadosAlterados =0;
			
		var cep = $("#cep").val();
		if(cep != '<?=$model->cep?>'){
			 dadosAlterados = 1;
		}
		
		var turno = $("#turno").val();
		if(turno != '<?=$model->turno?>'){
			 dadosAlterados = 2;
		}
		
		var horarioEntrada = $("#aluno-horarioentrada").val();
		if(horarioEntrada != '<?=$model->horarioEntrada?>'){
			 dadosAlterados = 3;
		}
		
		var horarioSaida = $("#aluno-horariosaida").val();
		if(horarioSaida != '<?=$model->horarioSaida?>'){
			 dadosAlterados = 4;
		}
		
		var tipoLogradouro = $("#tipo-logradouro").val();
		if(tipoLogradouro != '<?=$model->tipoLogradouro?>'){
			 dadosAlterados = 6;
		}
		
		var alunoEndereco = $("#aluno-endereco").val();
		if(alunoEndereco != '<?=$model->endereco?>'){
			 dadosAlterados = 7;
		}
		var alunoCidade = $("#aluno-cidade").val();
		if(alunoCidade != '<?=$model->cidade?>'){
			 dadosAlterados = 8;
		}		
		var alunoNumRes = $("#aluno-numeroresidencia").val();
		if(alunoNumRes != '<?=$model->numeroResidencia?>'){
			 dadosAlterados = 9;
		}
		console.log(dadosAlterados);
		if(dadosAlterados != 0){
			Swal.fire({
				title: 'Usuário(a)',
				text: "Alguns dos campos cep, endereço, horário de entrada e saída e turno foram alterados, então todas as solicitações ativas serão encerradas e deverão ser realizadas novamente. Deseja continuar?",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'SIM',
				cancelButtonText: 'NÃO'
			  }).then((result) => {
				if (result.value) {
					$("#formAluno").submit();
				}else{
					history.go(-1);
				}
			  });
		}else{
			$("#formAluno").submit();
		}
	}); 
	}
	}
  
 
		  
		  
  $('#cep').keypress(function(e) {
    if (e.which == 13) {
      $('#cep').blur();
      return false;
    };
  });



  var marker = L.marker();
  var geocoder;
  var address;
  var flag = false;
  var flagMapa = false;
  var selectedDiv;
  var currentLocation;
  var search;
  var enderecoAtual = '<?= $model->endereco ?>';
  var latAtual = <?= print $model->lat; ?>;
  var lngAtual = <?= print $model->lng; ?>;
  var action = '<?= Yii::$app->controller->module->requestedRoute ?>';
  
  if(redirect != 0 ){
	  
  }else{
	 if(action == 'aluno/update'){
		$( '<div class="alert alert-warning" role="alert">Ao transferir o aluno, todas as solicitações ativas serão encerradas e deverão ser realizadas novamente.</div>' ).insertAfter( "#escola-" );
	 } 
  }
  
  window.onload = function() {
 const myInput = document.getElementById('cep');
 myInput.onpaste = function(e) {
   e.preventDefault();
   alert('Digite o CEP manualmente');
 }
}

  var geocodeService = L.esri.Geocoding.geocodeService();
  $(".field-cep").append('<p class="loading"></p>');
  $(".field-aluno-endereco").append('<p class="loading"></p>');
  // $("#aluno-bairro").val('');
  $("#cep").change(function() {
    esconderTabela();
    let cep = $("#cep").val();
	let logradouro = $("#aluno-endereco").val();
	let tipo = $("#tipo-logradouro").val();
    if (!cep){
		return null;
	}else{
		logradouro = null;
	}
      
    
    
    $(".field-cep .loading").html('<i class="fas fa-hourglass-half"></i> Buscando informações...');
    $("#aluno-bairro").attr('readonly',true);
    $.getJSON("index.php?r=pesquisa-logradouro/pesquisa-logradouro", {
        "logradouro": logradouro,
        "tipo": tipo,
        "cep": cep,
        "bloquearCidade": true
      })
      .done(function(data) {
        $(".field-cep .loading").html('');

        $("#tabelaEndereco").css("display", "none");
        if (data.status) {
          //Trata caso do endereço diferente
          if(data.enderecos && data.enderecos[0]){
            let endereco = data.enderecos[0];
            console.log(endereco)
            if(endereco.CIDADE != 'SÃO JOSÉ DOS CAMPOS') {
              $("#aluno-bairro").attr('readonly',false);
              $("#cidade").css('display','block');
              $("#aluno-cidade").val(endereco.CIDADE)
              // $("#aluno-cidade").attr('readonly',false);
              // console.log("FORA")
            } else {
              $("#cidade").css('display','none');
              mostrarTabela(data.enderecos);
            }
          }
          
          //$('#aluno-endereco').val(data.endereco.TIPO_LOGRADOURO+' '+data.endereco.LOGRADOURO+', '+data.endereco.BAIRRO);
        } else {
          Swal.fire(
            'CEP não encontrado',
            'Confira os números do CEP',
            'warning'
          )
          $("#cep").focus();
          $("#cep").val("");
          $("#aluno-endereco").val("");
          mostrarMapa();
        }

      }).fail((e) => {
        return      Swal.fire(
            'CEP não encontrado',
            'Confira os números do CEP',
            'warning'
          );
      })
      ;
  });
  mostrarMapa();

  function ocultarMapa() {
    $("#mapUser").css("display", "none");
  }

  function mostrarMapa() {
    let logradouro = $("#aluno-endereco").val();
    let bairro = $("#aluno-bairro").val();
    let num = $("#aluno-numeroresidencia").val();
    let tipo = $("#tipo-logradouro").val();
    if (logradouro && num) {
      $("#mapUser").css("display", "block");
      let enderecoCompleto = tipo + ` ` + logradouro + `, ` + num + `, ` + bairro;
      // -- HERE 2-- //
	  //alert(enderecoCompleto);
      geoSearch(enderecoCompleto);
    } else {
      $("#mapUser").css("display", "none");
    }
    flagMapa = true;
  }

  $('#aluno-numeroresidencia').change(function() {
    mostrarMapa();
  });
  $("#aluno-endereco").change(function() {
    if ($("#cep").val() != "")
      return null;
    esconderTabela();
    mostrarMapa();
    flag = false;
    let logradouro = $("#aluno-endereco").val();
    let tipo = $("#tipo-logradouro").val();
    let cep = $("#cep").val();
    $(".field-aluno-endereco .loading").html('<i class="fas fa-hourglass-half"></i> Buscando informações...');
    $.getJSON("index.php?r=pesquisa-logradouro/pesquisa-logradouro", {
        "logradouro": logradouro,
        "tipo": tipo,
        "cep": cep,
        "bloquearCidade": true
      })
      .done(function(data) {
        $(".field-aluno-endereco .loading").html('');

        $("#tabelaEndereco").css("display", "none");
        if (data.status) {
          mostrarTabela(data.enderecos);
        } else {
          Swal.fire(
            'Logradouro não encontrado',
            'Digite o CEP ou o nome de um logradouro válido',
            'warning'
          )
          $("#aluno-endereco").focus();
          $("#cep").val("");
          $("#aluno-endereco").val("");
          //mostrarMapa();
        }

      });
  });

  function tipoLogradouro(flag = 0) {

    // console.log("tipoLogradouro()");
    // esconderTabela();
    // let logradouro = $("#aluno-endereco").val();
    // let tipo = $("#tipo-logradouro").val();

    // if(logradouro && tipo){
    //   console.log('Logradouro changed');
    //   $('#aluno-endereco').trigger('change');
    // } 

    // if(cep && tipo) {
    //   console.log('CEP CHANGED0');
    //   $('#cep').trigger('change');
    // }
  }
  // $("#aluno-endereco").change(() => {
  //   let endereco = $('#aluno-endereco').val();
  //   $.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest?text='+endereco+'&maxSuggestions=5&f=json').done((x) => console.log(x));
  // })
  function esconderTabela() {
    $("#tabelaEndereco").css("display", "none");
  }

  function mostrarTabela(data) {
    let num = $("#aluno-numeroresidencia").val();
    $("#tabelaEndereco").html("");
    if (data.length)
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
    for (let i = 0; i <= data.length; i++) {
      let local = data[i];
      if (local) {
        novoLogradouro = local.LOGRADOURO.replace(/["'"]/g,"");
        let enderecoCompleto = '';
        if (num) {
          enderecoCompleto = local.TIPO_LOGRADOURO + ` ` + novoLogradouro + `, ` + num + `, ` + local.BAIRRO;
        } else {
          enderecoCompleto = local.TIPO_LOGRADOURO + ` ` + novoLogradouro + `, ` + local.BAIRRO;

        }

        
        $('#tabelaEnderecoBody').append(`<tr><td>` + local.CEP + `</td><td>` + local.TIPO_LOGRADOURO + ` ` + novoLogradouro + `</td><td>` + local.BAIRRO + `</td><td align="center">` + local.CIDADE + `</td><td algn="center"><a class="btn btn-success" onclick='selecionarEndereco("` + enderecoCompleto + `","` + novoLogradouro + `","` + local.BAIRRO + `","` + local.CEP + `","` + local.TIPO_LOGRADOURO + `","` + local.CIDADE + `")' >Selecionar endereço</a></td></tr>`);
      }
    }
    $("#tabelaEndereco").append(`
      </tbody>
      </table>
      `);

  }

  //$.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest?text=rua+maria+carolina+de+jesus&maxSuggestions=5&f=json').done((x) => console.log(x.suggestions));
  //$.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest?text=rua+maria+carolina+de+jesus&maxSuggestions=5&f=json').done((x) => console.log(x));
  // // // 

  function selecionarEndereco(endereco, logradouro, bairro, cep, tipo, cidade=null) {
   
    flag = true;
    $("#aluno-endereco").val(logradouro);
    $("#aluno-bairro").val(bairro);
    $('#tipo-logradouro').val(tipo).trigger("change");
    $("#cep").val(cep);
    $("#tabelaEndereco").css("display", "none");
    mostrarMapa();
    // -- HERE 1-- //
    geoSearch(endereco);
  }

  function geoSearch(endereco) {
    if (!flagMapa)
      return null;
    console.log('geoSearch');
    // $.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?outSr=4326&forStorage=false&outFields=*&maxLocations=20&singleLine='+encodeURI(endereco)+'%2C%20S%C3%83O%20JOS%C3%89%20DOS%20CAMPOS%20-%20SP&f=json')
    // .done(function(data) {
    //       let posicao = data.candidates[0];
    //       addMarker(posicao.location.y, posicao.location.x, endereco);

    // });
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({
      'address': endereco + ', São José dos Campos, Brasil',
      'region': 'BR'
    }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        if (results[0]) {
          var latitude = results[0].geometry.location.lat();
          var longitude = results[0].geometry.location.lng();
          console.log('GEOCODE', latitude, longitude,endereco);		  
		  if((results[0].formatted_address.indexOf("São José dos Campos") != -1)){
			addMarker(latitude, longitude, endereco);  
		  }else{
			addMarker('-23.1851185', '-45.8875702', 'PRAÇA AFONSO PENA, 1000000, CENTRO, São José dos Campos, Brasil ') ; 
		  }
          
        }
      }
    });
  }
  $("#aluno-endereco").attr("autocomplete", "off");

  var map = L.map("mapUser", {
    'center': [-23.223701, -45.9009074],
    'zoom': 15,
    'minZoom': 10,
    'maxZoom': 18,
  });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);
  //this.map.zoomControl.remove();
  map.scrollWheelZoom.disable();

  function addMarker(lat, lng, endereco) {
    // console.log('ADDMARKER ', lat, lng)
    $("#aluno-lat").val(lat);
    $("#aluno-lng").val(lng);
    // if(endereco)
    //   $("#aluno-endereco").val(endereco);
    enderecoAtual = endereco;

    var myIcon = L.icon({
      iconUrl: 'img/pin2.png',
      iconSize: [25, 30],
      popupAnchor: [0, -11]
    });
    if (marker) {
      map.removeLayer(marker);
    }


    marker = L.marker(L.latLng(lat, lng), {
      icon: myIcon,
      draggable: true
    }).addTo(map);
    // console.log(marker);
    var featureGroup = L.featureGroup([marker]);

    map.fitBounds(featureGroup.getBounds());
    map.invalidateSize();
    if (marker) {
      marker.on("dragend", function(e) {
        var chagedPos = e.target.getLatLng();
        $("#aluno-lat").val(chagedPos.lat);
        $("#aluno-lng").val(chagedPos.lng);

        $.get('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + chagedPos.lat + '&lon=' + chagedPos.lng, function(data){
          let tipoLogradouro = data.address.road.substring(0, data.address.road.indexOf(' '));
          $("#tipo-logradouro").val(tipoLogradouro.toUpperCase()).trigger('change');;
          $("#aluno-endereco").val(data.address.road.replace (tipoLogradouro + ' ', ''));
          $("#aluno-numeroresidencia").val(data.address.house_number);
          $("#aluno-bairro").val(data.address.suburb);
          $("#aluno-cidade").val(data.address.city);
          $("#cep").val(data.address.postcode);
          // console.log(data)
        });

      });
    }
  }

  $(document).ready(function() {
    // $("#aluno-ra").attr('maxlength', 9);
    if (latAtual && lngAtual && latAtual != 1 && lngAtual != 1)
      addMarker(latAtual, lngAtual, enderecoAtual);
    $("#aluno-endereco").focusout(function() {
      //enableSearch();

    });

    $("#aluno-ra").keypress(function(e) {
      if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
      }
    })

  });


  marker.on("dragend", function(e) {
    var chagedPos = e.target.getLatLng();
    this.bindPopup(chagedPos.toString()).openPopup();
  });

  $("#aluno-cpfresponsavel").change(function(){
      let resp = $(this).val()
      console.log(reps);
  })
  $("#checkNomeResponsavel").on('click', function(event) {
    // console.log("checkNomeResponsavel")
    if ($("#checkNomeResponsavel").prop("checked") == true) {
      $("#aluno-nomepai").val($("#aluno-nomemae").val())
      $("#aluno-nomepai").attr('readonly', true);
    } else {
      $("#aluno-nomepai").attr('readonly', false);
    }
  });

  $("#checkMaioridade").on('click', function(event) {
    // console.log("checkMaioridade")
    if ($("#checkMaioridade").prop("checked") == true) {
      $("#aluno-datanascimentoresponsavel").val($("#aluno-datanascimento").val())
      $("#aluno-cpfresponsavel").val($("#aluno-cpf").val())
      $("#aluno-nomepai").val($("#aluno-nome").val())
      $("#aluno-datanascimentoresponsavel").attr('disabled', true);
      $("#aluno-cpfresponsavel").attr('readonly', true);
      $("#aluno-nomepai").attr('readonly', true);
      $("#checkNomeResponsavel").attr('disabled', true);
    } else {
      $("#aluno-datanascimentoresponsavel").attr('disabled', false);
      $("#aluno-cpfresponsavel").attr('readonly', false);
      $("#aluno-nomepai").attr('readonly', false);
      $("#aluno-rgresponsavel").attr('readonly', false);
      $("#checkNomeResponsavel").attr('disabled', false);
    }
  });

  $("#aluno-nomemae").change(function(event) {
    // console.log("nomemae changed")
    if ($("#checkNomeResponsavel").prop("checked") == true) {
      $("#aluno-nomepai").val($("#aluno-nomemae").val())
    }
  });

  $("#aluno-datanascimento").change(function(event) {
    // console.log("datanascimento changed")
    if ($("#checkMaioridade").prop("checked") == true) {
      $("#aluno-datanascimentoresponsavel").attr('readonly', false);
      $("#aluno-datanascimentoresponsavel").val($("#aluno-datanascimento").val())
      $("#aluno-datanascimentoresponsavel").attr('readonly', true);
    }
  });

  $("#aluno-cpf").change(function(event) {
    // console.log("cpf changed")
    if ($("#checkMaioridade").prop("checked") == true) {
      $("#aluno-cpfresponsavel").val($("#aluno-cpf").val())
    }
  });

  $("#aluno-nome").change(function(event) {
    // console.log("nome changed")
    if ($("#checkMaioridade").prop("checked") == true) {
      $("#aluno-nomepai").val($("#aluno-nome").val())
    }
  });
</script>

<script>
  $('#formAluno').submit(function(event) {
    // event.preventDefault();
    $("#aluno-datanascimentoresponsavel").attr('disabled', false);

    // if( $("#aluno-telefoneresidencial").val() == "" &&
    //     $("#aluno-telefoneresidencial2").val() == ""  &&
    //     $("#aluno-telefonecelular").val() == ""  &&
    //     $("#aluno-telefonecelular2").val() == "" ) {
    //         $("#erro-telefones").text('É obrigatório informar ao menos 1 número de telefone.');

    //         $("#aluno-telefoneresidencial, #aluno-telefoneresidencial2, #aluno-telefonecelular, #aluno-telefonecelular2").parent().removeClass("has-success").addClass("has-error");

    //         return
    // } else {
    //     this.submit();
    // }
  })
</script>

<!-- Validação de frete -->
<script type="text/javascript">
  const freteEscolar = <?php print Aluno::MODALIDADE_FRETE; ?>;

  function carregarBarreira() {
    if ($("#aluno-modalidadebeneficio").val() == freteEscolar) {
      $("#inputs-barreira").css("display", "block");
    } else {
      $("#inputs-barreira").css("display", "none");
    }
  }

  function carregarJustificativa() {
    let modalidadeBeneficio = $("#aluno-modalidadebeneficio").val();
    let barreiraFisica = $("#aluno-barreirafisica").val();
    let distanciaEscola = parseFloat($("#aluno-distanceescola").val());
    // modalidade -> 1 = frete
    // barreirafisica -> 1 = Sim
    if (modalidadeBeneficio == 1 && barreiraFisica == 1 && distanciaEscola < 2) {
      // console.log(modalidadeBeneficio, barreiraFisica, distanciaEscola);
      $("#justificativa-barreira").css("display", "block");
    } else {
      $("#justificativa-barreira").css("display", "none");
    }
  }

  $("#aluno-modalidadebeneficio").change((event) => {
    carregarJustificativa();
    carregarBarreira();
  });

  $("#aluno-barreirafisica").change((event) => {
    carregarJustificativa();
  });

  $("#aluno-distanceescola").change((event) => {
    carregarJustificativa();
  });

  window.onload = function() {
    // $("#aluno-endereco").geocomplete()    
    //   .bind("geocode:result", function(event, result){
    //     console.log("Result: " + result.formatted_address);
    //     console.log(result)
    //     $("#aluno-lat").val(result.geometry.location.lat())
    //     $("#aluno-lng").val(result.geometry.location.lng())
    //   })
    //   .bind("geocode:error", function(event, status){
    //     console.log("ERROR: " + status);
    //   })
    //   .bind("geocode:multiple", function(event, results){
    //     console.log("Multiple: " + results.length + " results found");
    //   });

    // $("#find").click(function(){
    //   $("#geocomplete").trigger("geocode");
    // });
    carregarJustificativa();
    carregarBarreira();
  };
  $("#aluno-ra").change(() => {
    // ativa a validação somente no create 
    if(action == 'aluno/create')
      getAlunoExistente()
  });
  
  $("#aluno-radigito").change(() => {
    // ativa a validação somente no create
    if(action == 'aluno/create')
      getAlunoExistente()
  });

  function getAlunoExistente(){
    let ra = $("#aluno-ra").val()
    let digito = $("#aluno-radigito").val()
    if(!ra || !digito)
      return console.warn('Digito/RA não foi preenchido')
    $.getJSON("index.php?r=aluno/aluno-ra", {
        "ra": ra,
        "digito": digito
      })
      .done(function(data) {
        if(data.status){
          Swal.fire({
            title: 'Já existe um aluno com esse RA',
            text: "Quer editar o cadastro existente?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO'
          }).then((result) => {
            if (result.value) {
              Swal.fire(
                'Redirecionando...',
                '',
                'success'
              )
              window.location.href = data.redirect
            }
          })
         
        }
      });
  }
</script>