<?php

use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use common\models\SolicitacaoTransporte;
use common\models\Escola;
use kartik\select2\Select2;
use Symfony\Component\Yaml\Inline;
/* @var $this yii\web\View */
/* @var $model common\models\Planoconta */
/* @var $form yii\widgets\ActiveForm */
?>
<style type="text/css">
  #justificativa-barreira,
  #inputs-barreira,
  #inputs-cartao,
  .solicitacao-frete,
  .solicitacao-frete-adaptado {
    display: none;
  }

  .center-radio {
    margin: 0 auto;
    width: 35%;
  }

  .radio1 {
    float: left;
    color: #00A65A;
  }

  .radio2 {
    float: right;
    color: #3980D8;
  }
</style>
<?= $form->field($model, 'idAluno')->hiddenInput(['maxlength' => true])->label(false); ?>
<?= $form->field($model, 'idEscola')->hiddenInput(['maxlength' => true])->label(false); ?>
<?= $form->field($model, 'tipoSolicitacao')->hiddenInput(['maxlength' => true])->label(false); ?>

<div class="alert alert-warning" role="alert">
  ESCOLAS PRÓXIMAS: <?= '<span style="color:yellow;font-weight:bold;  "><b>' . Escola::ARRAY_TIPO[$model->escola->tipo] . ' ' . $model->escola->nome . '</b></span>'; ?>
  <?php
  $str = ', ';
  foreach ($escolas as $escola)
    $str .= Escola::ARRAY_TIPO[$escola->tipo] . ' ' . $escola->nome . ', ';
  echo substr($str, 0, -2) . '.';
  ?>
  <a target="_new" href="<?= Url::toRoute(['solicitacao-transporte/mapa', 'idAluno' =>  $model->idAluno, 'idEscola' => $model->idEscola]) ?>">CLIQUE AQUI PARA VISUALIZAR NO MAPA</a>
</div>

<div class="alert alert-light" role="alert">
  Para baixar o documento de formalização de <B>solicitação de frete</b>, <a target="_new" href="arquivos/REQUISICAO_FRETE.docx">clique aqui</a>. Para <b>solicitação de passe</b>, <a target="_new" href="arquivos/REQUISICAO_PASSE_ESCOLAR_GRATUITO.docx">clique aqui</a>.
</div>
<div class="row">
  <div class="col-md-3">
    <?= $form->field($model, 'modalidadeBeneficio')->dropDownList(Aluno::ARRAY_MODALIDADE, ['prompt' => 'SELECIONE']) ?>
  </div>
  <div class="col-md-3">
    <?= $form->field($model, 'distanciaEscola')->textInput(['maxlength' => true, 'class' => 'form-control meters', 'autocomplete' => 'off', 'type' => 'text']); ?>
  </div>

  <div id="inputs-cartao">
    <div class="col-md-3">
      <?= $form->field($model, 'cartaoPasseEscolar')->textInput(['maxlength' => true, 'autocomplete' => 'off', 'type' => 'number']) ?>
    </div>
    <div class="col-md-3">
      <?= $form->field($model, 'cartaoValeTransporte')->textInput(['maxlength' => true, 'autocomplete' => 'off', 'type' => 'number']) ?>
    </div>
  </div>

  <div id="inputs-barreira">
    <div class="col-md-3">
      <?= $form->field($model, 'tipoFrete')->dropDownList(SolicitacaoTransporte::ARRAY_TIPO_FRETE, ['prompt' => 'SELECIONE']) ?>
    </div>
    <div class="col-md-3">
      <?= $form->field($model, 'barreiraFisica')->dropDownList([1 => 'Sim', 2 => 'Não'], ['prompt' => 'SELECIONE']) ?>
    </div>
    <div class="col-md-12" style="display:none;" id="motivoBarreiraFisica">
      <?= $form->field($model, 'motivoBarreiraFisica')->dropDownList(SolicitacaoTransporte::ARRAY_MOTIVO_BARREIRA_FISICA, ['prompt' => 'SELECIONE']) ?>
    </div>
  </div>
     <div class="col-md-12">
      <?=
        $form->field($model, 'EscolasProximas')->widget(Select2::classname(), [
          'data' => ArrayHelper::map(Escola::find()->all(), 'id', 'nome'),
          'value' => '',
          'language' => 'pt',
          'options' => ['class' => 'form-control', 'id' => 'escolas'],
          'pluginOptions' => [
            'allowClear' => true,
            'multiple' => true,
            'initialize' => true, 
          ],
        ])->label('Declarações Entregues (Informar Escolas)');
      ?>
    </div>
