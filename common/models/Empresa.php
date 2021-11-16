<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Empresa".
 *
 * @property int $id
 * @property string $cnpj CNPJ
 * @property string $nomeFantasia Nome Fantasia
 * @property string $razaoSocial Razão Social
 * @property string $endereco Endereço
 * @property string $lat Lat
 * @property string $lng Lng
 * @property string $telefone Telefone
 * @property string $email Email
 *
 * @property Veiculo[] $veiculos
 */
class Empresa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Empresa';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cnpj', 'nomeFantasia', 'razaoSocial', 'endereco', 'lat', 'lng', 'telefone', 'email'], 'required'],
            [['cnpj', 'email'], 'string', 'max' => 50],
            [['bairro','numeroResidencia','cep','complementoResidencia','tipoLogradouro'],'safe'],
            [['nomeFantasia', 'razaoSocial', 'lat', 'lng'], 'string', 'max' => 255],
            [['endereco'], 'string', 'max' => 200],
            [['telefone'], 'string', 'max' => 15],
            [['email'], 'email'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cnpj' => 'CNPJ',
            'nomeFantasia' => 'Nome fantasia',
            'razaoSocial' => 'Razão social',
            'endereco' => 'Endereço',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'telefone' => 'Telefone',
            'email' => 'E-mail',
            'cep' => 'CEP',
            'numeroResidencia' => 'Nº',
            'complementoResidencia' => 'Complemento',
            'tipoLogradouro' => 'Tipo'
        ];
    }

    public function beforeSave($insert)
    {
        foreach($this as $key => $value) {
            $this[$key] = mb_strtoupper($value, 'utf-8');
        }
        return parent::beforeSave($insert);

    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeiculos()
    {
        return $this->hasMany(Veiculo::className(), ['idProprietarioEmpresa' => 'id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutores()
    {
        return $this->hasMany(Condutor::className(), ['idEmpresa' => 'id']);
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
            case Usuario::PERFIL_SUPER_ADMIN: $actions = '{create} {associacao} {roterizar} {update} {view} {delete}';  break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO: $actions = '{create} {associacao} {update} {view} {delete}'; break;
            case Usuario::PERFIL_SECRETARIO: $actions = ''; break;
            case Usuario::PERFIL_DIRETOR: $actions = ''; break;
            case Usuario::PERFIL_DRE: $actions = ' '; break;
            case Usuario::PERFIL_TESC_PASSE_ESCOLAR: $actions = '{view}'; break;
            case Usuario::TESC_CONSULTA: $actions = '{view} {associacao}';break;
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
       
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->id,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idEmpresa',
                'id' => $this->id,
            ]);
        }
    }
}
