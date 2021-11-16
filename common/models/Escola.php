<?php

namespace common\models;

use Yii;
   
/**
 * This is the model class for table "Escola".
 *
 * @property string $id Código
 * @property int $tipo Tipo
 * @property string $nome Nome
 * @property string $endereco Endereço
 * @property string $lat Lat
 * @property string $lng Lng
 * @property string $telefone Telefone
 * @property string $email Email
 * @property string $codigoCie Código CIE
 * @property int $regiao
 * @property Aluno[] $alunos
 */
class Escola extends \yii\db\ActiveRecord
{    
    public $alunosEscola;

    public $inputSecretarios;
    public $inputDiretores;
    public $inputEnsino;
    public $distancia;
    public $ensino;

    const UNIDADE_MUNICIPAL = 1;
    const UNIDADE_ESTADUAL = 2;
    const UNIDADE_FILANTROPICA = 3;

    const ENSINO_INFANTIL = 1;
    const ENSINO_FUNDAMENTAL = 2;
    const ENSINO_MEDIO = 3;
    
    const ARRAY_ENSINO = [
        self::ENSINO_INFANTIL => 'ENSINO INFANTIL',
        self::ENSINO_FUNDAMENTAL => 'ENSINO FUNDAMENTAL',
        self::ENSINO_MEDIO => 'ENSINO MÉDIO',
    ];

    const ARRAY_UNIDADE = [
        1 => 'MUNICIPAL',
        2 => 'ESTADUAL',
        3 => 'FILANTRÓPICA',
    ];

    const TIPO_EMEF = 1;
    const TIPO_EE = 21;
  
    const ARRAY_TIPO_MUNICIPAL = [
        11 => 'CEDIN',
        12 => 'EMEI',
        13 => 'IMI',
        14 => 'EMEF',
        15 => 'NEI',
        16 => 'CECOI',
     ];

    const ARRAY_TIPO_ESTADUAL = [
        self::TIPO_EE => 'EE',        
    ];

    const ARRAY_TIPO_FILANTROPICA = [
        31 => 'FIL',        
        32 => 'AECE',
        33 => 'APAE',
        34 => 'CIPD',
        35 => 'ACENT',
    ]; 

    const ARRAY_TIPO = self::ARRAY_TIPO_MUNICIPAL + 
                       self::ARRAY_TIPO_ESTADUAL +
                       self::ARRAY_TIPO_FILANTROPICA;


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
        return 'Escola';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo', 'nome'], 'required'],
            [['tipo','unidade','regiao'], 'integer'],
            [['lat', 'lng','codigoCie'], 'number'],
            [['nome', 'endereco'], 'string', 'max' => 255],
            [['telefone'], 'string', 'max' => 15],
            [['email'], 'string', 'max' => 70],
            [['email'], 'email'],
            [['regiao','bairro','tipoLogradouro','complementoResidencia','numeroResidencia','cep'], 'safe'],

            [['codigoCie'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'tipo' => 'Tipo',
            'nome' => 'Nome',
            'endereco' => 'Endereço',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'telefone' => 'Telefone',
            'telefone2' => 'Telefone 2 ',
            'email' => 'E-mail',
            'codigoCie' => 'Código CIE',
            'unidade' => 'Unidade escolar',
            'inputSecretarios' => 'Secretários',
            'inputDiretores' => 'Diretores',
            'regiao' => 'Região',
            'cep' => 'CEP',
            'numeroResidencia' => 'Nº',
            'complementoResidencia' => 'Complemento',
            'tipoLogradouro' => 'Tipo',
            'bairro' => 'Bairro',
        ];
    }

