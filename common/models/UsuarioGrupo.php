<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "UsuarioGrupo".
 *
 * @property string $id Código
 * @property string $idUsuario Usuário
 * @property string $idGrupo Grupo
 *
 * @property Usuario $usuario
 */
class UsuarioGrupo extends \yii\db\ActiveRecord
{

    const GRUPO_ESP = 1;
    const GRUPO_ZN = 2;
    const GRUPO_SFX = 3;
    const GRUPO_MUN = 4;
    const GRUPO_EE = 5;
    const GRUPO_FINANCEIRO = 6;

    const ARRAY_GRUPOS = [
        self::GRUPO_ESP => 'ESP',
        self::GRUPO_ZN => 'ZN',
        self::GRUPO_SFX => 'SFX',
        self::GRUPO_MUN => 'MUN',
        self::GRUPO_EE => 'EE',
        self::GRUPO_FINANCEIRO => 'Financeiro'
    ];

    const ARRAY_GRUPOS_SEM_FINANCEIRO = [
        self::GRUPO_ESP => 'ESP',
        self::GRUPO_ZN => 'ZN',
        self::GRUPO_SFX => 'SFX',
        self::GRUPO_MUN => 'MUN',
        self::GRUPO_EE => 'EE',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'UsuarioGrupo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idUsuario', 'idGrupo'], 'required'],
            [['idUsuario', 'idGrupo'], 'integer'],
            [['idUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['idUsuario' => 'id']],
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
            'idGrupo' => 'Grupo',
            'inputGrupo' => 'Grupos'
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idUsuario']);
    }

    //Retorna se o usuário possui ou não este grupo
    // Ex de uso UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_EE)
    public static function permissao($grupo)
    {
        $grupos = array_column(\Yii::$app->User->identity->grupos, 'idGrupo');
        return in_array($grupo, $grupos);
    }

    public static function grupoSolicitacao($solicitacao)
    {
        $retorno = '';
        //CASO o Aluno tenha alguma necessidade especial
        if ($solicitacao->aluno->necessidades){
            // $retorno .= 'ESP, ';
            return 'ESP';
        }
            
        //Caso a escola seja da ZN
        if ($solicitacao->escola->regiao == Escola::REGIAO_NORTE){
            // $retorno .= 'ZN, ';
            return 'ZN';
        }
            
        // Caso a escola seja de SFX
        if ($solicitacao->escola->regiao == Escola::REGIAO_SFX){
            // $retorno .= 'SFX, ';
            return 'SFX';
        } 
            // Caso a escola seja do MUN ou FIL
        if ($solicitacao->escola->unidade == Escola::UNIDADE_MUNICIPAL || $solicitacao->escola->unidade == Escola::UNIDADE_FILANTROPICA){
            // $retorno .= 'MUN, ';
            return 'MUN';
        }
        // // CASO a escola seja EE
        if ($solicitacao->escola->unidade == Escola::UNIDADE_ESTADUAL){
            // $retorno .= 'EE, ';
            return 'EE';
        }
            

        // return mb_substr($retorno, 0, -2);
    }

    public static function solicitacaoPertenceGrupo($solicitacao, $grupo){
        $grupoSol = self::grupoSolicitacao($solicitacao);
        switch($grupo){
            case UsuarioGrupo::GRUPO_ESP:
                if ($grupoSol == 'ESP')
                    return true;
                break;
            case UsuarioGrupo::GRUPO_ZN:
                //Caso a escola seja da ZN
                if ($grupoSol == 'ZN')
                    return true;
                break;
            case UsuarioGrupo::GRUPO_SFX:
                // Caso a escola seja de SFX
                if ($grupoSol == 'SFX')
                    return true;
                break;
            case UsuarioGrupo::GRUPO_MUN:
                // Caso a escola seja do MUN ou FIL
                if ($grupoSol == 'MUN')
                    return true;
                break;
            case UsuarioGrupo::GRUPO_EE:
                // CASO a escola seja EE
                if ($grupoSol == 'EE')
                    return true;
                break;
            default: 
                break;
        }
        
        return false;
    }
    public static function solicitacaoPermitida($solicitacao)
    {
        //CASO o Aluno tenha alguma necessidade especial
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_ESP)) {
            if ($solicitacao->aluno->necessidades)
                return true;
        }

        // Caso a escola seja de SFX
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_SFX)) {
            if ($solicitacao->escola->regiao == Escola::REGIAO_SFX)
                return true;
        }

        //Caso a escola seja da ZN
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_ZN)) {
            if ($solicitacao->escola->regiao == Escola::REGIAO_NORTE)
                return true;
        }

        // Caso a escola seja do MUN ou FIL
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_MUN)) {
            if ($solicitacao->escola->unidade == Escola::UNIDADE_MUNICIPAL || $solicitacao->escola->unidade == Escola::UNIDADE_FILANTROPICA)
                return true;
        }

        // // CASO a escola seja EE
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_EE)) {
            if ($solicitacao->escola->unidade == Escola::UNIDADE_ESTADUAL)
                return true;
        }

        return false;
    }

    public static function solicitacoesPermitidas($solicitacoes)
    {
        $solicitacoesPermitidas = [];

        foreach ($solicitacoes as $solicitacao) {
            //CASO o Aluno tenha alguma necessidade especial
            if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_ESP)) {
                if ($solicitacao->aluno->necessidades)
                    $solicitacoesPermitidas[] = $solicitacao->id;
            }

            // Caso a escola seja de SFX
            if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_SFX)) {
                if ($solicitacao->escola->regiao == Escola::REGIAO_SFX)
                    $solicitacoesPermitidas[] = $solicitacao->id;
            }

            //Caso a escola seja da ZN
            if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_ZN)) {
                if ($solicitacao->escola->regiao == Escola::REGIAO_NORTE)
                    $solicitacoesPermitidas[] = $solicitacao->id;
            }

            // Caso a escola seja do MUN ou FIL
            if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_MUN)) {
                if ($solicitacao->escola->unidade == Escola::UNIDADE_MUNICIPAL || $solicitacao->escola->unidade == Escola::UNIDADE_FILANTROPICA)
                    $solicitacoesPermitidas[] = $solicitacao->id;
            }

            // // CASO a escola seja EE
            if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_EE)) {
                if ($solicitacao->escola->unidade == Escola::UNIDADE_ESTADUAL)
                    $solicitacoesPermitidas[] = $solicitacao->id;
            }
        }

        return $solicitacoesPermitidas;
    }


        // Verifica qual o grpo da solicitação e se o usuário pertence ao grupo dessa solicitação
    public static function autorizarSolicitacao($solicitacao){
        $grupoSolicitacao = self::grupoSolicitacao($solicitacao);
  
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_ESP) && $grupoSolicitacao == 'ESP') {
            return true;
        }

        //Caso a escola seja da ZN
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_ZN) && $grupoSolicitacao == 'ZN') {
            return true;
        }
        // Caso a escola seja de SFX
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_SFX) && $grupoSolicitacao == 'SFX') {
            return true;
        }
        // Caso a escola seja do MUN ou FIL
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_MUN) && $grupoSolicitacao == 'MUN') {  
            return true;
        }
        // // CASO a escola seja EE
        if (UsuarioGrupo::permissao(UsuarioGrupo::GRUPO_EE) && $grupoSolicitacao == 'EE') {
           return true;
        }

        return false;
    }
}
