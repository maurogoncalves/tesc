<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "SolicitacaoCredito".
 *
 * @property int $id Código
 * @property string $idEscola Escola
 * @property string $inicio Início
 * @property string $fim Fim
 * @property string $criado Criado
 *
 * @property Escola $escola
 * @property SolicitacaoCreditoAluno[] $solicitacaoCreditoAlunos
 */
class SolicitacaoCredito extends \yii\db\ActiveRecord
{
    
    public $nomeEscola;
    public $valor;
    public $quantidade;

    const STATUS_EM_ANDAMENTO = 1;
    const STATUS_EFETIVADA = 2;
    const STATUS_DEFERIDO = 3;
    const STATUS_DEFERIDO_DIRETOR = 4;
    const STATUS_DEFERIDO_DRE = 5;
    const STATUS_INDEFERIDO = 8;

    const ARRAY_STATUS = [ 
        Self::STATUS_EM_ANDAMENTO => 'Andamento',
        Self::STATUS_EFETIVADA => 'Efetivado',
        Self::STATUS_INDEFERIDO => 'Devolvido',
        Self::STATUS_DEFERIDO => 'Recebido',
        Self::STATUS_DEFERIDO_DIRETOR => 'Deferido pelo diretor',
        Self::STATUS_DEFERIDO_DRE => 'Deferido pela DRE',
    ];

    const TIPO_PASSE_ESCOLAR = 1;
    const TIPO_VALE_TRANSPORTE = 2;
    const TIPO_CREDITO_ADMINISTRATIVO = 3;
    const TIPO = [
        self::TIPO_PASSE_ESCOLAR => 'Passe Escolar',
        self::TIPO_VALE_TRANSPORTE => 'Vale Transporte',
        self::TIPO_CREDITO_ADMINISTRATIVO => 'Crédito Administrativo',
    ];

