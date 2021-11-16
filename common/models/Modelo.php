<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Modelo".
 *
 * @property string $id Código
 * @property string $idMarca Marca
 * @property string $nome Nome
 *
 * @property Marca $marca
 */
class Modelo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Modelo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idMarca'], 'integer'],
            [['nome'], 'required'],
            [['nome'], 'string', 'max' => 50],
            [['idMarca'], 'exist', 'skipOnError' => true, 'targetClass' => Marca::className(), 'targetAttribute' => ['idMarca' => 'id']],
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
            'id' => 'Código',
            'idMarca' => 'Marca',
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarca()
    {
        return $this->hasOne(Marca::className(), ['id' => 'idMarca']);
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
                'key' => 'idModelo',
                'id' => $this->id,
            ]);
        }
    }
}
