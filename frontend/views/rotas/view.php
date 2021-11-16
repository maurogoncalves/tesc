<?php 
use common\models\Escola;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Aluno;
use common\models\Ponto;
use common\models\Condutor;
use common\models\CondutorRota;
use common\models\SolicitacaoTransporte;
use common\models\Usuario;
function getAlunos(){
  $alunos = [];
  foreach(Aluno::find()->all() as $aluno){
    $alunos[$aluno->id] = $aluno->nome.' - '.substr($aluno->horarioEntrada,0,5).' às '.substr($aluno->horarioSaida,0,5);
  }
  return json_encode($alunos);
  // ArrayHelper::map(Aluno::find()->all(), 'id', 'nome')
}
?> 
<style type="text/css">
  ul {
      list-style-type: none;
      padding-left: 0px;

  }
  .escolas {
    color:#FF9A00;
  }
  .aluno {
    color:#163783;
  }
  .alunos {
    color:#1081E0;
  }
  .remove{
    color: red;

  }

  .visualizar-ponto {
    margin-right: 4px;
  }
  .no-gutter {
  margin-right: 0;
  margin-left: 0;
}

.no-gutter > [class*="col-"] {
  padding-right: 0;
  padding-left: 4px;
}
.red-warning {
  color: red;
}
.alert-red-warning {
    display: none;
}
.list-group-item {
      background-color: #f8f8f8;
}
#mapid {
  height:100%;
}
</style>
    <?php 
    function labelColor($sentido=''){
      switch($sentido){
        case 1: return 'primary'; break;
        case 2: return 'info'; break;
        default: return 'default'; break;
      }
    }
    ?>
    <?php if(isset($_GET['tipo'])): ?>
    <style>
      .remove {
        display: none;
      }
    </style>
    <?php endif; ?>
  <div class="box box-solid">
      <div class="box-header with-border">
        <h3>Código: <?= $model->id ?></h3>
        <h4>Sentido: <span class="label label-<?= labelColor($model->sentido); ?>"><?= $model->sentido ? CondutorRota::ARRAY_SENTIDO[$model->sentido] : '-'; ?></span>
        

        <h4>Descrição: <?= $model->descricao ? $model->descricao : '-'; ?>
        <h4>Viagem: <?= $model->viagem ? CondutorRota::ARRAY_VIAGEM[$model->viagem] : '-'; ?>
        <h4>Período: <?= $model->turno ? CondutorRota::ARRAY_TURNOS[$model->turno] : '-'; ?>
    </div>
    <div class="row" id="condutores">
          <div class="col-md-6">----------
            <?php
                echo '<label class="control-label">Condutor</label>';
                echo Select2::widget([
                    'name' => 'condutores',
                    'data' => ArrayHelper::map(Condutor::disponivelRotaAtivo(), 'id', 'nomePlaca'),
                    'disabled' => Usuario::permissao(Usuario::PERFIL_CONDUTOR),
                    'value' => $model->idCondutor,
                    'disabled' => true,
                    'options' => [
                        'id' => 'condutores-select',
                        'placeholder' => '',
                        'multiple' => false
                        
                    ],
                    'pluginEvents' => [
                            "change" => 'function() {   
                              gerenciarCondutor();
                            }',
                        ],
                ]);
             ?>
          </div>
           <div class="col-md-3">
              <div class="row">
                <div class="col-md-12">
                    <label>Alunos / Assentos livres</label>
                </div>
                <div class="col-md-2">
                     <h5 class="">
                      <b>
                        <span id="totalAlunos">0</span>/<span id="capacidade">0</span>
                      </b>
                    </h5>
                </div>
                <div class="col-md-10">
                 <h5 class="alert-red-warning pull-left">
                    <i class="fa fa-exclamation-triangle red-warning" data-toggle="tooltip" data-placement="top" title="Número de alunos excede a capacidade máxima do veículo" aria-hidden="true"></i>
                  </h5>
                </div>
              </div>
            
           
           </div>
            <div class="col-md-3">
              <Br>
              <?php if(SolicitacaoTransporte::permissaoEditar()):  ?>
              <button class="btn btn-success pull-right" onclick="showModal()">Novo Ponto</button>
               <?php endif; ?>
            </div>
      </div>
      <div class="row no-gutter">
  <div class="col-md-8">
    <div id="location-map">
      <div id="mapid" style="min-height:500px;">
      
      </div>
    </div>
  </div>
  <div class="col-md-4">

    <div class="row">
        <div class="col-md-12">
          <ul id="pontosContent"></ul>
        </div>
    </div>  
  </div>
