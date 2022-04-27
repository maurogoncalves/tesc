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
	$idade      = date("Y") - $aluno->dataNascimento;
    if (date("m") < $mesNasc){
        $idade -= 1;
    } elseif ((date("m") == $mesNasc) && (date("d") <= $diaNasc) ){
        $idade -= 1;
    }
 
    $alunos[$aluno->id] = $aluno->nome.' - '.substr($aluno->horarioEntrada,0,5).' às '.substr($aluno->horarioSaida,0,5).' - Idade '.($idade).' anos';
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
  <div class="box-body">
  <input type="hidden" name="condutor-rota" id="condutor-rota" value="<?= $model->id ?>" />
  <input type="hidden" name="editarPontoEncontro" id="editarPontoEncontro" value="" />

      <div class="row" id="condutores">
          <div class="col-md-6">
            <?php
                echo '<label class="control-label">Condutor</label>';
                echo Select2::widget([
                    'name' => 'condutores',
                    'data' => ArrayHelper::map(Condutor::disponivelRota(), 'id', 'nomePlaca'),
                    'disabled' => Usuario::permissao(Usuario::PERFIL_CONDUTOR),
                    'value' => $model->idCondutor,
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
				<?php if($statusCondutor <> 2){ ?>
					<button class="btn btn-success pull-right" onclick="showModal()">Novo Ponto</button>
				<?php }else{ ?>	
					<p class="btn btn-danger align-button"> Condutor Inativo receber novos alunos</p>
				<?php }  ?>
               <?php endif; ?>
            </div>
      </div>

    <Br>
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
 <div class="row">
  <div class="col-md-12">
  <?php if(SolicitacaoTransporte::permissaoEditar()):  ?>
    <button class="btn btn-primary pull-right" onclick="salvar(true)" id="salvar">Finalizar</button>
  <?php endif; ?>
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
      <input type="hidden" name="listaEscolas" id="listaEscolas" value='<?= json_encode(ArrayHelper::map(Escola::find()->all(), 'id', 'nome'))?>' />
      <input type="hidden" name="listaEscolasCompleta" id="listaEscolasCompleta" value='<?= json_encode(Escola::find()->asArray()->all()); ?>' />
      <input type="hidden" name="listaAlunos" id="listaAlunos" value='<?=  getAlunos() ?>' />

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
                      "change" => "function() { alterarPonto(this.value); validarConfirmar(); }",
                    ],
                ]);
             ?>       
          </div>
        </div> 

        <div class="row" id="escolas">
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

        <div class="row" id="alunos">
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
         <div class="row" id="aluno">
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
</div>
 

<script src="js/jquery-ui.js"></script>

<script type="text/javascript">
var isCondutor = <?= Usuario::permissao(Usuario::PERFIL_CONDUTOR) ? 'true' : 'false' ?>;
var idCondutorRota = <?= $model->id  ?>;
var geocoder; 
var address;
var currentLocation;
var map = null;
var pontos = [];
var logs = [];
// LOGS USADOS PARA FORÇAR O SALVAMENTO
var logsFixos = [];
var totalAlunos = 0;
var listaEscolas;
var listaAlunos;
var escolasSelecionadas = [];
var alunosSelecionados = [];
var alunosDiposniveisBanco = [];
//mantem o último condutor para validação de troca de condutor
var ultimoCondutor = <?= $model->condutor->id ?>;
//var bounds = new google.maps.LatLngBounds();

