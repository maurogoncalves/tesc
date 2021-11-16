<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Documento".
 *
 * @property string $id Código
 * @property string $idTipoDocumento Tipo do documento
 * @property string $nome Nome
 * @property string $caminho Caminho
 *
 * @property Documento $tipoDocumento
 */
class Documento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Documento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idTipoDocumento', 'nome', 'caminho'], 'required'],
            [['idTipoDocumento'], 'integer'],
            [['nome', 'caminho'], 'string', 'max' => 50],
            [['idTipoDocumento'], 'exist', 'skipOnError' => true, 'targetClass' => Documento::className(), 'targetAttribute' => ['idTipoDocumento' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idTipoDocumento' => 'Tipo do documento',
            'nome' => 'Nome',
            'caminho' => 'Caminho',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDocumento()
    {
        return $this->hasOne(Documento::className(), ['id' => 'idTipoDocumento']);
    }
}
