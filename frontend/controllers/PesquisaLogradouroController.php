<?php

namespace frontend\controllers;
use common\models\PesquisaLogradouro;
use yii\web\NotFoundHttpException;

class PesquisaLogradouroController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    
    public function actionPesquisaCep($cep){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $registro = PesquisaLogradouro::find()->where(['=', 'CEP' , $cep])->all();
        if($registro)
            return ['status' => true, 'enderecos' => $registro];
        return ['status' => false]; 
    }

    public function actionPesquisaLogradouro($logradouro='',$tipo='', $cep='', $bloquearCidade=false){
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // $registro = PesquisaLogradouro::find();
        
        if($cep)
            $cep = str_replace('-','',$cep);
        //     $registro->where(['=', 'CEP' , $cep]);
        // }
        // if($logradouro){
        //     $registro->where(['like', 'LOGRADOURO' , $logradouro]);
        // }
        
        // if($tipo){
        //     $registro->andWhere(['=','TIPO_LOGRADOURO',$tipo]);
        // }
        
        //$resultado = $registro->all();
        // $params = 'WHERE ID_CEP > 0 ';
        $params = ''; 
        if($cep)
            $params .= '@CEP VARCHAR(8) = '.$cep.',     ';
        else 
            $params .= '@CEP VARCHAR(8),    ';

        // if($bloquearCidade)
        //     $params .= "@CIDADE VARCHAR(MAX) = 'SÃO JOSÉ DOS CAMPOS',  ";
        // else 
            $params .= '@CIDADE VARCHAR(MAX),    ';

        if($logradouro)
            $params .= "@LOGRADOURO VARCHAR(MAX) = '".$logradouro."'  ";
        else 
            $params .= "@LOGRADOURO VARCHAR(MAX) = ''  ";
   
   

        $sql = "
        DECLARE     
            ".$params."
        SELECT * FROM VW_PESQUISA_LOGRADOURO  
        WHERE  
            ((@CEP IS NULL) OR (CEP = @CEP))
                AND
            ((@CIDADE IS NULL) OR (CIDADE = @CIDADE))
                AND
            ((@LOGRADOURO IS NULL) OR (CONCAT(TIPO_LOGRADOURO,' ',LOGRADOURO)  LIKE '%'+@LOGRADOURO+'%' COLLATE SQL_LATIN1_GENERAL_CP1251_CI_AS))";
        
        // print $sql;
        // return null;
        $resultado = PesquisaLogradouro::findBySql($sql)->all();
    
        if($resultado)
            return ['status' => true, 'enderecos' => $resultado];
        return ['status' => false ];
    }
}
 