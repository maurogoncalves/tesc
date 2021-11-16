<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Condutor".
 *
 * @property string $id Código
 * @property string $idUsuario Usuário
 * @property string $idVeiculo Veículo
 * @property string $dataNascimento Nascimento
 * @property string $alvara Alvará
 * @property int $inscricaoMunicipal Inscrição municipal
 * @property string $cpf CPF
 * @property string $lat Lat
 * @property string $lng Lng
 * @property string $nit NIT
 * @property string $endereco Endereço
 * @property string $bairro Bairro
 * @property string $telefone Telefone
 * @property string $email Email
 * @property string $cnhRegistro Número da CNH
 * @property string $cnhValidade Validade da CNH
 * @property string $dataInicioContrato Início do contrato
 * @property string $dataFimContrato Fim do contrato
 * @property string $tipoContrato Tipo do contrato
 * @property string $valorPagoKmViagem Valor por viagem
 * @property string $idCNHCondutor CNH do condutor
 * @property string $idComprovanteEndereco Comprovante de endereço
 * @property string $idCRLV CRLV
 * @property string $idVistoriaEstadual Vistoria estadual
 * @property string $idVstoriaMunicipal Vistoria municipal
 * @property string $idApoliceSeguro Apólice do seguro
 * @property string $idContrato Contrato
 * @property double $kmViagemAtual
 * @property double $kmViagemSabadoLetivo
 * @property double $valorAF
 * @property string $pendencias Pendencias
 * @property string $folhaPonto Folha de Ponto
 * @property string $pesquisaRota Pesquisa - Rota
 *
 * @property Condutor $contrato
 * @property Condutor[] $condutors
 * @property DocumentoCondutor $cNHCondutor
 * @property DocumentoCondutor $comprovanteEndereco
 * @property DocumentoCondutor $cRLV
 * @property DocumentoCondutor $vistoriaEstadual
 * @property DocumentoCondutor $vstoriaMunicipal
 * @property DocumentoCondutor $apoliceSeguro
 * @property Usuario $usuario
 * @property Veiculo $veiculo
 * @property DocumentoCondutor[] $documentoCondutors
 */
class Condutor extends \yii\db\ActiveRecord
{
    public $anexoFotoMotorista;

    public $documentoComprovanteEndereco;
    public $documentoCNHCondutor;
    public $documentoContrato;
    public $documentoMonitorRG;
    public $documentoMonitorCPF;
    public $documentoCertidaoInscricaoMunicipal;
    public $documentoDebitosMunicipais;
    public $documentoCertidaoNegativaAcoesCiveis;

    public $documentoCRLV;
    public $documentoApoliceSeguro;
    public $documentoAutorizacaoEscolar;
    public $documentoProntuarioCNH;

    public $documentoMonitorContratoTrabalho;
    public $documentoMonitorCertidaoAntecedentesCriminais;


    public $capacidadeVeiculoCondutor;
    public $veiculoAdaptadoCondutor;

    public $inputRegiao;

    public $alocacao;

    public $condutores;
    public $idCondutor;
    public $ano;
    public $mes;
    public $diasTrabalhados;
    public $sabadoLetivo;
    public $diasExcepcionais1;
    public $viagemKm1;
    public $diasExcepcionais2;
    public $viagemKm2;
    public $valorNota;
    public $protocoloTESC;
    public $protocoloGC;
    public $lote;
    public $saldoAF;
    public $novaSenha;

    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 2;
    const ARRAY_STATUS = [
        self::STATUS_ATIVO => 'Ativo',
        self::STATUS_INATIVO => 'Inativo'
    ];

    const TIPO_VIAGEM = 1;
    const TIPO_KM = 2;
    const ARRAY_TIPO = [
                           1 => 'VIAGEM',
                           2 => 'KM',
                       ];

    const REGIAO_CENTRO = 1;
    const REGIAO_NORTE = 2;
    const REGIAO_SUL = 3;
    const REGIAO_LESTE = 4;
    const REGIAO_OESTE = 5;
    const REGIAO_SUDESTE = 6;
    const REGIAO_SFX = 7;

    const ARRAY_REGIAO = [
        self::REGIAO_CENTRO => 'CENTRO',
        self::REGIAO_NORTE => 'NORTE',
        self::REGIAO_SUL => 'SUL',
        self::REGIAO_LESTE => 'LESTE',
        self::REGIAO_OESTE => 'OESTE',
        self::REGIAO_SUDESTE => 'SUDESTE',
        self::REGIAO_SFX => 'SFX'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Condutor';
    }


