<?php
namespace common\components;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
class ArrayPicker extends Component
{
	public function pick(array $array, $keys){
		if (!is_array($keys)) $keys = [$keys];
		return array_map(function ($el) use ($keys) {
			$o = [];
			foreach($keys as $key){
				$o[$key] = isset($el[$key])?$el[$key]:false;
			}
			return $o;
		}, $array);
	}
}
?>