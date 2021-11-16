<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Usuario".
 *
 * @property string $id Código
 * @property string $idPerfil Perfil
 * @property string $username Nome de Usuário
 * @property string $email Email
 * @property string $authKey AuthKey
 * @property string $passwordHash Senha
 * @property string $passwordResetToken Recuperação de Senha
 * @property string $idFirebase Firebase
 * @property int $status Ativo
 * @property string $imagem Imagem
 */
class Usuario extends \yii\db\ActiveRecord  implements \yii\web\IdentityInterface
{
    public $password;
    public $password2;
    public $rememberMe;
    public $inputGrupo;
    public $senhaAntiga;


    const STATUS_INATIVO = 0;
    const STATUS_ATIVO = 1;
    const STATUS_PENDENTE = 2;

    const PERFIL_SUPER_ADMIN = 1;
    const PERFIL_SECRETARIO = 2;
    const PERFIL_DIRETOR = 3;
    const PERFIL_DRE = 4;
    // const PERFIL_SUPERVISOR_TRANSPORTE = 5;
    const PERFIL_TESC_DISTRIBUICAO = 6;
    const PERFIL_TESC_PASSE_ESCOLAR = 7;
    const TESC_CONSULTA = 8;
    const PERFIL_CONDUTOR = 9;
    const PERFIL_RESPONSAVEL = 10;
    CONST PERFIL_AGUARDANDO_ADMINISTRADOR = 11;

    CONST PERMITIR_EDICAO_DADOS_PROTEGIDOS = 1;
    const ARRAY_PERFIS = [ 1 => 'Supervisor de Transporte', 
                           2 => 'Secretário',
                           3 => 'Diretor',
                           4 => 'DRE',
                           //5 => 'Supervisor de Transporte',
                           6 => 'TESC Distribuição',
                           7 => 'TESC Passe Escolar',
                           8 => 'TESC Consulta',
                           9 => 'Condutor',
                           10 => 'Responsável',
                           
                       ];
   const ARRAY_STATUS = [
                           1 => 'Ativo',
                           0 => 'Inativo',
                       ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Usuario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idPerfil','username'], 'required'],
            [['password','idPerfil','idPerfil', 'authKey', 'passwordHash', 'passwordResetToken','rg','cpf','ultimoLogin','idPortal','editarDadosProtegidos', 'senhaAntiga'], 'safe'],
            ['password2', 'compare', 'compareAttribute' => 'password', 'operator' => '==', 'message' => 'As senhas não são iguais'],
            //  ['password', 'compare', 'compareAttribute' => 'password2', 'operator' => '==', 'message' => 'As senhas não são iguais'],

            [['idPerfil', 'status','idPortal'], 'integer'],
            [['username','nome'], 'string', 'max' => 50],
            [['username'], 'unique'],
            [['email'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['authKey'], 'string', 'max' => 32],
            [['passwordHash', 'passwordResetToken', 'idFirebase'], 'string', 'max' => 255],
            [['imagem'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idPerfil' => 'Perfil',
            'idPortal' => 'Código do Portal de autenticação',
            'username' => 'Login',
            'email' => 'E-mail',
            'authKey' => 'AuthKey',
            'password' => 'Senha',
            'passwordHash' => 'Senha',
            'passwordResetToken' => 'Recuperação de senha',
            'idFirebase' => 'Firebase',
            'status' => 'Ativo',
            'imagem' => 'Imagem',
            'inputGrupo' => 'Grupos',
            'cpf' => 'CPF',
            'rg' => 'RG',
            'password2' => 'Repita a senha',
            'ultimoLogin' => 'Último login',
            'senhaAntiga' => 'Senha antiga'
        ];
    }
   public function beforeSave($insert)
    {
        if($this->nome)
            $this->nome = mb_strtoupper($this->nome,'utf-8');
            
        if($this->cpf)
            $this->cpf = $this->formatarCPF($this->cpf);
            
        // if($this->username)
        //     $this->username = $this->formatarCPF($this->username);
        if (parent::beforeSave($insert)) {
            if(!$this->passwordHash){
                $config = Configuracao::setup();
                $this->setPassword($config->senhaPadrao);
                $this->generateAuthKey();
                $this->generatePasswordResetToken();
            }
            return true;
        } else {
            return false;
        }
    }

   private function cut($str, $char){
        return str_replace($char,'',$str);

    }
    private function formatarCPF($cpf){
        $cpf = $this->cut($cpf,'.');
        $cpf = $this->cut($cpf,'-');
        return $cpf; 
    }
    // go horse
    public static function limparCPF($cpf){
        $cpf = str_replace('.','',$cpf);
        $cpf =str_replace('-','',$cpf);
        return $cpf; 
    }
    public function fields()
    {
        $fields = parent::fields();

        $fields['condutor'] = 'condutor';   
        
        return $fields;
    }

        /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {   
        $usuario = static::findOne(['id' => $id, 'status' => self::STATUS_ATIVO]);
        if($usuario->idPerfil == Usuario::PERFIL_RESPONSAVEL)
            return null;
        return $usuario;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['authKey' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        //return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        return static::findOne(['username' => $username, 'status' => self::STATUS_ATIVO]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'passwordResetToken' => $token,
            'status' => self::STATUS_ATIVO,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        //echo Yii::$app->security->generatePasswordHash($password);
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }

    public function getMeuPerfil()
    {
        return(self::ARRAY_PERFIS[$this->idPerfil]);
    }

    public function getMeuStatus()
    {
        
        return(self::ARRAY_STATUS[$this->status]);
    }

  /**
    * @return \yii\db\ActiveQuery
    */
    public function getSecretarios()
    {
        return $this->hasMany(EscolaSecretario::className(), ['idUsuario' => 'id']);
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getDiretores()
    {
        return $this->hasMany(EscolaDiretor::className(), ['idUsuario' => 'id']);
       
    }

    public static function permissao($idPerfil){
        return \Yii::$app->User->identity->idPerfil == $idPerfil;
    }

    public static function permissoes($perfis = []){
        if($perfis) 
            foreach ($perfis as $perfil) 
                if(\Yii::$app->User->identity->idPerfil == $perfil)
                    return true;
        return false;
    }
    
    public function getGrupos(){
        return $this->hasMany(UsuarioGrupo::className(), ['idUsuario' => 'id']);
    }

    
    /**
    * @return \yii\db\ActiveQuery
    */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['idUsuario' => 'id']);
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
            if($coluna == 'passwordHash'){
                $this->$coluna = 'SENHA DEFINIDA/ALTERADA';
                if( isset($atributosAlterados))
                $atributosAlterados[$coluna] = 'SENHA DEFINIDA/ALTERADA';
            }
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->username.' '.$this->cpf,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idUsuario',
                'id' => $this->id,
            ]);
        }
    }
       public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), 3600 * 24 * 30);
        } else {
            return false;
        }
    }

    public static function r(){
        return \Yii::$app->User->identity->editarDadosProtegidos == Usuario::PERMITIR_EDICAO_DADOS_PROTEGIDOS;
    }
    
}
