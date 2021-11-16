<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "CondutorRota".
 *
 * @property string $id id
 * @property string $idCondutor Condutor
 * @property int $turno Turno
 * @property int $sentido Sentido
 *
 * @property Condutor $condutor
 * @property Escola $escola
 */
class CondutorRota extends \yii\db\ActiveRecord
{   
    public $configuracao;
    // Usado para armazenar o antigo condutor da rota, CondutorRotaController actionSalvar
    public $oldIdCondutor = null;
    const TURNO_MANHA = 1;
    const TURNO_TARDE = 2;
    const TURNO_NOITE = 3;

    const SENTIDO_IDA = 1;
    const SENTIDO_VOLTA = 2;

    
    const ARRAY_TURNOS = [ self::TURNO_MANHA => 'MANHÃ', 
                           self::TURNO_TARDE => 'TARDE',
                           self::TURNO_NOITE => 'NOITE',
                       ];

    const ARRAY_SENTIDO = [
        self::SENTIDO_IDA => 'IDA - CASA-ESCOLA',
        self::SENTIDO_VOLTA => 'VOLTA - ESCOLA-CASA'
    ];  

    const ARRAY_SENTIDO_RESUMIDO = [
        self::SENTIDO_IDA => 'IDA',
        self::SENTIDO_VOLTA => 'VOLTA'
    ];  

    const ARRAY_CONFIGURACAO = [
       self::TURNO_MANHA.self::SENTIDO_IDA => 'MANHÃ - IDA',
       self::TURNO_MANHA.self::SENTIDO_VOLTA => 'MANHÃ - VOLTA',
       self::TURNO_TARDE.self::SENTIDO_IDA => 'TARDE - IDA',
       self::TURNO_TARDE.self::SENTIDO_VOLTA => 'TARDE - VOLTA',
       self::TURNO_NOITE.self::SENTIDO_IDA => 'NOITE - IDA',
       self::TURNO_NOITE.self::SENTIDO_VOLTA => 'NOITE - VOLTA',
    ];