</div>
      <div class="modal fade" id="modal"  role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Selecione o local</h4>
      </div>
      <div class="modal-body">
 

<!-- <li  class="draggable list-group-item">'.$aluno->nome.'<span class="hidden">'.$aluno->id.'</span></li>' -->
        <div class="row">
          <div class="col-md-12">

            <?php
                echo '<label class="control-label">Tipo</label>';
                echo Select2::widget([
                    'name' => 'tipo',
                    'data' => [
                        Ponto::PONTO_ESCOLA => 'Escola',
                        Ponto::PONTO_ALUNO => 'Casa de aluno(a)',
                        Ponto::PONTO_ENCONTRO => 'Ponto de encontro' 
                    ],
                    'options' => [
                      'id' => 'tipo-select',
                      'prompt' => 'SELECIONE',
                      'placeholder' => '',
                      'multiple' => false
                    ],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                    'pluginEvents' => [
                      "change" => "function() { settipoPonto(this.value) }",
                    ],
                ]);
             ?>       
          </div>
        </div> 

        <div class="row" id="escolas"  style="display: none;">
          <div class="col-md-12">
            <?php
                echo '<label class="control-label">Escola(s)</label>';
                echo Select2::widget([
                    'name' => 'escolas',
                    'data' => [],
                    'options' => [
                        'id' => 'escolas-select',
                        'placeholder' => '',
                        'multiple' => false
                    ],
                    'pluginEvents' => [
                      "change" => "function() { validarConfirmar(); }",
                    ],                    
                ]);
             ?>
          </div>
        </div>

        <div class="row" id="alunos"  style="display: none;">
          <div class="col-md-12">
            <?php
                echo '<label class="control-label">Alunos(as)</label>';
                echo Select2::widget([
                    'name' => 'alunos',
                    'data' => [],
                    'options' => [
                        'id' => 'alunos-select',
                        'placeholder' => '',
                        'multiple' => true
                    ],
                    'pluginEvents' => [
                      "change" => "function() { validarConfirmar(); }",
                    ],
                ]);
             ?>
          </div>
        </div>
         <div class="row" id="aluno" style="display: none;">
          <div class="col-md-12">
            <?php
                echo '<label class="control-label">Aluno(a)</label>';
                echo Select2::widget([
                    'name' => 'aluno',
                    'data' => [],
                    'options' => [
                        'prompt' => 'Selecione',
                        'id' => 'aluno-select',
                        'placeholder' => '',
                        'multiple' => false
                    ],
                    'pluginEvents' => [
                      "change" => "function() { validarConfirmar(); }",
                    ],
                ]);
             ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      
          <button type="button" class="btn btn-success pull-right" id="saveLocation">Confirmar</button>
       
      </div>
    </div>
  </div>
</div>
<script src="js/jquery-ui.js"></script>

<script>

var pontos = [];
var idRota = <?= $model->id ?>;
var escolas = [];

function showModal(){
    $('#modal').modal("show");
}

async function settipoPonto(tipo){
    switch(tipo) {
        //escolas
        case '2': 
            montarInput('escolas-disponiveis','escolas',{})
            $("#escolas").css("display", "block");

        break;
    }
}

//{ escolas: escolasSelecionadas }
async function montarInput(method, divAppend, data={}) {
    $.post( "index.php?r=rotas/"+method+"&id="+idRota, data)
      .done(function( data ) {
           $("#"+divAppend+"-select").append("<option value=''>Selecione</option>");
            switch(divAppend) {
                case 'escolas': escolas = data
            }
            data.forEach((item, i) => {
                $("#"+divAppend+"-select").append("<option value="+item.id+">"+item.nome+"</option>");
            });
   
      });
}

