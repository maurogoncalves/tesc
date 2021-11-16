<?php
use yii\helpers\Json;
/* @var $this yii\web\View */

$this->title = '';

?>
<div class="row sub-header">
  <div class="col-md-2">
    <select class="form-control" onchange="filtraMarcadores(this.value);">
      <option value="">Todos</option>
      <option value="chamado">Chamados</option>
      <option value="local">Locais</option>
      <option value="motorista">Motoristas</option>
    </select>
  </div>
</div>
<div id="map"></div>

<script type="text/javascript">
	<?= "var arrayMotoristas = ". JSON::encode($motoristas) . ";\n"; ?>
	<?= "var arrayLocais = ". JSON::encode($locais) . ";\n"; ?>
  <?= "var arrayChamados = ". JSON::encode($chamados) . ";\n"; ?>
	var map = null;
  var arrayMarkers = [];

	function initMap(latitude, longitude) {
		map = new google.maps.Map(document.getElementById('map'), {
			zoom: 11,
			center: {lat: -23.550375,lng: -46.6361576}
		});
	}

  function filtraMarcadores (getType) {
    console.log(getType);
    console.log("-------")
    for (var i = 0; i < arrayMarkers.length; i++) {
      console.log(arrayMarkers[i].type)
      if (arrayMarkers[i].type == getType || getType == "") {
        arrayMarkers[i].setVisible(true);
      } else {
        arrayMarkers[i].setVisible(false);
      }
    }
  }

	window.onload = function() {

    console.log(arrayMotoristas);
    console.log(arrayLocais);
    initMap();

    var iconUrl = 'http://localhost/dsc/frontend/web/img/';
    var icons = {
        chamado: {
          icon: iconUrl + "marker-chamado.png"
        },
        local: {
          icon: iconUrl + "marker-local.png"
        },
        motorista: {
          icon: iconUrl + "marker-motorista.png"
        }
      };

    arrayMotoristas.forEach(function(item) {
      var marker = new google.maps.Marker({
        position: {lat: parseFloat(item.latAtual), lng: parseFloat(item.lngAtual)},
        icon: {
        	url: icons['motorista'].icon,
        	scaledSize: new google.maps.Size(50, 50),
        },
        type: 'motorista',
        map: map,
      });

      var contentString = '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h1 id="firstHeading" class="firstHeading">Motorista</h1>'+
        '<div id="bodyContent">'+
        '<div class="user-panel"><div class="pull-left image">'+
        '<img src="'+item.foto+'" class="img-circle" alt="User Image"></div>'+
        '<div class="pull-left info"><p>'+item.endereco+'</p></div></div>'+
        '</div>'+
        '</div>';

      var infowindow = new google.maps.InfoWindow({
        content: contentString
      });

      marker.addListener('click', function() {
        console.log("click");
        infowindow.open(map, marker);
      });

      arrayMarkers.push(marker);
    });

    arrayChamados.forEach(function(item) {
      var marker = new google.maps.Marker({
        position: {lat: parseFloat(item.latAtual), lng: parseFloat(item.lngAtual)},
        icon: {
          url: icons['chamado'].icon,
          scaledSize: new google.maps.Size(50, 50),
        },
        type: 'chamado',
        map: map,
      });

      var contentString = '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h1 id="firstHeading" class="firstHeading">Motorista</h1>'+
        '<div id="bodyContent">'+
        '<div class="user-panel"><div class="pull-left image">'+
        '<img src="'+item.cliente.nome+'" class="img-circle" alt="User Image"></div>'+
        '<div class="pull-left info"><p>'+item.local.endereco+'</p></div></div>'+
        '</div>'+
        '</div>';

      var infowindow = new google.maps.InfoWindow({
        content: contentString
      });

      marker.addListener('click', function() {
        console.log("click");
        infowindow.open(map, marker);
      });

      arrayMarkers.push(marker);
    });

		arrayLocais.forEach(function(item) {
      console.log(item)
      var marker = new google.maps.Marker({
        position: {lat: parseFloat(item.lat), lng: parseFloat(item.lng)},
        icon: {
        	url: icons['local'].icon,
        	scaledSize: new google.maps.Size(50, 50),
        },
        type: 'local',
        map: map
      });

      var contentString = '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h1 id="firstHeading" class="firstHeading">'+item.tipo.nome+' - <small>'+item.cliente.nome+'</small></h1>'+
        '<div id="bodyContent">'+
        '<div class="user-panel"><div class="pull-left image">'+
        '<img src="'+item.cliente.logo+'" class="img-circle" alt="User Image"></div>'+
        '<div class="pull-left info"><p>'+item.endereco+'</p></div></div>'+
        '</div>'+
        '</div>';

      var infowindow = new google.maps.InfoWindow({
        content: contentString
      });

      marker.addListener('click', function() {
        console.log("click");
        infowindow.open(map, marker);
      });

      arrayMarkers.push(marker);
    });

	};
</script>

