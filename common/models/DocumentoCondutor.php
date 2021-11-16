<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "DocumentoCondutor".
 *
 * @property string $id Código
 * @property string $idTipo Tipo
 * @property string $idCondutor Condutor
 * @property string $nome Nome
 * @property string $arquivo Arquivo
 *
 * @property Condutor[] $condutors
 * @property Condutor[] $condutors0
 * @property Condutor[] $condutors1
 * @property Condutor[] $condutors2
 * @property Condutor[] $condutors3
 * @property Condutor[] $condutors4
 * @property Condutor $condutor
 * @property TipoDocumento $tipo
 */
class DocumentoCondutor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DocumentoCondutor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idTipo', 'idCondutor', 'nome', 'arquivo'], 'required'],
            [['idTipo', 'idCondutor'], 'integer'],
            [['nome'], 'string', 'max' => 100],
            [['arquivo'], 'string', 'max' => 255],
            [['dataCadastro'], 'safe'],
            [['idCondutor'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutor' => 'id']],
            [['idTipo'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumento::className(), 'targetAttribute' => ['idTipo' => 'id']],
        ];
    }


    public function afterSave($insert, $atributosAlterados)
    {
        parent::afterSave($insert, $atributosAlterados);
        if ($insert) {
            $novoRegistro = $this->attributes();
            foreach ($novoRegistro as $key => $coluna) {
                $this->salvarLog(Log::ACAO_INSERIR, $coluna);
            }
        }
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->salvarLog(Log::ACAO_DELETAR, 'id');
            return true;
        }
        return false;
    }

    private function salvarLog($acao, $coluna, $atributosAlterados = NULL)
    {

        if ($this->$coluna) {

            Log::salvarLog([
                'acao' => $acao,
                'referencia' => 'Documento ' . $this->tipo->nome,
                'tabela' => self::getTableSchema()->name,
                'coluna' => self::getAttributeLabel($coluna),
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idDocumentoCondutor',
                'id' => $this->id,
            ]);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idTipo' => 'Tipo',
            'idCondutor' => 'Condutor',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutors()
    {
        return $this->hasMany(Condutor::className(), ['idCNHCondutor' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutors0()
    {
        return $this->hasMany(Condutor::className(), ['idComprovanteEndereco' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutors1()
    {
        return $this->hasMany(Condutor::className(), ['idCRLV' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutors2()
    {
        return $this->hasMany(Condutor::className(), ['idVistoriaEstadual' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutors3()
    {
        return $this->hasMany(Condutor::className(), ['idVstoriaMunicipal' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutors4()
    {
        return $this->hasMany(Condutor::className(), ['idApoliceSeguro' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TipoDocumento::className(), ['id' => 'idTipo']);
    }
}
