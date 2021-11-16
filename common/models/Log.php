<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Log".
 *
 * @property int $id
 * @property string $data
 * @property int $acao
 * @property string $referencia
 * @property int $tabela
 * @property string $coluna
 * @property string $antes
 * @property string $depois
 * @property int $idUsuario
 * @property int $idAlunoTable
 * @property int $idEscolaTable
 * @property int $idSolicitacaoTransporteTable
 * @property int $idSolicitacaoCreditoTable
 * @property int $idCondutorRotaTable
 * @property int $idOcorrenciaTable
 * @property int $idCondutorTable
 * @property int $idVeiculoTable
 * @property int $idMarcaTable
 * @property int $idModeloTable
 * @property int $idUsuarioTable
 * @property int $idJustificativaTable
 * @property int $idNecessidadesEspeciaisTable
 * @property int $idConfiguracaoTable
 * @property int $idEmpresaTable
 *
 * @property Aluno $alunoTable
 * @property Condutor $condutorTable
 * @property CondutorRota $condutorRotaTable
 * @property Configuracao $configuracaoTable
 * @property Empresa $empresaTable
 * @property Escola $escolaTable
 * @property Justificativa $justificativaTable
 * @property Marca $marcaTable
 * @property Modelo $modeloTable
 * @property NecessidadesEspeciais $necessidadesEspeciaisTable
 * @property Ocorrencia $ocorrenciaTable
 * @property SolicitacaoCredito $solicitacaoCreditoTable
 * @property SolicitacaoTransporte $solicitacaoTransporteTable
 * @property Usuario $usuario
 * @property Usuario $usuarioTable
 * @property Veiculo $veiculoTable
 */
class Log extends \yii\db\ActiveRecord
{
    const ACAO_INSERIR = 1;
    const ACAO_ATUALIZAR = 2;
    const ACAO_DELETAR = 3;

    const ARRAY_ACAO = [
        self::ACAO_INSERIR => 'Inserir',
        self::ACAO_ATUALIZAR => 'Atualizar',
        self::ACAO_DELETAR => 'Deletar',
    ];



