<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Calendario".
 *
 * @property int $id
 *
 * @property CalendarioDia[] $calendarioDias
 * @property CalendarioEscola[] $calendarioEscolas
 */
class Calendario extends \yii\db\ActiveRecord
{
    public $inputEscola;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Calendario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ano' => 'ano'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendarioDias()
    {
        return $this->hasMany(CalendarioDia::className(), ['idCalendario' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendarioEscolas()
    {
        return $this->hasMany(CalendarioEscola::className(), ['idCalendario' => 'id']);
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
            case Usuario::PERFIL_SUPER_ADMIN: $actions = '{create} {view}  {delete}';  break;
            case Usuario::PERFIL_TESC_DISTRIBUICAO: $actions = '{create} {view} {delete}'; break;
            case Usuario::PERFIL_SECRETARIO: $actions = ''; break;
            case Usuario::PERFIL_DIRETOR: $actions = ''; break;
            case Usuario::PERFIL_DRE: $actions = ''; break;
            case Usuario::PERFIL_TESC_PASSE_ESCOLAR: $actions = ''; break;
            case Usuario::TESC_CONSULTA: $actions = '{view}';break;
            case Usuario::PERFIL_CONDUTOR: $actions = ''; break;
        }
        return $actions;
    }

}