</div>


<div class="row" id="justificativa-barreira">
  <div class="col-md-12">
    <?= $form->field($model, 'justificativaBarreiraFisica')->textarea(['rows' => '6', 'placeholder' => '']) ?>
  </div>
</div>

<div id="radios" class="row">
  <div class="center-radio">
    <div class="radio1">
      <label>
        <img src="img/add_student.png" style="height: 30px;">
        <input type="radio" id="novo-aluno" name="alunotipo" required> Novo aluno
      </label>
    </div>
    <div class="radio2">
      <label>
        <img src="img/renovation_student.png" style="height: 30px;">
        <input type="radio" id="renovacao-aluno" name="alunotipo" required> Renovação
      </label>
    </div>

  </div>
</div>

<div class="row">
  <div class="col-4">
    <?php
    echo $form->field($model, 'novaSolicitacao')->radioList([1 => 'Aluno novo', 0 => 'Renovação'], ["hidden" => true])->label(false);
    ?>
  </div>
</div>

<div class="box box-solid with-border">
  <div class="box-header with-border">
    <b> Declaro que os documentos marcados abaixo estão na escola: </b>
  </div>
  <div>
    <div class="row" style="background-color: #fff; padding-left: 20px; margin: 0px;">
      <?php echo $form->field($model, 'checkInex')->checkBox(['id' => 'checkInex']); ?>
    </div>
    <div class="row" style="background-color: #eee; padding-left: 20px; margin: 0px;">
      <?php echo $form->field($model, 'checkForm')->checkBox(['id' => 'checkForm', 'required' => true]); ?>
    </div>
    <div class="row" style="background-color: #fff; padding-left: 20px; margin: 0px;">
      <?php echo $form->field($model, 'checkEnd')->checkBox(['id' => 'checkEnd', 'required' => true]); ?>
    </div>
    <div class="row" id="validarLaudoMedico" style="display:none; background-color: #eee; padding-left: 20px; margin: 0px;">
      <?php echo $form->field($model, 'checkLaudoMedico')->checkBox(['id' => 'checkLaudoMedico']); ?>
    </div>
    <div class="row" id="validarSolicitacaoEspecial"  style="display:none; background-color: #fff; padding-left: 20px; margin: 0px;">
      <?php echo $form->field($model, 'checkSolicitacaoEspecial')->checkBox(['id' => 'checkSolicitacaoEspecial']); ?>
    </div>
    <div class="row" id="validarSolicitacaoVizinho"  style="display:none; background-color: #eee; padding-left: 20px; margin: 0px;">
      <?php echo $form->field($model, 'checkVizinho')->checkBox(['id' => 'checkVizinho']); ?>

    </div>
  </div>
</div>


<!-- <div class="col-md-4">
      <div class="template-fileinput <?php
                                      // if (empty($model->docInexistenciaVaga)) print 'without-files'; 
                                      ?>">
        <?php
        // echo $form->field($model, 'documentoInexistenciaVaga[]')->widget(FileInput::classname(), [
        //   'options' => ['accept' => 'application/pdf, image/*', 'multiple' => true],
        //   'pluginOptions' => ['allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
        // ])->label('Declaração de inexistência de vaga'); 
        ?>
        <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
      </div>
    </div> -->

<!-- <div class="row"> -->

<!-- <div class="col-md-4">
    <div class="template-fileinput <?php
                                    // if (empty($model->docFormalizacaoSolicitacao)) print 'without-files'; 
                                    ?>">
      <?php
      // echo $form->field($model, 'documentoFormalizacaoSolicitacao[]')->widget(FileInput::classname(), [
      //   'options' => ['accept' => 'application/pdf, image/*', 'multiple' => true, 'id' => 'formalizacaoSol'],
      //   'pluginOptions' => ['allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
      // ])->label('Formalização da solicitação'); 
      ?>
      <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
    </div>
  </div> -->

<!-- <div class="col-md-4">
    <div class="template-fileinput <?php
                                    // if (empty($model->docComprovanteEndereco)) print 'without-files'; 
                                    ?>">
      <?php
      // echo $form->field($model, 'documentoComprovanteEndereco[]')->widget(FileInput::classname(), [
      //   'options' => ['accept' => 'application/pdf, image/*', 'multiple' => true],
      //   'pluginOptions' => ['allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
      // ])->label('Comprovante de endereço'); 
      ?>
      <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
    </div>
  </div> -->


