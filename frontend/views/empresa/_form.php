<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Condutor;
use common\models\TipoLogradouro;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\Empresa */
/* @var $form yii\widgets\ActiveForm */

//<button class="btn btn-default pickLocation" type="button" ><i class="fa fa-map" aria-hidden="true"></i></button>
?>
<style>
.input-group {
  width: 100%;
}

.field-empresa-endereco  li {
  padding: 3px 20px;
  margin: 0;
}

.field-empresa-endereco  li:hover{
  background: #7FDFFF;
  border-color: #7FDFFF;
}

.geocoder-control-selected{
  background: #7FDFFF;
  border-color: #7FDFFF;
}

.field-empresa-endereco  ul li {
  list-style-type: none;
}

</style>
<div class="box-body">

    <?php $form = ActiveForm::begin([
        // 'id' => 'formVeiculo',
        'options' => ['enctype'=>'multipart/form-data'],
        'encodeErrorSummary' => false,
        'errorSummaryCssClass' => 'help-block',
    ]); ?>
    <div class="row">
        <div class="col-md-4">
            <?php
                 echo $form->field($model, 'cnpj')->textInput(
                [ 
                    'onBlur'=>'ValidarCNPJ(this);',
                    'onKeyPress'=>'MascaraCNPJ(this);',
                    'maxlength'=>'18'
                ])
             ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'razaoSocial')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
           <?= $form->field($model, 'nomeFantasia')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
          <?=  $form->field($model, 'cep')->textInput(['id' => 'cep', 'maxlength' => 9, 'autocomplete' => 'off']);?>
        </div>
        <div class="col-md-2">
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
        
        <div class="col-md-3">
            <?php
              echo $form->field($model, 'telefone')->textInput(
                [
                    'onBlur'=>'MascaraTelefone(this);',
                    'onKeyPress'=>'MascaraTelefone(this);',
                    'maxlength'=>'15'
                ])
             ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
    </div>




    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

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
$(".field-empresa-endereco").append('<p class="loading"></p>');
$("#cep").change(function() {
  esconderTabela();
  let cep = $("#cep").val();
  if(!cep)
    return null;
  let logradouro = $("#empresa-endereco").val(); 
  let tipo = $("#tipo-logradouro").val();
  $(".field-cep .loading").html('<i class="fas fa-hourglass-half"></i> Buscando informações...');
  $.getJSON( "index.php?r=pesquisa-logradouro/pesquisa-logradouro", {"logradouro": logradouro, "tipo": tipo, "cep": cep})
  .done(function(data) {
    $(".field-cep .loading").html('');

    $("#tabelaEndereco").css("display", "none");
    if(data.status) {
      mostrarTabela(data.enderecos);
      //$('#empresa-endereco').val(data.endereco.TIPO_LOGRADOURO+' '+data.endereco.LOGRADOURO+', '+data.endereco.BAIRRO);
    } else {
      Swal.fire(
            'CEP não encontrado',
            'Confira os números do CEP',
            'warning'
          )
      $("#cep").focus();
      $("#cep").val("");
      $("#empresa-endereco").val("");
      mostrarMapa(); 
    }
      
   });
});
mostrarMapa();
function ocultarMapa(){
  $("#mapUser").css("display", "none");
}
function mostrarMapa(){
  let logradouro = $("#empresa-endereco").val();
  let bairro= $("#empresa-bairro").val();
  let num = $("#empresa-numeroresidencia").val();
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

$('#empresa-numeroresidencia').change(function(){
  mostrarMapa();
});
$("#empresa-endereco").change(function() {
  esconderTabela();
  mostrarMapa();
  flag = false;
  let logradouro = $("#empresa-endereco").val();
  let tipo = $("#tipo-logradouro").val();
  let cep = $("#cep").val();
    $(".field-empresa-endereco .loading").html('<i class="fas fa-hourglass-half"></i> Buscando informações...');
  $.getJSON( "index.php?r=pesquisa-logradouro/pesquisa-logradouro", {"logradouro": logradouro, "tipo": tipo, "cep": cep})
  .done(function(data) {
    $(".field-empresa-endereco .loading").html('');

    $("#tabelaEndereco").css("display", "none");
    if(data.status) {
      mostrarTabela(data.enderecos);
    } else {
      Swal.fire(
            'Logradouro não encontrado',
            'Digite o CEP ou o nome de um logradouro válido',
            'warning'
          )
      $("#empresa-endereco").focus();
      $("#cep").val("");
      $("#empresa-endereco").val("");
      //mostrarMapa();
    }
      
   });
});

function tipoLogradouro(flag=0){
  
  // console.log("tipoLogradouro()");
  // esconderTabela();
  // let logradouro = $("#empresa-endereco").val();
  // let tipo = $("#tipo-logradouro").val();
  
  // if(logradouro && tipo){
  //   console.log('Logradouro changed');
  //   $('#empresa-endereco').trigger('change');
  // } 
  
  // if(cep && tipo) {
  //   console.log('CEP CHANGED0');
  //   $('#cep').trigger('change');
  // }
}
// $("#empresa-endereco").change(() => {
//   let endereco = $('#empresa-endereco').val();
//   $.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest?text='+endereco+'&maxSuggestions=5&f=json').done((x) => console.log(x));
// })
function esconderTabela(){
  $("#tabelaEndereco").css("display", "none");
}
function mostrarTabela(data){
  let num = $("#empresa-numeroresidencia").val();
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
  $("#empresa-endereco").val(logradouro);
  $("#empresa-bairro").val(bairro);
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
$("#empresa-endereco").attr("autocomplete", "off");

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
    $("#empresa-lat").val(lat);
    $("#empresa-lng").val(lng);
    // if(endereco)
    //   $("#empresa-endereco").val(endereco);
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
        $("#empresa-lat").val(chagedPos.lat);
        $("#empresa-lng").val(chagedPos.lng);
      });
    }
}

$(document).ready(function() {
  if(latAtual && lngAtual && latAtual != 1 && lngAtual != 1)
    addMarker(latAtual, lngAtual, enderecoAtual);
  $( "#empresa-endereco" ).focusout(function() {
    //enableSearch();
  
  });
});


marker.on("dragend",function(e){
  var chagedPos = e.target.getLatLng();
  this.bindPopup(chagedPos.toString()).openPopup();
});
</script>