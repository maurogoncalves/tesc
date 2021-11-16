<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "DocumentoSolicitacao".
 *
 * @property string $id Código
 * @property string $idTipo Tipo
 * @property int $idSolicitacao Solicitação
 * @property string $nome Nome
 * @property string $arquivo Arquivo
 * @property string $dataCadastro Data
 *
 * @property SolicitacaoTransporte $solicitacao
 * @property TipoDocumento $tipo
 */
class DocumentoSolicitacao extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DocumentoSolicitacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idTipo', 'idSolicitacaoTransporte', 'nome', 'arquivo'], 'required'],
            [['idTipo', 'idSolicitacaoTransporte'], 'integer'],
            [['dataCadastro'], 'safe'],
            [['nome'], 'string', 'max' => 100],
            [['arquivo'], 'string', 'max' => 255],
            [['idSolicitacaoTransporte'], 'exist', 'skipOnError' => true, 'targetClass' => SolicitacaoTransporte::className(), 'targetAttribute' => ['idSolicitacaoTransporte' => 'id']],
            [['idTipo'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumento::className(), 'targetAttribute' => ['idTipo' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idTipo' => 'Tipo',
            'idSolicitacaoTransporte' => 'Solicitação',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
            'dataCadastro' => 'Data',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSolicitacao()
    {
        return $this->hasOne(SolicitacaoTransporte::className(), ['id' => 'idSolicitacaoTransporte']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipo()
    {
        return $this->hasOne(TipoDocumento::className(), ['id' => 'idTipo']);
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
                'key' => 'idDocumentoSolicitacao',
                'id' => $this->id,
            ]);
        }
    }
}