<!-- <div class="col-md-4">
    <div class="template-fileinput solicitacao-frete" <?php
                                                      // if (empty($model->docDeclaracaoVizinho)) print 'without-files'; 
                                                      ?>>
      <?php
      // echo $form->field($model, 'documentoDeclaracaoVizinho[]')->widget(FileInput::classname(), [
      //   'options' => ['accept' => 'application/pdf, image/*', 'multiple' => true],
      //   'pluginOptions' => ['allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'],  'language' => Yii::$app->language,  'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
      // ])->label('Declaração de vizinhos'); 
      ?>
      <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
    </div>
  </div> -->


<!-- </div> -->
<!-- <div class="row">


  <div class="col-md-4">
    <div class="template-fileinput solicitacao-frete-adaptado <?php
                                                              // if (empty($model->docTransporteEspecial)) print 'without-files'; 
                                                              ?>">
      <?php
      // echo $form->field($model, 'documentoTransporteEspecial[]')->widget(FileInput::classname(), [
      //   'options' => ['accept' => 'application/pdf, image/*', 'multiple' => true],
      //   'pluginOptions' => ['allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
      // ])->label('Solicitação de transporte especial'); 
      ?>
      <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="template-fileinput solicitacao-frete-adaptado <?php
                                                              // if (empty($model->docLaudoMedico)) print 'without-files'; 
                                                              ?>">

      <?php
      // echo $form->field($model, 'documentoLaudoMedico[]')->widget(FileInput::classname(), [
      //   'options' => ['accept' => 'application/pdf, image/*', 'multiple' => true],
      //   'pluginOptions' => ['allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'],  'language' => Yii::$app->language, 'showPreview' => false, 'initialPreview' => [], 'showUpload' => false]
      // ])->label('Laudo médico'); 
      ?>
      <div class="substituir-arquivos">Clique aqui para substituir os arquivos</div>
    </div>
  </div>

</div> -->