    const ARRAY_TABELA = [
        'Aluno' => 'Aluno',
        'Modelo' => 'Modelo de veículo',
        'Marca' => 'Marca de veículo',
        'ReciboPagamentoAutonomo' => 'RPA',
        'Configuracao' => 'Configuração',
        'Justificativa' => 'Justificativa',
        'Usuario' => 'Usuário',
        'Condutor' => 'Condutor',
        'CondutorRota' => 'Rota',
        'SolicitacaoCredito' => 'Solicitação de crédito',
        'SolicitacaoTransporte' => 'Solicitação de transporte',
        'Escola' => 'Escola',
        'Veiculo' => 'Veículo'

    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'safe'],
            [['acao', 'referencia', 'coluna'], 'required'],
            [['acao', 'idReciboPagamentoAutonomoTable', 'idUsuario', 'idAlunoTable', 'idEscolaTable', 'idSolicitacaoTransporteTable', 'idSolicitacaoCreditoTable', 'idCondutorRotaTable', 'idOcorrenciaTable', 'idCondutorTable', 'idVeiculoTable', 'idMarcaTable', 'idModeloTable', 'idUsuarioTable', 'idJustificativaTable', 'idNecessidadesEspeciaisTable', 'idConfiguracaoTable', 'idEmpresaTable'], 'integer'],
            // [['antes', 'depois'], 'string'],
            // [['referencia'], 'string', 'max' => 255],
            [['coluna'], 'string', 'max' => 50],
            [['idAlunoTable'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAlunoTable' => 'id']],
            [['idCondutorTable'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutorTable' => 'id']],
            [['idCondutorRotaTable'], 'exist', 'skipOnError' => true, 'targetClass' => CondutorRota::className(), 'targetAttribute' => ['idCondutorRotaTable' => 'id']],
            [['idConfiguracaoTable'], 'exist', 'skipOnError' => true, 'targetClass' => Configuracao::className(), 'targetAttribute' => ['idConfiguracaoTable' => 'id']],
            [['idEmpresaTable'], 'exist', 'skipOnError' => true, 'targetClass' => Empresa::className(), 'targetAttribute' => ['idEmpresaTable' => 'id']],
            [['idEscolaTable'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscolaTable' => 'id']],
            [['idJustificativaTable'], 'exist', 'skipOnError' => true, 'targetClass' => Justificativa::className(), 'targetAttribute' => ['idJustificativaTable' => 'id']],
            [['idMarcaTable'], 'exist', 'skipOnError' => true, 'targetClass' => Marca::className(), 'targetAttribute' => ['idMarcaTable' => 'id']],
            [['idModeloTable'], 'exist', 'skipOnError' => true, 'targetClass' => Modelo::className(), 'targetAttribute' => ['idModeloTable' => 'id']],
            [['idNecessidadesEspeciaisTable'], 'exist', 'skipOnError' => true, 'targetClass' => NecessidadesEspeciais::className(), 'targetAttribute' => ['idNecessidadesEspeciaisTable' => 'id']],
            [['idOcorrenciaTable'], 'exist', 'skipOnError' => true, 'targetClass' => Ocorrencia::className(), 'targetAttribute' => ['idOcorrenciaTable' => 'id']],
            [['idSolicitacaoCreditoTable'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitacaoCredito::className(), 'targetAttribute' => ['idSolicitacaoCreditoTable' => 'id']],
            [['idSolicitacaoTransporteTable'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitacaoTransporte::className(), 'targetAttribute' => ['idSolicitacaoTransporteTable' => 'id']],
            [['idUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['idUsuario' => 'id']],
            [['idUsuarioTable'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['idUsuarioTable' => 'id']],
            [['idVeiculoTable'], 'exist', 'skipOnError' => true, 'targetClass' => Veiculo::className(), 'targetAttribute' => ['idVeiculoTable' => 'id']],
            [['idReciboPagamentoAutonomoTable'], 'exist', 'skipOnError' => true, 'targetClass' => ReciboPagamentoAutonomo::className(), 'targetAttribute' => ['idReciboPagamentoAutonomoTable' => 'id']],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'acao' => 'Ação',
            'referencia' => 'Referência',
            'tabela' => 'Cadastro',
            'coluna' => 'Campo',
            'antes' => 'Antes da alteração',
            'depois' => 'Depois da alteração',
            'idUsuario' => 'Usuário',
            'idAlunoTable' => 'Id Aluno Table',
            'idEscolaTable' => 'Id Escola Table',
            'idSolicitacaoTransporteTable' => 'Id Solicitacao Transporte Table',
            'idSolicitacaoCreditoTable' => 'Id Solicitacao Credito Table',
            'idCondutorRotaTable' => 'Id Condutor Rota Table',
            'idOcorrenciaTable' => 'Id Ocorrencia Table',
            'idCondutorTable' => 'Id Condutor Table',
            'idVeiculoTable' => 'Id Veiculo Table',
            'idMarcaTable' => 'Id Marca Table',
            'idModeloTable' => 'Id Modelo Table',
            'idUsuarioTable' => 'Id Usuario Table',
            'idJustificativaTable' => 'Id Justificativa Table',
            'idNecessidadesEspeciaisTable' => 'Id Necessidades Especiais Table',
            'idConfiguracaoTable' => 'Id Configuracao Table',
            'idEmpresaTable' => 'Id Empresa Table',
            'idReciboPagamentoAutonomoTable' => 'rpa'
        ];
    }


    public static function salvarLog($params)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $key = $params['key'] . 'Table';
        $log = new Log();
        $log->data = date('Y-m-d H:i:s');
        $log->acao = $params['acao'];
        $log->referencia = $params['referencia'];
        $log->tabela = $params['tabela'];
        $log->coluna = $params['coluna'];
        $log->antes = isset($params['antes']) ? $params['antes'] : '';
        $log->depois = isset($params['depois']) ? $params['depois'] : '';
        $log->idUsuario = isset($params['idUsuario']) ? $params['idUsuario'] : \Yii::$app->User->identity->id;
        $log->$key = $params['id'];
        $log->save();
        // if(!$log->save())
        //     print_r($log->getErrors());
        // exit(1);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunoTable()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAlunoTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorTable()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutorTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutorRotaTable()
    {
        return $this->hasOne(CondutorRota::className(), ['id' => 'idCondutorRotaTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfiguracaoTable()
    {
        return $this->hasOne(Configuracao::className(), ['id' => 'idConfiguracaoTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpresaTable()
    {
        return $this->hasOne(Empresa::className(), ['id' => 'idEmpresaTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscolaTable()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscolaTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJustificativaTable()
    {
        return $this->hasOne(Justificativa::className(), ['id' => 'idJustificativaTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarcaTable()
    {
        return $this->hasOne(Marca::className(), ['id' => 'idMarcaTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModeloTable()
    {
        return $this->hasOne(Modelo::className(), ['id' => 'idModeloTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNecessidadesEspeciaisTable()
    {
        return $this->hasOne(NecessidadesEspeciais::className(), ['id' => 'idNecessidadesEspeciaisTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOcorrenciaTable()
    {
        return $this->hasOne(Ocorrencia::className(), ['id' => 'idOcorrenciaTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitacaoCreditoTable()
    {
        return $this->hasOne(SolicitacaoCredito::className(), ['id' => 'idSolicitacaoCreditoTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitacaoTransporteTable()
    {
        return $this->hasOne(SolicitacaoTransporte::className(), ['id' => 'idSolicitacaoTransporteTable']);
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
    public function getUsuarioTable()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idUsuarioTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeiculoTable()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'idVeiculoTable']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReciboPagamentoAutonomoTable()
    {
        return $this->hasOne(ReciboPagamentoAutonomo::className(), ['id' => 'idReciboPagamentoAutonomoTable']);
    }
}