    const TIPO_SEM_CREDITO_ADM = [
        self::TIPO_PASSE_ESCOLAR => 'Passe Escolar',
        self::TIPO_VALE_TRANSPORTE => 'Vale Transporte',
    ];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SolicitacaoCredito';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // 'inicio', 'fim','status',
            [['idEscola'], 'required'],
            [['idEscola'], 'integer'],
            [['saldoRemanescente','numeroCartaoAdministrativo','dataTransferencia','valorTransferido','mesFim','mesInicio','diasLetivosFecharMes','antiUe','saldoFinalMes','inicio', 'fim', 'criado','creditoAdministrativo', 'valorNecessarioTotal', 'saldoRestante','diasLetivosRestantes', 'diasLetivosMes', 'saldoRestanteCartoes', 'valorCreditado'], 'safe'],
            [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
            [['numeroCartaoAdministrativo'], 'string', 'max' => 10],
            // [['creditoAdministrativo'], 'integer', 'min' => 30, 'max' => 100],
            // ['mesInicio', 'compare', 'compareAttribute' => 'mesFim', 'operator' => '<=', 'message' => 'Início não pode ser maior que Fim'],
            // ['mesFim', 'compare', 'compareAttribute' => 'mesInicio', 'operator' => '>=', 'message' => 'Fim não deve ser menor que Início'],
        ];
    }
    //
    

    
    public function beforeSave($insert)
    {
        if($this->saldoRemanescente)
            $this->saldoRemanescente = $this->toDecimal($this->saldoRemanescente);

        if($this->creditoAdministrativo)
            $this->creditoAdministrativo = $this->toDecimal($this->creditoAdministrativo);
        
        if($this->valorTransferido)
            $this->valorTransferido = $this->toDecimal($this->valorTransferido);
        
        if($this->dataTransferencia){
            $data = \DateTime::createFromFormat('d/m/Y', $this->dataTransferencia);
            if ($data)
                $this->dataTransferencia = $data->format('Y-m-d');
        }
         
    
            if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
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
 
    
    private function salvarLog($acao,$coluna,$atributosAlterados=NULL){
        if($this->$coluna)
        {
            // switch($coluna){
            //     case 'status':
            //         if(isset($atributosAlterados))
            //             $atributosAlterados[$coluna] = self::ARRAY_STATUS[$atributosAlterados[$coluna]];
            //         $this->$coluna = self::ARRAY_STATUS[$this->$coluna];
            //      break;
            //      case 'modalidadeBeneficio':
            //         if(isset($atributosAlterados))
            //             $atributosAlterados[$coluna] = Aluno::ARRAY_MODALIDADE[$atributosAlterados[$coluna]];
            //         $this->$coluna = Aluno::ARRAY_MODALIDADE[$this->$coluna];
            //      break;
            //      case 'barreiraFisica':
            //         if(isset($atributosAlterados)){
            //             if($atributosAlterados[$coluna] == 1){
            //                 $atributosAlterados[$coluna] = 'Sim';
            //                 $this->$coluna = 'Sim';
            //             } else {
            //                 $atributosAlterados[$coluna] = 'Não';
            //                 $this->$coluna = 'Não';
            //             }
            //         }  
            //      break;
            //      case 'tipoFrete':
            //      if(isset($atributosAlterados))
            //          $atributosAlterados[$coluna] = SolicitacaoTransporte::ARRAY_TIPO_FRETE[$atributosAlterados[$coluna]];
            //      $this->$coluna = Aluno::ARRAY_MODALIDADE[$this->$coluna];
            //     break;
                
            //     default: break;
            // }
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->id,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idSolicitacaoCredito',
                'id' => $this->id,
            ]);
        }
    }

    public function toDecimal($valor) {
        if(strpos($valor, ',')){
              $valor = str_ireplace(".","",$valor); 
              $valor = str_ireplace(",",".",$valor); 
        }
        return $valor; 
    }

   
    public function toReal($valor){
        return number_format(round($valor,2), 2, ',', '.');
        
    }

    public function getCreditoAdministrativoReal(){
        return $this->toReal($this->creditoAdministrativo);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idEscola' => 'Unidade Escolar',
            'inicio' => 'Início',
            'fim' => 'Fim',
            'criado' => 'Criado',
            'status' => 'Status',
            'mesInicio' => 'Início',
            'mesFim' => 'Fim',
            'valorTransferido' => 'Valor a ser transferido',
            'dataTransferencia' => 'Data da Transferência',
            'numeroCartaoAdministrativo' => 'Número do cartão administrativo',
            'creditoAdministrativo' => 'Valor Solicitado',
            'tipoSolicitacao' => 'Tipo da Solicitação',
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
    public function getSolicitacaoCreditoAlunos()
    {
        return $this->hasMany(SolicitacaoCreditoAluno::className(), ['idSolicitacao' => 'id']);
    }

    public function getHistorico(){
        return $this->hasMany(SolicitacaoCreditoStatus::className(), ['idSolicitacaoCredito' => 'id'])->orderBy(['id'=>SORT_DESC]);
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
            case Usuario::PERFIL_SUPER_ADMIN: $actions = '{create} {view}  {relatorio} {delete}';  break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO: $actions = '{create} {view} {relatorio}  {delete}'; break;
            case Usuario::PERFIL_SECRETARIO: $actions = '{create} {view} {relatorio} '; break;
            case Usuario::PERFIL_DIRETOR: $actions = '{create} {view} {relatorio} '; break;
            case Usuario::PERFIL_DRE: $actions = '{view} {relatorio} '; break;
            case Usuario::PERFIL_TESC_PASSE_ESCOLAR: $actions = '{view} {relatorio} '; break;
            case Usuario::TESC_CONSULTA: $actions = '{view} {relatorio}';break;
            case Usuario::PERFIL_CONDUTOR: $actions = ''; break;
        }
        return $actions;
    }

    public static function finalDeSemana($start, $end, $type='int'){
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $end->modify('+1 day');
        $interval = $end->diff($start);
        // total days
        $days = $interval->days;
        $daysArr = [];
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        
        // best stored as array, so you can add more than one
        //$holidays = array('2012-09-07');
        
        foreach($period as $dt) {
            $curr = $dt->format('D');
            if ($curr == 'Sat' or $curr == 'Sun') {

               $daysArr[] = $dt->format("Y-m-d");
            } else {
                $days--;
            }
        
        }
        if($type == 'int')
            return $days;
        return $daysArr;
    }
   public static function diasUteis($start, $end, $type='int'){
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $end->modify('+1 day');
        $interval = $end->diff($start);
        // total days
        $days = $interval->days;
        $daysArr = [];
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        
        // best stored as array, so you can add more than one
        //$holidays = array('2012-09-07');
        
        foreach($period as $dt) {
            $curr = $dt->format('D');
            
            // substract if Saturday or Sunday
            if ($curr == 'Sat' || $curr == 'Sun') {
                $days--;
            }

            if ($curr != 'Sat' && $curr != 'Sun') {
               $daysArr[] = $dt->format("Y-m-d");
            }
        
        }
        if($type == 'int')
            return $days;
        return $daysArr;
    }
    public static function workingDays($st, $ed, $idCalendario=null){

        $start = new \DateTime($st);
        $end = new \DateTime($ed);
        $end->modify('+1 day');
        $interval = $end->diff($start);

    
       

        $days = $interval->days;
        $daysArr = [];
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        
        foreach($period as $dt) {
            $curr = $dt->format('D');
            $currDate = $dt->format('Y-m-d');
            // substract if Saturday or Sunday
            if ($curr == 'Sat' || $curr == 'Sun') {
                $days--;
            }

            // if(in_array($currDate,$calendarioDiasArr)){
                
            // }
        }
        // print $idCalendario.'<br>'.$st.'<br>'.$ed;
        //Pega os dias registrados no calendário por tipo de escola
   
        if($idCalendario){
            $diasRegistrados = CalendarioDia::find()
                                                    ->where(['>=','data',$st])
                                                    ->andWhere(['<=','data',$ed])
                                                    ->andWhere(['=','idCalendario',$idCalendario])
                                                    ->all();
            foreach($diasRegistrados as $dia) {
                switch($dia->tipo){
                    case CalendarioDia::TIPO_COM_AULA: 
                        $days++;
                    break;
                    case CalendarioDia::TIPO_SEM_AULA: 
                        $days--;
                    break;
                }
            }
        }

        return $days;
    }

    public function getNome(){
        return 'Nº '.$this->id.' | '.$this->escola->nome;
    }
}
 