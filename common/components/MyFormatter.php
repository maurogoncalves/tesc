<?php

namespace common\components;

use Yii;
use yii\i18n\Formatter;

class MyFormatter extends Formatter {

	public static function asRg($string)
	{
		return substr($string, 0, 2) . '.' . substr($string, 2, 3) . '.' . substr($string, 5, 3) . '-' . substr($string, 8);
	}

	public static function asCep($string)
	{
		return substr($string, 0, 5) . '-' . substr($string, 5);
	}

	public static function asCpf($string)
	{
		return substr($string, 0, 3) . '.' . substr($string, 3, 3) . '.' . substr($string, 6, 3) . '-' . substr($string, 9);
	}

	public static function asCnpj($string)
	{
		return substr($string, 0, 2) . '.' . substr($string, 2, 3) . '.' . substr($string, 5, 3) . '/' . substr($string, 8, 4) . '-' . substr($string, 12);
	}

	public static function asTelefone($string)
	{
		$string = str_ireplace('-', '', $string);
		$string = str_ireplace(' ', '', $string);
		$string = str_ireplace('(', '', $string);
		$string = str_ireplace(')', '', $string);

		if (trim($string) != '')
		{
			if (substr($string, 2, 1) == '9')
				return '(' . substr($string, 0, 2) . ') ' . substr($string, 2, 5) . '-' . substr($string, 7, 4);
			else
				return '(' . substr($string, 0, 2) . ') ' . substr($string, 2, 4) . '-' . substr($string, 6, 4);
		}
		else
			return '-';
	}

	public static function asNIT($string)
	{
		return substr($string, 0, 1) . '.' . substr($string, 1, 3) . '.' . substr($string, 4, 3) . '.' . substr($string, 7, 3) . '-' . substr($string, 10);
	}

	public static function asInscricaoEstadual($string)
	{
		return substr($string, 0, 3) . '.' . substr($string, 3, 3) . '.' . substr($string, 6, 3) . '.' . substr($string, 9, 3);
	}

	public static function asMes($string)
	{
		return substr($string, 4, 2) . '/' . substr($string, 0, 4);
	}

	public static function asData($string)
	{
		return substr($string, 8, 2) . '/' . substr($string, 5, 2) . '/' . substr($string, 0, 4);
	}

	public static function asDataHora($string)
	{
		return substr($string, 8, 2) . '/' . substr($string, 5, 2) . '/' . substr($string, 0, 4) . ' ' . substr($string, 11);
	}
	public static function DoubletoReal($valor){
		return number_format(round($valor,2), 2, ',', '.');
	}
	public static function asDataSemSeparador($string)
	{
		return substr($string, 6, 2) . '/' . substr($string, 4, 2) . '/' . substr($string, 0, 4);
	}

	public static function asHora($string)
	{
		return substr($string, 0, 2) . ':' . substr($string, 2, 2) . ':' . substr($string, 4, 2);
	}
	public static function BRLtoDouble($valor){
		if(strpos($valor, ',')){
			$valor = str_ireplace(".","",$valor);
			$valor = str_ireplace(",",".",$valor);
	  }
	  return $valor;
	}

	public static function DoubletoBRL($valor){
		if(strpos($valor, ',')){
			$valor = str_ireplace(".","",$valor);
			$valor = str_ireplace(",",".",$valor);
	  }
	  return $valor;
	}
	public static function asExtenso ($value, $uppercase = 0)
	{
	    if (strpos($value, ",") > 0) {
	        $value = str_replace(".", "", $value);
	        $value = str_replace(",", ".", $value);
	    }
	    $singular = ["centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão"];
	    $plural = ["centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões"];
	 
	    $c = ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];
	    $d = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
	    $d10 = ["dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove"];
	    $u = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
	 
	    $z = 0;
	 
	    $value = number_format($value, 2, ".", ".");
	    $integer = explode(".", $value);
	    $cont = count($integer);
	    for ($i = 0; $i < $cont; $i++)
	        for ($ii = strlen($integer[$i]); $ii < 3; $ii++)
	            $integer[$i] = "0" . $integer[$i];
	 
	    $fim = $cont - ($integer[$cont - 1] > 0 ? 1 : 2);
	    $rt = '';
	    for ($i = 0; $i < $cont; $i++) {
	        $value = $integer[$i];
	        $rc = (($value > 100) && ($value < 200)) ? "cento" : $c[$value[0]];
	        $rd = ($value[1] < 2) ? "" : $d[$value[1]];
	        $ru = ($value > 0) ? (($value[1] == 1) ? $d10[$value[2]] : $u[$value[2]]) : "";
	 
	        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
	                $ru) ? " e " : "") . $ru;
	        $t = $cont - 1 - $i;
	        $r .= $r ? " " . ($value > 1 ? $plural[$t] : $singular[$t]) : "";
	        if ($value == "000"
	        )
	            $z++;
	        elseif ($z > 0)
	            $z--;
	        if (($t == 1) && ($z > 0) && ($integer[0] > 0))
	            $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
	        if ($r)
	            $rt = $rt . ((($i > 0) && ($i <= $fim) &&
	                    ($integer[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
	    }
	 
	    if (!$uppercase) {
	        return trim($rt ? $rt : "zero");
	    } elseif ($uppercase == "2") {
	        return trim(strtoupper($rt) ? strtoupper(strtoupper($rt)) : "Zero");
	    } else {
	        return trim(ucwords($rt) ? ucwords($rt) : "Zero");
	    }
	}
	
}