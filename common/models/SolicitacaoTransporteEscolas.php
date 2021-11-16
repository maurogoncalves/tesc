<?php

namespace common\models;

​
  /**
 * This is the model class for table "SolicitacaoTransporteEscolas".
 *
 * @property int $id
 * @property int $idEscola
 * @property int $idSolicitacaoTransporte
 *
 * @property Escola $escola
 * @property SolicitacaoTransporte $solicitacaoTransporte
 */
;
class SolicitacaoTransporteEscolas extends \yii\db\ActiveRecord
{
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'SolicitacaoTransporteEscolas';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['idEscola', 'idSolicitacaoTransporte'], 'required'],
      [['idEscola', 'idSolicitacaoTransporte'], 'integer'],
      [['idEscola'], 'exist', 'skipOnError' => true, 'targetClass' => Escola::className(), 'targetAttribute' => ['idEscola' => 'id']],
      [['idSolicitacaoTransporte'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitacaoTransporte::className(), 'targetAttribute' => ['idSolicitacaoTransporte' => 'id']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id' => 'ID',
      'idEscola' => 'Id Escola',
      'idSolicitacaoTransporte' => 'Id Solicitacao Transporte',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getEscola()
  {
    return $this->hasOne(Escola::className(), ['id' => 'idEscola']);
  }

  /**
   * {@inheritdoc}
   */
  public function getSolicitacaoTransporte()
  {
    return $this->hasOne(SolicitacaoTransporte::className(), ['id' => 'idSolicitacaoTransporte']);
  }
}
