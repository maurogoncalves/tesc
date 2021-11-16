<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "DocumentoAviso".
 *
 * @property int $id Código
 * @property int $idTipo Tipo
 * @property int $idAviso Aviso
 * @property string $nome Nome
 * @property string $arquivo Arquivo
 * @property string $dataCadastro Cadastrado
 *
 * @property Aviso $aviso
 * @property TipoDocumento $tipo
 */
class DocumentoAviso extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DocumentoAviso';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idTipo', 'idAviso', 'nome', 'arquivo'], 'required'],
            [['idTipo', 'idAviso'], 'integer'],
            [['dataCadastro'], 'safe'],
            [['nome'], 'string', 'max' => 100],
            [['arquivo'], 'string', 'max' => 255],
            [['idAviso'], 'exist', 'skipOnError' => true, 'targetClass' => Aviso::className(), 'targetAttribute' => ['idAviso' => 'id']],
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
            'idAviso' => 'Aviso',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
            'dataCadastro' => 'Cadastrado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAviso()
    {
        return $this->hasOne(Aviso::className(), ['id' => 'idAviso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TipoDocumento::className(), ['id' => 'idTipo']);
    }
}
