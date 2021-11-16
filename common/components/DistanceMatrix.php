<?php
namespace common\components;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
class DistanceMatrix extends Component
{
	public $mode = 'walking';
	public $apiKey = 'AIzaSyCLdXxxtVSN5I0NA2WJ2buip_pEwfF2pW0';
	public $urlBase = 'https://maps.googleapis.com/maps/api/distancematrix/json';
	public $originParams = '';
	public $destinationParams = '';

	private function requestUrl(){
		
		return $this->urlBase.'?origins='.$this->originParams.'&destinations='.$this->destinationParams.'&mode='.$this->mode.'&language=pt-BR&key='.$this->apiKey.'&sensor=false';
	}


	public function mountResponse($response, $origin, $destination){
		return [
			'distanceText' => $response['distance']['text'],
			'distanceValue' => $response['distance']['value'],
			'durationText' => $response['duration']['text'],
			'durationValue' => $response['duration']['value'],
			'origin' => $origin,
			'destination' => $destination,
		];
	}
	public function singleRoute($origins,$destinations){
		$response = $this->route($origins,$destinations);

		if($response && isset($response[0][0]))
			return $response[0][0];
		return null;
	}
	public function route($origins, $destinations){
		$output = [];
		$ch = curl_init();
		foreach ($origins as $origin) {
			$this->originParams .= '|'.$origin['lat'].','.$origin['lng'];
		}
		foreach ($destinations as $destination) {
			$this->destinationParams .= '|'.$destination['lat'].','.$destination['lng'];
		}
		
    	curl_setopt($ch, CURLOPT_URL, $this->requestUrl());
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	    $response = curl_exec($ch);
	    curl_close($ch);
    	$response = json_decode($response, true);
    	//print_r($response);
    	// For para iterar sobre cada uma das origens enviadas
    	for ($o=0; $o < count($response['rows']) ; $o++) { 
    		$origin = $response['origin_addresses'][$o];
    		// For para iterar sobre cada um dos destinos DENTRO de cada origem
    		for($d=0; $d < count($response['rows'][$o]['elements']) ; $d++){
    			$destination =  $response['destination_addresses'][$d];
    			if($response['rows'][$o]['elements'][$d]['status'] == 'OK')
    				$output[$o][] = $this->mountResponse($response['rows'][$o]['elements'][$d], $origin, $destination);
    		}
    	}


    	return $output;
	}	

	public function localDistance($lat1, $lon1, $lat2, $lon2, $unit = "K"){
		//distance(-23.180800,-45.813530,-23.207380,-45.897800,'K');
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);
		if ($unit == "K") {
				return ($miles * 1.609344);
		} else if ($unit == "N") {
				return ($miles * 0.8684);
		} else {
				return $miles;
		}
	}	
}
?>