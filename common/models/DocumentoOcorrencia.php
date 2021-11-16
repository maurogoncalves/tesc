<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "DocumentoOcorrencia".
 *
 * @property string $id Código
 * @property string $idTipo Tipo
 * @property string $idOcorrencia Ocorrência
 * @property string $nome Nome
 * @property string $arquivo Arquivo
 * @property string $dataCadastro Data
 *
 * @property Ocorrencia $ocorrencia
 * @property TipoDocumento $tipo
 */
class DocumentoOcorrencia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DocumentoOcorrencia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idTipo', 'idOcorrencia', 'nome', 'arquivo'], 'required'],
            [['idTipo', 'idOcorrencia'], 'integer'],
            [['dataCadastro'], 'safe'],
            [['nome'], 'string', 'max' => 100],
            [['arquivo'], 'string', 'max' => 255],
            [['idOcorrencia'], 'exist', 'skipOnError' => true, 'targetClass' => Ocorrencia::className(), 'targetAttribute' => ['idOcorrencia' => 'id']],
            [['idTipo'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumento::className(), 'targetAttribute' => ['idTipo' => 'id']],
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
            'idOcorrencia' => 'Ocorrência',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
            'dataCadastro' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOcorrencia()
    {
        return $this->hasOne(Ocorrencia::className(), ['id' => 'idOcorrencia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TipoDocumento::className(), ['id' => 'idTipo']);
    }
}
