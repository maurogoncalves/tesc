<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "AgrupamentoBairro".
 *
 * @property int $id
 * @property int $idBairro
 * @property string $nome
 * @property int $agrupamento
 */
class AgrupamentoBairro extends \yii\db\ActiveRecord
{
    public $bairrosDisponiveis;
    const ZONA_URBANA = 1;
    const ZONA_RURAL = 2;

    const ARRAY_BAIRRO = [
        self::ZONA_URBANA => 'Urbana',
        self::ZONA_RURAL => 'Rural'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'AgrupamentoBairro';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agrupamento', 'nome','bairrosDisponiveis'], 'required', 'on' => 'create'],
            [['agrupamento'], 'required', 'on' => 'update'],
            [['idBairro', 'agrupamento'], 'safe'],
            [['nome'], 'string', 'max' => 100],
            [['idBairro'], 'unique', 'targetAttribute' => ['idBairro']]

        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Cód',
            'idBairro' => 'Cód. Bairro',
            'nome' => 'Nome',
            'agrupamento' => 'Zona',
            'bairrosDisponiveis' => 'Bairros'
        ];
    }
    public function bairrosPorZona($zona)
    {
        return AgrupamentoBairro::find()->andWhere(['agrupamento' => $zona])->all();
    }
    static function zonaRural(){
        $bairros = self::find()->andWhere(['agrupamento' => self::ZONA_RURAL ])->all();
        return array_column($bairros, 'nome');
    }
    static function zonaUrbana(){
        $bairros = self::find()->andWhere(['agrupamento' => self::ZONA_URBANA ])->all();
        return array_column($bairros, 'nome');
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
