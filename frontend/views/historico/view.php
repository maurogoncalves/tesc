<?php 
use common\models\Escola;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use common\models\Aluno;
use common\models\Ponto;
use common\models\Condutor;
use common\models\CondutorRota;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use common\models\Comunicado;
use common\models\Justificativa;
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
</style>
            


        <div class="box box-solid">
            <div class="box-header with-border">
            <h3>Data: <?= Yii::$app->formatter->asDate($model->data, 'dd/MM/Y') ?> <?= $model->checkIn; ?> - <?= $model->checkOut; ?> </h3>
            <h4>Condutor: <?= $model->condutor->nome; ?></h4>
            <h4>Veículo: <?= $model->veiculo->modelo->marca->nome; ?>  <?= $model->veiculo->modelo->nome; ?> <?= $model->veiculo->placa; ?></h4>


          </div>
        <div class="box-body">


      <input type="hidden" name="listaEscolas" id="listaEscolas" value='<?= json_encode(ArrayHelper::map(Escola::find()->all(), 'id', 'nome'))?>' />

      <input type="hidden" name="listaAlunos" id="listaAlunos" value='<?=  json_encode(ArrayHelper::map(Aluno::find()->all(), 'id', 'nome'))?>' />

      <input type="hidden" name="condutor-rota" id="condutor-rota" value="<?= $model->condutorRota->id ?>" />
      <input type="hidden" name="idHistorico" id="idHistorico" value="<?= $model->id ?>" />

    <Br>
  <div class="row no-gutter">
  <div class="col-md-8">
    <div id="location-map">
      <div id="map_canvas">
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
<br>
 <div class="row ">
   <div class="col-md-6">
    <h3>Ocorrências</h3>
        <?= GridView::widget([
                                'dataProvider' => new ArrayDataProvider([
                                    'allModels' => $model->ocorrencias,
                                    'key' => 'id',
                                    'pagination' => [
                                        'pageSize' => 20,
                                    ],
                                ]),
                                'pjax' => true,
                                'pjaxSettings' =>[
                                    'neverTimeout'=>true,
                                    'options'=>[
                                            'id'=>'gridSolicitacoes',
                                        ]
                                    ],
                                'options' => [
                                    'class' => 'table-header-ajax',
                                 ],
                                'striped' => false,
                                'bootstrap' => true,
                                // 'summary' => "Mostrando de {begin} a {end} de {totalCount}",
                                'emptyText' => '<h4class="vazio">Nenhuma ocorrência</h4>',
                                'columns' => [

                         [
                            'label' => 'Data',
                            'attribute' => 'data',
                            'value' => function($model) {
                                 return ($model->data)?Yii::$app->formatter->asDateTime($model->data, 'dd/MM/Y HH:i:s'):'-';
                            }
                        ],

                        // 'idCondutor',

                        // [
                        //     'label' => 'Rota',
                        //     'attribute' => 'idCondutorRota',
                        //     'value' => function($model) {
                        //         return $model->condutorRota->nomeRota;
                        //     }
                        // ],
                        [
                            'attribute' => 'idJustificativa',
                            'label' => 'Justificativa',
                            'value' => function($model) {
                                return $model->justificativa->nome;
                            }
                        ],  
                        [
                            'label' => 'Descrição',
                            'attribute' => 'descricao',
                            
                        ],
                      
                                ],
                            ]); ?>


   </div>
   <div class="col-md-6">
    <h3>Comunicados</h3>
        <?= GridView::widget([
                                'dataProvider' => new ArrayDataProvider([
                                    'allModels' => $model->comunicados,
                                    'key' => 'id',
                                    'pagination' => [
                                        'pageSize' => 20,
                                    ],
                                ]),
                                'pjax' => true,
                                'pjaxSettings' =>[
                                    'neverTimeout'=>true,
                                    'options'=>[
                                            'id'=>'x',
                                        ]
                                    ],
                                'options' => [ 
                                    'class' => 'table-header-ajax',
                                 ],
                                'striped' => false,
                                'bootstrap' => true,
                                // 'summary' => "Mostrando de {begin} a {end} de {totalCount}",
                                'emptyText' => '<h4class="vazio">Nenhuma comunicado</h4>',
                                'columns' => [
                                  [
                                      'attribute' => 'data',
                                      'value' => function($model) {
                                             return ($model->data)?Yii::$app->formatter->asDateTime($model->data, 'dd/MM/Y HH:i:s'):'-';
                                      }
                                  ],
                                  [
                                    'attribute' => 'enviadoPor',
                                    'value' => function($model){
                                        return $model->enviadoPor ? Comunicado::ARRAY_ENVIADO[$model->enviadoPor] : '-';
        
                                    },
                                    'filterType' => GridView::FILTER_SELECT2,
                                    'filter' =>  Comunicado::ARRAY_ENVIADO, 
                                    'filterWidgetOptions' => [
                                        'pluginOptions' => ['allowClear' => true], 
                                    ],
                                    'filterInputOptions' => [
                                        'placeholder' => '-',
                                        
                                    ]
                                ],
                                 [
                                  'attribute' => 'tipo',
                                  'label' => 'Sentido',
                                  'value' => function($model){
                                        return $model->tipo ? Comunicado::ARRAY_TIPO[$model->tipo] : '-';

                                    },
                                 ],
                                  [
                                  'attribute' => 'idAluno',
                                  'label' => 'Aluno',
                                  'value' => 'aluno.nome'
                                 ],
                                 [
                                  'attribute' => 'idJustificativa',
                                  'label' => 'Justificativa',
                                  'value' => 'justificativa.nome'
                                 ],
                                ],
                            ]); ?>


   </div>
 </div>
