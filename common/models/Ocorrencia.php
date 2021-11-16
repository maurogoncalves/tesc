<?php

namespace common\models;
use kartik\daterange\DateRangeBehavior;

use Yii;

/**
 * This is the model class for table "Ocorrencia".
 *
 * @property string $id Cód,
 * @property string $idCondutor Condutor
 * @property string $idCondutorRota Rota
 * @property string $idJustificativa Justificativa
 * @property string $idVeiculo Veículo
 * @property string $data Data
 * @property string $descricao Descrição
 *
 * @property Condutor $condutor
 * @property CondutorRota $condutorRota
 * @property Justificativa $justificativa
 * @property Veiculo $veiculo
 */
class Ocorrencia extends \yii\db\ActiveRecord
{
    // public $dataFinal;
    // public $dataInicial;



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Ocorrencia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCondutor', 'idCondutorRota', 'idJustificativa', 'data', 'descricao'], 'required'],
            [['idCondutor', 'idCondutorRota', 'idJustificativa', 'idVeiculo'], 'integer'],
            // [['data'], 'safe'],

            [['descricao'], 'string', 'max' => 250],
            [['idCondutor'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutor' => 'id']],
            [['idCondutorRota'], 'exist', 'skipOnError' => true, 'targetClass' => CondutorRota::className(), 'targetAttribute' => ['idCondutorRota' => 'id']],
            [['idJustificativa'], 'exist', 'skipOnError' => true, 'targetClass' => Justificativa::className(), 'targetAttribute' => ['idJustificativa' => 'id']],
            [['idVeiculo'], 'exist', 'skipOnError' => true, 'targetClass' => Veiculo::className(), 'targetAttribute' => ['idVeiculo' => 'id']],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['justificativa'] = 'justificativa';   

        //$fields['condutor'] =   'condutor';  
        
        return $fields;
    }

    
    
    public static function permissaoCriar(){
        $permissoes = self::permissaoActions();
        return strstr($permissoes,'{create}');
    }
    public static function permissaoEditar(){
        $permissoes = self::permissaoActions();
        return strstr($permissoes,'{update}');
    } 
    public static function permissaoRemover(){
        $permissoes = self::permissaoActions();
        return strstr($permissoes,'{delete}');
    }

    public static function permissaoActions(){
        $actions = '';
        switch(\Yii::$app->User->identity->idPerfil){
            case Usuario::PERFIL_SUPER_ADMIN: $actions = '{view} {delete}';  break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO: $actions = '{view} {delete}'; break;
            case Usuario::PERFIL_SECRETARIO: $actions = ''; break;
            case Usuario::PERFIL_DIRETOR: $actions = ''; break;
            case Usuario::PERFIL_DRE: $actions = ''; break;
            case Usuario::PERFIL_TESC_PASSE_ESCOLAR: $actions = '{view}'; break;
            case Usuario::TESC_CONSULTA: $actions = '{view}';break;
            case Usuario::PERFIL_CONDUTOR: $actions = ''; break;
        } 
        return $actions;
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Cód',
            'idCondutor' => 'Condutor',
            'idCondutorRota' => 'Rota',
            'idJustificativa' => 'Justificativa',
            'idVeiculo' => 'Veículo',
            'data' => 'Data',
            'descricao' => 'Descrição',
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
    public function getCondutorRota()
    {
        return $this->hasOne(CondutorRota::className(), ['id' => 'idCondutorRota']);
    }

    public function getFotos(){
        return $this->hasMany(DocumentoOcorrencia::className(), ['idOcorrencia' => 'id']);

    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJustificativa()
    {
        return $this->hasOne(Justificativa::className(), ['id' => 'idJustificativa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeiculo()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'idVeiculo']);
    }

     
}
