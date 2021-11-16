<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "HistoricoMovimentacaoRota".
 *
 * @property int $id
 * @property int $tipo
 * @property int $idSolicitacaoTransporte
 * @property int $idVeiculoAnterior
 * @property int $idVeiculoAtual
 * @property int $idAluno
 * @property int $idEscola
 * @property int $idCondutorAtual
 * @property int $idCondutorAnterior
 * @property int $idCondutorRotaAtual
 * @property int $idCondutorRotaAnterior
 * @property int $idUsuario
 * @property string $criacao
 *
 * @property Aluno $aluno
 * @property Condutor $condutorAtual
 * @property CondutorRota $condutorRotaAtual
 * @property CondutorRota $condutorRotaAnterior
 * @property Condutor $condutorAnterior
 * @property Escola $escola
 * @property SolicitacaoTransporte $solicitacaoTransporte
 * @property Usuario $usuario
 * @property Veiculo $veiculoAnterior
 * @property Veiculo $veiculoAtual
 */
class HistoricoMovimentacaoRota extends \yii\db\ActiveRecord
{
    const STATUS_ALUNO_INSERIDO = 10;
    const STATUS_ALUNO_REMOVIDO = 11;
    const STATUS_CONDUTOR_INSERIDO = 20;
    const STATUS_CONDUTOR_REMOVIDO = 21;
    
    public $inicio;
    public $fim;
    public $horarioEntrada;
    public $horarioSaida;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'HistoricoMovimentacaoRota';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idHistoricoMovimentacaoAssociado','tipo', 'idSolicitacaoTransporte', 'idVeiculoAnterior', 'idVeiculoAtual', 'idAluno', 'idEscola', 'idCondutorAtual', 'idCondutorAnterior', 'idCondutorRotaAtual', 'idCondutorRotaAnterior', 'idUsuario'], 'integer'],
            [['criacao'], 'safe'],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
            [['idCondutorAtual'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutorAtual' => 'id']],
            [['idCondutorRotaAtual'], 'exist', 'skipOnError' => true, 'targetClass' => CondutorRota::className(), 'targetAttribute' => ['idCondutorRotaAtual' => 'id']],
            [['idCondutorRotaAnterior'], 'exist', 'skipOnError' => true, 'targetClass' => CondutorRota::className(), 'targetAttribute' => ['idCondutorRotaAnterior' => 'id']],
            [['idCondutorAnterior'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutorAnterior' => 'id']],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
            [['idSolicitacaoTransporte'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitacaoTransporte::className(), 'targetAttribute' => ['idSolicitacaoTransporte' => 'id']],
            [['idUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['idUsuario' => 'id']],
            [['idVeiculoAnterior'], 'exist', 'skipOnError' => true, 'targetClass' => Veiculo::className(), 'targetAttribute' => ['idVeiculoAnterior' => 'id']],
            [['idVeiculoAtual'], 'exist', 'skipOnError' => true, 'targetClass' => Veiculo::className(), 'targetAttribute' => ['idVeiculoAtual' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'idSolicitacaoTransporte' => 'Id Solicitacao Transporte',
            'idVeiculoAnterior' => 'Id Veiculo Anterior',
            'idVeiculoAtual' => 'Id Veiculo Atual',
            'idAluno' => 'Id Aluno',
            'idEscola' => 'Id Escola',
            'idCondutorAtual' => 'Id Condutor Atual',
            'idCondutorAnterior' => 'Id Condutor Anterior',
            'idCondutorRotaAtual' => 'Id Condutor Rota Atual',
            'idCondutorRotaAnterior' => 'Id Condutor Rota Anterior',
            'idUsuario' => 'Id Usuario',
            'criacao' => 'Criacao',
        ];
    }
    public function beforeSave($insert) {

        if ($insert) {
            date_default_timezone_set('America/Sao_Paulo');
            $this->criacao = date("Y-m-d");
            $this->criacaoHora = date("H:i:s");

        }

        return parent::beforeSave($insert);

    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovimentacaoAssociada()
    {
        return $this->hasOne(HistoricoMovimentacaoRota::className(), ['id' => 'idHistoricoMovimentacaoAssociado']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorAtual()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutorAtual']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorRotaAtual()
    {
        return $this->hasOne(CondutorRota::className(), ['id' => 'idCondutorRotaAtual']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorRotaAnterior()
    {
        return $this->hasOne(CondutorRota::className(), ['id' => 'idCondutorRotaAnterior']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorAnterior()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutorAnterior']);
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
    public function getSolicitacaoTransporte()
    {
        return $this->hasOne(SolicitacaoTransporte::className(), ['id' => 'idSolicitacaoTransporte']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idUsuario']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeiculoAnterior()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'idVeiculoAnterior']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeiculoAtual()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'idVeiculoAtual']);
    }

    public function getInicioAtendimento(){
        if($this->geradoViaSistema == 1){
            return 'Anterior a 02/03/2020';
        }
        return ($this->criacao && $this->criacao != '0000-00-00') ? date("d/m/Y", strtotime($this->criacao)) : '-';
    }

    public function getFimAtendimento(){
           if($this->movimentacaoAssociada)
                return ($this->movimentacaoAssociada->criacao && $this->movimentacaoAssociada->criacao != '0000-00-00') ? date("d/m/Y", strtotime($this->movimentacaoAssociada->criacao)) : '-';
            return '-';
    }

    public function getEntrada(){
        return  $this->sentido &&  $this->sentido == CondutorRota::SENTIDO_IDA ? $this->aluno->horarioEntrada : '-';

    }

    public function getSaida(){
        return  $this->sentido &&  $this->sentido == CondutorRota::SENTIDO_VOLTA ? $this->aluno->horarioSaida : '-';
    }
    public static function salvar($params)
    {

        $model = new HistoricoMovimentacaoRota();
        //$model->x = $params['acao'];
        foreach($params as $key=>$value) {
            $model->$key = $value;
        }
        $model->save();
        return $model;
        // if(!$model->save())
        //     print_r($log->getErrors());
        // exit(1);
    }
}