    public function beforeSave($insert)
    {
        foreach($this as $key => $value) {
            $this[$key] = mb_strtoupper($value, 'utf-8');
        }
        return parent::beforeSave($insert);

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
            switch($coluna){
                case 'regiao':
                    if(isset($atributosAlterados))
                        $atributosAlterados[$coluna] = Escola::ARRAY_REGIAO[$atributosAlterados[$coluna]];
                    $this->$coluna = Escola::ARRAY_REGIAO[$this->$coluna];
                 break;
                case 'unidade': 
                    if(isset($atributosAlterados))
                        $atributosAlterados[$coluna] = Escola::ARRAY_UNIDADE[$atributosAlterados[$coluna]];
                    $this->$coluna = Escola::ARRAY_UNIDADE[$this->$coluna];
                break;
                case 'tipo':
                if(isset($atributosAlterados))
                    $atributosAlterados[$coluna] = Escola::ARRAY_TIPO[$atributosAlterados[$coluna]];
                $this->$coluna = Escola::ARRAY_TIPO[$this->$coluna];   
                break;
                default: break;
            }
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->nome,
                'tabela' => self::getTableSchema()->name,
                'coluna' => self::getAttributeLabel($coluna),
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idEscola',
                'id' => $this->id,
            ]);
        }
    }


    public function extraFields(){
       return ['alunosRota'];
    }
    public function fields()
    {
        $fields = parent::fields();

        //$fields['alunosRota'] = 'alunosRota';   
        $fields['nome'] = 'nomeCompleto';   
        // $fields['alunosEscola'] = 'alunosEscola';  
        //$fields['alunosEscola']
       // $fields['alunos'] =   'alunos';  
        
        return $fields;
    }

    public static function disponiveisCalendario(){
        $disponiveis = Escola::ARRAY_TIPO;
        $calendarioEscolas = CalendarioEscola::find()->groupBy(['tipoEscola'])->all();
        
        foreach($calendarioEscolas as $escola){
            unset($disponiveis[$escola->tipoEscola]);
        }
        return $disponiveis;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunos()
    {
        return $this->hasMany(Aluno::className(), ['idEscola' => 'id']);
    }

    //AlunosEscola
    public function getAlunosEscola(){
        return 1;
    }
    public function getAlunosRota()
    {
        return $this->hasMany(AlunoRota::className(), ['idEscola' => 'id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getSecretarios()
    {
        return $this->hasMany(EscolaSecretario::className(), ['idEscola' => 'id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getDiretores()
    {
        return $this->hasMany(EscolaDiretor::className(), ['idEscola' => 'id']);
       
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getAtendimento()
    {
        return $this->hasMany(EscolaAtendimento::className(), ['idEscola' => 'id']);
    }
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getHomologacao()
    {
        return $this->hasOne(EscolaHomologacao::className(), ['idEscola' => 'id']);
       
    }
    //     /**
    //  * @return \yii\db\ActiveQuery
    //  */
    // public function getCondutores()
    // {
    //     return $this->hasMany(CondutorRota::className(), ['idEscola' => 'id']);
    // }

    public function isAtendimento($atendimento){
        return in_array($atendimento, array_column($this->atendimento,'idAtendimento'));
    }
    
    public static function mountSelectTipo($unidade){
        switch ($unidade) {
            case self::UNIDADE_ESTADUAL:
                return Escola::ARRAY_TIPO_ESTADUAL;
                break;   
            case self::UNIDADE_MUNICIPAL:
                return Escola::ARRAY_TIPO_MUNICIPAL;
                break;
            case self::UNIDADE_FILANTROPICA:
                return Escola::ARRAY_TIPO_FILANTROPICA;
                break;
        }
    }

    // Criado para facilitar a exibição de escolas quando temos o perfil
    // Usuario::PERFIL_SECRETARIO
    // Primeiro obtemos a lista GERAL de escolas e caso SEJA um SECRETÁRIO
    // Solicitamos somente escolas que sejam atribuídas a ele (EscolaSecretario)
    // Também é necessário incluir a escola que JÁ estava salva no model para evitar conflitos
    public static function escolasPerfis($escolaAtual=''){
        $escolas =  Escola::find();

        if(Usuario::Permissao(Usuario::PERFIL_SECRETARIO) ||
            Usuario::Permissao(Usuario::PERFIL_DIRETOR) ){
            
            if(Usuario::Permissao(Usuario::PERFIL_SECRETARIO)){
                $escolas->where([
                    'in',
                    'id',
                    array_column(\Yii::$app->User->identity->secretarios, 'idEscola')
                    ]);
            }

            if( Usuario::Permissao(Usuario::PERFIL_DIRETOR) ) {
                $escolas->where([
                    'in',
                    'id',
                    array_column(\Yii::$app->User->identity->diretores, 'idEscola')
                    ]);
            }
            
            if($escolaAtual)
                $escolas->orWhere(['=', 'id',$escolaAtual->id]);
            
        }
        return $escolas->all(); 
    }

    //Distance = Km
    public static function escolasProximas($lat, $lng, $distanceKm, $tipo, $escolaAtual){
        //->andWhere(['tipo' => $tipo])
        if(!$lat or !$lng)
            return [];
        return self::find()->select("Escola.*, ( 6371 * acos (cos ( radians(".$lat.") ) * cos( radians( Escola.lat ) ) * cos( radians( Escola.lng ) - radians(".$lng.") ) + sin ( radians(".$lat.") ) * sin( radians( Escola.lat ) ))  ) AS distancia")->having('distancia < '.$distanceKm)->andWhere(['<>','Escola.id',$escolaAtual])->all();
    }

    public function getNomeCompleto(){
        return self::ARRAY_TIPO[$this->tipo].' '.$this->nome;
    }

    public static function permissaoGerenciar(){
        return Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_DISTRIBUICAO]); 
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
            case Usuario::PERFIL_SUPER_ADMIN: $actions = '{create} {view} {update} {delete}';  break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO: $actions = '{create} {view} {update} {delete}'; break;
            case Usuario::PERFIL_SECRETARIO: $actions = '{view}'; break;
            case Usuario::PERFIL_DIRETOR: $actions = '{update} {view}'; break;
            case Usuario::PERFIL_DRE: $actions = '{view}'; break;
            case Usuario::PERFIL_TESC_PASSE_ESCOLAR: $actions = '{view}'; break;
            case Usuario::TESC_CONSULTA: $actions = '{view}';break;
            case Usuario::PERFIL_CONDUTOR: $actions = ''; break;
        }
        return $actions;
    }

    public function getCalendario(){
        $calendario = CalendarioEscola::find()->where(['=','tipoEscola',$this->tipo])->one();
        if($calendario)
            return $calendario->idCalendario;
        return null;
    }

}

