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
<div class="alert alert-warning" style="background:#FFF200 !important;color:#000!important;" role="alert">
  <b style="color:red!important;text-align:center !important;">ATENÇÃO!</b><br>
  <b>Favor informar os dados referentes a matrícula do (a) Aluno (a) para o <span style="color:#297DBA!important;">Ano Letivo de <?php print date('Y')+1 ?></span>. Caso o HORÁRIO DE ENTRADA e HORÁRIO DE SAÍDA estejam incorretos, altere no Cadastro do (a) Aluno (a) e crie uma <span style="color:#5DC334!important;">NOVA SOLICITAÇÃO DE TRANSPORTE</span></b>
</div>
<div id="radios" class="row">
  <div class="center-radio" style="width: 40%"> 
    <div class="radio1">
      <label>
        <img src="img/add_student.png" style="height: 30px;">
        <input type="radio" id="novo-aluno" name="alunotipo" required> Novo aluno <span class="anoVigente"></span>
      </label>
    </div> 
    <div class="radio2">
      <label>
        <img src="img/renovation_student.png" style="height: 30px;">
        <input type="radio" id="renovacao-aluno" name="alunotipo" required> Renovação <span class="anoVigente"></span>
      </label>
    </div>
  </div>
</div>
<div id="novaSolicitacao" style="display:none !important">
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
        <?= $form->field($model, 'barreiraFisica')->dropDownList([1 => 'Sim', 2 => 'Não'], ['prompt' => 'SELECIONE']) ?>
      </div>
      <div class="col-md-3">
        <?= $form->field($model, 'tipoFrete')->dropDownList(SolicitacaoTransporte::ARRAY_TIPO_FRETE, ['prompt' => 'SELECIONE']) ?>
      </div>
      <div class="col-md-12" style="display:none;" id="motivoBarreiraFisica">
        <?= $form->field($model, 'motivoBarreiraFisica')->dropDownList(SolicitacaoTransporte::ARRAY_MOTIVO_BARREIRA_FISICA, ['prompt' => 'SELECIONE']) ?>
      </div>
    </div>
  </div>
  <div class="row" id="justificativa-barreira">
    <div class="col-md-12">
      <?= $form->field($model, 'justificativaBarreiraFisica')->textarea(['rows' => '6', 'placeholder' => '']) ?>
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
        <?php echo $form->field($model, 'checkForm')->checkBox(['id' => 'checkForm']); ?>
      </div>
      <div class="row" style="background-color: #fff; padding-left: 20px; margin: 0px;">
        <?php echo $form->field($model, 'checkEnd')->checkBox(['id' => 'checkEnd']); ?>
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

  <div class="row" id="escolasProximas" style="display:none;">
      <div class="col-md-12">
        <?=
          $form->field($model, 'EscolasProximas')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Escola::find()->all(), 'id', 'nomeCompleto'),
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
</div>
  <div class="row" id="renovacaoSolicitacao"  style="display:none;">
  
    <div class="col-md-6">
    <?php
      echo '<label class="control-label">Ensino</label>';
      echo Select2::widget([
          'name' => 'aluno-ensino',
          'data' => Escola::ARRAY_ENSINO,
          'options' => [ 
              'id' => 'aluno-ensino-select',
              'placeholder' => '',
              'multiple' => false
              
          ],
      ]);
    ?>
    </div>
    <div class="col-md-3">
    <?php
      echo '<label class="control-label">Série/Ano</label>';
      echo Select2::widget([
          'name' => 'aluno-serie',
          'data' => Aluno::ARRAY_SERIES,
          'options' => [
              'id' => 'aluno-serie-select',
              'placeholder' => '',
              'multiple' => false
              
          ],
      ]);
    ?>
    </div>
    <div class="col-md-3">
    <?php
      echo '<label class="control-label">Turma</label>';
      echo Select2::widget([
          'name' => 'aluno-turma',
          'data' => Aluno::ARRAY_TURMA,
          'options' => [
              'id' => 'aluno-turma-select',
              'placeholder' => '',
              'multiple' => false
              
          ],
      ]);
    ?>
    </div>
    <div class="col-md-12">
    <div class="row" >
        <?php echo $form->field($model, 'checkRenovacao1')->checkBox(['id' => 'checkRenovacao1'])->label(' Confirmo que o horário do aluno no Ano Letivo de '.(date('Y')+1).' é das <b>'.$model->aluno->horarioEntrada.'</b> às <b>'.$model->aluno->horarioSaida.'</b>.'); ?>
    </div>
    <div class="row">
    <?php echo $form->field($model, 'checkRenovacao2')->checkBox(['id' => 'checkRenovacao2'])->label('Afirmo que os dados informados na Renovação do Benefício para o <b>Ano Letivo de '.(date('Y')+1).' estão de acordo e que,<br>caso ocorram alterações, devo cadastrar Nova Solicitação de Transporte.'); ?>

    </div>
    

   
    </div>
  </div> 
  <div class="row">
    <div class="col-4">
      <?php
      echo $form->field($model, 'novaSolicitacao')->radioList([1 => 'Aluno novo', 2 => 'Renovação'], ["hidden" => true])->label(false);
    ?>
    </div>
  </div>
