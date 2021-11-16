<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Condutor;

/**
 * GestaoDocumentosSearch represents the model behind the search form about `common\models\Condutor`.
 */
class GestaoDocumentosSearch extends Condutor
{
    public $crlv;
    public $dataVistoriaEstadual;
    public $dataVencimentoSeguro;
    public $idadeVeiculo;
    public $anoFabricacao;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'idUsuario', 'idEmpresa', 'idVeiculo', 'regiao', 'lugares', 'alvara', 'inscricaoMunicipal', 'cnhRegistro', 'numeroApolice', 'tipoContrato', 'idCNHCondutor', 'idComprovanteEndereco', 'idCRLV', 'idVistoriaEstadual', 'idVstoriaMunicipal', 'idApoliceSeguro', 'idContrato', 'minKmDia', 'maxKmDia', 'maxViagensDia', 'numeroResidencia'], 'integer'],
            [['anoFabricacao','dataVencimentoSeguro','dataVistoriaEstadual','crlv','cnhValidade','nome', 'fotoMotorista', 'dataNascimento', 'cpf', 'rg', 'orgaoEmissor', 'nit', 'endereco', 'bairro', 'telefone', 'celularMonitor', 'telefoneWhatsapp', 'telefone2', 'celular', 'celular2', 'telefoneMonitor', 'telefoneMonitorWhatsapp', 'telefoneWhatsapp2', 'celularWhatsapp', 'celularWhatsapp2', 'celularMonitorWhatsapp', 'email', 'cnhValidade', 'dataInicioContrato', 'dataFimContrato', 'nomeMonitor', 'rgMonitor', 'cpfMonitor', 'cep', 'complementoResidencia', 'tipoLogradouro','anoFabricacao'], 'safe'],
            [['lat', 'lng', 'valorPagoKmViagem'], 'number'],
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

        if($this->cnhValidade){
            $data = explode('- ', $this->cnhValidade);
       
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(cnhValidade,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(cnhValidade,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        }

        if($this->crlv){
            $data = explode('- ', $this->crlv);
       
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(Veiculo.dataVencimentoCRLV,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(Veiculo.dataVencimentoCRLV,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        }
        if($this->dataVistoriaEstadual){
            $data = explode('- ', $this->dataVistoriaEstadual);
       
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(Veiculo.dataVistoriaEstadual,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(Veiculo.dataVistoriaEstadual,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        }
        if($this->dataVencimentoSeguro){
            $data = explode('- ', $this->dataVencimentoSeguro);
       
            $data[1] = explode('/', $data[1]);
            $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];

            $data[0] = explode('/', $data[0]);
            $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
            $query->andFilterWhere ( [ '>=' , 'DATE_FORMAT(Veiculo.dataVencimentoSeguro,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[0]) ) ) ] );
            $query->andFilterWhere ( [ '<=' , 'DATE_FORMAT(Veiculo.dataVencimentoSeguro,"%Y-%m-%d")' , date ( 'Y-m-d' , strtotime ( trim($data[1]) ) ) ] );
        }

        if($this->anoFabricacao) {
            $ano = date('Y') - $this->anoFabricacao;
            $query->andFilterWhere ( [ '=' , 'anoFabricacao' , $ano] );

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
