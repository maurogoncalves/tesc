<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ReciboPagamentoAutonomo".
 *
 * @property int $id Código
 * @property string $idCondutor Condutor
 * @property int $numRecibo
 * @property int $data Data
 * @property integer $mes Mês
 * @property integer $ano Ano
 * @property integer $quantidade Quantidade
 *
 * @property Condutor $condutor
 */
class ReciboPagamentoAutonomo extends \yii\db\ActiveRecord
{
    public $documentoRecibo;

    const ARRAY_MESES = [
        1 => 'JAN',
        2 => 'FEV',
        3 => 'MAR',
        4 => 'ABR',
        5 => 'MAI',
        6 => 'JUN',
        7 => 'JUL',
        8 => 'AGO',
        9 => 'SET',
        10 => 'OUT',
        11 => 'NOV',
        12 => 'DEZ'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ReciboPagamentoAutonomo';
    }

    public function beforeSave($insert)
    {
        if ($this->valor)
            $this->valor = $this->toDecimal($this->valor);

        if (parent::beforeSave($insert)) {
            return true;
        }

        return false;
    }

    public function toDecimal($valor)
    {
        if (strpos($valor, ',')) {
            $valor = str_ireplace(".", "", $valor);
            $valor = str_ireplace(",", ".", $valor);
        }
        return $valor;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idCondutor', 'data', 'valor', 'mes', 'ano', 'quantidade', 'diasLetivos'], 'required'],
            [['idCondutor', 'data', 'idRecibo', 'valor', 'numRecibo', 'mes', 'ano', 'quantidade', 'diasLetivos'], 'safe'],
            [['idCondutor'], 'exist', 'skipOnError' => true, 'targetClass' => Condutor::className(), 'targetAttribute' => ['idCondutor' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idCondutor' => 'Condutor',
            'numRecibo' => 'No. Recibo',
            'data' => 'Data',
            'diasLetivos' => 'Dias Letivos',
            'numRecibo' => 'Recibo',
            'valor' => 'Valor total'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }

    public function getDocRecibo()
    {
        return $this->hasMany(DocumentoReciboPagamentoAutonomo::className(), ['idRecibo' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_RECIBO_PAGAMENTO_AUTONOMO]);
    }

    public function getNome()
    {
        return 'Nº ' . $this->numRecibo . ' | ' . $this->condutor->nome;
    }
    public function afterSave($insert, $atributosAlterados)
    {
        parent::afterSave($insert, $atributosAlterados);
        //UPDATE
        if (!$insert) {
            foreach ($atributosAlterados as $key => $value) {
                if ($atributosAlterados[$key] && $value != $this->$key) {
                    $this->salvarLog(Log::ACAO_ATUALIZAR, $key, $atributosAlterados);
                }
            }
        }
        //INSERT
        else {
            $novoRegistro =  $this->attributes();
            foreach ($novoRegistro as $key => $coluna) {
                $this->salvarLog(Log::ACAO_INSERIR, $coluna);
            }
        }
    }

    public function getNomeMes()
    {
        if ($this->mes) {
            return self::ARRAY_MESES[$this->mes];
        } else {
            return '';
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
                'referencia' => $this->idCondutor,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idReciboPagamentoAutonomo',
                'id' => $this->id,
            ]);
        }
    }
}
