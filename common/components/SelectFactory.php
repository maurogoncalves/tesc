<?php

namespace common\components;

class SelectFactory {
    /**
     * @inheritdoc
     */
    function createOption($val){
        $selected = isset($_GET['pageSize']) && $val == $_GET['pageSize'] ? ' selected ' : '';
        return '<option value="'.$val.'" '.$selected.' >'.$val.'</option>';
    }

    function showingEntries(){
        $str = 'Mostrar&nbsp;<select class="" name="" id="paginacao">';
        $str .= $this->createOption(20);
        $str .= $this->createOption(50);
        $str .= $this->createOption(100);
        $str .= $this->createOption(200);
        $str .= $this->createOption(500);
        $str .= $this->createOption(1000);
        $str .= $this->createOption(5000);
        $str .= $this ->createOption(10000);
        $str .= '</select>';
        $str .= '&nbsp;registros.';
        return $str;
    }
}
?>