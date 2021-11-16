<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "SolicitacaoCreditoAluno".
 *
 * @property int $id Código
 * @property int $tipo Tipo
 * @property int $idSolicitacao Solicitação
 * @property string $idAluno Aluno
 * @property string $valor Valor
 * @property string $justificativa Justificativa
 *
 * @property Aluno $aluno
 * @property SolicitacaoCredito $solicitacao
 */
class SolicitacaoCreditoAluno extends \yii\db\ActiveRecord
{

    const TIPO_PASSE_ESCOLAR = 1;
    const TIPO_VALE_TRANSPORTE = 2;

 
    const ARRAY_TIPO = [
           1 => 'Passe Escolar',
           2 => 'Vale Transporte'
    ];
    /**

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SolicitacaoCreditoAluno';
    }
    
    public function beforeSave($insert)
    {
        if($this->valor)
            $this->valor = $this->toDecimal($this->valor);
        if($this->saldo)
            $this->saldo = $this->toDecimal($this->saldo);

        // if($this->valor)
        //     $this->valor = $this->toDecimal($this->valor);
        // if($this->valor)
        //     $this->valor = $this->toDecimal($this->valor);
        
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

    public static function toDouble($valor) {
        if(strpos($valor, ',')){
              $valor = str_ireplace(".","",$valor); 
              $valor = str_ireplace(",",".",$valor); 
        }
        return $valor; 
    }

    public function toReal($valor){
        return number_format(round($valor,2), 2, ',', '.');
    }

    public function getValorReal(){
        return $this->toReal($this->valor);
    }
    public function getSaldoReal(){
        return $this->toReal($this->saldo);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo', 'idSolicitacao', 'idAluno', 'valor'], 'required'],
            [['tipo', 'idSolicitacao', 'idAluno'], 'integer'],
            [['valor','saldo','valorNecessario','fundhas'], 'safe'],
            [['justificativa'], 'string', 'max' => 500],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
            [['idSolicitacao'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitacaoCredito::className(), 'targetAttribute' => ['idSolicitacao' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'tipo' => 'Tipo',
            'idSolicitacao' => 'Solicitação',
            'idAluno' => 'Aluno',
            'valor' => 'Valor',
            'justificativa' => 'Justificativa',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitacao()
    {
        return $this->hasOne(SolicitacaoCredito::className(), ['id' => 'idSolicitacao']);
    }
}
