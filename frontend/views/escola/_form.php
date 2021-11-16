<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Escola;
use yii\helpers\ArrayHelper;
use common\models\Usuario;
use common\models\TipoLogradouro;

use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Escola */
/* @var $form yii\widgets\ActiveForm */

function listarSecretarios(){
    $usuarios = Usuario::find()->all();
    $lista = [];
    foreach ($usuarios as $usuario) {
        if($usuario->idPerfil == Usuario::PERFIL_SECRETARIO){
            $lista[] = $usuario;
        }
    }
    return $lista;
}
function listarDiretores(){
    $usuarios = Usuario::find()->all();
    $lista = [];
    foreach ($usuarios as $usuario) {
        if($usuario->idPerfil == Usuario::PERFIL_DIRETOR){
            $lista[] = $usuario;
        }
    }
    return $lista;
}
?>
<style>
.input-group {
  width: 100%;
}

.field-escola-endereco  li {
  padding: 3px 20px;
  margin: 0;
}

.field-escola-endereco  li:hover{
  background: #7FDFFF;
  border-color: #7FDFFF;
}

.geocoder-control-selected{
  background: #7FDFFF;
  border-color: #7FDFFF;
}

.field-escola-endereco  ul li {
  list-style-type: none;
}

</style>
<div class="box-body">

    <?php $form = ActiveForm::begin([
        'encodeErrorSummary' => false,
        'errorSummaryCssClass' => 'help-block',
        ]); ?>
 
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
            <label class="control-label" for="idServico">Unidade</label>
            <?php 
            echo Select2::widget([
                        'model' => $model,
                        'attribute' => 'unidade',
                        'data' =>  Escola::ARRAY_UNIDADE,
                        'options' => [
                            'id' => 'id-unidade',
                            'placeholder' => 'Selecione a unidade',
                            'multiple' => false,
                        ],
                        'pluginEvents' => [
                            "change" => 'function() { 
                                 
                                  $("#id-tipo").select2("val", "");
                                  $("#id-tipo").html("");  

                                  $.get( "index.php?r=escola/tipo", { tipoEscola: $(this).val() } )
                                  .done(function( data ) {
                                    console.log(data);
                                    data.forEach((item, i) => {
                                        console.log(item);
                                          $("#id-tipo").append($("<option/>", {
                                            value: item.value,
                                            text: item.text
                                        }));
                                    });
                                  });
                               
                            }',
                        ],
                    ]);

                ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="idServico">Tipo</label>
                <?php 
                echo Select2::widget([
                            'model' => $model,
                            'attribute' => 'tipo',
                            'data' =>  Escola::mountSelectTipo($model->unidade),
                            'options' => [
                                'id' => 'id-tipo',
                                'placeholder' => 'Selecione o tipo',
                                'multiple' => false,
                            ],
                            'pluginEvents' => [
                                "change" => 'function() { 

                                }',
                            ],
                        ]); 
                ?>
            </div>
        </div>
        <div class="col-md-6">     
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
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
            <div class="form-group">
                <label class="control-label" for="idServico">Ensino</label>
                <?php
                    $lista = [];
                      foreach ($model->atendimento as $es) {
                          array_push($lista,$es->idAtendimento);
                      }
                      $model->inputEnsino = $lista;
                 ?>
                <?php 
                echo Select2::widget([
                            'model' => $model,
                            'attribute' => 'inputEnsino',
                            'data' =>  Escola::ARRAY_ENSINO,
                            'options' => [
                                'id' => 'id-ensino',
                                'placeholder' => 'Selecione os tipos de ensino',
                                'multiple' => true,
                            ],
                            'pluginEvents' => [
                                "change" => 'function() { 
                                    console.log($(this).val());
                                }',
                            ],
                        ]); 
                ?>
            </div>
        </div>
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
        <div class="col-md-3">
            <?= $form->field($model, 'codigoCie')->textInput(['maxlength' => true, 'type' => 'number']) ?>
        </div>    
    </div>
 
    <div class="row">
    <?php if(!Usuario::permissao(Usuario::PERFIL_SECRETARIO) && !Usuario::permissao(Usuario::PERFIL_DIRETOR)) { ?>
        <div class="col-md-6">
        <?php
              $lista = [];
              foreach ($model->secretarios as $es) {
                  array_push($lista,$es->idUsuario);
              }
              $model->inputSecretarios = $lista;
         ?>
        <?=
             $form->field($model, 'inputSecretarios')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(listarSecretarios(), 'id', 'nome'),
                    'language' => 'pt',
                    'options' => ['placeholder' => 'Selecione todos os secretários', 'class' => 'form-control', 'id' => 'secretariosGrupo'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                        'initialize' => true,
                    ],
                ]);
        ?>
        </div>
        <?php } ?>
        <?php if(!Usuario::permissao(Usuario::PERFIL_SECRETARIO) && !Usuario::permissao(Usuario::PERFIL_DIRETOR)) { ?>
        <div class="col-md-6">
           <?php
       
              foreach ($model->diretores as $es)
                  $model->inputDiretores[] = $es->idUsuario;
         
         ?>
        <?=
             $form->field($model, 'inputDiretores')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(listarDiretores(), 'id', 'nome'),
                    'value' =>  $model->diretores,
                    'language' => 'pt',
                    'options' => [
                            'placeholder' => 'Selecione todos os diretores',
                            'class' => 'form-control',
                            'id' => 'diretoresGrupo'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'multiple' => true,
                        'initialize' => true,
                    ],
                ]);
        ?>
        </div>
        <?php } ?>
    </div>
    <div class="row">
       <div class="col-md-4">
            <label class="control-label" for="idServico">Região</label>
            <?php echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'regiao',
                    'data' =>  Escola::ARRAY_REGIAO,
                    'options' => [
                        'id' => 'id-regiao',
                        'placeholder' => 'Selecione a região',
                        'multiple' => false,
                    ]
                ]); 
        ?>
       </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<div id= "mapInput" style="style:none;"></div>

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
$(".field-escola-endereco").append('<p class="loading"></p>');
$("#cep").change(function() {
  esconderTabela();
  let cep = $("#cep").val();
  if(!cep)
    return null;
  let logradouro = $("#escola-endereco").val(); 
  let tipo = $("#tipo-logradouro").val();
  $(".field-cep .loading").html('<i class="fas fa-hourglass-half"></i> Buscando informações...');
  $.getJSON( "index.php?r=pesquisa-logradouro/pesquisa-logradouro", {"logradouro": logradouro, "tipo": tipo, "cep": cep})
  .done(function(data) {
    $(".field-cep .loading").html('');

    $("#tabelaEndereco").css("display", "none");
    if(data.status) {
      mostrarTabela(data.enderecos);
      //$('#escola-endereco').val(data.endereco.TIPO_LOGRADOURO+' '+data.endereco.LOGRADOURO+', '+data.endereco.BAIRRO);
    } else {
      Swal.fire(
            'CEP não encontrado',
            'Confira os números do CEP',
            'warning'
          )
      $("#cep").focus();
      $("#cep").val("");
      $("#escola-endereco").val("");
      mostrarMapa(); 
    }
      
   });
});
mostrarMapa();
function ocultarMapa(){
  $("#mapUser").css("display", "none");
}
function mostrarMapa(){
  let logradouro = $("#escola-endereco").val();
  let bairro= $("#escola-bairro").val();
  let num = $("#escola-numeroresidencia").val();
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

$('#escola-numeroresidencia').change(function(){
  mostrarMapa();
});
$("#escola-endereco").change(function() {
  esconderTabela();
  mostrarMapa();
  flag = false;
  let logradouro = $("#escola-endereco").val();
  let tipo = $("#tipo-logradouro").val();
  let cep = $("#cep").val();
    $(".field-escola-endereco .loading").html('<i class="fas fa-hourglass-half"></i> Buscando informações...');
  $.getJSON( "index.php?r=pesquisa-logradouro/pesquisa-logradouro", {"logradouro": logradouro, "tipo": tipo, "cep": cep})
  .done(function(data) {
    $(".field-escola-endereco .loading").html('');

    $("#tabelaEndereco").css("display", "none");
    if(data.status) {
      mostrarTabela(data.enderecos);
    } else {
      Swal.fire(
            'Logradouro não encontrado',
            'Digite o CEP ou o nome de um logradouro válido',
            'warning'
          )
      $("#escola-endereco").focus();
      $("#cep").val("");
      $("#escola-endereco").val("");
      //mostrarMapa();
    }
      
   });
});

function tipoLogradouro(flag=0){
  
  // console.log("tipoLogradouro()");
  // esconderTabela();
  // let logradouro = $("#escola-endereco").val();
  // let tipo = $("#tipo-logradouro").val();
  
  // if(logradouro && tipo){
  //   console.log('Logradouro changed');
  //   $('#escola-endereco').trigger('change');
  // } 
  
  // if(cep && tipo) {
  //   console.log('CEP CHANGED0');
  //   $('#cep').trigger('change');
  // }
}
// $("#escola-endereco").change(() => {
//   let endereco = $('#escola-endereco').val();
//   $.getJSON('https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/suggest?text='+endereco+'&maxSuggestions=5&f=json').done((x) => console.log(x));
// })
function esconderTabela(){
  $("#tabelaEndereco").css("display", "none");
}
function mostrarTabela(data){
  let num = $("#escola-numeroresidencia").val();
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
  $("#escola-endereco").val(logradouro);
  $("#escola-bairro").val(bairro);
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
$("#escola-endereco").attr("autocomplete", "off");

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
    $("#escola-lat").val(lat);
    $("#escola-lng").val(lng);
    // if(endereco)
    //   $("#escola-endereco").val(endereco);
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
        $("#escola-lat").val(chagedPos.lat);
        $("#escola-lng").val(chagedPos.lng);
      });
    }
}

$(document).ready(function() {
  if(latAtual && lngAtual && latAtual != 1 && lngAtual != 1)
    addMarker(latAtual, lngAtual, enderecoAtual);
  $( "#escola-endereco" ).focusout(function() {
    //enableSearch();
  
  });
});


marker.on("dragend",function(e){
  var chagedPos = e.target.getLatLng();
  this.bindPopup(chagedPos.toString()).openPopup();
});
</script>