<script type="text/javascript">
  var freteEscolar = <?php print Aluno::MODALIDADE_FRETE; ?>;
  var idAluno = <?= $model->idAluno ?>;
  var solicitacaoAtual;
  var tipoSolicitacao = <?= $model->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_BENEFICIO ?>;
  var novaSolicitacao = 1; // 1= nova sol , 0 = renovacao
  var configuracoes;
  

  $('#checkInex').click(function(i, value){
    if($(this).is(":checked")){
      $("#escolasProximas").show(); 
    } else {
      $("#escolasProximas").hide();
    }
  })
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
    } else {
      delError('.field-solicitacaotransporte-cartaopasseescolar');
      delError('.field-solicitacaotransporte-cartaovaletransporte');

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
        $(".field-solicitacaotransporte-tipofrete").show();

      } else {
        desabilitarCheckboxes();
        $("#inputs-cartao").css("display", "block");
        $("#inputs-barreira").css("display", "block");
        $(".field-solicitacaotransporte-tipofrete").hide();
        $('#validarSolicitacaoVizinho').hide();
        $("#solicitacaotransporte-cartaopasseescolar").val("");
        $("#solicitacaotransporte-cartaovaletransporte").val("");
        $("#solicitacaotransporte-tipofrete").val("");

      }
    }
  }


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
    //modalidadeBeneficio == 1 && barreiraFisica == 1 && 
    if (distanciaEscola < 2 && tipoFrete != 2) {
      console.log(modalidadeBeneficio, barreiraFisica, distanciaEscola);
      $("#justificativa-barreira").css("display", "block");
      addError('.field-solicitacaotransporte-justificativabarreirafisica');
      if(novaSolicitacao)
        $("#solicitacaotransporte-justificativabarreirafisica").prop('required', true)
    } else {
      $("#justificativa-barreira").css("display", "none");
      if(novaSolicitacao)
        $("#solicitacaotransporte-justificativabarreirafisica").prop('required', false)

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
  function validateButton(){
    $("#submitButton").prop("disabled", true);
    let inputs = [];
    let modalidadeBeneficio = $("#solicitacaotransporte-modalidadebeneficio").val();
    // Inputs requeridos frete Escolar
    if(modalidadeBeneficio == freteEscolar){
      inputs = [
        'solicitacaotransporte-modalidadebeneficio',
        'solicitacaotransporte-distanciaescola',
        'solicitacaotransporte-barreirafisica',
        'solicitacaotransporte-tipofrete',
        // 'solicitacaotransporte-justificativabarreirafisica'
      ];
    } 
    // Inputs requeridos para Passe Escolar
    else {
      inputs = [
        'solicitacaotransporte-distanciaescola',
        'solicitacaotransporte-barreirafisica',
        // 'solicitacaotransporte-cartaopasseescolar',
        // 'solicitacaotransporte-cartaovaletransporte',
        // 'solicitacaotransporte-justificativabarreirafisica'
      ]; 
    }

    let inputsValidos = 0;
    for(let i = 0; i < inputs.length; i++){
        let thisInput =  $('#'+inputs[i]).val();
        // Se um dos inputs estiver sem valor ele desabilita o submit
        if( thisInput ){
          inputsValidos++;
          delError('.field-'+inputs[i]);
        } else {
          addError('.field-'+inputs[i]);
        }
    }
    // Se TODOS os inputs requeridos ESTÃO com valor então habilita o save
    // E se não estamos no meio do processamento de uma request
    if(inputs.length == inputsValidos && !window.request)
      $("#submitButton").prop("disabled", false);
  }
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
      // só devem ser requeridos em nova sol, em renovacao serao clonados
      if(novaSolicitacao){
        $("#checkSolicitacaoEspecial").attr('required', true); 
        $("#checkLaudoMedico").attr('required', true); 
      }
    } else {
      desabilitarCheckboxes();

    }
    carregarJustificativa();
    carregarAnexos();
  });

  function limparCampos(){
    $("#renovacaoSolicitacao").hide()
    let camposEstaticos = ['idAluno','idEscola', 'tipoSolicitacao', 'novaSolicitacao'];

    for (const [key, value] of Object.entries(solicitacaoAtual.solicitacao)) {
      // Se este campos NÃO faz parte dos campos que não devem ser alterados
      // console.log(key)
      if(camposEstaticos.indexOf(key) !== -1){
        // console.warn(key)
        console.warn('key encontrada', key)
      } else {
        $("*[name='SolicitacaoTransporte["+key+"]']").val('').change();
        if(value)
          $("*[name='SolicitacaoTransporte["+key+"]']").prop('checked',false);
        
      }
    }
    // campos de select2 que necessitam ser limpos
    $('#aluno-ensino-select').val(null).trigger("change");
    $('#aluno-serie-select').val(null).trigger("change");
    $('#aluno-turma-select').val(null).trigger("change");
  }
  function getConfiguracoes(){
      $.getJSON("index.php?r=configuracao%2Fview-ajax")
      .done(function(response) {
        configuracoes = response; 
        $(".anoVigente").html(response.anoVigente)
      });
  }
  function getSolicitacaoAtual(){
      $.getJSON("index.php?r=solicitacao-transporte%2Fsolicitacao-vigente-ajax", {
        "idAluno": idAluno
      })
      .done(function(response) {
        if(response.status){
          // global var
          solicitacaoAtual = response
          $("#novaSolicitacao").hide()
          $("#renovacaoSolicitacao").show()
          for (const [key, value] of Object.entries(response.solicitacao)) {
            if(key != 'novaSolicitacao'){
              $("*[name='SolicitacaoTransporte["+key+"]']").val(value).change();
              if(value)
                $("*[name='SolicitacaoTransporte["+key+"]']").prop('checked',true); 
            }

            
            // $('#aluno-ensino-select').val(response.aluno.ensino).trigger("change");
            // $('#aluno-serie-select').val(response.aluno.serie).trigger("change");
            // $('#aluno-turma-select').val(response.aluno.turma).trigger("change");
          }
        } else {
          $("#novoAluno").show()
          $("#renovacaoSolicitacao").hide()
          $("#novo-aluno").prop('checked',true).change()

          Swal.fire(
            'Nenhuma solicitação vigente',
            'Este aluno não é atendido em nenhuma solicitação',
            'warning'
          )          
        }
        // $("#tabelaEndereco").css("display", "none");
        // if (data.status) {
        //   mostrarTabela(data.enderecos);
        // } else {
        //   Swal.fire(
        //     'Logradouro não encontrado',
        //     'Digite o CEP ou o nome de um logradouro válido',
        //     'warning'
        //   )
        //   $("#aluno-endereco").focus();
        //   $("#cep").val("");
        //   $("#aluno-endereco").val("");
        // mostrarMapa();
        //}

      });
  }
  window.onload = function() {
    carregarJustificativa();
    carregarBarreira();
  };
  $(document).ready(function() {
    
    $("#novo-aluno").change(function() {
      $("#solicitacaotransporte-novasolicitacao--0").prop("checked", true);
      //variavel global
      novaSolicitacao = 1;
      $("#checkForm").attr('required', true); 
      $("#checkEnd").attr('required', true); 
      $("#novaSolicitacao").show()
      if(solicitacaoAtual)
        limparCampos()
    })

    $("#renovacao-aluno").change(function() {
      novaSolicitacao = 0;
      $("#checkForm").attr('required', false); 
      $("#checkEnd").attr('required', false); 
      $("#solicitacaotransporte-novasolicitacao--1").prop("checked", true);
      getSolicitacaoAtual()
    })



    // $('.meters').mask('^?\d*[.,]?\d*$', {reverse: true});
    $("#solicitacaotransporte-distanciaescola").inputFilter(function(value) {
      return /^-?\d*[.,]?\d*$/.test(value)
    });

    // Cria um gatilho de validação por tempo e não por .change()
    // Evita repetição 
    setInterval(() => {
      // o botão só deve ser validado para CRIAR/RENOVAR solicitação, então criamos uma flag global pra isso
      //habiltia o botão de renovação direto, quem vai clonar os dados é o back
      if(tipoSolicitacao && novaSolicitacao)
        validateButton();
        //força o botao de renovacao a estar sempre habilitado
        // DESDE QUE não esteja processando uma request
        if(!novaSolicitacao && !window.request &&  $('#aluno-ensino-select').val() != '' && $('#aluno-serie-select').val() != '' && $('#aluno-turma-select').val() != '' && $("#checkRenovacao1").prop('checked') && $("#checkRenovacao2").prop('checked')) {
          console.log( '!!!!!!!!!!!!!!!!')
          $("#submitButton").prop("disabled", false);
        } else {
          //desabilita se os critérios da renovação não baterem
          if(!novaSolicitacao)
            $("#submitButton").prop("disabled", true);

        }
    }, 1000 * 0.5)

    $("#submitButton").prop("disabled", true);
    getConfiguracoes()
  });
</script>