function popular(method) {
    $.get( "index.php?r=rotas/"+method)
      .done(function( data ) {
         switch(method)
         {
             
         }
   
      });
}

function validarConfirmar() {
    function validarConfirmar(){
      //console.warn("Validar Confirmar");
      if($('#aluno-select').val() && $('#aluno-select').val().length)
        return $("#saveLocation").attr("disabled", false);
      if($('#alunos-select').val() && $('#alunos-select').val().length)
        return $("#saveLocation").attr("disabled", false);
      if($('#escolas-select').val() && $('#escolas-select').val().length)
        return $("#saveLocation").attr("disabled", false);
    
      return $("#saveLocation").attr("disabled", true);
  }
}

function switchDiv(shows, hides){
  shows.forEach((div) => $("#"+div).css("display", "block"));
  hides.forEach((div) => {
    $("#"+div).css("display", "none");
    $('#'+div+'-select').val(null);
  });
}
function clear(){
  //console.warn('LIMPAR INPUTS');
  $("#tipo-select").val(null).trigger("change");
  $('#escolas-select').val(null).trigger("change");
  $('#aluno-select').val(null).trigger("change");
  $('#alunos-select').val(null).trigger("change");
  switchDiv([],['escolas','alunos','aluno']);  
  $('#escolas-select').empty();
  $('#aluno-select').empty();
  $('#alunos-select').empty();
  
}

$(document).on('hide.bs.modal','#modal', function () {
    clear();
});


$("#saveLocation").click(function(){
    
    console.log('**NOVO PONTO **');
    let tipo = $("#tipo-select").val();
    switch(tipo) {
      case '2': 
        //console.warn('Ponto Escola');
        let escolas = $("#escolas-select").val();
        addMarker(tipo,[], [escolas]);
      break;

      case '3':
        console.warn('Ponto Aluno');
        let aluno =  $("#aluno-select").val();
        addMarker(tipo, [aluno], []);
      break;

      case '4':
        console.warn('Ponto Encontro');  
        let alunos =  $("#alunos-select").val();
        addMarker(tipo, alunos, []);
      break;
      
      default:
        console.warn('NENHUMA OPÇÃO VÁLIDA EM #saveLocation .click()') 
      break;
    } 

    clear();
    validarSalvar();
    $('#modal').modal("hide");
});

function getEscola(idEscola) {
    console.log(idEscola)
    console.warn(escolas.find((escola) => escola.id == idEscola));
    return escolas.find((escola) => escola.id == idEscola)
}
function getAluno(idAluno) {
    return alunos.find((aluno) => aluno.id == idAluno)
}

