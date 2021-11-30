<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Aluno".
 *
 * @property string $id Código
 * @property string $nome Nome
 * @property string $idEscola Escola
 * @property string $dataNascimento Data de Nascimento
 * @property string $nomeMae Nome da mãe
 * @property string $nomePai Nome do pai
 * @property string $RA RA
 * @property string $endereco Endereço
 * @property string $lat Latitude
 * @property string $lng Longitude
 * @property int $modalidadeBeneficio Modalidade do Benefício
 * @property string $horarioEntrada Horário de Entrada
 * @property string $horarioSaida Horário de Saída
 * @property string $turno Turno
 * @property string $distanceEscola Distância da Escola
 * @property int $barreiraFisica Barreira Física
 * @property string $idRgAluno RG do Aluno
 * @property string $idComprovanteEndereco Comprovante de Endereço
 * @property string $idRgResponsavel RG do Responsável
 * @property string $idDeclaracaoVizinhos Declaração de Vizinhos
 * @property string $idLaudoMedico Laudo Médico
 * @property string $idTransporteEspecialAdaptado Laudo Transp. Esp. Adaptado
 * @property string $idDeclaracaoInexistenciaVaga Declaração de inexistência de vaga das escolas próximas
 * @property string $telefoneResidencial Telefone Residencial
 * @property string $telefoneResidencial2 Telefone Residencial 2
 * @property string $telefoneCelular Telefone Celular 
 * @property string $telefoneCelular2 Telefone Celular 2
 *
 * @property DocumentoAluno $rgAluno
 * @property DocumentoAluno $comprovanteEndereco
 * @property DocumentoAluno $rgResponsavel
 * @property DocumentoAluno $declaracaoVizinhos
 * @property DocumentoAluno $laudoMedico
 * @property DocumentoAluno $transporteEspecialAdaptado
 * @property DocumentoAluno $declaracaoInexistenciaVaga
 * @property Escola $escola
 * @property DocumentoAluno[] $documentoAlunos
 */
class Aluno extends \yii\db\ActiveRecord
{

    public $inputCursoLivre;
    public $valeTransporte;
    public $passeEscolar;

    public $necessidadesEspeciais;
    public $necessidadeEspecial;
    public $documentoRgAluno;
    public $documentoComprovanteEndereco;
    public $documentoDeclaracaoVizinho;
    public $documentoLaudoMedico;
    public $documentoInexistenciaVaga;
    public $documentoTransporteEspecial;
    public $documentoRgResponsavel;

    // public $solicitacao;
    public $status;
    public $redeEnsino;
    public $modalidadeBeneficio;
    public $tipoFrete;
	

    const MODALIDADE_NENHUM = 0;
    const MODALIDADE_FRETE = 1;
    const MODALIDADE_PASSE = 2;

    const SEGUNDA = 1;
    const TERCA = 2;
    const QUARTA = 3;
    const QUINTA = 4;
    const SEXTA = 5;
    const SABADO = 6;
    const DOMINGO = 7;
    const ARRAY_DIAS_CURSO = [
        1 => 'SEGUNDA',
        2 => 'TERÇA',
        3 => 'QUARTA',
        4 => 'QUINTA',
        5 => 'SEXTA',
        6 => 'SÁBADO',
        7 => 'DOMINGO',
    ];



    const ARRAY_MODALIDADE = [
        //  0 => 'Nenhum',
        1 => 'FRETE',
        2 => 'PASSE ESCOLAR'
    ];
    const ARRAY_BARREIRA_FISICA = [
        0 => 'NÃO',
        1 => 'SIM'
    ];

    const STATUS_BENEFICIO_ATIVO = 1;
    const ARRAY_STATUS_BENEFICIO = [
        0 => 'INATIVO',
        1 => 'ATIVO'
    ];


