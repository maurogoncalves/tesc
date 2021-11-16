<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "SolicitacaoStatus".
 *
 * @property int $id ID
 * @property int $idSolicitacaoTransporte Solicitação de transporte
 * @property string $idUsuario Usuário
 * @property string $justificativa Justificativa
 *
 * @property SolicitacaoTransporte $solicitacaoTransporte
 * @property Usuario $usuario
 */
class SolicitacaoStatus extends \yii\db\ActiveRecord
{
    const TIPO_INSERIDO = 1;
    const TIPO_REMOVIDO = 2;

    const ARRAY_TIPO = [
        self::TIPO_INSERIDO => 'Inserido na rota',
            self::TIPO_REMOVIDO => 'Removido da rota'
    ];
    const ARRAY_STATUS = [
        1 => 'APROVADO',
        2 => 'REPROVADO',
    ]; 
    public static function find()
    {
        return parent::find()->where(['>', 'mostrar', 0]);
    }
    // ALTER TABLE `SolicitacaoStatus` ADD COLUMN `mostrar` TINYINT NULL DEFAULT '1' AFTER `tipo`;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SolicitacaoStatus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idSolicitacaoTransporte', 'idUsuario', 'justificativa'], 'required'],
            [['idSolicitacaoTransporte', 'idUsuario'], 'integer'],
            [['justificativaSetor','idCondutor','idCondutorRota','idEscola','idAluno','idVeiculo','tipo'], 'safe'],
            // [['justificativa'], 'string', 'max' => 100],
            [['idSolicitacaoTransporte'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitacaoTransporte::className(), 'targetAttribute' => ['idSolicitacaoTransporte' => 'id']],
            [['idUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['idUsuario' => 'id']],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idSolicitacaoTransporte' => 'Solicitação de transporte',
            'idUsuario' => 'Usuário',
            'justificativa' => 'Justificativa',
            'justificativaSetor' => 'Justificativa do setor'
        ];
    }

    public function beforeSave($insert)
    {
        $this->mostrar = 1;
        foreach($this as $key => $value) {
            $this[$key] = mb_strtoupper($value, 'utf-8');
        }

        // self::removerSemelhantes($this);
        return parent::beforeSave($insert);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitacaoTransporte()
    {
        return $this->hasOne(SolicitacaoTransporte::className(), ['id' => 'idSolicitacaoTransporte']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idUsuario']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorRota()
    {
        return $this->hasOne(CondutorRota::className(), ['id' => 'idCondutorRota']);
    }

    // Objetivo deste método é remover lançamentos que batam com o mesmo dia e o mesmo status
    // Ele foi criado para sanar o problema de repetir status
    public static function removerSemelhantes($model){
        $listaStatus = self::find()->where(['status' => $model->status])
                                    ->andWhere(['dataCadastro' => $model->dataCadastro])
                                    ->andWhere(['idSolicitacaoTransporte' => $model->idSolicitacaoTransporte ])
                                    ->andWhere(['idCondutorRota' => $model->idCondutorRota ])
                                    ->andWhere(['tipo' => $model->tipo ])
                                    ->all();
        foreach($listaStatus as $status){
            $status->mostrar = 0;
            $status->save();
        }
    }
}
