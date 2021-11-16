<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Ausencia".
 *
 * @property string $id Cód.
 * @property string $data Data
 * @property string $idAluno Aluno
 * @property string $idJustificativa Justificativa
 * @property string $tipo Tipo
 * @property string $condutorCiente Condutor Ciente
 *
 * @property Aluno $aluno
 * @property Justificativa $justificativa
 */
class Comunicado extends \yii\db\ActiveRecord
{
    const TIPO_IDA = 1;
    const TIPO_VOLTA = 2;
    const TIPO_IDAVOLTA = 3;

    const ENVIADO_RESPONSAVEL = 1;
    const ENVIADO_SISTEMA = 2;
    const ENVIADO_CONDUTOR = 3;
    
    const ARRAY_TIPO = [
        self::TIPO_IDA => 'Ida',
        self::TIPO_VOLTA => 'Volta',
        self::TIPO_IDAVOLTA => 'Ida/Volta',
    ];

    const ARRAY_ENVIADO = [
        self::ENVIADO_RESPONSAVEL => 'Enviado pelo responsável',
        self::ENVIADO_SISTEMA => 'Enviado pelo sistema',
        self::ENVIADO_CONDUTOR => 'Enviado pelo condutor'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Comunicado';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data', 'idAluno', 'idJustificativa', 'tipo'], 'required'],
            [['data', 'condutorCiente','responsavelCiente','idCondutor'], 'safe'],
            [['idAluno', 'idJustificativa', 'tipo','enviadoPor','idCondutorRota'], 'integer'],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
            [['idJustificativa'], 'exist', 'skipOnError' => true, 'targetClass' => Justificativa::className(), 'targetAttribute' => ['idJustificativa' => 'id']],
            [['idCondutorRota'], 'exist', 'skipOnError' => true, 'targetClass' => CondutorRota::className(), 'targetAttribute' => ['idCondutorRota' => 'id']],

        ];
    }

   public function fields()
    {
        $fields = parent::fields();

        // $fields['aluno'] = 'aluno'; 

        $fields['justificativa'] = 'justificativa';
        $fields['tipo'] = 'tipoText';
        $fields['enviadoPorText'] = 'enviadoPorText'; 
        $fields['nomeAlunoText'] = 'nomeAlunoText';
        $fields['nomeEscolaText'] = 'nomeEscolaText';
        $fields['enderecoAlunoText'] = 'enderecoAlunoText';

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Cód.',
            'data' => 'Data',
            'idAluno' => 'Aluno',
            'idJustificativa' => 'Justificativa',
            'tipo' => 'Sentido',
            'enviadoPor' => 'Enviado por',
            'condutorCiente' => 'Condutor Ciente',
            'idCondutor' => 'Condutor',
        ];
    }

    public function getTipoText(){
        return self::ARRAY_TIPO[$this->tipo];
    }

    public function getEnviadoPorText(){
        return self::ARRAY_ENVIADO[$this->enviadoPor];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
    }

    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNomeAlunoText()
    {
        return $this->aluno->nome;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNomeEscolaText()
    {
        return $this->aluno->escola->nome;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnderecoAlunoText()
    {
        return $this->aluno->escola->endereco;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJustificativa()
    {
        return $this->hasOne(Justificativa::className(), ['id' => 'idJustificativa']);
    }
}