    const ARRAY_SERIES = [
        1 => 'INFANTIL I',
        2 => 'INFANTIL II',
        3 => 'PRÉ I',
        4 => 'PRÉ II',
        5 => '1º EF',
        6 => '2º EF',
        7 => '3º EF',
        8 => '4º EF',
        9 => '5º EF',
        10 => '6º EF',
        11 => '7º EF',
        30 => '8º EF',
        12 => '9º EF',
        13 => '1º EM',
        14 => '2º EM',
        15 => '3º EM',
        16 => '1º T - EJA I',
        17 => '2º T - EJA I',
        18 => '3º T - EJA I',
        19 => '4º T - EJA I',
        20 => '5º T - EJA I',
        21 => '1º T - EJA II',
        22 => '2º T - EJA II',
        23 => '3º T - EJA II',
        24 => '4º T - EJA II',
        25 => '1º T - EJA EM',
        26 => '2º T - EJA EM',
        27 => '3º T - EJA EM',
        28 => 'SR - MANHÃ',
        29 => 'SR - TARDE'
    ];

    const ARRAY_TURMA = [
        1 => 'A',
        2 => 'B',
        3 => 'C',
        4 => 'D',
        5 => 'E',
        6 => 'F',
        7 => 'G',
        8 => 'H',
        9 => 'I',
        10 => 'J',
        11 => 'K',
        26 => 'L',
        12 => 'M',
        13 => 'N',
        14 => 'O',
        15 => 'P',
        16 => 'Q',
        17 => 'R',
        18 => 'S',
        19 => 'T',
        20 => 'U',
        21 => 'V',
        22 => 'X',
        23 => 'W',
        24 => 'Y',
        25 => 'Z',
    ];

