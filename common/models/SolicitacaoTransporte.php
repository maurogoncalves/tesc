<?php

namespace common\models;

use Yii;
use common\models\AlunoPonto;

/**
 * This is the model class for table "SolicitacaoTransporte".
 *
 * @property int $id ID
 * @property string $idAluno Aluno
 * @property string $idEscola Escola
 * @property string $data Data
 * @property int $status Status
 * @property string $justificativaBarreiraFisica Justificativa
 * @property int $modalidadeBeneficio Benefício
 * @property string $cartaoPasseEscolar Passe escolar
 * @property string $cartaoValeTransporte Vale transporte
 * @property int $barreiraFisica Barreira física
 * @property string $distanciaEscola Distância da escola
 * @property int $idDiretor
 * @property int $idDre
 * @property string $dataStatusDiretor
 * @property string $dataStatusDre  
 *
 * @property Aluno $aluno
 * @property Escola $escola
 */
class SolicitacaoTransporte extends \yii\db\ActiveRecord
{
    public $documentoComprovanteEndereco;
    public $documentoDeclaracaoVizinho;
    public $documentoLaudoMedico;
    public $documentoInexistenciaVaga;
    public $documentoTransporteEspecial;
    public $documentoFormalizacaoSolicitacao;

    public $RA;
    public $RAdigito;
    public $nomeEscola;
    public $quantidade;
    public $EscolasProximas;

    public $necessidadeEspecial;
    public $oldRotaIda = null;
    public $oldRotaVolta= null;

    public $checkRenovacao1;
    public $checkRenovacao2;
    const NOVA_SOLICITACAO = 1;
    const RENOVACAO = 2;

    const ARRAY_NOVA_SOLICITACAO = [
        self::NOVA_SOLICITACAO => 'NOVA SOLICITAÇÃO',
        self::RENOVACAO => 'RENOVAÇÃO'
    ];
    
    const STATUS_ANDAMENTO = 1;
    const STATUS_INDEFERIDO = 2;
    const STATUS_DEFERIDO = 3;
    const STATUS_DEFERIDO_DIRETOR = 4;
    const STATUS_DEFERIDO_DRE = 5;
    const STATUS_ATENDIDO = 6;
    const STATUS_ENCERRADA = 7;
    const STATUS_CANCELADO = 9; //Usado para evetivar o cancalmento de uma sol. transp do tipo "Cancelamento"
	const STATUS_CONCEDIDO = 10;//Usado para modalidade passe
    
    const ARRAY_STATUS = [
        Self::STATUS_ANDAMENTO => 'ANDAMENTO',
        Self::STATUS_INDEFERIDO => 'DEVOLVIDO',
        Self::STATUS_DEFERIDO => 'RECEBIDO',
        Self::STATUS_DEFERIDO_DIRETOR => 'DEFERIDO PELO DIRETOR',
        Self::STATUS_ATENDIDO => 'ATENDIDO',
        Self::STATUS_DEFERIDO_DRE => 'DEFERIDO PELA DRE',
        Self::STATUS_ENCERRADA => 'ENCERRADO',
        self::STATUS_CANCELADO => 'CANCELADO',
		self::STATUS_CONCEDIDO => 'CONCEDIDO'
    ];

	 
    const MOTIVO_RENOVACAO = [
	    0 => 'ESCOLHA',
		1 => 'CONCLUINTE',
        2 => 'FALECIDO',
        3 => 'MUDOU-SE',
        4 => 'RECLASSIFICADO',     
		5 => 'TRANSFERIDO', 	
		6 => 'OUTRO', 
		7 => 'REMATRÍCULA',
		8 => 'AGUARDANDO'
    ];
	
    const TURNO_MANHA = 1;
    const TURNO_TARDE = 2;
    const TURNO_NOITE = 3;

    const ARRAY_TURNOS = [
        self::TURNO_MANHA => 'MANHÃ',
        self::TURNO_TARDE => 'TARDE',
        self::TURNO_NOITE => 'NOITE',
    ];
    private $_oldAttributes = array();
    const TIPO_FRETE_COMUM = 1;
    const TIPO_FRETE_ADAPTADO = 2;
    const ARRAY_TIPO_FRETE = [
        self::TIPO_FRETE_COMUM => 'FRETE COMUM',
        self::TIPO_FRETE_ADAPTADO => 'FRETE ADAPTADO',
    ];

