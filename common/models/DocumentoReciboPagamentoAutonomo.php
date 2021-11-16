<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "DocumentoReciboPagamentoAutonomo".
 *
 * @property string $id Código
 * @property string $idTipo Tipo
 * @property string $idCondutor Condutor
 * @property string $nome Nome
 * @property string $arquivo Arquivo
 * @property string $dataCadastro Data
 *
 * @property Condutor $condutor
 * @property TipoDocumento $tipo
 */
class DocumentoReciboPagamentoAutonomo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DocumentoReciboPagamentoAutonomo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idTipo', 'nome', 'arquivo'], 'required'],
            [['idTipo'], 'integer'],
            [['dataCadastro','idRecibo'], 'safe'],
            [['nome'], 'string', 'max' => 100],
            [['arquivo'], 'string', 'max' => 255],
            // [['idTipo'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumento::className(), 'targetAttribute' => ['idTipo' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idTipo' => 'Tipo',
            'idCondutor' => 'Condutor',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
            'dataCadastro' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TipoDocumento::className(), ['id' => 'idTipo']);
    }
}