	 const ARRAY_TURNO = [
		0 => 'ESCOLHA',
        1 => 'MANHÃ',
        2 => 'TARDE',
        3 => 'NOITE',     
		4 => 'INTEGRAL', 	
    ];

	
	
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Aluno';
    }

    public function beforeSave($insert)
    {
        foreach ($this as $key => $value) {
            $this[$key] = mb_strtoupper($value, 'utf-8');
        }

        if ($this->cpf)
            $this->cpf = $this->formatarCPF($this->cpf);

        if ($this->nome) {
            $this->nome = $this->cut($this->nome, "'");
            $this->nome = $this->cut($this->nome, '"');
        }
        if ($this->cpfResponsavel)
            $this->cpfResponsavel = $this->formatarCPF($this->cpfResponsavel);

        if (parent::beforeSave($insert)) {
            return true;
        }

        return false;
    }
    public function afterSave($insert, $atributosAlterados)
    {
		
        parent::afterSave($insert, $atributosAlterados);

		
			//UPDATE
			if (!$insert) {

				foreach ($atributosAlterados as $key => $value) {
					if ($atributosAlterados[$key] && $value != $this->$key) {
						$this->salvarLog(Log::ACAO_ATUALIZAR, $key, $atributosAlterados);
					}
				}
				$sqlEnderecoAluno ='select a.atualiza_endereco_renovacao from Aluno a where a.id = '.$this->id; 
				$dadosEnderecoAluno = Yii::$app->getDb()->createCommand($sqlEnderecoAluno)->queryAll();
						
				if($dadosEnderecoAluno[0]['atualiza_endereco_renovacao'] == '1'){
					
					
				}else{
					 //print_r($atributosAlterados);exit;
					// exit(1);
					// array_key_exists("idEscola", $atributosAlterados) || 
					if (array_key_exists("cep", $atributosAlterados) ||  array_key_exists("tipoLogradouro", $atributosAlterados) || array_key_exists("cidade", $atributosAlterados) || array_key_exists("bairro", $atributosAlterados) || array_key_exists("endereco", $atributosAlterados) || array_key_exists("horarioEntrada", $atributosAlterados) || array_key_exists("horarioSaida", $atributosAlterados)) {
						//print_r($atributosAlterados);exit;
						
						$this->encerrarSolicitacoesViaCadastro('ENCERRADO PELO SISTEMA. FORAM ALTERADOS CAMPOS NO CADASTRO DO ALUNO');
					}
					if (array_key_exists("idEscola", $atributosAlterados)) {
						if ($atributosAlterados['idEscola'] != $this->idEscola)
							$this->encerrarSolicitacoesViaCadastro('ENCERRADO PELO SISTEMA. FORAM ALTERADOS CAMPOS NO CADASTRO DO ALUNO');
					}
					if (array_key_exists("turno", $atributosAlterados)) {
						if ($atributosAlterados['turno'] != $this->turno)
							$this->encerrarSolicitacoesViaCadastro('ENCERRADO PELO SISTEMA. FORAM ALTERADOS CAMPOS NO CADASTRO DO ALUNO');
					}
					if (array_key_exists("numeroResidencia", $atributosAlterados)) {
						if ($atributosAlterados['numeroResidencia'] != $this->numeroResidencia)
							$this->encerrarSolicitacoesViaCadastro('ENCERRADO PELO SISTEMA. FORAM ALTERADOS CAMPOS NO CADASTRO DO ALUNO');
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
		
        
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->salvarLog(Log::ACAO_DELETAR, 'id');
            return true;
        }
        return false;
    }

    public function encerrarSolicitacoesViaCadastro($msgJustificativa = '')
    {
        // if (\Yii::$app->User->identity->editarDadosProtegidos == Usuario::PERMITIR_EDICAO_DADOS_PROTEGIDOS) {
            // return null;
        // }
        return $this->encerrarSolicitacoes($msgJustificativa);
    }
    public function encerrarSolicitacoes($msgJustificativa = '')
    {


        $solicitacoes = SolicitacaoTransporte::find()->where(['idAluno' => $this->id])->all();
		
		$sqlTodosPontos ='select idPonto from PontoAluno p where p.idAluno  = '.$this->id; 
		$todosPontos = Yii::$app->getDb()->createCommand($sqlTodosPontos)->queryAll();
		foreach($todosPontos as $pont){					
			if($pont['idPonto']){	
				\Yii::$app->db->createCommand()->delete('Ponto', ['id' => $pont['idPonto']])->execute();
			}
		}
		\Yii::$app->db->createCommand()->delete('PontoAluno', ['idAluno' => $solicitacao->idAluno])->execute();
			
        PontoAluno::removerTodasRotas($this->id);
        foreach ($solicitacoes as $solicitacao) {
            $modelStatus = new SolicitacaoStatus();
            if ($solicitacao->idRotaIda) {

                $modelStatus->idCondutor = $solicitacao->rotaIda->idCondutor;
                $modelStatus->idCondutorRota = $solicitacao->rotaIda->id;
                $modelStatus->idVeiculo = $solicitacao->rotaIda->condutor->idVeiculo;
                SolicitacaoTransporte::logRotaIda($solicitacao);
                $solicitacao->idRotaIda = null;
                // print 'IDA ';
            }
            if ($solicitacao->idRotaVolta) {
                $modelStatus->idCondutor = $solicitacao->rotaVolta->idCondutor;
                $modelStatus->idCondutorRota = $solicitacao->rotaVolta->id;
                $modelStatus->idVeiculo = $solicitacao->rotaVolta->condutor->idVeiculo;
                SolicitacaoTransporte::logRotaVolta($solicitacao);
                $solicitacao->idRotaVolta = null;
                // print 'VOLTA ';
            }
            // exit(1);
            $solicitacao->ultimaMovimentacao = date('Y-m-d');
            $solicitacao->status = SolicitacaoTransporte::STATUS_ENCERRADA;
            $solicitacao->save();

            $modelStatus->idEscola = $solicitacao->idEscola;
            $modelStatus->idAluno = $solicitacao->idAluno;
            $modelStatus->idUsuario = \Yii::$app->User->identity->id;
            $modelStatus->dataCadastro = date('Y-m-d');
            $modelStatus->status = SolicitacaoTransporte::STATUS_ENCERRADA;
            $modelStatus->idSolicitacaoTransporte = $solicitacao->id;
            if (!$msgJustificativa)
                $modelStatus->justificativa = 'ENCERRADO PELO SISTEMA. NOVA SOLICITAÇÃO VIGENTE #' . $solicitacao->id . '.';
            else
                $modelStatus->justificativa = $msgJustificativa;
            $modelStatus->mostrar = 1;
            $modelStatus->save();
			
			
			
        }
    }

    private function salvarLog($acao, $coluna, $atributosAlterados = NULL)
    {
        if ($this->$coluna) {
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => 'RA: ' . $this->RA . '-' . $this->RAdigito,
                'tabela' => self::getTableSchema()->name,
                'coluna' => self::getAttributeLabel($coluna),
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idAluno',
                'id' => $this->id,
            ]);
        }
    }

    private function cut($str, $char)
    {
        return str_replace($char, '', $str);
    }
    private function formatarCPF($cpf)
    {
        $cpf = $this->cut($cpf, '.');
        $cpf = $this->cut($cpf, '-');
        return $cpf;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ensino', 'turma', 'serie', 'numeroResidencia', 'dataNascimento', 'nome', 'idEscola', 'endereco', 'nomeMae', 'RAdigito', 'RA', 'RAdigito', 'horarioEntrada', 'horarioSaida', 'turno', 'cpfResponsavel', 'dataNascimentoResponsavel'], 'required'],
            [['idEscola', 'modalidadeBeneficio', 'barreiraFisica'], 'integer'],
            [[
                'dataNascimento',
                'horarioEntrada',
                'horarioSaida',
				'turno',
                'idRgAluno',
                'idComprovanteEndereco',
                'idRgResponsavel',
                'idDeclaracaoVizinhos',
                'idLaudoMedico',
                'documentoRgAluno',
                'idTransporteEspecialAdaptado',
                'idDeclaracaoInexistenciaVaga',
                'documentoComprovanteEndereco',
                'documentoDeclaracaoVizinho',
                'documentoLaudoMedico',
                'documentoInexistenciaVaga',
                'documentoTransporteEspecial',
                'documentoRgResponsavel',
                'necessidadesEspeciais',
                'RAdigito',
                'status',
                'justificativaBarreiraFisica',
                'cartaoValeTransporte',
                'cartaoPasseEscolar',
                'cpf',
                'distanceEscola',
                'cpfResponsavel',
                'nascimentoResponsavel',
                'rg',
                'ensino',
                'turma',
                'serie',
                'cep',
                'lat',
                'lng',
                'numeroResidencia',
                'complementoResidencia',
                'tipoLogradouro',
                'cidade',
                'bairro',
            ], 'safe'],
            [['lat', 'lng'], 'number'],
            [['cpf'], 'unique'],
            [['nome', 'RA', 'cartaoPasseEscolar', 'cartaoValeTransporte'], 'string', 'max' => 50],
            [['nomeMae', 'nomePai', 'endereco'], 'string', 'max' => 255],
            [['telefoneResidencial', 'telefoneResidencial2', 'telefoneCelular', 'telefoneCelular2'], 'string', 'max' => 16],
            [['telefoneResidencial'], 'required', 'when' => function ($model) {
                if (empty($model->telefoneResidencial) && empty($model->telefoneResidencial2) && empty($model->telefoneCelular) && empty($model->telefoneCelular2)) {
                    $this->addError('telefoneResidencial', 'É obrigatório informar pelo menos 1 número de telefone.');
                    return true;
                }
            }, 'whenClient' => "function(){
                if($('#aluno-telefoneresidencial').val() == '' && $('#aluno-telefoneresidencial2').val() == '' && $('#aluno-telefonecelular').val() == '' && $('#aluno-telefonecelular2').val() == '') {
                    true;
                } else {
                    false;
                }
            }"],
            [['telefoneResidencial2'], 'required', 'when' => function ($model) {
                if (empty($model->telefoneResidencial) && empty($model->telefoneResidencial2) && empty($model->telefoneCelular) && empty($model->telefoneCelular2)) {
                    $this->addError('telefoneResidencial2', 'É obrigatório informar pelo menos 1 número de telefone.');
                    return true;
                }
            }, 'whenClient' => "function(){
                if($('#aluno-telefoneresidencial').val() == '' && $('#aluno-telefoneresidencial2').val() == '' && $('#aluno-telefonecelular').val() == '' && $('#aluno-telefonecelular2').val() == '') {
                    true;
                } else {
                    false;
                }
            }"],
            [['telefoneCelular'], 'required', 'when' => function ($model) {
                if (empty($model->telefoneResidencial) && empty($model->telefoneResidencial2) && empty($model->telefoneCelular) && empty($model->telefoneCelular2)) {
                    $this->addError('telefoneCelular', 'É obrigatório informar pelo menos 1 número de telefone.');
                    return true;
                }
            }, 'whenClient' => "function(){
                if($('#aluno-telefoneresidencial').val() == '' && $('#aluno-telefoneresidencial2').val() == '' && $('#aluno-telefonecelular').val() == '' && $('#aluno-telefonecelular2').val() == '') {
                    true;
                } else {
                    false;
                }
            }"],
            [['telefoneCelular2'], 'required', 'when' => function ($model) {
                if (empty($model->telefoneResidencial) && empty($model->telefoneResidencial2) && empty($model->telefoneCelular) && empty($model->telefoneCelular2)) {
                    $this->addError('telefoneCelular2', 'É obrigatório informar pelo menos 1 número de telefone.');
                    return true;
                }
            }, 'whenClient' => "function(){
                if($('#aluno-telefoneresidencial').val() == '' && $('#aluno-telefoneresidencial2').val() == '' && $('#aluno-telefonecelular').val() == '' && $('#aluno-telefonecelular2').val() == '') {
                    true;
                } else {
                    false;
                }
            }"],

            // [['email'], 'email'],
            // [['idRgAluno'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoAluno::className(), 'targetAttribute' => ['idRgAluno' => 'id']],
            // [['idComprovanteEndereco'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoAluno::className(), 'targetAttribute' => ['idComprovanteEndereco' => 'id']],
            // [['idRgResponsavel'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoAluno::className(), 'targetAttribute' => ['idRgResponsavel' => 'id']],
            // [['idDeclaracaoVizinhos'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoAluno::className(), 'targetAttribute' => ['idDeclaracaoVizinhos' => 'id']],
            // [['idLaudoMedico'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoAluno::className(), 'targetAttribute' => ['idLaudoMedico' => 'id']],
            // [['idTransporteEspecialAdaptado'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoAluno::className(), 'targetAttribute' => ['idTransporteEspecialAdaptado' => 'id']],
            // [['idDeclaracaoInexistenciaVaga'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoAluno::className(), 'targetAttribute' => ['idDeclaracaoInexistenciaVaga' => 'id']],
            // [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['historicoAluno'] = 'historicoAluno';
        $fields['comunicados'] = 'comunicados';
        $fields['escola'] = 'escola';
        $fields['presenca'] = 'presencaText';
        $fields['faltaJustificada'] = 'faltaJustificada';
        return $fields;
    }

    public function getCpfFormatado()
    {
        if (strlen($this->cpf) >=  11) {
            $nbr_cpf = $this->cpf;

            $parte_um     = substr($nbr_cpf, 0, 3);
            $parte_dois   = substr($nbr_cpf, 3, 3);
            $parte_tres   = substr($nbr_cpf, 6, 3);
            $parte_quatro = substr($nbr_cpf, 9, 2);

            $monta_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";

            return $monta_cpf;
        }
        return $this->cpf;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'nome' => 'Nome',
            'idEscola' => 'Escola',
            'dataNascimento' => 'Data de nascimento',
            'nomeMae' => 'Nome da mãe',
            'nomePai' => 'Nome do responsável',
            'RA' => 'RA',
            'RAdigito' => 'RA Digito',
            'endereco' => 'Endereço',
            'lat' => 'Latitude',
            'lng' => 'Longitude',
            'modalidadeBeneficio' => 'Modalidade do benefício',
            'cartaoPasseEscolar' => 'Cartão de passe',
            'horarioEntrada' => 'Horário de entrada',
            'horarioSaida' => 'Horário de saída',
			'turno' => 'Turno',
            'distanceEscola' => 'Distância da escola',
            'barreiraFisica' => 'Barreira física',
            'idRgAluno' => 'RG do aluno',
            'idComprovanteEndereco' => 'Comprovante de endereço',
            'idRgResponsavel' => 'RG do responsável',
            'idDeclaracaoVizinhos' => 'Declaração de vizinhos',
            'idLaudoMedico' => 'Laudo médico',
            'idTransporteEspecialAdaptado' => 'Laudo transp. esp. adaptado',
            'idDeclaracaoInexistenciaVaga' => 'Declaração de inexistência de vaga das escolas próximas',
            'telefoneResidencial' => 'Telefone residencial',
            'telefoneResidencial2' => 'Telefone residencial 2',
            'telefoneCelular' => 'Telefone celular ',
            'telefoneCelular2' => 'Telefone celular 2',
            'RAdigito' => 'Dígito',
            'status' => 'Situação do benefício',
            'justificativaBarreiraFisica' => 'Justificativa para solicitação de transporte',
            'cpf' => 'CPF',
            'cartaoValeTransporte' => 'Cartão de vale transporte',
            'cpfResponsavel' => 'CPF responsável',
            'nascimentoResponsavel' => 'Data de nascimento do responsável',
            'rg' => 'RG',
            'cpfResponsavel' => 'CPF responsável',
            'dataNascimentoResponsavel' => 'Nascimento do responsável',
            'serie' => 'Ano/Série',
            'turma' => 'Turma',
            'ensino' => 'Ensino',
            'cep' => 'CEP',
            'numeroResidencia' => 'Nº',
            'complementoResidencia' => 'Complemento',
            'tipoLogradouro' => 'Tipo',
            'bairro' => 'Bairro',
			
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */




    public function getRgAluno()
    {
        return $this->hasOne(DocumentoAluno::className(), ['id' => 'idRgAluno']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComprovanteEndereco()
    {
        return $this->hasOne(DocumentoAluno::className(), ['id' => 'idComprovanteEndereco']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRgResponsavel()
    {
        return $this->hasOne(DocumentoAluno::className(), ['id' => 'idRgResponsavel']);
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
    public function getDocumentoAlunos()
    {
        return $this->hasMany(DocumentoAluno::className(), ['idAluno' => 'id']);
    }

    public function getDocRgAluno()
    {
        return $this->hasMany(DocumentoAluno::className(), ['idAluno' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_RG_ALUNO]);
    }



    public function getDocRgResponsavel()
    {
        return $this->hasMany(DocumentoAluno::className(), ['idAluno' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_RG_RESPONSAVEL]);
    }

    public function getAlunoCurso()
    {
        return $this->hasMany(AlunoCurso::className(), ['idAluno' => 'id']);
    }

    // Retorna o registro de dia da semana em que o aluno tem curso
    // $dia = Dia da semana = 0,1,2,3,4,5,6.
    public function cursoDia($dia)
    {
        return AlunoCurso::find()->where(['=', 'idAluno', $this->id])->andWhere(['=', 'dia', $dia])->one();
    }


    public function getNecessidades()
    {
        return $this->hasMany(AlunoNecessidadesEspeciais::className(), ['idAluno' => 'id']);
    }

    public function getPresencaText()
    {
        return false;
    }

    public function getFaltaJustificada()
    {
        $comunicado = Comunicado::find()->where(['idAluno' => $this->id])->andWhere(['DATE_FORMAT(data,"%Y-%m-%d")' => date('Y-m-d')])->one();
        return $comunicado ? true : false;
    }

    // public function estaEmUmaRotaIda() {
    //     if($this->alunoPonto 
    // }
    // public function estaEmUmaRotaVolta() {

    // }
    // public function getDocComprovanteEndereco()
    //     { 
    //         return $this->hasMany(DocumentoAluno::className(), ['idAluno' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_COMPROVANTE_ENDERECO]);
    //     }

    //     public function getDocDeclaracaoVizinho() 
    //     {
    //         return $this->hasMany(DocumentoAluno::className(), ['idAluno' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_DECLARACAO_VIZINHOS]);
    //     }

    //     public function getDocLaudoMedico()
    //     {
    //         return $this->hasMany(DocumentoAluno::className(), ['idAluno' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_LAUDO_MEDICO]);
    //     }

    //     public function getDocInexistenciaVaga()
    //     {
    //         return $this->hasMany(DocumentoAluno::className(), ['idAluno' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_DECLARACAO_INEXISTENCIA_VAGA]);
    //     }

    //     public function getDocTransporteEspecial()
    //     {
    //         return $this->hasMany(DocumentoAluno::className(), ['idAluno' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_DECLARACAO_TRANSPORTE_ESPECIAL]);
    //     }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitacoes()
    {
        return $this->hasMany(SolicitacaoTransporte::className(), ['idAluno' => 'id'])->orderBy(['id' => SORT_DESC]);
    }
    public function getSolicitacaoAtiva()
    {
        return $this->hasOne(SolicitacaoTransporte::className(), ['idAluno' => 'id'])->where(['=', 'status', SolicitacaoTransporte::STATUS_ATENDIDO]);
    }
    public function getSolicitacaoAtivaPasse()
    {
        return $this->hasOne(SolicitacaoTransporte::className(), ['idAluno' => 'id'])->where(['=', 'status', SolicitacaoTransporte::STATUS_DEFERIDO])->andWhere(['=', 'SolicitacaoTransporte.modalidadeBeneficio', Aluno::MODALIDADE_PASSE]);
    }
    public function getSolicitacao()
    {
        return $this->hasOne(SolicitacaoTransporte::className(), ['idAluno' => 'id'])->orderBy(['id' => SORT_DESC]);
    }

    public function getStatusNoPeriodo($inicio, $termino)
    {
        $status = 0;
        // $inicio = explode('/', $inicio);
        // $inicio = trim($inicio[2]) . '-' . trim($inicio[1]) . '-' . trim($inicio[0]);
        // $termino = explode('/', $termino);
        // $termino = trim($termino[2]) . '-' . trim($termino[1]) . '-' . trim($termino[0]);

        $solicitacoes = SolicitacaoTransporte::find()->where(['=', 'idAluno', $this->id])->andWhere(['<', 'data', $termino])->all();
        $ids = [];
        foreach ($solicitacoes as $solicitacao)
        {
            $ids[] = $solicitacao->id;

            // $inicio = \DateTime::createFromFormat( 'Y-m-d', $inicio);
            // $termino = \DateTime::createFromFormat( 'Y-m-d', $termino);
            // $data = \DateTime::createFromFormat( 'Y-m-d', $solicitacao->data);

            // // if ($data > $termino)
            // //     break;

            // if ($solicitacao->status == SolicitacaoTransporte::STATUS_ENCERRADA)
            // {
            //     if ($data < $inicio)
            //         $status = $solicitacao->status;
            // }
            // else
            //     $status = $solicitacao->status;

        }

        $historicoStatus = SolicitacaoStatus::find()->where(['IN', 'idSolicitacaoTransporte', $ids])->orderBy('id ASC')->all();

        foreach ($historicoStatus as $historico)
        {            
            $inicio = \DateTime::createFromFormat( 'Y-m-d', $inicio);
            $termino = \DateTime::createFromFormat( 'Y-m-d', $termino);
            $dataCadastro = \DateTime::createFromFormat( 'Y-m-d', $historico->dataCadastro);

            if ($dataCadastro > $termino)
                break;

            if ($historico->status == SolicitacaoTransporte::STATUS_ENCERRADA)
            {
                if ($dataCadastro < $inicio)
                    $status = $historico->status;
            }
            else
                $status = $historico->status;
        }

        return $status;
    }

    public function temPasseEscolar()
    {
        $st = $this->solicitacaoAtivaPasse;

        $temSolicitacaoCreditoPasseEscolar = Yii::$app->db->createCommand('
                SELECT 
                    SolicitacaoCredito.id 
                FROM 
                    SolicitacaoCredito  
                JOIN 
                    SolicitacaoCreditoAluno on SolicitacaoCreditoAluno.idSolicitacao = SolicitacaoCredito.id
                WHERE 
                    SolicitacaoCredito.tipoSolicitacao = ' . SolicitacaoCredito::TIPO_PASSE_ESCOLAR . ' AND
                    SolicitacaoCredito.status = ' . SolicitacaoCredito::STATUS_DEFERIDO . ' AND
                    SolicitacaoCreditoAluno.idAluno = ' . $this->id)
            ->queryOne();

        $temSolicitacaoCreditoValeTransporte = Yii::$app->db->createCommand('
                SELECT 
                    SolicitacaoCredito.id 
                FROM 
                    SolicitacaoCredito  
                JOIN 
                    SolicitacaoCreditoAluno on SolicitacaoCreditoAluno.idSolicitacao = SolicitacaoCredito.id
                WHERE 
                    SolicitacaoCredito.tipoSolicitacao = ' . SolicitacaoCredito::TIPO_VALE_TRANSPORTE . ' AND
                    SolicitacaoCredito.status = ' . SolicitacaoCredito::STATUS_DEFERIDO . ' AND
                    SolicitacaoCreditoAluno.idAluno = ' . $this->id)
            ->queryOne();

        if ($st && !boolval($temSolicitacaoCreditoPasseEscolar) && boolval($temSolicitacaoCreditoValeTransporte)) {
            return false;
        }

        return true;
    }

    public function temValeTransporte()
    {
        $temCurso = $this->alunoCurso;

        $temSolicitacaoCreditoValeTransporte = Yii::$app->db->createCommand('
            SELECT 
                SolicitacaoCredito.id 
            FROM 
                SolicitacaoCredito  
            JOIN 
                SolicitacaoCreditoAluno on SolicitacaoCreditoAluno.idSolicitacao = SolicitacaoCredito.id
            WHERE 
                SolicitacaoCredito.tipoSolicitacao = ' . SolicitacaoCredito::TIPO_VALE_TRANSPORTE . ' AND
                SolicitacaoCredito.status = ' . SolicitacaoCredito::STATUS_DEFERIDO . ' AND
                SolicitacaoCreditoAluno.idAluno = ' . $this->id)
        ->queryOne();

        if ($temCurso || $temSolicitacaoCreditoValeTransporte) {
            return true;
        }
        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistoricoAluno()
    {
        return HistoricoAluno::find()->where(['idAluno' => $this->id])->orderBy(['id' => SORT_DESC])->all();
    }

    public function getComunicados()
    {
        return $this->hasMany(Comunicado::className(), ['idAluno' => 'id'])->orderBy(['data' => SORT_DESC]);
    }

    public function getAlunoPonto()
    {
        return $this->hasOne(PontoAluno::className(), ['idAluno' => 'id']);
    }

    public function getAlunoPontoRota()
    {
        return $this->hasOne(PontoAluno::className(), ['idAluno' => 'id']);
    }

    public function getPontoAlunoIda()
    {
        return $this->hasOne(PontoAluno::className(), ['idAluno' => 'id'])->andOnCondition(['sentido' => 1]);
    }

    public function getPontoAlunoVolta()
    {
        return $this->hasOne(PontoAluno::className(), ['idAluno' => 'id'])->andOnCondition(['sentido' => 2]);
    }

    public function getMeusPontos()
    {
        return $this->hasMany(PontoAluno::className(), ['idAluno' => 'id']);
    }


    public static function alunosProximos($lat, $lng, $distanceKm, $tipo)
    {
        //->andWhere(['tipo' => $tipo])
        return self::find()->select("HistoricoAluno.idHistorico, Aluno.*, ( 6371 * acos (cos ( radians(" . $lat . ") ) * cos( radians( HistoricoAluno.lat ) ) * cos( radians( HistoricoAluno.lng ) - radians(" . $lng . ") ) + sin ( radians(" . $lat . ") ) * sin( radians( HistoricoAluno.lat ) ))  ) AS distancia")->having('distancia < ' . $distanceKm)->all();
    }

    public function getRACompleto()
    {
        return $this->RA . '-' . $this->RAdigito;
    }
    public static function permissaoGerenciar()
    {
        return Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_DISTRIBUICAO]);
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

    public static function permissaoActions()
    {
        $actions = '';
        switch (\Yii::$app->User->identity->idPerfil) {
            case Usuario::PERFIL_SUPER_ADMIN:
                $actions = '{create} {view} {update} {delete}';
                break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO:
                $actions = '{create} {view} {update} {delete}';
                break;
            case Usuario::PERFIL_SECRETARIO:
                $actions = '{create} {view} {update}';
                break;
            case Usuario::PERFIL_DIRETOR:
                $actions = '{create} {view} {update}';
                break;
            case Usuario::PERFIL_DRE:
                $actions = '{create} {view} {update} ';
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

    public function enderecoCompleto()
    {
        $endereco  = $this->tipoLogradouro ? $this->tipoLogradouro . ' ' . $this->endereco : $this->endereco;
        if ($this->numeroResidencia)
            $endereco .= ' Nº ' . $this->numeroResidencia;
        return $endereco;
    }

    public function entradaRota($idCondutor = null)
    {
        if (!$idCondutor) {
            $condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
            $idCondutor = $condutor->id;
        }

        $hist = HistoricoMovimentacaoRota::find()->where(['idAluno' => $this->id])->andWhere(['tipo' => HistoricoMovimentacaoRota::STATUS_ALUNO_INSERIDO])->andWhere(['idCondutorAtual' => $condutor->id])->orderBy(['id' => SORT_DESC])->one();
        return $hist->inicioAtendimento;
    }
}
