<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "SolicitacaoCreditoStatus".
 *
 * @property int $id ID
 * @property string $status Status
 * @property int $idSolicitacaoCredito Solicitação de crédito
 * @property string $idUsuario Usuário
 * @property string $justificativa Justificativa
 * @property string $dataCadastro Cadastro
 *
 * @property SolicitacaoCredito $solicitacaoCredito
 * @property Usuario $usuario
 */
class SolicitacaoCreditoStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SolicitacaoCreditoStatus';
    }
 
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'idSolicitacaoCredito', 'idUsuario', 'justificativa', 'dataCadastro'], 'required'],
            [['status', 'idSolicitacaoCredito', 'idUsuario'], 'integer'],
            [['justificativa'], 'string'],
            [['dataCadastro'], 'safe'],
            [['idSolicitacaoCredito'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitacaoCredito::className(), 'targetAttribute' => ['idSolicitacaoCredito' => 'id']],
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
            'status' => 'Status',
            'idSolicitacaoCredito' => 'Solicitação de crédito',
            'idUsuario' => 'Usuário',
            'justificativa' => 'Justificativa',
            'dataCadastro' => 'Cadastro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitacaoCredito()
    {
        return $this->hasOne(SolicitacaoCredito::className(), ['id' => 'idSolicitacaoCredito']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idUsuario']);
    }
}
