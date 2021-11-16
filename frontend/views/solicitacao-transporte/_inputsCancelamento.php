<?php
use kartik\widgets\FileInput;
use kartik\date\DatePicker;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use common\models\SolicitacaoTransporte;
use common\models\Escola;
 

/* @var $this yii\web\View */
/* @var $model common\models\Planoconta */
/* @var $form yii\widgets\ActiveForm */
?> 
<style type="text/css">
  #justificativa-barreira, #inputs-barreira, #inputs-cartao, .solicitacao-frete, .solicitacao-frete-adaptado  {
    display: none;
  }
</style>
    <?= $form->field($model, 'idAluno')->hiddenInput(['maxlength' => true])->label(false); ?>
    <?= $form->field($model, 'idEscola')->hiddenInput(['maxlength' => true])->label(false); ?> 
    
    
    <?= $form->field($model, 'tipoSolicitacao')->hiddenInput(['maxlength' => true])->label(false); ?> 
    <!-- <?= "TIPO_SOLICITACAO: ".$model->tipoSolicitacao ?> -->
    <!-- <div class="alert alert-light" role="alert">
      Para baixar o documento de formalização de <B>solicitação de frete</b>, <a target="_new" href="arquivos/REQUISICAO_FRETE.docx">clique aqui</a>. Para <b>solicitação de passe</b>, <a target="_new" href="arquivos/REQUISICAO_PASSE_ESCOLAR_GRATUITO.docx">clique aqui</a>.
    </div> -->


    <div class="row">
         <div class="col-md-12">
          <?= $form->field($model, 'justificativaBarreiraFisica')->textarea(['rows' => '6','placeholder' => '']) ?> 
        </div>
    </div>

    <script type="text/javascript">
  var freteEscolar = <?php print Aluno::MODALIDADE_FRETE; ?>;
  function ocultarAnexos(){
    $(".solicitacao-frete").css("display", "none");
    $(".solicitacao-frete-adaptado").css("display", "none");
  }
  // $("#solicitacaotransporte-cartaopasseescolar").change((event) => {
  //   validarCartoes();
  // });
  
  // $("#solicitacaotransporte-cartaovaletransporte").change((event) => {
  //   validarCartoes();
  // });

  function validarCartoes(){
    let passeEscolar = $("#solicitacaotransporte-cartaopasseescolar").val();
    let valeTransporte = $("#solicitacaotransporte-cartaovaletransporte").val();
    if(!passeEscolar || !valeTransporte){
      addError('.field-solicitacaotransporte-cartaopasseescolar');
      addError('.field-solicitacaotransporte-cartaovaletransporte');
      $("#submitButton").prop("disabled",true);
    } else {
      delError('.field-solicitacaotransporte-cartaopasseescolar');
      delError('.field-solicitacaotransporte-cartaovaletransporte');
      $("#submitButton").prop("disabled",false);
      
    }
    
  }
  function carregarAnexos(){
    ocultarAnexos();
    let beneficio = $("#solicitacaotransporte-modalidadebeneficio").val();
    let tipoFrete = $("#solicitacaotransporte-tipofrete").val();

    if(beneficio == freteEscolar){
      $(".solicitacao-frete").css("display", "block");      
    }
    // tipoFrete = 2 = Adaptado
    if(tipoFrete == 2){
      $(".solicitacao-frete-adaptado").css("display", "block");   
      $(".field-solicitacaotransporte-documentoinexistenciavaga").css("display", "none");

    } else {
      $(".field-solicitacaotransporte-documentoinexistenciavaga").css("display", "block");


    }

  }
  function carregarBarreira(){
    let beneficio = $("#solicitacaotransporte-modalidadebeneficio").val();
    if(beneficio){
       if(beneficio == freteEscolar){
          $("#inputs-barreira").css("display", "block");
          $("#inputs-cartao").css("display", "none");
        } else {
          $("#inputs-cartao").css("display", "block");
          $("#inputs-barreira").css("display", "none");
          $("#solicitacaotransporte-cartaopasseescolar").val("");
          $("#solicitacaotransporte-cartaovaletransporte").val("");
        }
     }
    
  }

 
  $( "#solicitacaotransporte-justificativabarreirafisica" ).keyup(function() {
    if($( "#solicitacaotransporte-justificativabarreirafisica" ).val()){
      $("#submitButton").prop("disabled",false);
      delError('.field-solicitacaotransporte-justificativabarreirafisica');
    } else {
      $("#submitButton").prop("disabled",true);
      addError('.field-solicitacaotransporte-justificativabarreirafisica');
    }
  });
  function addError(classname){
    $( "body" ).find( classname ).addClass('has-error');
  }
  function delError(classname){
    $( "body" ).find( classname ).removeClass('has-error');
  }
  
  function carregarJustificativa(){
    let modalidadeBeneficio = $("#solicitacaotransporte-modalidadebeneficio").val();
    let barreiraFisica = $("#solicitacaotransporte-barreirafisica").val();
    let distanciaEscola = parseFloat($("#solicitacaotransporte-distanciaescola").val());
    let tipoFrete = $("#solicitacaotransporte-tipofrete").val();
    // modalidadeBeneficio = 1 = frete
    // barreirafisica = 1 = Sim
    // tipofrete = 2 = Frete Adaptado
    if(modalidadeBeneficio == 1 && barreiraFisica == 1 && distanciaEscola < 2 && tipoFrete != 2){
      console.log(modalidadeBeneficio,barreiraFisica,distanciaEscola);
      $("#justificativa-barreira").css("display", "block");
      $("#submitButton").prop("disabled",true);
      addError('.field-solicitacaotransporte-justificativabarreirafisica');
    } else {
      $("#justificativa-barreira").css("display", "none");
      $("#submitButton").prop("disabled",false);
    }
  }

  $("#solicitacaotransporte-modalidadebeneficio").change((event) => {
    let modalidadeBeneficio = $("#solicitacaotransporte-modalidadebeneficio").val();
    //Se for passe

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

  
  $("#solicitacaotransporte-tipofrete").change((event) => {
    carregarJustificativa();
    carregarAnexos();
  });

  window.onload = function() {
      carregarJustificativa();
      carregarBarreira();
  };  
  $( document ).ready(function() {
	$('.meters').mask('#.##0.00', {reverse: true});
});
</script>