function addMarker(tipo, alunos, escolas, lat=-23.223701, lng=-45.9009074){

// desabilitar requests async para evitar atropelamentos
jQuery.ajaxSetup({async:false});

let icon = '';
let info = null;
switch(tipo) {
    case '2': 
      icon = 'escola';
      info = getEscola(escolas[0])
      //console.warn('Ponto Escola');
    break;

    case '3':
      icon = 'residencia';
      info = getAluno(alunos[0])
      //console.warn('Ponto Aluno');
    break;

    case '4':
      icon = 'ponto';
      //console.warn('Ponto Encontro');  
    break;
    
    default:
      console.warn('NENHUMA OPÇÃO VÁLIDA EM addMarker .click()') 
      return null;
    break;
}


  let position =  L.latLng(lat,lng);
  var myIcon = L.icon({
    iconUrl:   'img/icon_'+icon+'.png',
  });


  
  let marker = L.marker(position, {icon:myIcon, draggable: true});
  //.bindPopup((pontos.length + 1).toString());
  marker.position = position;
  marker.tipo = tipo;
  marker.alunos = alunos;
  marker.escolas = escolas;
  marker.id = Date.now();
  marker.order = pontos.length + 1;
  pontos.push(marker);
  marker.addTo(map);
  var group = new L.featureGroup(pontos);

  map.fitBounds(group.getBounds());

marker.on("dragend",function(e){
  let indexPonto = pontos.indexOf(marker);


    atualizarPonto(indexPonto, marker);

});
function calcularPontos(){
  $("#pontosContent").html("");
  let icon ='';
  totalAlunos = 0;
  escolasSelecionadas = [];
  alunosSelecionados = [];
  for (var i = 0; i < pontos.length; i++) {
  switch(pontos[i].tipo) {
      case '2': 
        //console.warn('Ponto Escola');
        
        icon =  `<img src="img/icon_escola.png" style="margin-bottom: 2px;height:15px"> <span class="escolas">`+pontos[i].order+`º 
                  Escola(s)
                  <i class="fa fa-times remove pull-right" onclick="removerPonto(`+pontos[i].id+`)" ></i>
                   </span>
                  <i class="far fa-eye pull-right visualizar-ponto" onclick="visualizarPonto(`+pontos[i].id+`)"></i>
                 `;
        // if(isCondutor)
            icon =`<img src="img/icon_escola.png" style="margin-bottom: 2px;height:15px"> <span class="escolas">`+pontos[i].order+`º 
                  Escola(s)`; 
          $("#pontosContent").append(mountEscolas(pontos[i], icon));
      break;
      
      case '3':
        //console.warn('Ponto Aluno');
        icon =  `<img src="img/icon_residencia.png" style="margin-bottom: 2px;height:15px"> <span class="aluno">`+pontos[i].order+`º  
                        Residência de aluno(a)
                    
                      <i class="fa fa-times remove pull-right" onclick="removerPonto(`+pontos[i].id+`)" ></i>
                      </span>
                      <i class="far fa-eye pull-right visualizar-ponto" onclick="visualizarPonto(`+pontos[i].id+`)"></i>
                      `;
        // if(isCondutor)
            icon =`<img src="img/icon_residencia.png" style="margin-bottom: 2px;height:15px"> <span class="aluno">`+pontos[i].order+`º  
                        Residência de aluno(a)`;
        $("#pontosContent").append(mountAlunos(pontos[i], icon));

      break;
      
      case '4':


        icon =  `<img src="img/icon_ponto.png" style="margin-bottom: 2px;height:15px"> <span class="alunos">`+pontos[i].order+`º
                       Ponto de encontro
                  
                     <i class="fa fa-times remove pull-right" onclick="removerPonto(`+pontos[i].id+`)" ></i>
                     </span>   
                     <i class="far fa-eye pull-right visualizar-ponto" onclick="visualizarPonto(`+pontos[i].id+`)"></i>
                     <i class="far fa-edit pull-right editar-ponto" onclick="editarPonto(`+pontos[i].id+`)"></i>

                    `;
          if(isCondutor)
            icon =`<img src="img/icon_ponto.png" style="margin-bottom: 2px;height:15px"> <span class="alunos">`+pontos[i].order+`º
                       Ponto de encontro`;
        $("#pontosContent").append(mountAlunos(pontos[i], icon));
      break;
      
      default:
        console.warn('NENHUMA OPÇÃO VÁLIDA EM alterarPonto()') 
      break;
    }
  }

  $("#totalAlunos").text(totalAlunos);
  validarAlerta();

}
calcularPontos();
//reabilitar aync
//jQuery.ajaxSetup({async:true});



} 




$(document).ready(function() {


function initializeGMap(lat, lng) {
  var titleLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
      attributions: 'www.ipplan.org.br',
      maxZoom: 18,
      
    });

  map = L.map("location-map", {
      'center': [0,0],
      'zoom' : 15,
      'layers': [titleLayer]

    }); 

    map.setView(new L.latLng(-23.223701,-45.9009074));

}
initializeGMap(-23.223701, -45.9009074);
    $("#location-map").css("width", "100%");
    $("#map_canvas").css("width", "100%");
});







$("#pontosContent").sortable({
  'reverse': true,
  stop: function(ev, ui) {
    //Get the updated positions by calling refreshPositions and then .children on the resulting object.
    var children = $('#pontosContent').sortable('refreshPositions').children();
    //console.warn('Positions: ');
    //Loopp through each item in the children array and print out the text.
    let posicoes =[]; 
    $.each(children, function() {
        //console.warn($(this).text().trim());
        posicoes.push($(this).find('.ponto-id').text());
    });
    reordenarPontos(posicoes);
  }
});