<script type="text/javascript">
  var freteEscolar = <?php print Aluno::MODALIDADE_FRETE; ?>;

  function ocultarAnexos() {
    $(".solicitacao-frete").css("display", "none");
    $(".solicitacao-frete-adaptado").css("display", "none");
  }
  // $("#solicitacaotransporte-cartaopasseescolar").change((event) => {
  //   validarCartoes();
  // });

  // $("#solicitacaotransporte-cartaovaletransporte").change((event) => {
  //   validarCartoes();
  // });

  function validarCartoes() {
    let passeEscolar = $("#solicitacaotransporte-cartaopasseescolar").val();
    let valeTransporte = $("#solicitacaotransporte-cartaovaletransporte").val();
    if (!passeEscolar || !valeTransporte) {
      addError('.field-solicitacaotransporte-cartaopasseescolar');
      addError('.field-solicitacaotransporte-cartaovaletransporte');
      $("#submitButton").prop("disabled", true);
    } else {
      delError('.field-solicitacaotransporte-cartaopasseescolar');
      delError('.field-solicitacaotransporte-cartaovaletransporte');
      $("#submitButton").prop("disabled", false);

    }

  }

  function carregarAnexos() {
    ocultarAnexos();
    let beneficio = $("#solicitacaotransporte-modalidadebeneficio").val();
    let tipoFrete = $("#solicitacaotransporte-tipofrete").val();

    //se beneficio for passe escolar deve-se aparecer os campos de

    // if (beneficio == passeEscolar) {
    //   console.log(beneficio, tipoFrete);
    //   $(".field-solicitacaotransporte-documentoinexistenciavaga").css("display", "block");
    // }

    if (beneficio == freteEscolar) {
      $(".solicitacao-frete").css("display", "block");

    }
    // tipoFrete = 2 = Adaptado
    if (tipoFrete == 2) {
      $(".solicitacao-frete-adaptado").css("display", "block");
      $(".field-solicitacaotransporte-documentoinexistenciavaga").css("display", "none");

    } else {
      $(".field-solicitacaotransporte-documentoinexistenciavaga").css("display", "block");
    }

  }

  function carregarBarreira() {
    let beneficio = $("#solicitacaotransporte-modalidadebeneficio").val();
    if (beneficio) {
      if (beneficio == freteEscolar) {
        $("#inputs-barreira").css("display", "block");
        $("#inputs-cartao").css("display", "none");
        $('#validarSolicitacaoVizinho').show();


      } else {
        desabilitarCheckboxes();
        $("#inputs-cartao").css("display", "block");
        $("#inputs-barreira").css("display", "none");
        $('#validarSolicitacaoVizinho').hide();
        $("#solicitacaotransporte-cartaopasseescolar").val("");
        $("#solicitacaotransporte-cartaovaletransporte").val("");
        $("#solicitacaotransporte-tipofrete").val("");

      }
    }
  }


  $("#solicitacaotransporte-justificativabarreirafisica").keyup(function() {
    if ($("#solicitacaotransporte-justificativabarreirafisica").val()) {
      $("#submitButton").prop("disabled", false);
      //delError('.field-solicitacaotransporte-justificativabarreirafisica');
    } else {
      $("#submitButton").prop("disabled", true);
      //addError('.field-solicitacaotransporte-justificativabarreirafisica');
    }
  });


  function addError(classname) {
    $("body").find(classname).addClass('has-error');
  }

  function delError(classname) {
    $("body").find(classname).removeClass('has-error');
  }

  function carregarJustificativa() {
    let modalidadeBeneficio = $("#solicitacaotransporte-modalidadebeneficio").val();
    let barreiraFisica = $("#solicitacaotransporte-barreirafisica").val();
    let distanciaEscola = parseFloat($("#solicitacaotransporte-distanciaescola").val());
    let tipoFrete = $("#solicitacaotransporte-tipofrete").val();
    // modalidadeBeneficio = 1 = frete
    // barreirafisica = 1 = Sim
    // tipofrete = 2 = Frete Adaptado

    if (barreiraFisica == 1) {

      $("#motivoBarreiraFisica").css("display", "block");
    } else {
      $("#motivoBarreiraFisica").css("display", "none");
      $("#solicitacaotransporte-motivobarreirafisica").val("");
    }
    if (modalidadeBeneficio == 1 && barreiraFisica == 1 && distanciaEscola < 2 && tipoFrete != 2) {
      console.log(modalidadeBeneficio, barreiraFisica, distanciaEscola);
      $("#justificativa-barreira").css("display", "block");
      $("#submitButton").prop("disabled", true);
      //addError('.field-solicitacaotransporte-justificativabarreirafisica');
    } else {
      $("#justificativa-barreira").css("display", "none");
      $("#submitButton").prop("disabled", false);
    }

  }

  $("#solicitacaotransporte-modalidadebeneficio").change((event) => {
    let modalidadeBeneficio = $("#solicitacaotransporte-modalidadebeneficio").val();

    carregarJustificativa();
    carregarBarreira();
    carregarAnexos();
    // if(modalidadeBeneficio == 2) {
    //   validarCartoes();
    // }
  });

  $("#solicitacaotransporte-barreirafisica").change((event) => {
    carregarJustificativa();
  });

  $("#solicitacaotransporte-distanciaescola").change((event) => {
    carregarJustificativa();
  });

  function desabilitarCheckboxes(){
     $("#validarSolicitacaoEspecial").hide();
      $("#checkSolicitacaoEspecial").attr('required', false); 
      $("#checkSolicitacaoEspecial").prop("checked", false);

      $("#validarLaudoMedico").hide();
      $("#checkLaudoMedico").attr('required', false); 
      $("#checkLaudoMedico").prop("checked", false);
  }
  $("#solicitacaotransporte-tipofrete").change((event) => {
    let tipoFrete = $("#solicitacaotransporte-tipofrete").val();
    // 2 == adaptado
    if(tipoFrete == 2){
      $("#validarSolicitacaoEspecial").show();
      $("#validarLaudoMedico").show();
      $("#checkSolicitacaoEspecial").attr('required', true); 
      $("#checkLaudoMedico").attr('required', true); 
    } else {
      desabilitarCheckboxes();

    }
    carregarJustificativa();
    carregarAnexos();
  });

  window.onload = function() {
    carregarJustificativa();
    carregarBarreira();
  };
  $(document).ready(function() {

    $("#novo-aluno").change(function() {
      $("#solicitacaotransporte-novasolicitacao--0").prop("checked", true);
    })

    $("#renovacao-aluno").change(function() {
      $("#solicitacaotransporte-novasolicitacao--1").prop("checked", true);
    })



    // $('.meters').mask('^?\d*[.,]?\d*$', {reverse: true});
    $("#solicitacaotransporte-distanciaescola").inputFilter(function(value) {
      return /^-?\d*[.,]?\d*$/.test(value)
    });
  });
</script>