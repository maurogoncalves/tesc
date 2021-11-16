<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Justificativa".
 *
 * @property string $id Cód.
 * @property string $nome Nome
 *
 * @property Comunicado[] $ausencias
 */
class Justificativa extends \yii\db\ActiveRecord
{
    const CLASSIFICACAO_RESPONSAVEL = 1;
    const CLASSIFICACAO_CONDUTOR = 2;
    const CLASSIFICACAO_ROTA = 3;

    const ARRAY_CLASSIFICACAO = [
        self::CLASSIFICACAO_RESPONSAVEL => 'AUSÊNCIA DE ALUNO (RESPONSÁVEL)',
        self::CLASSIFICACAO_CONDUTOR => 'OCORRÊNCIA DO CONDUTOR',
        self::CLASSIFICACAO_ROTA => 'INICIAR ROTA SEM TODOS OS ALUNOS NO VEÍCULO (CONDUTOR)',
        
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Justificativa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome','classificacao'], 'required'],
            [['nome'], 'string', 'max' => 50],
        ];
    }

    public function beforeSave($insert)
    {
        foreach($this as $key => $value) {
            $this[$key] = mb_strtoupper($value, 'utf-8');
        }
        return parent::beforeSave($insert);

    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Cód.',
            'nome' => 'Nome',
            'classificacao' => 'Classificação',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAusencias()
    {
        return $this->hasMany(Comunicado::className(), ['idJustificativa' => 'id']);
    }

    public function afterSave($insert, $atributosAlterados) {
        parent::afterSave($insert, $atributosAlterados);
        //UPDATE
        if(!$insert) {
            foreach($atributosAlterados as $key=>$value)
            {
                if($atributosAlterados[$key] && $value != $this->$key)
                {
                   $this->salvarLog(Log::ACAO_ATUALIZAR,$key,$atributosAlterados);
                }
            }
        } 
        //INSERT
        else 
        {
            $novoRegistro =  $this->attributes();
            foreach($novoRegistro as $key=>$coluna)
            {
                $this->salvarLog(Log::ACAO_INSERIR,$coluna);
            }
        }
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            $this->salvarLog(Log::ACAO_DELETAR,'id');
            return true;
        }
        return false;
    }
 
    
    private function salvarLog($acao,$coluna,$atributosAlterados=NULL){
        if($this->$coluna)
        {
       
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->id,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idJustificativa',
                'id' => $this->id,
            ]);
        }
    }
}
