<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "DocumentoVeiculo".
 *
 * @property string $id Código
 * @property string $idTipo Tipo
 * @property string $idVeiculo Veículo
 * @property string $nome Nome
 * @property string $arquivo Arquivo
 * @property string $dataCadastro Cadastrado
 *
 * @property TipoDocumento $tipo
 * @property Veiculo $veiculo
 */
class DocumentoVeiculo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DocumentoVeiculo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idTipo', 'idVeiculo', 'nome', 'arquivo'], 'required'],
            [['idTipo', 'idVeiculo'], 'integer'],
            [['dataCadastro'], 'safe'],
            [['nome'], 'string', 'max' => 100],
            [['arquivo'], 'string', 'max' => 255],
            [['idTipo'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumento::className(), 'targetAttribute' => ['idTipo' => 'id']],
            [['idVeiculo'], 'exist', 'skipOnError' => true, 'targetClass' => Veiculo::className(), 'targetAttribute' => ['idVeiculo' => 'id']],
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
            'idVeiculo' => 'Veículo',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
            'dataCadastro' => 'Cadastrado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TipoDocumento::className(), ['id' => 'idTipo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeiculo()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'idVeiculo']);
    }
}
