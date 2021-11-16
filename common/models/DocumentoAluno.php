<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "DocumentoAluno".
 *
 * @property string $id Código
 * @property string $idTipo Tipo
 * @property string $idAluno Aluno
 * @property string $nome Nome
 * @property string $arquivo Arquivo
 *
 * @property Aluno[] $alunos
 * @property Aluno[] $alunos0
 * @property Aluno[] $alunos1
 * @property Aluno[] $alunos2
 * @property Aluno[] $alunos3
 * @property Aluno[] $alunos4
 * @property Aluno[] $alunos5
 * @property Aluno $aluno
 * @property TipoDocumento $tipo
 */
class DocumentoAluno extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DocumentoAluno';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idTipo', 'idAluno'], 'integer'],
            [['nome', 'arquivo', 'dataCadastro'], 'safe'],


            [['nome'], 'string', 'max' => 100],
            [['arquivo'], 'string', 'max' => 255],
            [['idAluno'], 'exist', 'skipOnError' => true, 'targetClass' => Aluno::className(), 'targetAttribute' => ['idAluno' => 'id']],
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
            'idAluno' => 'Aluno',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunos()
    {
        return $this->hasMany(Aluno::className(), ['idRgAluno' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunos0()
    {
        return $this->hasMany(Aluno::className(), ['idComprovanteEndereco' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunos1()
    {
        return $this->hasMany(Aluno::className(), ['idRgResponsavel' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunos2()
    {
        return $this->hasMany(Aluno::className(), ['idDeclaracaoVizinhos' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunos3()
    {
        return $this->hasMany(Aluno::className(), ['idLaudoMedico' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunos4()
    {
        return $this->hasMany(Aluno::className(), ['idTransporteEspecialAdaptado' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlunos5()
    {
        return $this->hasMany(Aluno::className(), ['idDeclaracaoInexistenciaVaga' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluno()
    {
        return $this->hasOne(Aluno::className(), ['id' => 'idAluno']);
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
        // print 'beforedelete';		
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
                'key' => 'idDocumentoAluno',
                'id' => $this->id,
            ]);
        }
    }
}