    const ARRAY_VIAGEM = [
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 16,
    ];
    public $manha, $tarde, $noite, $ida, $volta, $escolas;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'CondutorRota';
    }

    public static function find()
    {
        return parent::find()->where(['>', 'rotaAtiva', 0]);
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {


        return [
            // [['idCondutor'], 'required'],
            [['id', 'idCondutor', 'turno', 'sentido','rotaAtiva','viagem'], 'integer'],
            [['id'], 'unique'],
            [['entrada','saida', 'turno', 'sentido','descricao'], 'safe'],
            [['idCondutor'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutor' => 'id']],
            // [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Cód.',
            'idCondutor' => 'Condutor',
            // 'idEscola' => 'Escola',
            'turno' => 'Período',
            'sentido' => 'Sentido',
            'manha' => 'Manhã',
            'tarde' => 'Tarde',
            'noite' => 'Noite',
            'ida' => 'Ida',
            'volta' => 'Volta',
            'configuracao' => 'Configuração',
            'entrada' => 'Entrada',
            'saida' => 'Saída',
            'descricao' => 'Descrição'
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
		
    }



    public function getConfiguracaoDisponivel(){
      $disponiveis = self::ARRAY_CONFIGURACAO;
      $atribuidos = CondutorRota::find()->where(['=','idCondutor', $this->idCondutor])->all();
       
      foreach ($atribuidos as $at) {
        unset($disponiveis[$at->turno.$at->sentido]);
      }
      return $disponiveis;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getEscola()
    // {
    //     return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
    // }

    public function getPontos()
    {
        return $this->hasMany(Ponto::className(), ['idCondutorRota' => 'id']);
    }

    public function getAlunoPonto()
    {
        return $this->hasMany(PontoAluno::className(), ['idPonto' => 'id'])->via('pontos');
    }
    public function getEscolaPonto()
    {
        return $this->hasMany(PontoEscola::className(), ['idPonto' => 'id'])->via('pontos');
    }


    public function checarVinculo($turno, $sentido){
        $vinculo = CondutorRota::find()->where(['=','turno', $turno])->andWhere(['=','sentido', $sentido])->one();
        if($vinculo)
            return true;
        return false;
    }

    public function fields()
    {
        $fields = parent::fields();

        $fields['sentido'] = 'sentidoText';   
        $fields['turno'] =   'turnoText'; 
        $fields['inicioRota'] = 'inicioRotaText';
        $fields['fimRota'] = 'fimRotaText';  
        $fields['pontos'] = 'pontos'; 
        $fields['inicioRotaIcon'] = 'inicioRotaIcon';  
        $fields['fimRotaIcon'] = 'fimRotaIcon';  
        //$fields['condutor'] =   'condutor';  
        
		
        return $fields;
    }
    public function extraFields(){
       return ['condutor'];
    }

    public function getSentidoText(){
        return $this->sentido ? self::ARRAY_SENTIDO[$this->sentido] : '';
    }

    public function getTurnoText(){
        return $this->turno ? self::ARRAY_TURNOS[$this->turno] : '';
    }
    public function getInicioRotaIcon(){
        return $this->sentido == self::SENTIDO_IDA ? 'trajeto-casa@2x.png' : 'trajeto-escola@2x.png';
    }
    public function getFimRotaIcon(){
        return $this->sentido == self::SENTIDO_IDA ? 'trajeto-escola@2x.png' : 'trajeto-casa@2x.png';
    }

    public function getInicioRotaText(){
        return $this->sentido == self::SENTIDO_IDA ? 'Casa' : 'Escola';
    }

    public function getFimRotaText(){
        return $this->sentido == self::SENTIDO_IDA ? 'Escola' : 'Casa';
    }



    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunoRota()
    {
        return $this->hasMany(AlunoRota::className(), ['idCondutorRota' => 'id']);
    }

    public function getComunicado(){
        return $this->hasMany(Comunicado::className(), ['idAluno' => 'idAluno'])->via('alunoRota');
    }

    public function getNomeRota(){
        return $this->id.' '.$this->descricao;
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
            case Usuario::PERFIL_SUPER_ADMIN: $actions = '{create}  {update} {roterizar} {delete}';  break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO: $actions = '{create} {roterizar} {update} {delete}'; break;
            case Usuario::PERFIL_SECRETARIO: $actions = ''; break;
            case Usuario::PERFIL_DIRETOR: $actions = ''; break;
            case Usuario::PERFIL_DRE: $actions = '{roterizar} '; break;
            case Usuario::PERFIL_TESC_PASSE_ESCOLAR: $actions = '{roterizar}'; break;
            case Usuario::TESC_CONSULTA: $actions = '{roterizar}';break;
            case Usuario::PERFIL_CONDUTOR: $actions = ''; break;
        }
        return $actions;
    }

    public function afterSave($insert, $atributosAlterados) {		
        parent::afterSave($insert, $atributosAlterados);
        //UPDATE
        if(!$insert) {
            foreach($atributosAlterados as $key=>$value)
            {
                if($key == 'idCondutor' && $atributosAlterados[$key] && $value != $this->$key) {
                    $condutor = Condutor::findOne($atributosAlterados['idCondutor']);
                    HistoricoMovimentacaoRota::salvar([
                        'tipo' => HistoricoMovimentacaoRota::STATUS_CONDUTOR_REMOVIDO,
                        'idCondutorRotaAnterior' => $this->id,
                        'idCondutorAnterior' => $atributosAlterados['idCondutor'],
                        'idVeiculoAnterior' => $condutor->idVeiculo,
                        'idUsuario' => \Yii::$app->User->identity->id,
                        'sentido' => $this->sentido
                    ]);

                    HistoricoMovimentacaoRota::salvar([
                        'tipo' => HistoricoMovimentacaoRota::STATUS_CONDUTOR_INSERIDO,
                        'idCondutorRotaAtual' => $this->id,
                        'idCondutorAtual' => $this->idCondutor,
                        'idVeiculoAtual' => $this->condutor->idVeiculo,
                        'idUsuario' => \Yii::$app->User->identity->id,
                        'sentido' => $this->sentido
                    ]);
                  
                }
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
			//print_r($novoRegistro);exit;
            foreach($novoRegistro as $key=>$coluna)
            {
                $this->salvarLog(Log::ACAO_INSERIR,$coluna);
            }
            HistoricoMovimentacaoRota::salvar([
                'tipo' => HistoricoMovimentacaoRota::STATUS_CONDUTOR_INSERIDO,
                'idCondutorRotaAtual' => $this->id,
                'idCondutorAtual' => $this->idCondutor,
                'idVeiculoAtual' => $this->condutor->idVeiculo,
                'idUsuario' => \Yii::$app->User->identity->id,
                'sentido' => $this->sentido
            ]);
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
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->id,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idCondutorRota',
                'id' => $this->id,
            ]);
        }
    }
    
}
