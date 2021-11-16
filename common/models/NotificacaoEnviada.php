<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "NotificacaoEnviada".
 *
 * @property string $id ID
 * @property string $idUsuario Usuário
 * @property string $data Data
 * @property string $idFirebase Firebase
 * @property int $tipo Tipo
 * @property string $texto Texto
 *
 * @property Usuario $usuario
 */
class NotificacaoEnviada extends \yii\db\ActiveRecord
{
    const TIPO_VENCIMENTO_CNH = 1;
    const TIPO_VEICULO_CHEGANDO = 2;
    const TIPO_VENCIMENTO_APOLICE = 3;
    const TIPO_PERDA_BENEFICIO = 4;
    
    const ARRAY_TIPOS = [
        self::TIPO_VENCIMENTO_CNH => 'Vencimento da CNH',
        self::TIPO_VEICULO_CHEGANDO => 'Veículo chegando',
        self::TIPO_VENCIMENTO_APOLICE => 'Vencimento da apólice',
        self::TIPO_PERDA_BENEFICIO => 'Alerta de perda de benefício',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'NotificacaoEnviada';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idUsuario', 'data', 'tipo', 'texto'], 'required'],
            [['idUsuario', 'tipo'], 'integer'],
            [['data','idFirebase'], 'safe'],
            [['idFirebase'], 'string', 'max' => 200],
            [['texto'], 'string', 'max' => 250],
            [['idUsuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuario::className(), 'targetAttribute' => ['idUsuario' => 'id']],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idUsuario' => 'Usuário',
            'idAluno' => 'Aluno',
            'data' => 'Data',
            'idFirebase' => 'Firebase',
            'tipo' => 'Tipo',
            'texto' => 'Texto',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuario::className(), ['id' => 'idUsuario']);
    }

    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
    }

    public static function notificadoHoje($idUsuario){
        $notificacoes = self::find()
                        ->where(['idUsuario' => $idUsuario])
                        ->andWhere(['>=','data', date('Y-m-d 00:00:00')])
                        ->andWhere(['<=','data', date('Y-m-d 23:59:59')])
                        ->all();
        if($notificacoes)
            return $notificacoes;
        return false;
    }
}
