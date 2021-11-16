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
    color:#6991FD;
  }
  .aluno {
    color:#FF9900;
  }
  .alunos {
    color:#00EB4E;
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
            <h3>Próxima atualização em  <span id="atualizacao"></span></h3>
        


          </div>
        <div class="box-body">

    <Br>
  <div class="row no-gutter">
  <div class="col-md-12">
    <div id="location-map">
      <div id="map_canvas" style="height: 700px;">
      </div>
    </div>
  </div>

</div>
<br>

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
var contador = 59;



function addMarker(dados,lat=0, lng=0){
  console.log(dados);
  var contentString = '<div id="content">'+
  '<div id="siteNotice">'+
  '</div>'+
  '<h3 id="firstHeading" class  firstHeading">'+dados.condutor.nome+'</h3>'+
  '<div id="bodyContent"><p>'+
  '<b>Placa: </b>'+ dados.veiculo.placa+
  '<br><b>Última atualização do GPS: </b>'+ converterData(dados.veiculo.ultimaAtualizacaoGPS)+
  '</p></div>'+
  '</div>';


 

  let position =  L.latLng(lat,lng);
    var myIcon = L.icon({
      iconUrl:   'img/icon_van.png',
      // iconSize:     [38, 95], // size of the icon
      // shadowSize:   [50, 64], // size of the shadow
      // iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
      // shadowAnchor: [4, 62],  // the same for the shadow
      // popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
    });

    let marker = L.marker(position, {icon:myIcon, draggable: false}).bindPopup(contentString).addTo(map);;


  // InfoWindow
  // marker3.addListener('click', function() {
  //   infowindow.open(map, marker3);
  // });

  pontos.push(marker);
  //Listen for drag events!
  // google.maps.event.addListener(marker3, 'dragend', function(event){
   
 let indexPonto = pontos.indexOf(marker);
    atualizarPonto(indexPonto, marker);
  // });

 // map.setCenter(marker3.getPosition());  


}  
function atualizarPonto(index,pontoAtualizado){
  pontos[index] = pontoAtualizado;  
  //console.warn('AFTER', pontos[index], pontos[index].getPosition().lng());

}




$(document).ready(function() {

  var myLatlng; 
  iniciarPontos();
  setInterval(() => {
    iniciarPontos();
  }, 1000 * 61);
  function initializeGMap(lat, lng) {
    var titleLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_labels_under/{z}/{x}/{y}{r}.png', {
        attributions: 'www.ipplan.org.br',
          'zoom' : 14,
          // 'minZoom': 14,
          // 'maxZoom': 18
        
      });

    map = L.map("map_canvas", {
        'center': [-23.223701,-45.9009074],
        'zoom' : 12,
        'layers': [titleLayer]

      }); 
  
      map.setView(new L.latLng(-23.223701,-45.9009074));
      map.invalidateSize();
  }

  

  function load(){ 
    initializeGMap(-23.223701, -45.9009074);
    $("#location-map").css("width", "100%");
    $("#map_canvas").css("width", "100%");
    // google.maps.event.trigger(map, "resize");
    // map.setCenter(myLatlng);
  }

  function iniciarPontos(){
    $.get( "index.php?r=condutor/ao-vivo-ajax" )
      .done(function( data ) {
        // contador = 61;
        for (var i = pontos.length - 1; i >= 0; i--) {

            map.removeLayer(pontos[i]);

        }
        pontos=[];
        
        for (var j = 0; j < data.length; j++) {
          let ponto = data[j];
          if(ponto.veiculo.lat)
            addMarker(ponto, ponto.veiculo.lat, ponto.veiculo.lng);

        }
    });
  }

  load();

 



});
  



function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        //display.textContent = minutes + ":" + seconds;
          display.textContent = seconds + ' segundo(s)';

        if (--timer < 0) {
            timer = duration;
        }
    }, 1000);
}
   function converterData(date){
      var dNow = new Date(date);
 
      return dNow.toLocaleDateString('pt-Br') + ' ' +dNow.toLocaleTimeString('pt-Br') ;
    }
window.onload = function () {
    display = document.querySelector('#atualizacao');
    startTimer(contador, display);
};

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

$(document).ready(function() {
    setInterval(() => {
    map.invalidateSize();
  }, 200);
});



</script>

