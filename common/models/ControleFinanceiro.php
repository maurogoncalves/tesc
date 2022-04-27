<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Condutor".
 *
 * @property int $idCondutor
 * @property int $ano
 * @property int $mes
 * @property int $diasTrabalhados
 * @property int $sabadoLetivo
 * @property int $diasExcepcionais1
 * @property int $viagemKm1
 * @property int $diasExcepcionais2
 * @property int $viagemKm2
 * @property int $valorNota
 * @property int $protocoloTESC
 * @property int $protocoloGC
 * @property int $lote
 * @property int $saldoAF
 *
 */
class ControleFinanceiro extends \yii\db\ActiveRecord
{
    public $cpf;
    public $nit;
    public $rg;
    public $cep;
    public $nome;
    public $endereco;
    public $bairro;
    public $telefone;
    public $email;
    public $condutores;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ControleFinanceiro';
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        }

        return false;
    }

    public function toDecimal($valor) {
      

        if(strpos($valor, ',')){
              $valor = str_ireplace(".","",$valor);
              $valor = str_ireplace(",",".",$valor);
        }
        return $valor;
    }
 
    
    private function cut($str, $char){
        return str_replace($char,'',$str);
    }

    private function formatarCPF($cpf){
        $cpf = $this->cut($cpf,'.');
        $cpf = $this->cut($cpf,'-');
        return $cpf; 
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['diasTrabalhados','sabadoLetivo','diasExcepcionais1','viagemKm1','diasExcepcionais2','viagemKm2','lote'], 'integer'],
            [['idCondutor','ano','mes','diasTrabalhados','sabadoLetivo','diasExcepcionais1','viagemKm1','diasExcepcionais2','viagemKm2','valorNota','protocoloTESC','protocoloGC','lote','saldoAF','valorViagemKm1'], 'safe'],
        ];
    }
 
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idCondutor' => '',
            'ano' => '',
            'mes' => '',
            'diasTrabalhados' => '',
            'sabadoLetivo' => '',
            'diasExcepcionais1' => '',
            'viagemKm1' => '',
			'valorViagemKm1' => '',
            'diasExcepcionais2' => '',
            'viagemKm2' => '',
            'valorNota' => '',
            'protocoloTESC' => '',
            'protocoloGC' => '',
            'lote' => '',
            'saldoAF' => ''
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }
}