function reordenarPontos(pontosReordenados){
    var novoPontos = []
 
    for (var i = 0; i < pontosReordenados.length; i++) {
      let pontoNovo = pontosReordenados[i];
       //Go Horse para pegar a posição EXATA do item
        try {
          let index = pontoNovo;
          //console.warn('Procurando Obj ponto no index', index);
          let pontoBuscado = pontos.find(ponto => ponto.id == index);
          //pontoBuscado.labelContent =  (i+1).toString();
           pontoBuscado.order = i + 1;
          //  pontoBuscado.setLabel({
          //   text: (i + 1).toString(),
          //   color: '#FFFFFF'
          //  });
          // pontoBuscado.setShape(); // Force the marker icon to redraw
          // pontoBuscado.label.setContent();
          novoPontos.push(pontoBuscado);

          let indexPonto = pontos.indexOf(pontoBuscado);
          atualizarPonto(indexPonto, pontoNovo);
        } catch(e) {
          console.log('ÍNDICE de pontos reordenados não está seguindo o padrão esperado.',e.message)
        }
    }

    // console.warn('=====');
    // for (var i = 0; i < pontos.length; i++) {
    //   console.warn(pontos[i].tipo);
    // }
    //    console.warn('****');
    // for (var i = 0; i < novoPontos.length; i++) {
    //   console.warn(novoPontos[i].tipo);
    // }
    //    console.warn('=====');
    pontos = novoPontos;
    calcularPontos()
    //console.warn('pontos',pontos);
  }

  function atualizarPonto(index,pontoAtualizado){
  pontos[index] = pontoAtualizado;  
  //console.warn('AFTER', pontos[index], pontos[index].getPosition().lng());

}
function mountEscolas(ponto, icon =''){
  let str = `<li class="draggable list-group-item" id="`+ponto.id+`">`;
    console.log(ponto)

  if(icon)
      str += icon;

    if(ponto.escolas.length > 0)
          str +=`<ul>`;
    // escolasSelecionadas = escolasSelecionadas.concat(ponto.escolas);
    for (var j = 0; j < ponto.escolas.length; j++) {
        let info = getEscola(ponto.escolas[j])
        str +=`<li>`+info.nome+`</li>`; 
    } 
    if(ponto.escolas.length > 0 )
          str += `</ul>`;

    str += `<span class="hidden ponto-id">`+ponto.id+`</span>`;
    str +=`</li>`;
  return str;
}


function mountAlunos(ponto, icon = ''){
  let str = `<li class="draggable list-group-item" id="`+ponto.id+`">`;
  if(icon)
      str += icon;
    if(ponto.alunos.length > 0)
          str +=`<ul>`;
    totalAlunos += ponto.alunos.length;
    alunosSelecionados = alunosSelecionados.concat(ponto.alunos);
    
    for (var j = 0; j < ponto.alunos.length; j++) {
        let indexAluno = ponto.alunos[j];
        str +=`<li>`+listaAlunos[indexAluno]+`</li>`; 
        console.log(listaAlunos)
    } 
    if(ponto.alunos.length > 0 )
          str += `</ul>`;
    str += `<span class="hidden ponto-id">`+ponto.id+`</span>`;
    str +=`</li>`;
  return str;
}

function validarSalvar(){
    //console.log('VALIDAR SALVAR', Date.now());
    $("#salvar").attr("disabled", true);
    //pontos.length > 0
    if(!validarAlerta() && $("#condutores-select").val() && $("#condutores-select").val().length)
      return $("#salvar").attr("disabled", false);
  
  }
  
  function validarAlerta(){
  //console.warn('Validando Alerta');
  if(totalAlunos > parseInt($("#capacidade").text())){
    $(".alert-red-warning").css("display", "block");
    return true;
  } else {
    $(".alert-red-warning").css("display", "none");
    return false;
  }

}
</script>