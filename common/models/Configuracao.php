<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Configuracao".
 *
 * @property int $id
 * @property string $valeTransporte Vale Transporte
 * @property string $passeEscolar Passe Escolar
 *  * @property string $dataVigente
 */
class Configuracao extends \yii\db\ActiveRecord
{

    public $documentoFolhaPonto;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Configuracao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['valeTransporte', 'passeEscolar','dataVigente'], 'required'],
            [['valeTransporte', 'passeEscolar'], 'number'],
            [['dataVigente','anoVigente','folhaPonto','documentoFolhaPonto'], 'safe'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'valeTransporte' => 'Vale Transporte',
            'passeEscolar' => 'Passe Escolar',
            'dataVigente' => 'Data limite para solicitações'

        ];
    }

    public static function setup(){
        return Configuracao::findOne(1);
    }

    public function afterSave($insert, $atributosAlterados) {
        parent::afterSave($insert, $atributosAlterados);
        //UPDATE
        if(!$insert) {
            foreach($atributosAlterados as $key=>$value)
            {
                if($atributosAlterados[$key] && $value != $this->$key)
                {
                   $this->salvarLog(Log::ACAO_ATUALIZAR,$key,$atributosAlterados);
                }
            }
        } 
        //INSERT
        else 
        {
            $novoRegistro =  $this->attributes();
            foreach($novoRegistro as $key=>$coluna)
            {
                $this->salvarLog(Log::ACAO_INSERIR,$coluna);
            }
        }
    }

    public function beforeDelete()
    { 
        if (parent::beforeDelete()) {
            $this->salvarLog(Log::ACAO_DELETAR,'id');
            return true;
        }
        return false;
    }
    
    public function calcularAno(){
        $dataVigente = explode('-',$this->dataVigente);
        if(date("Y-m-d") >= $this->dataVigente){
            // Pega somente o ano da dataVigente
            if($dataVigente[0])
                return $dataVigente[0] + 1;
        } 
        return date('Y');
    }
    
    private function salvarLog($acao,$coluna,$atributosAlterados=NULL){
        if($this->$coluna)
        {
       
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->id,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idConfiguracao',
                'id' => $this->id,
            ]);
        }
    }
}