function switchDiv(shows, hides){
  shows.forEach((div) => $("#"+div).css("display", "block"));
  hides.forEach((div) => {
    $("#"+div).css("display", "none");
    $('#'+div+'-select').val(null);
  });
}
//Esconde Todas as opções;
switchDiv([],['escolas','alunos','aluno']);
function alterarPonto(ponto){
  $('#escolas-select').val(null).trigger("change");
  $('#aluno-select').val(null).trigger("change");
  $('#alunos-select').val(null).trigger("change");
  $('#escolas-select').empty();
  $('#aluno-select').empty();
  $('#alunos-select').empty();
  
  //console.warn('PONTO:', ponto, typeof(ponto));
  switch(ponto) {
    case '2': 
      //console.warn('Ponto Escola');
      switchDiv(['escolas'],['alunos','aluno']);
      $.post( "index.php?r=condutor-rota/search-escolas", { escolas: escolasSelecionadas })
      .done(function( data ) {
           $("#escolas-select").append("<option value=''>Selecione</option>");

            data.forEach((item, i) => {
                $("#escolas-select").append("<option value="+item.id+">"+item.nome+"</option>");
            });
      });
    break;
    
    case '3':
      console.warn('Ponto Aluno');
      switchDiv(['aluno'],['escolas','alunos']);  
      $.post( "index.php?r=condutor-rota/search-alunos", {idCondutor: $("#condutores-select").val(), alunos: alunosSelecionados, alunosBanco: alunosDiposniveisBanco, idCondutorRota: idCondutorRota })
      .done(function( data ) {
              $("#aluno-select").append("<option value=''>Selecione</option>");
            data.forEach((item, i) => {
                $("#aluno-select").append("<option value="+item.id+">"+item.nome+"</option>");
            });
      });
    break;
    
    case '4':
      //console.warn('Ponto Encontro');
      switchDiv(['alunos'],['escolas','aluno']);  
      $.post( "index.php?r=condutor-rota/search-alunos", {idCondutor: $("#condutores-select").val()  , alunos: alunosSelecionados, alunosBanco: alunosDiposniveisBanco, idCondutorRota: idCondutorRota })
      .done(function( data ) {
          
            data.forEach((item, i) => {
                $("#alunos-select").append("<option value="+item.id+">"+item.nome+"</option>");
            });
      });
    break;
    
    default:
      // console.warn('NENHUMA OPÇÃO VÁLIDA EM alterarPonto()') 
    break;
  }
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

function showModal(){
   $('#modal').modal("show");
}

function addMarker(tipo, alunos, escolas, lat=0, lng=0){

  // desabilitar requests async para evitar atropelamentos
  jQuery.ajaxSetup({async:false});
  
  let icon = '';
  switch(tipo) {
      case '2': 
        icon = 'escola';
        //console.warn('Ponto Escola');
      break;

      case '3':
        icon = 'residencia';
        // console.warn('Ponto Aluno: '+lat);
        // Estou plotando este ponto a pedido da Monique no card (https://trello.com/c/ofPqkTc1)...
        // tecnicamente isso não está correto, pois essa tela deveria mostrar os pontos da rota que nem sempre serão a residência do aluno.
		
			
        if(alunos.length == 1){
          console.log(alunos)
          //mantive um for mesmo sendo para UM aluno pois no futuro eles podem querer alterar
          for (var i = 0; i < alunos.length; i++) {
            $.get( "index.php?r=aluno/aluno-ajax", {"id": alunos[i]}, function( data ) {
              // console.warn('Aluno do servidor', data);
              if(data.lat && data.lng){
                lat = data.lat;
                lng = data.lng;
              }
            
            }, 'json');
          }
        }
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

  if(!lat){
    //Inicializa lat lng das escolas quando adcionamos um ponto novo
    // objetivo: Marcar a escola no ponto correto
    if(escolas.length){
      for (var i = 0; i < escolas.length; i++) {
        let escolaBuscada = listaEscolasCompleta.find(escola => escola.id == escolas[i]);
        if(escolaBuscada){
          lat = escolaBuscada.lat;
          lng = escolaBuscada.lng;
        }
      }
    }

    //mesma coisa só que para alunos
    //decidi fazer um $get pois se printasse a lista completa de alunos no HTML ia ficar MUITO grande
     if(alunos.length == 1){
       console.log(alunos)
      //mantive um for mesmo sendo para UM aluno pois no futuro eles podem querer alterar
      for (var i = 0; i < alunos.length; i++) {
        $.get( "index.php?r=aluno/aluno-ajax", {"id": alunos[i]}, function( data ) {
          // console.warn('Aluno do servidor', data);
          if(data.lat && data.lng){
            lat = data.lat;
            lng = data.lng;
          }
        
        }, 'json');
      }
    }
  }
  //Se mesmo após a verificação dos registros o lat continua vazio então atribui o default
  if(!lat) {
    console.log('NENHUM LAT/LNG CADASTRADO, ATRIBUINDO O PADRÃO');
    lat = -23.223701;
    lng = -45.9009074;
  }

    let position =  L.latLng(lat,lng);
    var myIcon = L.icon({
      iconUrl:   'img/icon_'+icon+'.png',
      // iconSize:     [38, 95], // size of the icon
      // shadowSize:   [50, 64], // size of the shadow
      // iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
      // shadowAnchor: [4, 62],  // the same for the shadow
      // popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
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
  // let marker3 = new google.maps.Marker({
  //     position: position,
  //     map: map,
  //     draggable: true,
  //     title: (pontos.length + 1).toString(),
  //     icon: 'img/icon_'+icon+'.png',
  //     id: Date.now(),
  //     order: pontos.length + 1,
  //     tipo: tipo,
  //     alunos: alunos,
  //     escolas: escolas,
  //     label: {
  //       text: (pontos.length + 1).toString(),
  //       color: '#FFFFFF',
  //     }
  // });

  // InfoWindow
  // marker3.addListener('click', function() {
  //   infowindow.open(map, marker3);
  // });

  //pontos.push(marker3);
  //Listen for drag events!

  marker.on("dragend",function(e){
    let indexPonto = pontos.indexOf(marker);


atualizarPonto(indexPonto, marker);

});
  //map.setCenter(marker3.getPosition());  
  
  calcularPontos();
  //reabilitar aync
  jQuery.ajaxSetup({async:true});

  // bounds.extend(position);
  // map.fitBounds(bounds);


}  
function atualizarPonto(index,pontoAtualizado){
  pontos[index] = pontoAtualizado;  
  //console.warn('AFTER', pontos[index], pontos[index].getPosition().lng());

}
function mountEscolas(ponto, icon =''){
  let str = `<li class="draggable list-group-item" id="`+ponto.id+`">`;


  if(icon)
      str += icon;

    if(ponto.escolas.length > 0)
          str +=`<ul>`;
    escolasSelecionadas = escolasSelecionadas.concat(ponto.escolas);
    for (var j = 0; j < ponto.escolas.length; j++) {
        let indexEscola = ponto.escolas[j];
        str +=`<li>`+listaEscolas[indexEscola]+`</li>`; 
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
        // console.log(listaAlunos)
    } 
    if(ponto.alunos.length > 0 )
          str += `</ul>`;
    str += `<span class="hidden ponto-id">`+ponto.id+`</span>`;
    str +=`</li>`;
  return str;
}

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
        if(isCondutor)
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
        if(isCondutor)
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
  function visualizarPonto(id){
     //console.warn("CENTRALIZAR O MAPA EM", id);
     let pontoBuscado = pontos.find(ponto => ponto.id == id);
       //map.setCenter(pontoBuscado.getPosition());  
       map.panTo(pontoBuscado.getLatLng());
  }

$(document).on('hide.bs.modal','#modal', function () {
    $("#editarPontoEncontro").val('');
    clear();
});

  // setInterval(() => {
  //   console.log(pontos)
  // }, 1000 * 3);

  function editarPonto(id){
    $("#editarPontoEncontro").val(id);
    let pontoBuscado = pontos.find(ponto => ponto.id == id);
    let indexPonto = pontos.indexOf(pontoBuscado);
    // pontos[indexPonto] = pontoBuscado;
    showModal();
    $('#tipo-select').val(4).trigger("change");


    let editarAlunosSelecionados = alunosSelecionados;
    let alunosBanco = [];
    for(let i = 0; i < pontoBuscado.alunos.length; i++){
        
        let aluno = pontoBuscado.alunos[i];
        alunosBanco.push(aluno);

        // var index = editarAlunosSelecionados.indexOf(aluno);
        // if (index > -1) {
        //   editarAlunosSelecionados.splice(index, 1);
        // }
    }
    // console.log(editarAlunosSelecionados);
 
    // console.log(editarAlunosSelecionados);
    $.post( "index.php?r=condutor-rota/search-alunos", { idCondutorRota: idCondutorRota, alunos: editarAlunosSelecionados, alunosBanco: alunosBanco, idCondutor: $("#condutores-select").val()  })
      .done(function( data ) {
          $('#alunos-select').empty();
            data.forEach((item, i) => {
                $("#alunos-select").append("<option value="+item.id+">"+item.nome+"</option>");
            });
            $('#alunos-select').val(pontoBuscado.alunos).trigger("change");
      });
  }

  Array.prototype.remove = function() {
      var what, a = arguments, L = a.length, ax;
      while (L && this.length) {
          what = a[--L];
          while ((ax = this.indexOf(what)) !== -1) {
              this.splice(ax, 1);
          }
      }
      return this;
  };
  // Função responsáve p
  function addLog(id, log){
    id = parseInt(id)
    logs.push({ponto: id, log: log})
    removerLogsDuplicados()
    // console.log(JSON.stringify(logs))
  }
  function removerLogsDuplicados(){
    let listaLimpa = []
    for (var i = 0; i < logs.length; i++) {
      const log = logs[i];
      let tmpList = logs.filter((item) => item.ponto == logs[i].ponto);
      // pega pontos repetidos
      if(tmpList.length == 1){
        listaLimpa.push(logs[i])
      }
    }
    logs = listaLimpa
  }
  function removerPonto(id){

      //console.warn("ID A SER REMOVIDO", id);
      let pontoBuscado = pontos.find(ponto => ponto.id == id);
      pontoBuscado.alunos.forEach((aluno) => addLog(aluno, 'REMOVIDO'));
      let indexPonto = pontos.indexOf(pontoBuscado);
      
      //Limpa dos arrs selecionados
      if(pontoBuscado.escolas)
          for (var i = 0; i < pontoBuscado.escolas.length; i++) 
            escolasSelecionadas.remove(pontoBuscado.escolas[i]);
        
      if(pontoBuscado.alunos)
          for (var i = 0; i < pontoBuscado.alunos.length; i++) 
            alunosSelecionados.remove(pontoBuscado.alunos[i]);

      pontos.splice(indexPonto, 1);
      //console.warn(pontoBuscado);  
     // pontoBuscado.setMap(null);
      $("#"+id).remove();
       var children = $('#pontosContent').children();
      //console.warn('Positions: ');
      //Loopp through each item in the children array and print out the text.
      let posicoes =[];
      $.each(children, function() {
         // console.warn($(this).text().trim());
          posicoes.push($(this).find('.ponto-id').text());
      });
      reordenarPontos(posicoes);
      map.removeLayer(pontoBuscado);

      //map.removeOverlay(pontoBuscado);
      clear();
      validarSalvar();
      salvarPonto();
  }

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
  
  function processarNovoCondutor(){
    // console.warn('** processarNovoCondutor **')
    let condutorAtual = $("#condutores-select").val()
    $.get("index.php?r=veiculo/search-ajax", {
                idCondutor: condutorAtual
        })
        .done(function(data) {
            
            //console.warn(data);
            if (data && data.capacidade)
              $("#capacidade").text(data.capacidade);
            else
              $("#capacidade").text(0);
            validarAlerta();
            validarSalvar();
            ultimoCondutor = condutorAtual
    });
  }
  // remove alunos não associados a um condutor
  //actionSearchEscolasAjax
  function removerAlunosNaoAssociados(){
    // map.clearLayers();
    for(var i = 0; i < pontos.length; i++){
        pontos[i].alunos.forEach((idAluno) => addLog(idAluno, 'REMOVIDO') );
        map.removeLayer(pontos[i]);
    }
    
    pontos = [];
    escolasSelecionadas = [];
    alunosSelecionados = [];
    alunosDiposniveisBanco = [];
    $("#pontosContent").html("")
    iniciarPontos(true);
  }
  function gerenciarCondutor() {
    let condutorAtual = $("#condutores-select").val()
    // console.warn(condutorAtual, ultimoCondutor)

    if(ultimoCondutor == condutorAtual)
      return processarNovoCondutor()
    
    Swal.fire({
    title: 'Tem certeza?',
    html: "Alunos e escolas que não pertecem ao condutor serão removidos da rota.<br><b>Esta operação não pode ser revertida.</b>",
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, altere o condutor',
    cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.value) {
        processarNovoCondutor()
        removerAlunosNaoAssociados()
        Swal.fire({
      title: 'Processando solicitação',
      html: '',
      timer: 2000,
      timerProgressBar: true,
      onBeforeOpen: () => {
        Swal.showLoading()
        timerInterval = setInterval(() => {
              const content = Swal.getContent()
              if (content) {
                const b = content.querySelector('b')
                if (b) {
                  b.textContent = Swal.getTimerLeft()
                }
              }
            }, 100)
          },
          onClose: () => {
            clearInterval(timerInterval)
          }
        }).then((result) => {
          /* Read more about handling dismissals below */
          if (result.dismiss === Swal.DismissReason.timer) {
            salvar(true, false)
            $("#salvar").attr("disabled", false);
            logs = [];
            logsFixos = [];
          }
        })
      
        // Swal.fire(
        //   '',
        // 'Operação realizada com sucesso.',
        // 'success'
        // )
        
      } else {
        $("#condutores-select").val(ultimoCondutor).change()
        console.warn("NÃO DEVEMOS TROCAR O CONDUTOR")
      }
    })
  }
  function iniciarPontos(refreshPontos=false){ 
    console.warn('**Iniciando Pontos **');
    let payload = { idCondutorRota: $('#condutor-rota').val(), idCondutor: null }
    if(refreshPontos)
      payload.idCondutor = $("#condutores-select").val()
    $.get( "index.php?r=condutor-rota/view-ajax", payload )
      .done(function( data ) {
		  
        console.log(data);
        for (const [key, value] of Object.entries(data.pontos)) {
          let ponto = value;
		  
          // console.warn(ponto,'p')
          let alunos = ponto.alunos.map(function(item) {return item.id;});
          let escolas = ponto.escolas.map(function(item) {return item.id;});
		  
          // console.warn(alunos);
          //console.log([
          //     ponto.tipo.toString(),
          //     alunos,
          //     escolas,
          //     ponto.lat,
          //     ponto.lng
          // ]);
          if(refreshPontos){
            console.log('REFRESHEDDDDDDDDD', ponto)
            ponto.alunos.forEach((aluno) => logsFixos.push({ponto: aluno.id, log:'ADICIONADO'}))
          }
          addMarker(
              ponto.tipo.toString(),
              alunos,
              escolas,
              ponto.lat,
              ponto.lng)
     
          ultimoCondutor  = data.idCondutor;
          $('#condutores-select').val(data.idCondutor).trigger("change");
        //console.warn(data);
        // $("#capacidade").text(data.capacidade);
        // validarAlerta();
        // validarSalvar();
        map.zoomControl.remove();
        // for(let i = 0; i < pontos.length; i++){
        //   pontos[i].addTo(map);
        // }
      }
     
         
    });
	
  }
$(document).ready(function() {

  var myLatlng; 
  listaEscolas = JSON.parse($("#listaEscolas").val());
  listaAlunos = JSON.parse($("#listaAlunos").val());
  listaEscolasCompleta =  JSON.parse($("#listaEscolasCompleta").val());
  iniciarPontos();
  processarNovoCondutor();

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
    //   lat = -23.223701;
    // lng = -45.9009074;
      map.setView(new L.latLng(-23.223701,-45.9009074));
    // myLatlng = new google.maps.LatLng(lat,lng);
    // geocoder = new google.maps.Geocoder;
    // var myStyles = [
    //     {
    //         featureType: "poi",
    //         elementType: "labels",
    //         stylers: [
    //               { visibility: "off" }
    //         ]
    //     }
    // ];
    // var myOptions = {
    //   zoom: 12,
    //   zoomControl: true,
    //   center: myLatlng,
    //   mapTypeId: google.maps.MapTypeId.ROADMAP,
    //    styles: myStyles 
    // };

    // map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    // google.maps.event.addListener(map, 'click', function(event) { 
    //     //console.warn('clicado');               
    //     var clickedLocation = event.latLng;
    // });
  }

  function load(){ 
    initializeGMap(-23.223701, -45.9009074);
    $("#location-map").css("width", "100%");
    $("#map_canvas").css("width", "100%");
    //google.maps.event.trigger(map, "resize");
    //map.setCenter(myLatlng);
  }



  load();


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


    // var $sortableList = $( "#pontosContent" ).sortable({
    //    revert: true,
    //    stop: sortEventHandler,
    // });

    // var sortEventHandler = function(event, ui){
    // console.warn("Nova ordem do arr!");
    //  setTimeout(() => {
    //    let listElements  = $('#pontosContent').sortable("refreshPositions").children();
    //   let listValues = [];
    //   for(var i = 0; i < listElements.length; i++){
    //     //validação para garantir que não vai pegar nenhum lixo no arr
    //     if(listElements[i].innerHTML)
    //     listValues.push(listElements[i].innerHTML);
    //   }
    //   // listElements.forEach(function(element){
    //   //     listValues.push(element.innerHTML);
    //   // });
    //    reordenarPontos(listValues);
    //   console.warn(listValues); // [ "Item 1", "Item 2", ... ]
    //  }, 1000)

    // };

    // $sortableList.on("sortchange", sortEventHandler);

    // $( ".droppable" ).droppable({
    //   drop: function( event, ui ) {
    //     //AAAAAAAAAAAAAA
    //   }
    // });
   
    validarConfirmar();
    validarSalvar();
    //Blink alert
    setInterval(() => {
      $('.red-warning').fadeOut(500);
      $('.red-warning').fadeIn(500);
    });

});

function salvarPonto(){
  let postPontos = [];
  for (var i = 0; i < pontos.length; i++) {
	 
        let ponto = pontos[i];
        postPontos.push({
          alunos: ponto.alunos,
          escolas: ponto.escolas,
          tipo: ponto.tipo,
          lat: ponto.getLatLng().lat,
          lng: ponto.getLatLng().lng,
        });
    }
    console.warn('pontos salvarPonto', pontos)
      $.post( "index.php?r=condutor-rota/salvar-rota", 
      {
          logs: logs, 
          pontos: postPontos,
          idCondutorRota: $("#condutor-rota").val()
      })
      .done(function( data ) {
        logs =[]
        console.warn('LOGS LIMPOS')
      });
}
//Acionado no onclick na botão na modal para salvar nosso novo ponto
$("#saveLocation").click(function(){
  //Se está editando um ponto
  let editarPontoEncontro = $("#editarPontoEncontro").val();
  if(editarPontoEncontro){
    console.log('**EDITANDO PONTO **');
    var logTmpRemovido = [];
    let pontoBuscado = pontos.find(ponto => ponto.id == editarPontoEncontro);
    for (var i = 0; i < pontoBuscado.alunos.length; i++){
        alunosSelecionados.remove(pontoBuscado.alunos[i]);
        logTmpRemovido.push(pontoBuscado.alunos[i]);
        //alunosDiposniveisBanco.push(pontoBuscado.alunos[i]);
      }
    console.warn(logTmpRemovido)
    let indexPonto = pontos.indexOf(pontoBuscado);
    let pontoAntigo = pontoBuscado.alunos;
    let alunosInput =  $("#alunos-select").val();
    let logTmpAdicionado = JSON.parse(JSON.stringify(alunosInput))
    console.warn('BEFORE',logTmpAdicionado, logTmpRemovido)



    for (var j= logTmpRemovido.length-1; j>=0; j--) {
      for (var i= alunosInput.length-1; i>=0; i--) {
      if (parseInt(alunosInput[i]) === parseInt(logTmpRemovido[j])) {
          logTmpAdicionado.splice(i, 1);
          logTmpRemovido.splice(j, 1);
          // break;
          }
      }
    }

    console.warn('AFTER',logTmpAdicionado, logTmpRemovido)

    
    logTmpAdicionado.forEach((ponto) => {
      console.warn(ponto)
      addLog(ponto, 'ADICIONADO')
    })
    
    logTmpRemovido.forEach((ponto) => {
      console.error(ponto)
      addLog(ponto, 'REMOVIDO')
    })


    // alunosInput.each((aluno) => )
    pontos[indexPonto].alunos = alunosInput;
    let postPontos = [];
 
    calcularPontos();
    clear();
  } else {
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
        addLog(aluno, 'ADICIONADO')
        addMarker(tipo, [aluno], []);
      break;

      case '4':
        console.warn('Ponto Encontro');  
        let alunos =  $("#alunos-select").val();
        // console.warn(alunos)

        alunos.forEach((aluno) => addLog(aluno, 'ADICIONADO'));
        addMarker(tipo, alunos, []);
      break;
      
      default:
        console.warn('NENHUMA OPÇÃO VÁLIDA EM #saveLocation .click()') 
      break;
    } 
  
  }
        
    salvarPonto();
    clear();
    validarSalvar();
    $('#modal').modal("hide");
});
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


  function validarSalvar(){
    //console.log('VALIDAR SALVAR', Date.now());
    $("#salvar").attr("disabled", true);
    //pontos.length > 0
    if(!validarAlerta() && $("#condutores-select").val() && $("#condutores-select").val().length)
      return $("#salvar").attr("disabled", false);
    
  
  }
  

  function salvar(clicadoBotaoSalvar=false, redirecionar=true){
    $("#salvar").attr("disabled", true);
    let postPontos = [];
    for (var i = 0; i < pontos.length; i++) {
        let ponto = pontos[i];
        postPontos.push({
          alunos: ponto.alunos,
          escolas: ponto.escolas,
          tipo: ponto.tipo,
          lat: ponto.getLatLng().lat,
          lng: ponto.getLatLng().lng,
        });
    }
    let data = {
          logs: logs.concat(logsFixos),
          pontos: postPontos,
          idCondutorRota: $("#condutor-rota").val()
      }
    if(clicadoBotaoSalvar){
      data.idCondutor = $("#condutores-select").val();
    }
    $.post( "index.php?r=condutor-rota/salvar-rota", 
      data).done(function( data ) {
        //console.log(data);
          Swal.fire(
            '',
            'Operação realizada com sucesso',
            'success'
          )
          Swal.fire({
            // title: 'Are you sure?',
            text: "Operação realizada com sucesso",
            type: 'success',
            confirmButtonText: 'OK'
          }).then((result) => {
            if (result.value && redirecionar) {
              window.location.href = "<?= Url::toRoute(["condutor-rota/index"]) ?>"
            }
          })
        // $("#salvar").attr("disabled", false);
      });
  }


</script>