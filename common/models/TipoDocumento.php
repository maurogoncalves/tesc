<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "TipoDocumento".
 *
 * @property string $id
 * @property string $nome
 */
class TipoDocumento extends \yii\db\ActiveRecord
{
    const TIPO_CONTRATO = 1;
    const TIPO_APOLICE = 2;
    const TIPO_VISTORIA_MUNICIPAL = 3;
    const TIPO_VISTORIA_ESTADUAL = 4;
    const TIPO_CNH = 5;
    const TIPO_RG_RESPONSAVEL = 6;
    const TIPO_LAUDO_MEDICO = 7;
    const TIPO_DECLARACAO_VIZINHOS = 8;
    const TIPO_COMPROVANTE_ENDERECO = 9;
    const TIPO_DECLARACAO_INEXISTENCIA_VAGA = 10;
    const TIPO_DECLARACAO_TRANSPORTE_ESPECIAL = 11;
    const TIPO_RG_ALUNO = 12;
    const TIPO_CRLV = 13;
    const TIPO_RECIBO_PAGAMENTO_AUTONOMO = 14;
    const TIPO_CERTIDAO_INSCRICAO_MUNICIPAL = 15;
    const TIPO_CERTIDAO_NEGATIVA_DEBITOS_MUNICIPAIS = 16;
    const TIPO_CERTIDAO_NEGATIVA_ACOES_CIVEIS = 17;
    const TIPO_CONTRATO_TRABALHO = 18;
    const TIPO_CERTIDAO_ANTECEDENTES_CRIMINAIS = 19;
    const TIPO_DPVAT = 20;
    const TIPO_RG_MONITOR = 21;
    const TIPO_CPF_MONITOR = 22;
    const TIPO_OCORRENCIA = 23;
    const TIPO_FORMALIZACAO_SOLICITACAO = 24;
    const TIPO_AVISO_LEGISLACAO = 25;
    const TIPO_AVISO_FRETE = 26;
    const TIPO_AVISO_PASSE = 27;
    const TIPO_AVISO_ORIENTACOES_SETOR = 28;
    const TIPO_AVISO_ATUALIZACOES_SISTEMA = 29;
    const TIPO_APOLICE_SEGURO = 30;
    const TIPO_AUTORIZACAO_ESCOLAR = 31;
    const TIPO_PRONTUARIO_CNH = 32;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'TipoDocumento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['nome'], 'string', 'max' => 50],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
        ];
    }
}
