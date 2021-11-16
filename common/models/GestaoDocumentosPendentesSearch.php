<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Condutor;

/**
 * GestaoDocumentosSearch represents the model behind the search form about `common\models\Condutor`.
 */
class GestaoDocumentosPendentesSearch extends Condutor
{

   const SOMENTE_AMARELO = 'FFC90E';
    const SOMENTE_VERMELHO = 'ED1C24';

    const ARRAY_OPCOES = [
    self::SOMENTE_AMARELO => 'A Vencer',
    self::SOMENTE_VERMELHO => 'Vencidos'
    
    ];
    
    public $crlv;
    public $dataVistoriaEstadual;
    public $dataVencimentoSeguro;
    public $idadeVeiculo;
    public $_anoFabricacao;
    public $_validadeCNH;
    public $_crlv;
    public $_vistoriaEstadual;
    public $_validadeSeguro;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_validadeCNH','_crlv','_vistoriaEstadual','_validadeSeguro','_anoFabricacao'],'safe'],


            // [['id', 'status', 'idUsuario', 'idEmpresa', 'idVeiculo', 'regiao', 'lugares', 'alvara', 'inscricaoMunicipal', 'cnhRegistro', 'numeroApolice', 'tipoContrato', 'idCNHCondutor', 'idComprovanteEndereco', 'idCRLV', 'idVistoriaEstadual', 'idVstoriaMunicipal', 'idApoliceSeguro', 'idContrato', 'minKmDia', 'maxKmDia', 'maxViagensDia', 'numeroResidencia'], 'integer'],
            // [['anoFabricacao','dataVencimentoSeguro','dataVistoriaEstadual','crlv','cnhValidade','nome', 'fotoMotorista', 'dataNascimento', 'cpf', 'rg', 'orgaoEmissor', 'nit', 'endereco', 'bairro', 'telefone', 'celularMonitor', 'telefoneWhatsapp', 'telefone2', 'celular', 'celular2', 'telefoneMonitor', 'telefoneMonitorWhatsapp', 'telefoneWhatsapp2', 'celularWhatsapp', 'celularWhatsapp2', 'celularMonitorWhatsapp', 'email', 'cnhValidade', 'dataInicioContrato', 'dataFimContrato', 'nomeMonitor', 'rgMonitor', 'cpfMonitor', 'cep', 'complementoResidencia', 'tipoLogradouro','anoFabricacao'], 'safe'],
            // [['lat', 'lng', 'valorPagoKmViagem'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Condutor::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }



      

        
        // grid filtering conditions
        $query->andFilterWhere([
            // 'id' => $this->id,
            // 'status' => $this->status,
            // 'idUsuario' => $this->idUsuario,
            // 'idEmpresa' => $this->idEmpresa,
            // 'idVeiculo' => $this->idVeiculo,
            // 'regiao' => $this->regiao,
            // 'lugares' => $this->lugares,
            // 'dataNascimento' => $this->dataNascimento,
            // 'alvara' => $this->alvara,
            // 'inscricaoMunicipal' => $this->inscricaoMunicipal,
            // 'lat' => $this->lat,
            // 'lng' => $this->lng,
            // 'cnhRegistro' => $this->cnhRegistro,
            // 'numeroApolice' => $this->numeroApolice,
            // 'cnhValidade' => $this->cnhValidade,
            // 'dataInicioContrato' => $this->dataInicioContrato,
            // 'dataFimContrato' => $this->dataFimContrato,
            // 'tipoContrato' => $this->tipoContrato,
            // 'valorPagoKmViagem' => $this->valorPagoKmViagem,
            // 'idCNHCondutor' => $this->idCNHCondutor,
            // 'idComprovanteEndereco' => $this->idComprovanteEndereco,
            // 'idCRLV' => $this->idCRLV,
            // 'idVistoriaEstadual' => $this->idVistoriaEstadual,
            // 'idVstoriaMunicipal' => $this->idVstoriaMunicipal,
            // 'idApoliceSeguro' => $this->idApoliceSeguro,
            // 'idContrato' => $this->idContrato,
            // 'minKmDia' => $this->minKmDia,
            // 'maxKmDia' => $this->maxKmDia,
            // 'maxViagensDia' => $this->maxViagensDia,
            // 'numeroResidencia' => $this->numeroResidencia,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome]);
            // ->andFilterWhere(['like', 'fotoMotorista', $this->fotoMotorista])
            // ->andFilterWhere(['like', 'cpf', $this->cpf])
            // ->andFilterWhere(['like', 'rg', $this->rg])
            // ->andFilterWhere(['like', 'orgaoEmissor', $this->orgaoEmissor])
            // ->andFilterWhere(['like', 'nit', $this->nit])
            // ->andFilterWhere(['like', 'endereco', $this->endereco])
            // ->andFilterWhere(['like', 'bairro', $this->bairro])
            // ->andFilterWhere(['like', 'telefone', $this->telefone])
            // ->andFilterWhere(['like', 'celularMonitor', $this->celularMonitor])
            // ->andFilterWhere(['like', 'telefoneWhatsapp', $this->telefoneWhatsapp])
            // ->andFilterWhere(['like', 'telefone2', $this->telefone2])
            // ->andFilterWhere(['like', 'celular', $this->celular])
            // ->andFilterWhere(['like', 'celular2', $this->celular2])
            // ->andFilterWhere(['like', 'telefoneMonitor', $this->telefoneMonitor])
            // ->andFilterWhere(['like', 'telefoneMonitorWhatsapp', $this->telefoneMonitorWhatsapp])
            // ->andFilterWhere(['like', 'telefoneWhatsapp2', $this->telefoneWhatsapp2])
            // ->andFilterWhere(['like', 'celularWhatsapp', $this->celularWhatsapp])
            // ->andFilterWhere(['like', 'celularWhatsapp2', $this->celularWhatsapp2])
            // ->andFilterWhere(['like', 'celularMonitorWhatsapp', $this->celularMonitorWhatsapp])
            // ->andFilterWhere(['like', 'email', $this->email])
            // ->andFilterWhere(['like', 'nomeMonitor', $this->nomeMonitor])
            // ->andFilterWhere(['like', 'rgMonitor', $this->rgMonitor])
            // ->andFilterWhere(['like', 'cpfMonitor', $this->cpfMonitor])
            // ->andFilterWhere(['like', 'cep', $this->cep])
            // ->andFilterWhere(['like', 'complementoResidencia', $this->complementoResidencia])
            // ->andFilterWhere(['like', 'tipoLogradouro', $this->tipoLogradouro]);

        return $dataProvider;
    }
}
