<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Ponto".
 *
 * @property string $id ID
 * @property string $idCondutorRota Rota
 * @property int $tipo Tipo
 * @property string $lat Latitude
 * @property string $lng Longitude
 * @property string $distanciaEscola Distância até a escola
 * @property string $ordem Ordem do ponto
 *
 * @property CondutorRota $condutorRota
 * @property PontoAluno[] $pontoAlunos
 */
class Ponto extends \yii\db\ActiveRecord
{
    //A linha abaixo foi comentada para não quebrar o método que JÁ EXISTE
    // se mantem comentada até que se prove necessária
    // public $pontoAlunos; 
    const PONTO_NADA = 0;
    const PONTO_INICIO = 1;
    const PONTO_ESCOLA = 2;
    const PONTO_ALUNO = 3;
    const PONTO_ENCONTRO = 4;

    const ARRAY_PONTO = [
        self::PONTO_NADA => 'Nada',
        self::PONTO_INICIO => 'Início da rota',
        self::PONTO_ESCOLA => 'Escola',
        self::PONTO_ALUNO => 'Casa de aluno',
        self::PONTO_ENCONTRO => 'Ponto de encontro',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Ponto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCondutorRota', 'tipo', 'lat', 'lng'], 'required'],
            [['idCondutorRota', 'tipo','confirmacaoPassagem','sentido'], 'integer'],
            [['lat', 'lng', 'distanciaEscola'], 'number'],
            [['idCondutorRota'], 'exist', 'skipOnError' => true, 'targetClass' => CondutorRota::className(), 'targetAttribute' => ['idCondutorRota' => 'id']],
        ];
    }
    
    public function fields()
    {
        $fields = parent::fields();

        $fields['tipoText'] = 'tipoText';
        $fields['alunos'] =   'alunos';
        $fields['escolas'] =  'escolas';  
        
        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idCondutorRota' => 'Rota',
            'tipo' => 'Tipo',
            'lat' => 'Latitude',
            'lng' => 'Longitude',
            'distanciaEscola' => 'Distância até a escola',
            'ordem' => 'Ordem do ponto',
        ];
    }

    public function getTipoText(){
        return self::ARRAY_PONTO[$this->tipo];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorRota()
    {
        return $this->hasOne(CondutorRota::className(), ['id' => 'idCondutorRota']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        //        return $this->hasMany(Escola::className(), ['id' => 'idEscola'])->via('pontoEscolas');

        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor'])->via('condutorRota');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPontoAlunos()
    {
        return $this->hasMany(PontoAluno::className(), ['idPonto' => 'id']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getPontoEscolas()
    {
        return $this->hasMany(PontoEscola::className(), ['idPonto' => 'id']);
    }

    // public function getMotoristas()
    // {
    //     return $this->hasMany(Motorista::className(), ['id' => 'idMotorista'])
    //         ->via('motoristaChamado');
    // }
    
    public function getAlunos(){
        return $this->hasMany(Aluno::className(), ['id' => 'idAluno'])->via('pontoAlunos');
    }

    public function getEscolas(){
        return $this->hasMany(Escola::className(), ['id' => 'idEscola'])->via('pontoEscolas');
    }
}