</div>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script type="text/javascript">
var geocoder; 
var address;
var currentLocation;
var map = null;
var pontos = [];
var totalAlunos = 0;
var listaEscolas;
var listaAlunos;
var escolasSelecionadas = [];
var alunosSelecionados = [];
var pontosFinais = [];
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
          
            data.forEach((item, i) => {
                $("#escolas-select").append("<option value="+item.id+">"+item.nome+"</option>");
            });
      });
                                   

    break;
    
    case '3':
      //console.warn('Ponto Aluno');
      switchDiv(['aluno'],['escolas','alunos']);  
      $.post( "index.php?r=condutor-rota/search-alunos", { alunos: alunosSelecionados })
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
      $.post( "index.php?r=condutor-rota/search-alunos", { alunos: alunosSelecionados })
      .done(function( data ) {
          
            data.forEach((item, i) => {
                $("#alunos-select").append("<option value="+item.id+">"+item.nome+"</option>");
            });
      });
    break;
    
    default:
      console.warn('NENHUMA OPÇÃO VÁLIDA EM alterarPonto()') 
    break;
  }
}

function validarAlerta(){
  console.warn('Validando Alerta');
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

function addMarker(tipo, alunos, escolas, lat=0, lng=0, pontoRemoto={}){

  // var contentString = '<div id="content">'+
  // '<div id="siteNotice">'+
  // '</div>'+
  // '<h1 id="firstHeading" class="firstHeading">'+Date.now()+'</h1>'+
  // '<div id="bodyContent">'+
  // '<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large ' +
  // 'sandstone rock formation in the southern part of the '+
  // 'Northern Territory, central Australia. It lies 335&#160;km (208&#160;mi) '+
  // 'south west of the nearest large town, Alice Springs; 450&#160;km '+
  // '(280&#160;mi) by road. Kata Tjuta and Uluru are the two major '+
  // 'features of the Uluru - Kata Tjuta National Park. Uluru is '+
  // 'sacred to the Pitjantjatjara and Yankunytjatjara, the '+
  // 'Aboriginal people of the area. It has many springs, waterholes, '+
  // 'rock caves and ancient paintings. Uluru is listed as a World '+
  // 'Heritage Site.</p>'+
  // '<p>Attribution: Uluru, <a href="https://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">'+
  // 'https://en.wikipedia.org/w/index.php?title=Uluru</a> '+
  // '(last visited June 22, 2009).</p>'+
  // '</div>'+
  // '</div>';

  // var infowindow = new google.maps.InfoWindow({
  //   content: contentString
  // });


  let icon = '';
  switch(tipo) {
      case '2': 
        icon = 'escola';
        //console.warn('Ponto Escola');
      break;

      case '3':
        icon = 'residencia';
        //console.warn('Ponto Aluno');
      break;

      case '4':
        icon = 'ponto';
        //console.warn('Ponto Encontro');  
      break;
      
      default:
        icon = 'van';
        console.warn('NENHUMA OPÇÃO VÁLIDA EM addMarker .click()') 
      break;
  }
  if(!lat){
    lat = -23.223701;
    lng = -45.9009074;
  }
  let marker3 = new google.maps.Marker({
      position: new google.maps.LatLng(lat, lng),
      map: map,
      //draggable: true,
      title: (pontos.length + 1).toString(),
      icon: 'img/icon_'+icon+'.png',
      id: Date.now(),
      order: pontos.length + 1,
      tipo: tipo,
      alunos: alunos,
      escolas: escolas,
      pontoRemoto: pontoRemoto,
      label: {
        text: (pontos.length + 1).toString(),
        color: '#FFFFFF',
      }
  });

  // InfoWindow
  // marker3.addListener('click', function() {
  //   infowindow.open(map, marker3);
  // });

  pontos.push(marker3);
  //Listen for drag events!
  // google.maps.event.addListener(marker3, 'dragend', function(event){
   
 let indexPonto = pontos.indexOf(marker3);
    atualizarPonto(indexPonto, marker3);
  // });

  map.setCenter(marker3.getPosition());  
  calcularPontos();

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
    str +=`<li>`+ponto.escolas[0].nome+`</li>`; 
    str +=`<li>CheckOut: `+ponto.pontoRemoto.checkOut+`</li>`; 
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
    str +=`<li>`+ponto.alunos[0]+`</li>`;
    str +=`<li>CheckIn: `+ponto.pontoRemoto.checkIn+` CheckOut: `+ponto.pontoRemoto.checkOut+`</li>`; 

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
                   </span>
                  <i class="far fa-eye pull-right visualizar-ponto" onclick="visualizarPonto(`+pontos[i].id+`)"></i>

                 `;
      $("#pontosContent").append(mountEscolas(pontos[i], icon));

      break;
      
      case '3':
        //console.warn('Ponto Aluno');
        icon =  `<img src="img/icon_residencia.png" style="margin-bottom: 2px;height:15px"> <span class="aluno">`+pontos[i].order+`º
                       Residência de aluno(a)
                      
                  
                      </span>
                      <i class="far fa-eye pull-right visualizar-ponto" onclick="visualizarPonto(`+pontos[i].id+`)"></i>
                      `;

        $("#pontosContent").append(mountAlunos(pontos[i], icon));

      break;
      
      case '4':


        icon =  `<img src="img/icon_ponto.png" style="margin-bottom: 2px;height:15px"> <span class="alunos">`+pontos[i].order+`º
                      Ponto de encontro
                    
              
                     <i class="far fa-eye pull-right visualizar-ponto" onclick="visualizarPonto(`+pontos[i].id+`)"></i>

                    `;

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
     console.warn("CENTRALIZAR O MAPA EM", id);
     let pontoBuscado = pontos.find(ponto => ponto.id == id);
       map.setCenter(pontoBuscado.getPosition());  
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
           pontoBuscado.setLabel({
            text: (i + 1).toString(),
            color: '#FFFFFF'
           });
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

$(document).ready(function() {

  var myLatlng; 
  listaEscolas = JSON.parse($("#listaEscolas").val());
  listaAlunos = JSON.parse($("#listaAlunos").val());
  iniciarPontos();
  function initializeGMap(lat, lng) {
    myLatlng = new google.maps.LatLng(lat,lng);
    geocoder = new google.maps.Geocoder;
    var myStyles = [
        {
            featureType: "poi",
            elementType: "labels",
            stylers: [
                  { visibility: "off" }
            ]
        }
    ];
 
    var myOptions = {
      zoom: 12,
      zoomControl: true,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      styles: myStyles 
    };

    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    google.maps.event.addListener(map, 'click', function(event) { 
        //console.warn('clicado');               
        var clickedLocation = event.latLng;
    });
  }

  function load(){ 
    initializeGMap(-23.223701, -45.9009074);
    $("#location-map").css("width", "100%");
    $("#map_canvas").css("width", "100%");
    google.maps.event.trigger(map, "resize");
    map.setCenter(myLatlng);
  }

  function iniciarPontos(){
    $.get( "index.php?r=historico/view-ajax", { id: $('#idHistorico').val() } )
      .done(function( data ) {
        pontosFinais = [];


        for (var j = 0; j < data.historicoAlunos.length; j++) {
          let ponto = data.historicoAlunos[j];
          pontosFinais.push({
              tipo: '3',
              aluno: ponto.alunoNome,
              escola: {},
              lat: ponto.lat,
              lng: ponto.lng,
              checkOut: ponto.checkOut,
              checkIn: ponto.checkIn

            });

        }

        for (var j = 0; j < data.historicoEscolas.length; j++) {
          let ponto = data.historicoEscolas[j];

          pontosFinais.push({
              tipo: '2',
              aluno: {},
              escola: ponto.escola,
              lat: ponto.lat,
              lng: ponto.lng,
              checkOut: ponto.checkOut
            });

        }

        for (var j = 0; j < data.historicoVeiculo.length; j++) {
          let ponto = data.historicoVeiculo[j];          
           pontosFinais.push({
              tipo: '999999',
              aluno: {},
              escola: {},
              lat: ponto.lat,
              lng: ponto.lng})

        }

        for ( i = 0; i < pontosFinais.length; i++) {
        pontosFinais[i].distance = calculateDistance(pontosFinais[0].lat,pontosFinais[0].lng,pontosFinais[i].lat,pontosFinais[i].lng,"K");
      }
      pontosFinais.sort(function(a, b) { 
        return a.distance - b.distance;
      });

      setTimeout(() => {
        for (var i = 0; i < pontosFinais.length; i++) {
          let ponto = pontosFinais[i];
          addMarker(ponto.tipo,[ponto.aluno],[ponto.escola],ponto.lat,ponto.lng, ponto);
        }
      }, 100);
    });
  }

  load();






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
  


function calculateDistance(lat1, lon1, lat2, lon2, unit) {
  var radlat1 = Math.PI * lat1/180
  var radlat2 = Math.PI * lat2/180
  var radlon1 = Math.PI * lon1/180
  var radlon2 = Math.PI * lon2/180
  var theta = lon1-lon2
  var radtheta = Math.PI * theta/180
  var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
  dist = Math.acos(dist)
  dist = dist * 180/Math.PI
  dist = dist * 60 * 1.1515
  if (unit=="K") { dist = dist * 1.609344 }
  if (unit=="N") { dist = dist * 0.8684 }
  return dist;
}



function markerLocation(){
  return new Promise((res, rej) => {
      //Get location.
      currentLocation = marker.getPosition();
      geocoder.geocode({'location': currentLocation}, function(results, status) {
            if (status === 'OK') {
              if (results[0]) {
                console.warn(address);
                address = results[0].formatted_address;
                res(1);
              } else {
                window.alert('No results found');
                res(1);
              }
            } else {
              window.alert('Geocoder failed due to: ' + status);
              res(1);
            }
      }); 
  });
}
//Acionado no onclick na botão na modal para salvar nosso novo ponto
$("#saveLocation").click(function(){
    let tipo = $("#tipo-select").val();
    switch(tipo) {
      case '2': 
        //console.warn('Ponto Escola');
        let escolas = $("#escolas-select").val();
        addMarker(tipo,[], escolas);
      break;

      case '3':
        //console.warn('Ponto Aluno');
        let aluno =  $("#aluno-select").val();
        addMarker(tipo, [aluno], []);
      break;

      case '4':
        //console.warn('Ponto Encontro');  
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
function clear(){
  console.warn('LIMPAR INPUTS');
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
    console.log('VALIDAR SALVAR', Date.now());
    $("#salvar").attr("disabled", true);

    if(!validarAlerta() && pontos.length > 0 && $("#condutores-select").val() && $("#condutores-select").val().length)
      return $("#salvar").attr("disabled", false);
    
  
  }
  


</script>