    public function beforeSave($insert)
    {
    
        foreach($this as $key => $value) {
            if($key != 'fotoMotorista')
                $this[$key] = mb_strtoupper($value, 'utf-8');
        } 
        if($this->cpf)
            $this->cpf = $this->formatarCPF($this->cpf);

        if($this->valorPagoKmViagem)
            $this->valorPagoKmViagem = $this->toDecimal($this->valorPagoKmViagem);

        if (parent::beforeSave($insert)) {
            return true;
        }

        return false;
    }

    public function toDecimal($valor) {
      

        if(strpos($valor, ',')){
              $valor = str_ireplace(".","",$valor);
              $valor = str_ireplace(",",".",$valor);
        }
        return $valor;
    }
 
    
    private function cut($str, $char){
        return str_replace($char,'',$str);
    }
    private function formatarCPF($cpf){
        $cpf = $this->cut($cpf,'.');
        $cpf = $this->cut($cpf,'-');
        return $cpf; 
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'cpf', 'dataNascimento', 'folhaPonto', 'pesquisaRota'], 'required'],
            [['idUsuario', 'alvara', 'inscricaoMunicipal', 'cnhRegistro', 'tipoContrato', 'idCNHCondutor', 'idComprovanteEndereco', 'idCRLV', 'idVistoriaEstadual', 'idVstoriaMunicipal', 'idApoliceSeguro', 'idContrato','numeroApolice','status'], 'integer'],
            [['bairro','dataNascimento', 'cnhValidade', 'dataInicioContrato','documentoComprovanteEndereco',
              'documentoCNHCondutor', 'documentoCRLV', 'documentoVistoriaEstadual', 'documentoVistoriaMunicipal','anexoFotoMotorista','fotoMotorista',
              'documentoContrato','nome','cpf','dataFimContrato','idVeiculo','idEmpresa','telefoneWhatsapp','telefoneMonitor','telefoneMonitorWhatsapp','valorPagoKmViagem','regiao','rg','numeroApolice','orgaoEmissor','nomeMonitor','rgMonitor','cpfMonitor','telefone2','celular','celular2','telefoneWhatsapp','telefoneWhatsapp2', 'celularWhatsapp','celularWhatsapp2','celularMonitor','celularMonitorWhatsapp', 'maxKmDia', 'minKmDia', 'maxViagensDia','numeroResidencia','cep','complementoResidencia','tipoLogradouro','kmViagemAtual','kmViagemSabadoLetivo','valorAF', 'pendencias', 'documentoCRLV', 'documentoApoliceSeguro', 'documentoAutorizacaoEscolar', 'documentoProntuarioCNH', 'folhaPonto', 'pesquisaRota', 'novaSenha'], 'safe'],
            [['lat', 'lng'], 'number'],
            [['folhaPonto', 'pesquisaRota'], 'url'],
            [['cpf'], 'unique'],
            [['cpf'], 'string', 'max' => 15],
            [['pendencias'], 'string', 'max' => 300],
            [['nit', 'bairro', 'email'], 'string', 'max' => 50],
            [['endereco'], 'string', 'max' => 255],
            [['telefone'], 'string', 'max' => 15],

            [['orgaoEmissor'] ,'string', 'max' => 14],
            [['orgaoEmissor'] ,'match', 'pattern'=>'/^[a-zA-Z-\/]+$/', 'message' => 'Este campo deve ser somente letras'],

            [['email'], 'email'],
            // [['idContrato'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idContrato' => 'id']],
            // [['idCNHCondutor'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoCondutor::className(), 'targetAttribute' => ['idCNHCondutor' => 'id']],
            // [['idComprovanteEndereco'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoCondutor::className(), 'targetAttribute' => ['idComprovanteEndereco' => 'id']],
            // [['idCRLV'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoCondutor::className(), 'targetAttribute' => ['idCRLV' => 'id']],
            // [['idVistoriaEstadual'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoCondutor::className(), 'targetAttribute' => ['idVistoriaEstadual' => 'id']],
            // [['idVstoriaMunicipal'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoCondutor::className(), 'targetAttribute' => ['idVstoriaMunicipal' => 'id']],
            // [['idApoliceSeguro'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoCondutor::className(), 'targetAttribute' => ['idApoliceSeguro' => 'id']],
            // [['idUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['idUsuario' => 'id']],
            // [['idVeiculo'], 'exist', 'skipOnError' => true, 'targetClass' => Veiculo::className(), 'targetAttribute' => ['idVeiculo' => 'id']],
        ];
    }
 
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idUsuario' => 'Usuário',
            'idVeiculo' => 'Veículo',
            'dataNascimento' => 'Nascimento',
            'alvara' => 'Alvará',
            'inscricaoMunicipal' => 'Inscrição Municipal',
            'cpf' => 'CPF',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'nit' => 'NIT',
            'endereco' => 'Endereço',
            'bairro' => 'Bairro',
            'telefone' => 'Telefone',
            'email' => 'E-mail',
            'cnhRegistro' => 'Número da CNH',
            'cnhValidade' => 'Validade da CNH',
            'dataInicioContrato' => 'Início do Contrato',
            'dataFimContrato' => 'Fim do Contrato',
            'tipoContrato' => 'Tipo do Contrato',
            'valorPagoKmViagem' => 'Valor Pago',
            'idCNHCondutor' => 'CNH do Condutor',
            'idComprovanteEndereco' => 'Comprovante de Endereço',
            'idCRLV' => 'CRLV',
            'idVistoriaEstadual' => 'Vistoria Estadual',
            'idVstoriaMunicipal' => 'Vistoria Municipal',
            'idApoliceSeguro' => 'Apólice do Seguro',
            'idContrato' => 'Contrato',
            'telefoneMonitor' => 'Telefone Monitor',
            'documentoContrato' => 'Contrato',
            'documentoComprovanteEndereco' => 'Comprovante de Endereço',
            'documentoCNHCondutor' => 'CNH Condutor',
            'rg' => 'RG',
            'numeroApolice' => 'Núm. Apólice',
            'orgaoEmissor' => 'Órgão emissor',
            'nomeMonitor' => 'Nome',
            'rgMonitor' => 'RG',
            'cpfMonitor' => 'CPF',
            'telefoneMonitor' => 'Telefone',
            'documentoMonitorRG' => 'CNH/RG com CPF',
            'documentoMonitorCPF' => 'CPF',
            'documentoCertidaoInscricaoMunicipal' => 'Certidão de Inscrição Municipal',
            'documentoDebitosMunicipais' => 'Certidão de Débitos Municipais',
            'documentoCertidaoNegativaAcoesCiveis' => 'Certidão negativa de ações cíveis',
            'documentoMonitorContratoTrabalho' => 'Contrato de Trabalho',
            'documentoMonitorCertidaoAntecedentesCriminais' => 'Certidão de antecedentes criminais',
            'regiao' => 'Região de atuação',
            'anexoFotoMotorista' => 'Foto de perfil',
            'cep' => 'CEP',
            'numeroResidencia' => 'Nº',
            'complementoResidencia' => 'Complemento',
            'tipoLogradouro' => 'Tipo',
            'bairro' => 'Bairro',
            'inputRegiao' => 'Região',
            'status' => 'Status',
            'kmViagemAtual' => 'KM/Viagem atual',
            'kmViagemSabadoLetivo' => 'KM/Viagem Sábado letivo',
            'valorAF' => 'Valor AF (R$)',
            'pendencias' => 'Pendências',
            'documentoApoliceSeguro' => 'Apólice de Seguro Veicular',
            'documentoAutorizacaoEscolar' => 'Autorização Escolar (Vistoria Semestral) - DETRAN',
            'documentoProntuarioCNH' => 'Certidão de prontuário da CNH - DETRAN',
            'folhaPonto' => 'Folha de Ponto',
            'pesquisaRota' => 'Pesquisa - Rota',
        ];
    }

    //  public function extraFields(){
    //    return ['escolas'];
    // }
   public function fields()
    {
        $fields = parent::fields();

        $fields['escolas'] = 'escolas';
        $fields['veiculo'] = 'veiculo';
        $fields['tipoContratoText'] = 'tipoContratoText';
        $fields['valorPagoKmViagemText'] = 'valorPagoKmViagemText';
        //$fields['condutor'] =   'condutor';

        return $fields;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSaldoAFAnterior()
    {
        if ($this->valorAF)
            return $this->valorAF;

        $ultimoRegistro = ControleFinanceiro::find()->where(['=', 'idCondutor', $this->id])->orderBy('id DESC')->one();            
        if ($ultimoRegistro)
            return $ultimoRegistro->saldoAF;

        return 0;
    }
    public function getControleFinanceiro(){
        return $this->hasMany(ControleFinanceiro::className(), ['idCondutor' => 'id']);
    }
    public function getHistoricoFinanceiro($ano, $mes)
    {
        return ControleFinanceiro::find()->where(['=', 'idCondutor', $this->id])->andWhere(['=','ano', $ano])->andWhere(['=', 'mes', $mes])->one();
    }
    public function getApoliceSeguro()
    {
        return $this->hasOne(DocumentoCondutor::className(), ['id' => 'idApoliceSeguro']);
    }
    public function getRegioes(){
        return $this->hasMany(CondutorRegiao::className(), ['idCondutor' => 'id']);
    }
    public function getDocContrato()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CONTRATO]);
    }
    public function getDocCRLV()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CRLV]);
    }
    public function getDocApoliceSeguro()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_APOLICE_SEGURO]);
    }
    public function getDocAutorizacaoEscolar()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_AUTORIZACAO_ESCOLAR]);
    }
    public function getDocProntuarioCNH()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_PRONTUARIO_CNH]);
    }
    public function getDocComprovanteEndereco()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_COMPROVANTE_ENDERECO]);
    }
    public function getDocCnhCondutor()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CNH]);
    }

    public function getDocRgMonitor()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_RG_MONITOR]);
    }
    public function getDocCpfMonitor()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CPF_MONITOR]);
    }
    public function getDocContratoTrabalho()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CONTRATO_TRABALHO]);
    }
    public function getDocCertidaoAntecedentesCriminais()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CERTIDAO_ANTECEDENTES_CRIMINAIS]);
    }

    public function getDocCertidaoInscricaoMunicipal()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CERTIDAO_INSCRICAO_MUNICIPAL]);
    }

        public function getDocDebitosMunicipais()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CERTIDAO_NEGATIVA_DEBITOS_MUNICIPAIS]);
    }

        public function getDocCertidaoNegativaAcoesCiveis()
    {
        return $this->hasMany(DocumentoCondutor::className(), ['idCondutor' => 'id'])->where(['=','idTipo', TipoDocumento::TIPO_CERTIDAO_NEGATIVA_ACOES_CIVEIS]);
    }

    public function getTipoContratoText(){
        return $this->tipoContrato ? Condutor::ARRAY_TIPO[$this->tipoContrato] : '';
    }

    public function getNomePlaca(){
        return $this->veiculo ? $this->veiculo->placa.' | '.$this->nome : $this->nome;
    }
    public function getValorPagoKmViagemText(){
        return  \Yii::$app->formatter->asDecimal($this->valorPagoKmViagem,2);
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
    public function getVeiculo()
    {
        return $this->hasOne(Veiculo::className(), ['id' => 'idVeiculo']);
    }

    public function getVinculo()
    {
        return $this->hasMany(CondutorRota::className(), ['idCondutor' => 'id']);
    }

    public function getEscolas(){
       return $this->hasMany(CondutorEscola::className(), ['idCondutor' => 'id']);
    }

    public function getAlunos()
    {
      return $this->hasMany(Aluno::className(), ['id' => 'idAluno'])
      ->via('pontoAluno');
    }

    public function getPontoAluno()
    {
      return $this->hasMany(PontoAluno::className(), ['idPonto' => 'id'])
        ->via('ponto');
    }

    public function getPonto()
    {
      return $this->hasMany(Ponto::className(), ['idCondutorRota' => 'id'])
        ->via('vinculo');
    }

    // $idCondutor = Condutor Atual do veículo corrente
    public static function disponivelVeiculo($idCondutor=''){
        $condutores = Condutor::find()->andWhere('idVeiculo IS NULL');
        if($idCondutor){
            $condutores->orWhere(['=', 'id',$idCondutor]);
        }
        return $condutores->all();
    }


    public static function disponivelRota($condutor=''){
        $condutores = Condutor::find()->andWhere('idVeiculo IS NOT NULL');
        if($condutor){
            $condutores->orWhere(['=', 'id',$condutor->id]);
        }
        return $condutores->all();
    }
	
	 public static function condutoresAtivos($condutor=''){
        $condutores = Condutor::find()->andWhere('status = 1');
        if($condutor){
            $condutores->orWhere(['=', 'id',$condutor->id]);
        }
        return $condutores->all();
    }
	
    public function getCpfFormatado(){
        if(strlen($this->cpf) >=  11){
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
            case Usuario::PERFIL_SUPER_ADMIN: $actions = '{create} {view} {update} {cartaApresentacao} {folhaPonto} {delete}';  break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO: $actions = '{create} {view} {update} {cartaApresentacao} {folhaPonto} {delete}'; break;
            case Usuario::PERFIL_SECRETARIO: $actions = ''; break;
            case Usuario::PERFIL_DIRETOR: $actions = ''; break;
            case Usuario::PERFIL_DRE: $actions = ''; break;
            case Usuario::PERFIL_TESC_PASSE_ESCOLAR: $actions = '{create} {view} {update} {cartaApresentacao} {folhaPonto} {delete}'; break;
            case Usuario::TESC_CONSULTA: $actions = '{create} {update} {cartaApresentacao} {folhaPonto} {view}';break;
            case Usuario::PERFIL_CONDUTOR: $actions = '{cartaApresentacao} {folhaPonto} {view} {update}'; break;
        }
        return $actions;
    }

    public function afterSave($insert, $atributosAlterados) {
        parent::afterSave($insert, $atributosAlterados);
        //UPDATE
        if(!$insert) {
            foreach($atributosAlterados as $key=>$value)
            {
                if($atributosAlterados[$key] && $value != $this->$key)
                {
                   $this->salvarLog(Log::ACAO_ATUALIZAR,$key,$atributosAlterados);
                }
            }
        } 
        //INSERT
        else 
        {
            $novoRegistro =  $this->attributes();
            foreach($novoRegistro as $key=>$coluna)
            {
                $this->salvarLog(Log::ACAO_INSERIR,$coluna);
            }
        }
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->salvarLog(Log::ACAO_DELETAR,'id');
            return true;
        }
        return false;
    }
    public function getTelefoneValido() {
        if($this->celular && strlen($this->celular) > 5)
        return $this->celular;
        if($this->celular2 && strlen($this->celular2) > 5)
            return $this->celular2;
        if($this->telefone && strlen($this->telefone) > 5)
            return $this->telefone;
        if($this->telefone2 && strlen($this->telefone2) > 5)
            return $this->telefone2;
        return '-';
      }
    
    private function salvarLog($acao,$coluna,$atributosAlterados=NULL){
        if($this->$coluna)
        {
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->cpf,
                'tabela' => self::getTableSchema()->name,
                
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idCondutor',
                'id' => $this->id,
            ]);
        }
    }
    private function addDays($days, $date){
        return date('Y-m-d',(strtotime ( '+'.$days.' day' , strtotime ($date) ) ) );
    }
    private function removeDays($days, $date){
        return date('Y-m-d',(strtotime ( '-'.$days.' day' , strtotime ($date) ) ) );
    }
    public function getRegioesAsString()
    {

        $list = [];
        foreach ($this->regioes as $item)
             $list[] = Condutor::ARRAY_REGIAO[$item->regiao];
        return implode (', ', $list);
    }
    public function toDate($date) {
        return Yii::$app->formatter->asDate($date, "php:d/m/Y"); 
    }
    private function classAlert() {
        return 'background:#FFC90E;color:#000;padding:10px;border-radius:10px;font-weight:bold;';

    }
    private function classDanger() {
        return 'background:#ED1C24;color:#FFF;padding:10px;border-radius:10px;font-weight:bold;';
    }
    public function cnhAlerta() {
        if(!$this->cnhValidade)
            return '-';
        if($this->cnhValidade > date('Y-m-d'))
            return $this->toDate($this->cnhValidade);
        $dataApos = $this->cnhValidade;//$this->addDays(30, $this->cnhValidade);
        $datetime1 = new \DateTime($dataApos);

        $datetime2 = new \DateTime(date('Y-m-d'));
        
        $difference = $datetime1->diff($datetime2);
        $diff = $difference->days;        
        
        if($diff <= 30)
            return '<span style="'.$this->classAlert().'">'.$this->toDate($this->cnhValidade).'</span>';

            if($diff > 30)
            return '<span style="'.$this->classDanger().'">'.$this->toDate($this->cnhValidade).'</span>';
        
    }

    public function enderecoCompleto() {
        $endereco  = $this->tipoLogradouro ? $this->tipoLogradouro.' '.$this->endereco : $this->endereco;
        if($this->numeroResidencia)
            $endereco .= ' Nº '.$this->numeroResidencia;
        return $endereco;
    }
}
