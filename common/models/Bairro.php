<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "TB_BAIRRO".
 *
 * @property int $ID_BAIRRO
 * @property int $ID_CIDADE
 * @property string $BAIRRO
 * @property int $STATUS
 * @property string $DT_ATUALIZACAO
 * @property int $OFICIAL
 * @property int $MODERACAO
 *
 * @property TBCIDADE $cIDADE
 * @property TBCEP[] $tBCEPs
 * @property TBREGIAOBAIRRO[] $tBREGIAOBAIRROs
 */
class Bairro extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'TB_BAIRRO';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('ipplanDb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ID_CIDADE', 'BAIRRO', 'DT_ATUALIZACAO'], 'required'],
            [['ID_CIDADE', 'STATUS', 'OFICIAL', 'MODERACAO'], 'integer'],
            [['BAIRRO'], 'string'],
            [['DT_ATUALIZACAO'], 'safe'],
            [['ID_CIDADE'], 'exist', 'skipOnError' => true, 'targetClass' => TBCIDADE::className(), 'targetAttribute' => ['ID_CIDADE' => 'ID_CIDADE']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ID_BAIRRO' => 'I D B A I R R O',
            'ID_CIDADE' => 'I D C I D A D E',
            'BAIRRO' => 'B A I R R O',
            'STATUS' => 'S T A T U S',
            'DT_ATUALIZACAO' => 'D T A T U A L I Z A C A O',
            'OFICIAL' => 'O F I C I A L',
            'MODERACAO' => 'M O D E R A C A O',
        ];
    }

    static function bairrosDisponiveis($bairrosIndisponiveis){
        
        return self::find()
                            ->select('ID_BAIRRO,BAIRRO')
                            ->andWhere(['ID_CIDADE' => '5265'])
                            ->andWhere(['not in', 'ID_BAIRRO', $bairrosIndisponiveis])
                            ->all();
    }

    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCIDADE()
    {
        return $this->hasOne(TBCIDADE::className(), ['ID_CIDADE' => 'ID_CIDADE']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTBCEPs()
    {
        return $this->hasMany(TBCEP::className(), ['ID_BAIRRO' => 'ID_BAIRRO']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTBREGIAOBAIRROs()
    {
        return $this->hasMany(TBREGIAOBAIRRO::className(), ['ID_BAIRRO' => 'ID_BAIRRO']);
    }
}
