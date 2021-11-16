<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "EscolaHomologacao".
 *
 * @property string $id Código
 * @property string $idEscola Escola
 * @property int $unidade Unidade escolar
 * @property int $tipo Tipo
 * @property int $regiao Região
 * @property string $nome Nome
 * @property string $endereco Endereço
 * @property string $lat Lat
 * @property string $lng Lng
 * @property string $telefone Telefone
 * @property string $telefone2
 * @property string $email Email
 * @property string $codigoCie Código CIE
 *
 * @property Escola $escola
 */
class EscolaHomologacao extends \yii\db\ActiveRecord
{
    public $alunosEscola;

    public $inputSecretarios;
    public $inputDiretores;
    public $inputEnsino;
    public $distancia;

    public $atendimento;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'EscolaHomologacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    { 
        return [
            [['idEscola', 'unidade', 'tipo', 'nome'], 'required'],
            [['idEscola', 'unidade', 'tipo', 'regiao'], 'integer'],
            [['lat', 'lng'], 'number'],
            [['nome', 'endereco'], 'string', 'max' => 255],
            [['telefone', 'telefone2'], 'string', 'max' => 15],
            [['email'], 'string', 'max' => 70],
            [['codigoCie'], 'string', 'max' => 200],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idEscola' => 'Escola',
            'unidade' => 'Unidade escolar',
            'tipo' => 'Tipo',
            'regiao' => 'Região',
            'nome' => 'Nome',
            'endereco' => 'Endereço',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'telefone' => 'Telefone',
            'telefone2' => 'Telefone2',
            'email' => 'Email',
            'codigoCie' => 'Código CIE',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscola()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    }
}
