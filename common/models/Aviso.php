<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Aviso".
 *
 * @property int $id Código
 * @property string $data Data
 * @property string $mensagem Mensagem
 * @property int $idUsuario Usuário
 *
 * @property Usuario $usuario
 */
class Aviso extends \yii\db\ActiveRecord
{
    public $documentoLegislacao;
    public $documentoFrete;
    public $documentoPasse;
    public $documentoOrientacoesSetor;
    public $documentoAtualizacaoSistema;

    const AVISO_FIXADO = 2;
    const AVISO_NAO_FIXADO = 1;
    const ARRAY_FIXADO = [
        self::AVISO_FIXADO => 'SIM',
        self::AVISO_NAO_FIXADO => 'NÃO'
    ];

    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Aviso';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data', 'mensagem', 'idUsuario','titulo','fixado'], 'required'],
            [['data','fixado'], 'safe'],
            [['mensagem','titulo','link'], 'string'],
            [['idUsuario'], 'integer'],
            [['idUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['idUsuario' => 'id']],
            [['titulo'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'data' => 'Aviso disponível a partir de',
            'titulo' => 'Título',
            'mensagem' => 'Mensagem',
            'idUsuario' => 'Usuário',
            'documentoLegislacao' => 'Legislação',
            'documentoFrete' => 'Frete',
            'documentoPasse' => 'Passe',
            'documentoOrientacoesSetor' => 'Orientações Setor',
            'documentoAtualizacaoSistema' => 'Atualizações Sistema',
            'fixado' => 'Fixar aviso',
            'link' => 'Link do Vimeo '
        ];
    }

    public function beforeSave($insert)
    {
        foreach($this as $key => $value) {
            $this[$key] = mb_strtoupper($value, 'utf-8');
        }
        return parent::beforeSave($insert);
    }

    public function getDocLegislacao()
    {
        return $this->hasMany(DocumentoAviso::className(), ['idAviso' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_AVISO_LEGISLACAO]);
    }

    public function getDocFrete()
    {
        return $this->hasMany(DocumentoAviso::className(), ['idAviso' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_AVISO_FRETE]);
    }

    public function getDocPasse()
    {
        return $this->hasMany(DocumentoAviso::className(), ['idAviso' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_AVISO_PASSE]);
    }

    public function getDocOrientacoesSetor()
    {
        return $this->hasMany(DocumentoAviso::className(), ['idAviso' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_AVISO_ORIENTACOES_SETOR]);
    }

    
    public function getDocAtualizacaoSistema()
    {
        return $this->hasMany(DocumentoAviso::className(), ['idAviso' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_AVISO_ATUALIZACOES_SISTEMA]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idUsuario']);
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

}
