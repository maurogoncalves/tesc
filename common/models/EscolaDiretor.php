<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "EscolaDiretor".
 *
 * @property int $id
 * @property string $idEscola
 * @property string $idUsuario
 *
 * @property Escola $escola
 * @property Usuario $usuario
 */
class EscolaDiretor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'EscolaDiretor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idEscola', 'idUsuario'], 'required'],
            [['idEscola', 'idUsuario'], 'integer'],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
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
            'idEscola' => 'Id Escola',
            'idUsuario' => 'Id Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscola()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idUsuario']);
    }
    
    public static function listaEscolas(){
        $escolas = \Yii::$app->User->identity->diretores;
        return array_column($escolas, 'idEscola');
    }
}