    //Tipo da solicitação
    const SOLICITACAO_BENEFICIO = 1;
    const SOLICITACAO_CANCELAMENTO = 2;
    const ARRAY_TIPO_SOLICITACAO = [
        Self::SOLICITACAO_BENEFICIO => 'SOLICITAÇÃO',
        Self::SOLICITACAO_CANCELAMENTO => 'CANCELAMENTO',
    ];
    const ARRAY_MOTIVO_BARREIRA_FISICA = [
        'Rodovias e Ferrovias sem passarelas ou faixa de travessia sem semáforo' => 'Rodovias e Ferrovias sem passarelas ou faixa de travessia sem semáforo',
        'Rios, Lagos, Lagoas, Brejos, Ribeirões, Riachos, Braços de Mar sem Pontes ou Passarelas' => 'Rios, Lagos, Lagoas, Brejos, Ribeirões, Riachos, Braços de Mar sem Pontes ou Passarelas',
        'Trilhas em Matas, Serras, morros ou Locais Desertos' => 'Trilhas em Matas, Serras, morros ou Locais Desertos',
        'Divisórias Físicas Fixas - Muros ou Cercas' => 'Divisórias Físicas Fixas - Muros ou Cercas',
        'Linhas Eletrificadas' => 'Linhas Eletrificadas',
        'Vazadouros - Lixões' => 'Vazadouros - Lixões'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SolicitacaoTransporte';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //  ,'' 
            [['idAluno', 'idEscola', 'modalidadeBeneficio'], 'required'],
            //[['idAluno', 'idEscola', 'status' , 'barreiraFisica'], 'integer'],
            [
                ['idRotaVolta','idRotaIda','data', 'modalidadeBeneficio', 'distanciaEscola', 'cartaoPasseEscolar', 'cartaoValeTransporte', 'justificativaBarreiraFisica', 'barreiraFisica', 'idDiretor', 'idDre', 'dataStatusDiretor', 'dataStatusDre', 'tipoFrete', 'tipoSolicitacao', 'motivoBarreiraFisica', 'idCondutor', 'checkForm', 'checkInex', 'checkEnd', 'checkSed', 'checkMemorando', 'checkVizinho', 'EscolasProximas', 'novaSolicitacao','checkLaudoMedico','checkSolicitacaoEspecial','anoVigente','ultimaMovimentacao'], 'safe'
            ],
            // [['justificativaBarreiraFisica'], 'string'],
            // [['distanciaEscola'], 'number'],
            // [['cartaoPasseEscolar', 'cartaoValeTransporte'], 'string', 'max' => 50],
            [['cartaoPasseEscolar', 'cartaoValeTransporte','idRotaVolta','idRotaIda'], 'integer', 'min' => 0],

            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
            //['barreiraFisica', 'required', 'when' => function($model) { return $this->modalidadeBeneficio == Aluno::MODALIDADE_FRETE; } ],
        ];
    }

 
    public function afterSave($insert, $atributosAlterados)
    {
        parent::afterSave($insert, $atributosAlterados);
        //UPDATE
        if (!$insert) {

           
            foreach ($atributosAlterados as $key => $value) {
                // if($value != $this->$key) {
                //     print $value.' '.$this->$key;
                // }
                if ($atributosAlterados[$key] && $value != $this->$key) {
                    $this->salvarLog(Log::ACAO_ATUALIZAR, $key, $atributosAlterados);
                }
            }
        }
        //INSERT
        else {
            $novoRegistro =  $this->attributes();
            foreach ($novoRegistro as $key => $coluna) {
                $this->salvarLog(Log::ACAO_INSERIR, $coluna);
            }
        }

        // exit(1);
    }
    public function beforeSave($insert)
    {
        // foreach($this as $key => $value) {
        //     //$this[$key] = mb_strtoupper($value, 'utf-8');
        // }
        if($this->justificativaBarreiraFisica)
            $this->justificativaBarreiraFisica = mb_strtoupper($this->justificativaBarreiraFisica, 'utf-8');
        if($this->motivoBarreiraFisica)
            $this->motivoBarreiraFisica = mb_strtoupper($this->motivoBarreiraFisica, 'utf-8');
       
        if ($this->distanciaEscola)
            $this->distanciaEscola = $this->toDecimal($this->distanciaEscola);
        $this->oldRotaIda = $insert->idRotaIda;
        $this->oldRotaVolta = $insert->idRotaVolta;
        if (parent::beforeSave($insert)) {
            return true;
        }

        return false;
    }

    public function toDecimal($valor)
    {
        if (strpos($valor, ',')) {
            $valor = str_ireplace(".", "", $valor);
            $valor = str_ireplace(",", ".", $valor);
        }
        return $valor;
    }
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->salvarLog(Log::ACAO_DELETAR, 'id');
            return true;
        }
        return false;
    }


    private function salvarLog($acao, $coluna, $atributosAlterados = NULL)
    {
     
        if ($this->$coluna) {
            switch ($coluna) {
                case 'status':
                    if (isset($atributosAlterados))
                        $atributosAlterados[$coluna] = self::ARRAY_STATUS[$atributosAlterados[$coluna]];
                    $this->$coluna = self::ARRAY_STATUS[$this->$coluna];
                    break;
                case 'modalidadeBeneficio':
                    if (isset($atributosAlterados))
                        $atributosAlterados[$coluna] = Aluno::ARRAY_MODALIDADE[$atributosAlterados[$coluna]];
                    $this->$coluna = Aluno::ARRAY_MODALIDADE[$this->$coluna];
                    break;
                case 'barreiraFisica':
                    if (isset($atributosAlterados)) {
                        if ($atributosAlterados[$coluna] == 1) {
                            $atributosAlterados[$coluna] = 'Sim';
                            $this->$coluna = 'Sim';
                        } else {
                            $atributosAlterados[$coluna] = 'Não';
                            $this->$coluna = 'Não';
                        }
                    }
                    break;
                case 'tipoFrete':
                    if (isset($atributosAlterados))
                        $atributosAlterados[$coluna] = SolicitacaoTransporte::ARRAY_TIPO_FRETE[$atributosAlterados[$coluna]];
                    $this->$coluna = Aluno::ARRAY_MODALIDADE[$this->$coluna];
                    break;

                default:
                    break;
            }
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->id,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idSolicitacaoTransporte',
                'id' => $this->id,
            ]);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idAluno' => 'Aluno',
            'idEscola' => 'Escola',
            'data' => 'Data',
            'status' => 'Status',
            'justificativaBarreiraFisica' => 'Justificativa para solicitação',
            'modalidadeBeneficio' => 'Benefício',
            'barreiraFisica' => 'Barreira física',
            'distanciaEscola' => 'Distância da escola (Km)',
            'cartaoValeTransporte' => 'Cartão de vale transporte',
            'cartaoPasseEscolar' => 'Cartão de passe escolar',
            'documentoFormalizacaoSolicitacao' => 'Formalização da solicitação',
            'documentoFormalizacaoSolicitacao' => 'Formalização da solicitação',
            'tipoSolicitacao' => 'Tipo',
            'motivoBarreiraFisica' => 'Motivo da barreira física',
            'checkForm' => 'Formalização da Solicitação',
            'checkInex' => 'Declaração de inexistência de Vagas',
            'checkEnd' => 'Comprovante de Endereço',
            'checkVizinho' => 'Declaração de Vizinhos (opcional)',
            'checkSed' => 'Dados conforme SED',
            'checkLaudoMedico' => 'Declaração de láudo médico',
            'checkSolicitacaoEspecial' => 'Solicitação de Transporte Especial',
            'checkMemorando' => 'Memorando/Ofício',
            'ultimaMovimentacao' => 'Última movimentação',
            'checkRenovacao1' => '',
            'checkRenovacao2' => ''
     
        ];
    }

    public static function logRotaIda($solicitacao, $rota=false){
        $idRota = $solicitacao->idRotaIda;
        $saida = HistoricoMovimentacaoRota::salvar([ 
            'tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_REMOVIDO,
            'idCondutorRotaAnterior' => $idRota,
            'idCondutorAnterior' =>$rota && $rota->oldIdCondutor ? $rota->oldIdCondutor : $solicitacao->rotaIda->idCondutor,
            'idVeiculoAnterior' => $solicitacao->rotaIda->condutor->idVeiculo,
            'idSolicitacaoTransporte' => $solicitacao->id,
            'idAluno' => $solicitacao->idAluno,
            'idEscola' => $solicitacao->idEscola,
            'idUsuario' => \Yii::$app->User->identity->id,
            'sentido' => $solicitacao->rotaIda->sentido
        ]);
        
        $entrada = HistoricoMovimentacaoRota::find()
                        ->andWhere(['idCondutorRotaAtual' => $idRota])
                        ->andWhere(['idAluno' => $solicitacao->idAluno])
                        ->andWhere('idHistoricoMovimentacaoAssociado IS NULL')

                        ->orderBy(['id' => SORT_DESC])
                        ->one();
        if(!$entrada){
            $entrada = SolicitacaoTransporte::logRotaIdaInsercao($solicitacao, 'geradoViaSistema');
        }
  
        $saida->idHistoricoMovimentacaoAssociado = $entrada->id;
        $entrada->idHistoricoMovimentacaoAssociado = $saida->id;
        $entrada->save();
        $saida->save();
      
        $log = new Log();
        $log->data = date('Y-m-d H:i:s');
        $log->acao = Log::ACAO_ATUALIZAR;
        $log->referencia = $solicitacao->id;
        $log->tabela =  'SolicitacaoTransporte';
        $log->coluna = 'idRotaIda';
        $log->antes =  $solicitacao->idRotaIda;
        $log->depois = '';
        $log->idUsuario = \Yii::$app->User->identity->id;
        $log->idSolicitacaoTransporteTable = $solicitacao->id;
        $log->idCondutorRotaTable = $solicitacao->idRotaIda;
        $log->idAlunoTable = $solicitacao->idAluno;
        $log->save();
    }

    public static function logRotaVolta($solicitacao, $rota=false){
        $idRota = $solicitacao->idRotaVolta;

        $saida = HistoricoMovimentacaoRota::salvar([
            'tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_REMOVIDO,
            'idCondutorRotaAnterior' => $solicitacao->idRotaVolta,
            // REGISTRA O CONDUTOR ANTIGO, CASO EXISTA CONDUTOR ANTIGO
            'idCondutorAnterior' => $rota && $rota->oldIdCondutor ? $rota->oldIdCondutor : $solicitacao->rotaVolta->idCondutor,
            'idVeiculoAnterior' => $solicitacao->rotaVolta->condutor->idVeiculo,
            'idSolicitacaoTransporte' => $solicitacao->id,
            'idAluno' => $solicitacao->idAluno,
            'idEscola' => $solicitacao->idEscola,
            'idUsuario' => \Yii::$app->User->identity->id,
            'sentido' => $solicitacao->rotaVolta->sentido
        ]);
        $entrada = HistoricoMovimentacaoRota::find()
        ->andWhere(['idCondutorRotaAtual' => $idRota])
        ->andWhere(['idAluno' => $solicitacao->idAluno])
        ->andWhere('idHistoricoMovimentacaoAssociado IS NULL')
        ->orderBy(['id' => SORT_DESC])
        ->one();
        if(!$entrada){
            $entrada = SolicitacaoTransporte::logRotaVoltaInsercao($solicitacao, 'geradoViaSistema');
        }
        $saida->idHistoricoMovimentacaoAssociado = $entrada->id;
        $entrada->idHistoricoMovimentacaoAssociado = $saida->id;
        $entrada->save();
        $saida->save();

        $log = new Log();
        $log->data = date('Y-m-d H:i:s');
        $log->acao = Log::ACAO_ATUALIZAR;
        $log->referencia = $solicitacao->id;
        $log->tabela =  'SolicitacaoTransporte';
        $log->coluna = 'idRotaVolta';
        $log->antes =  $solicitacao->idRotaVolta;
        $log->depois = '';
        $log->idUsuario = \Yii::$app->User->identity->id;
        $log->idSolicitacaoTransporteTable = $solicitacao->id;
        $log->idCondutorRotaTable = $solicitacao->idRotaVolta;
        $log->idAlunoTable = $solicitacao->idAluno;
        $log->save();
    }

    public static function logRotaIdaInsercao($solicitacao, $geradoViaSistema=false){
        $historico = HistoricoMovimentacaoRota::salvar([
            'tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_INSERIDO,
            'idCondutorRotaAtual' => $solicitacao->idRotaIda,
            'idCondutorAtual' => $solicitacao->rotaIda->idCondutor,
            'idVeiculoAtual' => $solicitacao->rotaIda->condutor->idVeiculo,
            'idSolicitacaoTransporte' => $solicitacao->id,
            'idAluno' => $solicitacao->idAluno,
            'idEscola' => $solicitacao->idEscola,
            'idUsuario' => \Yii::$app->User->identity->id,
            'sentido' => $solicitacao->rotaIda->sentido,
            'geradoViaSistema' => $geradoViaSistema ? true : false
        ]);
        $log = new Log();
        $log->data = date('Y-m-d H:i:s');
        $log->acao = Log::ACAO_ATUALIZAR;
        $log->referencia = $solicitacao->id;
        $log->tabela =  'SolicitacaoTransporte';
        $log->coluna = 'idRotaIda';
        $log->antes = '';
        $log->depois =  $solicitacao->idRotaIda;
        $log->idUsuario = \Yii::$app->User->identity->id;
        $log->idSolicitacaoTransporteTable = $solicitacao->id;
        $log->idCondutorRotaTable = $solicitacao->idRotaIda;
        $log->idAlunoTable = $solicitacao->idAluno;
        $log->save();

        return $historico;
    }
    

    public static function logRotaVoltaInsercao($solicitacao, $geradoViaSistema=false) {
        $historico = HistoricoMovimentacaoRota::salvar([
            'tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_INSERIDO,
            'idCondutorRotaAtual' => $solicitacao->idRotaVolta,
            'idCondutorAtual' => $solicitacao->rotaVolta->idCondutor,
            'idVeiculoAtual' => $solicitacao->rotaVolta->condutor->idVeiculo,
            'idSolicitacaoTransporte' => $solicitacao->id,
            'idAluno' => $solicitacao->idAluno,
            'idEscola' => $solicitacao->idEscola,
            'idUsuario' => \Yii::$app->User->identity->id,
            'sentido' => $solicitacao->rotaVolta->sentido,
            'geradoViaSistema' => $geradoViaSistema ? true : false

        ]);
        $log = new Log();
        $log->data = date('Y-m-d H:i:s');
        $log->acao = Log::ACAO_ATUALIZAR;
        $log->referencia = $solicitacao->id;
        $log->tabela =  'SolicitacaoTransporte';
        $log->coluna = 'idRotaVolta';
        $log->antes = '';
        $log->depois =  $solicitacao->idRotaVolta;
        $log->idUsuario = \Yii::$app->User->identity->id;
        $log->idSolicitacaoTransporteTable = $solicitacao->id;
        $log->idCondutorRotaTable = $solicitacao->idRotaVolta;
        $log->idAlunoTable = $solicitacao->idAluno;
        $log->save();

        return $historico;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiretorAprovacao()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idDiretorAprovacao']);
    }

    public function getDreAprovacao()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idDreAprovacao']);
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
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }
    
      /**
     * @return \yii\db\ActiveQuery
     */
    public function getRotaIda()
    {
		if(!empty($this->aluno->pontoAlunoIda->ponto)){
			return $this->aluno->pontoAlunoIda->ponto->condutorRota;
		}else{
			return 0;
		}
        
        // return $this->hasOne(CondutorRota::className(), ['id' => 'idRotaIda']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRotaVolta()
    {
		/** 
		* mauro dia 17/08/2021
		* alteração para não dar erro para o usuário, quando excluir o condutor
		*/
		
		if(!empty($this->aluno->pontoAlunoVolta->ponto)){
			return $this->aluno->pontoAlunoVolta->ponto->condutorRota;
		}else{
			return 0;
		}
        
		
        
        // return $this->hasOne(CondutorRota::className(), ['id' => 'idRotaVolta']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEscola()
    {
        return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    }
    public function getAtendimento()
    {
        return $this->hasOne(SolicitacaoStatus::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=', 'status', self::STATUS_ATENDIDO]);
    }
    public function getRecebimento()
    {
        return $this->hasOne(SolicitacaoStatus::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=', 'status', self::STATUS_DEFERIDO]);
    }
    public function getPontoAluno()
    {
        return PontoAluno::findOne(['idAluno' => $this->idAluno]);
        //return $this->hasOne(SolicitacaoStatus::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=','status', self::STATUS_ATENDIDO]);
    }

    public function getDocComprovanteEndereco()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['id' => 'idSolicitacaoTransporte'])->where(['=', 'idTipo', TipoDocumento::TIPO_COMPROVANTE_ENDERECO]);
    }

    public function getDocEndereco()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_COMPROVANTE_ENDERECO]);
    }


    public function getDocDecVizinho()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_DECLARACAO_VIZINHOS]);
    }

    public function getDocLaudo()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_LAUDO_MEDICO]);
    }


    public function getDocInexistenciaVagas()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_DECLARACAO_INEXISTENCIA_VAGA]);
    }


    public function getDocsTransporteEspecial()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_DECLARACAO_TRANSPORTE_ESPECIAL]);
    }

    public function getDocDeclaracaoVizinho()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['id' => 'idSolicitacaoTransporte'])->where(['=', 'idTipo', TipoDocumento::TIPO_DECLARACAO_VIZINHOS]);
    }

    public function getDocLaudoMedico()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['id' => 'idSolicitacaoTransporte'])->where(['=', 'idTipo', TipoDocumento::TIPO_LAUDO_MEDICO]);
    }

    public function getDocInexistenciaVaga()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['id' => 'idSolicitacaoTransporte'])->where(['=', 'idTipo', TipoDocumento::TIPO_DECLARACAO_INEXISTENCIA_VAGA]);
    }

    public function getDocTransporteEspecial()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['id' => 'idSolicitacaoTransporte'])->where(['=', 'idTipo', TipoDocumento::TIPO_DECLARACAO_TRANSPORTE_ESPECIAL]);
    }
    public function getDocFormalizacaoSolicitacao()
    {
        return $this->hasMany(DocumentoSolicitacao::className(), ['idSolicitacaoTransporte' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_FORMALIZACAO_SOLICITACAO]);
    }

    public function getHistorico()
    {
        return $this->hasMany(SolicitacaoStatus::className(), ['idSolicitacaoTransporte' => 'id'])->orderBy(['id' => SORT_DESC]);
    }



    /**
    * @return \yii\db\ActiveQuery
    */
    public function getSolicitacaoEscolasProximas()
    {
        return $this->hasMany(SolicitacaoTransporteEscolas::className(), ['idSolicitacaoTransporte' => 'id']);
       
    }
    public static function retornarDeferido($idAluno, $rota = '', $logRemovido)
    {   
        if(in_array($idAluno, $logRemovido)){
            $mostrar = 1;
        } else {
            $mostrar = 0;
        }
        // PRECISO SABER se ele estiver em uma ÚNICA rota o sistema vai tornar ele pra deferido
        $emUmaRota = PontoAluno::find()
            ->select('idAluno, COUNT(id) as contagem, sentido')
            ->groupBy('idAluno')
            ->having(['<', 'contagem', 2])
            ->andWhere(['=','idAluno', $idAluno])
            ->one();
    //    print_r($emUmaRota);
        $solicitacao = SolicitacaoTransporte::find()
        ->andWhere(['=', 'status', self::STATUS_ATENDIDO])
        ->andWhere(['=', 'idAluno', $idAluno])
        ->one();
        if($emUmaRota){
            // print_r($idAluno);
            // print 'retornado oficialmente '.$idAluno;
      
            // print_r($solicitacao);
            if ($solicitacao) {
				
						//verificar se o aluno já está nos 2 pontos, senão coloca como recebido
			$sqlTotal ='select count(*) as total from PontoAluno p where p.idAluno  = '.$logAdicionado[0]; 
			$totalPontos = Yii::$app->getDb()->createCommand($sqlTotal)->queryAll();
			// print$totalPontos[0]->total;
			// print'-';
			// print$totPont = $totalPontos[0]->total  + 1;exit;
			if($totalPontos[0]->total == 2){
				 $solicitacao->status = self::STATUS_ATENDIDO;
			}else{
				 $solicitacao->status = self::STATUS_DEFERIDO;
			}      
                $solicitacao->ultimaMovimentacao = date('Y-m-d');
                $solicitacao->save();
                $modelStatus = new SolicitacaoStatus();
                $modelStatus->idUsuario = \Yii::$app->User->identity->id;
                $modelStatus->dataCadastro = date('Y-m-d');
                $modelStatus->status = SolicitacaoTransporte::STATUS_DEFERIDO;
                $modelStatus->idSolicitacaoTransporte = $solicitacao->id;
                $modelStatus->idCondutorRota = $rota->id;
                $modelStatus->tipo = SolicitacaoStatus::TIPO_REMOVIDO;
                $modelStatus->mostrar = $mostrar;
                    
                // $modelStatus->justificativa = 'NÃO ESTÁ ATRIBUÍDO EM NENHUMA ROTA. RETORNADO PARA DEFERIDO'; 
                if($rota){
                    $nomeCondutor = $rota->condutor->nome;
                    if($rota->oldIdCondutor)
                        $nomeCondutor = Condutor::findOne($rota->oldIdCondutor)->nome;
                    $modelStatus->justificativa = 'REMOVIDO DA ROTA #'.$rota->id.' DO CONDUTOR(A) '.$nomeCondutor.'. RETORNADO PARA RECEBIDO';
                    
                } else {
                    $modelStatus->justificativa = 'REMOVIDO DA ROTA. RETORNADO PARA RECEBIDO';
                }
                    $modelStatus->save();
                // print_r($modelStatus);
                // if(!$modelStatus->save())
                //     print_r($modelStatus->getErrors());
            }

            // CASO O ALUNO AINDA ESTEJA EM OUTRA RODA
        } else {
            if($solicitacao){
                $solicitacao->ultimaMovimentacao = date('Y-m-d');
                $solicitacao->save();
                $modelStatus = new SolicitacaoStatus();
                $modelStatus->idUsuario = \Yii::$app->User->identity->id;
                $modelStatus->dataCadastro = date('Y-m-d');
                $modelStatus->status = SolicitacaoTransporte::STATUS_ATENDIDO;
                $modelStatus->idSolicitacaoTransporte = $solicitacao->id;
                $modelStatus->idCondutorRota = $rota->id;
                $modelStatus->tipo = SolicitacaoStatus::TIPO_REMOVIDO;
                $modelStatus->mostrar = $mostrar;

                if($rota){
                    $nomeCondutor = $rota->condutor->nome;
                    if($rota->oldIdCondutor)
                        $nomeCondutor = Condutor::findOne($rota->oldIdCondutor)->nome;
                    $modelStatus->justificativa = 'REMOVIDO DA ROTA #'.$rota->id.' DO CONDUTOR(A) '.$nomeCondutor.'.';
                } else {
                    $modelStatus->justificativa = 'REMOVIDO DA ROTA.';
                }
                $modelStatus->save();
            }
        }
    }
    public static function retornarAtendido($idAluno, $rota='', $logAdicionado)
    {if(in_array($idAluno, $logAdicionado)){
        $mostrar = 1;
    } else {
        $mostrar = 0;
    }
        //self::updateAll(['status' => self::STATUS_ATENDIDO], ['status' => self::STATUS_DEFERIDO, 'idAluno' => $idAluno ] );
        
        $solicitacao = SolicitacaoTransporte::find()
            ->andWhere(['in', 'status', [self::STATUS_DEFERIDO, self::STATUS_ATENDIDO]])
            ->andWhere(['=', 'idAluno', $idAluno])
            ->one();
            // print_r($solicitacao);
            // print '<br>';
        if ($solicitacao) { 
            $solicitacao->status = self::STATUS_ATENDIDO;
            $solicitacao->ultimaMovimentacao = date('Y-m-d');
            $solicitacao->save(false); 
            $modelStatus = new SolicitacaoStatus();
            $modelStatus->idUsuario = \Yii::$app->User->identity->id;
            $modelStatus->dataCadastro = date('Y-m-d');
            $modelStatus->status = SolicitacaoTransporte::STATUS_ATENDIDO;
            $modelStatus->idSolicitacaoTransporte = $solicitacao->id;
            $modelStatus->idCondutorRota = $rota->id;
            $modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
            $modelStatus->mostrar = $mostrar;
            if($rota)
                $modelStatus->justificativa = 'ATRIBUÍDO EM ROTA. ROTA #'.$rota->id.' DO CONDUTOR '.$rota->condutor->nome;
            else
                $modelStatus->justificativa = 'EM ROTA';
                // $modelStatus->justificativa = ' ';
            $modelStatus->save(false);
        }
    }



    public static function permissaoCriar()
    {
        $permissoes = self::permissaoActions();
        return strstr($permissoes, '{create}');
    }
    public static function permissaoEditar()
    {
        $permissoes = self::permissaoActions();
        return strstr($permissoes, '{update}');
    }
    public static function permissaoRemover()
    {
        $permissoes = self::permissaoActions();
        return strstr($permissoes, '{delete}');
    }
	
	 public static function permissaoIrmao(){
        $permissoes = self::permissaoActions();
        return strstr($permissoes,'{verIrmao}');
    }

    public static function permissaoActions()
    {
        $actions = '';
        switch (\Yii::$app->User->identity->idPerfil) {
            case Usuario::PERFIL_SUPER_ADMIN:
                $actions = '{create} {view} {update} {delete} {verIrmao}';
                break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO:
                $actions = '{create} {view} {update} {delete} {verIrmao}{verIrmao}';
                break;
            case Usuario::PERFIL_SECRETARIO:
                $actions = '{create} {view} {update} ';
                break;
            case Usuario::PERFIL_DIRETOR:
                $actions = '{create} {view} {update}';
                break;
            case Usuario::PERFIL_DRE:
                $actions = '{create} {view} {update}';
                break;
            case Usuario::PERFIL_TESC_PASSE_ESCOLAR:
                $actions = '{view}';
                break;
            case Usuario::TESC_CONSULTA:
                $actions = '{view}';
                break;
            case Usuario::PERFIL_CONDUTOR:
                $actions = '';
                break;
        }
        return $actions;
    }
    public function getUltimoCondutorIda(){
        // CASO AINDA EXISTA UMA ROTA DE IDA, ENTÃO A SOLICITAÇÃO AINDA ESTÁ ATIVA
        if($this->aluno->solicitacaoAtiva->idRotaIda) {
            return $this->aluno->solicitacaoAtiva->rotaIda->condutor;
        } else {
            $relation = HistoricoMovimentacaoRota::find()
            // ->andWhere(['idSolicitacaoTransporte' => $this->id])
            ->andWhere(['idAluno' => $this->idAluno])
            ->andWhere(['tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_REMOVIDO])
            ->andWhere(['sentido' => CondutorRota::SENTIDO_IDA])
            ->orderBy(['id' => SORT_DESC])
            ->one();
            return $relation->condutorAnterior;
        }
    } 
    public function getUltimoCondutorVolta(){
         // CASO AINDA EXISTA UMA ROTA DE IDA, ENTÃO A SOLICITAÇÃO AINDA ESTÁ ATIVA
         if($this->aluno->solicitacaoAtiva->idRotaVolta) {
            // print 'ok';
            return $this->aluno->solicitacaoAtiva->rotaVolta->condutor;
        } else {
            $relation = HistoricoMovimentacaoRota::find()
            // ->andWhere(['idSolicitacaoTransporte' => $this->id])
            ->andWhere(['idAluno' => $this->idAluno])
            ->andWhere(['tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_REMOVIDO])
            ->andWhere(['sentido' => CondutorRota::SENTIDO_VOLTA])
            ->orderBy(['id' => SORT_DESC])
            ->one();
            return $relation->condutorAnterior;
        }
    }
    public function getNome()
    {
        return 'Nº ' . $this->id . ' | ' . $this->aluno->nome;
    }


    
  public static function addEscolaArr($key, $solicitacao, &$escolasArr, &$totaisArr) {
    if(!isset($escolasArr[$solicitacao->idEscola])) {
     
      $escolasArr[$solicitacao->idEscola] = [
        'solicitacao' => [],
        'STATUS_ANDAMENTO' => ['BENEFICIO' => 0, 'CANCELAMENTO' => 0, 'TOTAL' => 0],
        'STATUS_INDEFERIDO' => ['BENEFICIO' => 0, 'CANCELAMENTO' => 0, 'TOTAL' => 0],
        'STATUS_DEFERIDO' => ['BENEFICIO' => 0, 'CANCELAMENTO' => 0, 'TOTAL' => 0],
        'STATUS_DEFERIDO_DIRETOR' => ['BENEFICIO' => 0, 'CANCELAMENTO' => 0, 'TOTAL' => 0],
        'STATUS_ATENDIDO' => ['BENEFICIO' => 0, 'CANCELAMENTO' => 0, 'TOTAL' => 0],
        'STATUS_DEFERIDO_DRE' => ['BENEFICIO' => 0, 'CANCELAMENTO' => 0, 'TOTAL' => 0],
        'STATUS_ENCERRADA' => ['BENEFICIO' => 0, 'CANCELAMENTO' => 0, 'TOTAL' => 0],
        'STATUS_CANCELADO' => ['BENEFICIO' => 0, 'CANCELAMENTO' => 0, 'TOTAL' => 0],
      ];
    }

    if($solicitacao->tipoSolicitacao == SolicitacaoTransporte::SOLICITACAO_BENEFICIO) {
        $escolasArr[$solicitacao->idEscola][$key]['BENEFICIO'] += 1;
        $totaisArr['SOLICITACOES_PENDENTES_BENEFICIO'] += 1;
    } else {
        $escolasArr[$solicitacao->idEscola][$key]['CANCELAMENTO'] += 1;
        $totaisArr['SOLICITACOES_PENDENTES_CANCELAMENTO'] += 1;

    }
    $escolasArr[$solicitacao->idEscola][$key]['TOTAL'] += 1;
    $totaisArr['TOTAL'] += 1;
  }

  public static function agruparSolicitacoesPendentesPorEscola() {
    $solicitacoes = SolicitacaoTransporte::find()->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')->all();
    $escolasArr = [];
    
    // - Exibir Totalizador por “Tipo” de Solicitação (Benefício/Cancelamento) / Solicitações Pendentes (Benefício) 
    // – Soma das Colunas Benefício/Andamento e Benefício/Deferido pela Diretor e Solicitações Pendentes (Cancelamento) – Soma das Colunas Cancelamento/Andamento e Cancelamento/Deferido pela Diretor;
    // - Exibir Totalizador Geral / Total de Solicitações Pendentes;
    $totaisArr = [
      'SOLICITACOES_PENDENTES_BENEFICIO' => 0,  //Solicitações Pendentes (Benefício) 
      'SOLICITACOES_PENDENTES_CANCELAMENTO' => 0,
      'SOLICITACOES_PENDENTES_DEFERIDO_DIRETOR' => 0,
      'SOLICITACOES_PENDENTES_ANDAMENTO' => 0,
      'TOTAL' => 0,

    ];
  
    foreach($solicitacoes as $s) {
      
      // $escolas[$s->idEscola]['solicitacao'][] = $s;
      switch($s->status) {
        case SolicitacaoTransporte::STATUS_ANDAMENTO:
            $totaisArr['SOLICITACOES_PENDENTES_ANDAMENTO'] += 1;
          SolicitacaoTransporte::addEscolaArr('STATUS_ANDAMENTO', $s, $escolasArr, $totaisArr);
          break;
        // case SolicitacaoTransporte::STATUS_INDEFERIDO:
        //   $this->addEscolaArr('STATUS_INDEFERIDO', $s, $escolasArr);
        //   break;
        // case SolicitacaoTransporte::STATUS_DEFERIDO:
        //   $this->addEscolaArr('STATUS_DEFERIDO', $s, $escolasArr);
        //   break;
        case SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR:
        if($s->escola->tipo == Escola::TIPO_EE) {
            $totaisArr['SOLICITACOES_PENDENTES_DEFERIDO_DIRETOR'] += 1;
          
          SolicitacaoTransporte::addEscolaArr('STATUS_DEFERIDO_DIRETOR', $s, $escolasArr, $totaisArr);
        }
            
          break;
        // case SolicitacaoTransporte::STATUS_ATENDIDO:
        //   $this->addEscolaArr('STATUS_ATENDIDO', $s, $escolasArr);
        //   break;
        // case SolicitacaoTransporte::STATUS_DEFERIDO_DRE:

        //   $this->addEscolaArr('STATUS_DEFERIDO_DRE', $s, $escolasArr);
        //   break;
        // case SolicitacaoTransporte::STATUS_ENCERRADA:
        //   $this->addEscolaArr('STATUS_ENCERRADA', $s, $escolasArr);
        //   break;
        // case SolicitacaoTransporte::STATUS_CANCELADO:
        //   $this->addEscolaArr('STATUS_CANCELADO', $s, $escolasArr);
        //   break;
        default: break;
      }
    }

    $idsEscolas = [];
    foreach($escolasArr as $key=>$value) {
      $idsEscolas[] = $key;
    }
   
    $escolas = Escola::find()->where(['in', 'id', $idsEscolas ])->orderBy(['nome' => SORT_ASC])->all();
    $GLOBALS['escolasArr'] = $escolasArr;

    return ['escolasArr' => $escolasArr, 'totaisArr' => $totaisArr, 'escolas' => $escolas];
  }
}
