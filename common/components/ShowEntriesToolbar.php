<?php

namespace common\components;

class ShowEntriesToolbar {
    /**
     * @inheritdoc
     */
    function create(){
        $toolbar = [];      
        $select = '<div class="pull-left">';
        $select .= \Yii::$app->selectFactory->showingEntries();
        $select .= '<br>{summary}</div><div class="pull-right" style="text-align:right !important;">';
        $toolbar[]='{export}';
        $toolbar[]='</div>';	
        $toolbar[] = $select;
        // $toolbar[] = '';
        return $toolbar;
    }


}
?>
