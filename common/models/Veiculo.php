<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Veiculo".
 *
 * @property string $id Código
 * @property string $idModelo Modelo
 * @property string $placa Placa
 * @property int $capacidade Capacidade
 * @property int $combustivel Combustível
 * @property string $dataVistoriaEstadual Vistoria estadual
 * @property string $dataVistoriaMunicipal Vistoria municipal
 * @property string $dataVencimenoSeguro Vencimento do Seguro
 * @property int $tipoVeiculo Tipo de veículo
 * @property int $alocacao Alocação
 *
 * @property Modelo $modelo
 */
class Veiculo extends \yii\db\ActiveRecord
{
    public $anexoFotoVeiculo;
    public $anexoFotoPlaca;

    public $documentoCRLV;
    public $documentoVistoriaEstadual;
    public $documentoVistoriaMunicipal;
    public $documentoApoliceSeguro;
    public $documentoDPVAT;


    const TIPO_ALCOOL = 1;
    const TIPO_GASOLINA = 2;
    const TIPO_GAS = 3;
    const TIPO_DIESEL = 4;
    const FLEX_ETANOL_GASOLINA = 5;
    const FLEX_GASOLINA_GNV = 6;
    const TRIFLEX_ETANOL_GASOLINA_GNV = 7;
    const ARRAY_TIPO = [
        1 => 'Etanol',
        2 => 'Gasolina',
        3 => 'GNV',
        4 => 'Diesel',
        5 => 'FLEX (ETANOL/GASOLINA)',
        6 => 'FLEX (GASOLINA/GNV)',
        7 => 'TRIFLEX (ETANOL/GASOLINA/GNV)',
    ];

    const ADAPTADO_SIM = 1;
    const ADAPTADO_NAO = 2;
    const ARRAY_ADAPTADO = [
        1 => 'Não',
        2 => 'Sim',
    ];

    const TIPO_PERUA = 1;
    const TIPO_MICRO_ONIBUS = 2;
    const TIPO_ONIBUS = 3;
    const TIPO_VEICULO_ADAPTADO = 4;

    const ARRAY_TIPO_VEICULO = [
        self::TIPO_PERUA => 'Perua',
        self::TIPO_MICRO_ONIBUS => 'Micro ônibus',
        self::TIPO_ONIBUS => 'Ônibus',
        self::TIPO_VEICULO_ADAPTADO => 'Veículo adaptado',
    ];

    const ALOCACAO_FRETADO = 1;
    const ALOCACAO_FROTA_PROPRIA = 2;

    const ARRAY_ALOCACAO = [
        self::ALOCACAO_FRETADO => 'Fretado',
        self::ALOCACAO_FROTA_PROPRIA => 'Frota própria'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Veiculo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idModelo', 'idProprietarioEmpresa', 'placa', 'capacidade', 'fotoVeiculo', 'fotoPlaca', 'anexoFotoVeiculo', 'anexoFotoPlaca', 'combustivel', 'idCondutor', 'adaptado','dataVencimentoCRLV', 'dataVencimentoSeguro', 'dataVistoriaMunicipal', 'dataVistoriaEstadual', 'idMarca', 'numApolice', 'enderecoGPS', 'lat', 'lng', 'ultimaAtualizacaoGPS', 'numApolice', 'tipoVeiculo', 'alocacao'], 'safe'],
            [['idModelo', 'capacidade', 'combustivel'], 'integer'],
            //['idCondutor', 'unique', 'targetClass' => Condutor::classname(), 'message' => 'Este condutor(a) já possui um veículo atribuído.'],
            [['idCondutor', 'placa'], 'unique'],
            // ['placa','match','pattern'=>'/^[a-zA-Z]{3}[0-9]{4}\b/'],

            //[['idCondutor'], 'required'],
            //[['dataVistoriaEstadual', 'dataVistoriaMunicipal', 'dataVencimenoSeguro','idProprietarioEmpresa','idProprietarioCondutor','tipoProprietario'], 'safe'],
            [['idModelo', 'capacidade', 'adaptado', 'placa', 'dataVistoriaMunicipal', 'dataVistoriaEstadual', 'dataVencimentoSeguro', 'numApolice','anoFabricacao','anoModelo'], 'required'],
            [['idModelo'], 'exist', 'skipOnError' => true, 'targetClass' => Modelo::className(), 'targetAttribute' => ['idModelo' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Código',
            'idModelo' => 'Modelo',
            'placa' => 'Placa',
            'adaptado' => 'Adaptado',
            'capacidade' => 'Capacidade',
            'combustivel' => 'Combustível',
            'dataVistoriaEstadual' => 'Vistoria semestral - Valido até',
            'dataVistoriaMunicipal' => 'Vistoria municipal',
            'dataVencimentoSeguro' => 'Vencimento da apólice',
            'idCondutor' => 'Condutor',
            'numApolice' => 'Número da apólice',
            'documentoApoliceSeguro' => 'Apólice do seguro',
            'documentoCRLV' => 'CRLV',
            'documentoVistoriaMunicipal' => 'Vistoria municipal',
            'documentoVistoriaEstadual' => 'Vistoria estadual',
            'documentoDPVAT' => 'DPVAT',
            'anexoFotoVeiculo' => 'Foto do veículo',
            'anexoFotoPlaca' => 'Foto da placa',
            'anoFabricacao' => 'Ano de fabricação',
            'anoModelo' => 'Ano do modelo',
            'dataVencimentoCRLV' => 'Vencimento do CRLV',
            'tipoVeiculo' => 'Tipo do veículo',
            'alocacao' => 'Alocação do veículo'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCondutor()
    {
        return $this->hasOne(Condutor::className(), ['id' => 'idCondutor']);
    }

    public function getDocCRLV()
    {
        return $this->hasMany(DocumentoVeiculo::className(), ['idVeiculo' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_CRLV]);
    }
    public function getDocVistoriaEstadual()
    {
        return $this->hasMany(DocumentoVeiculo::className(), ['idVeiculo' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_VISTORIA_ESTADUAL]);
    }
    public function getDocVistoriaMunicipal()
    {
        return $this->hasMany(DocumentoVeiculo::className(), ['idVeiculo' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_VISTORIA_MUNICIPAL]);
    }
    public function getDocApolice()
    {
        return $this->hasMany(DocumentoVeiculo::className(), ['idVeiculo' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_APOLICE]);
    }
    public function getDocDpvat()
    {
        return $this->hasMany(DocumentoVeiculo::className(), ['idVeiculo' => 'id'])->where(['=', 'idTipo', TipoDocumento::TIPO_DPVAT]);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelo()
    {
        return $this->hasOne(Modelo::className(), ['id' => 'idModelo']);
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
        if ($this->$coluna && $coluna != 'ultimaAtualizacaoCRON' && $coluna != 'lng' && $coluna != 'lng' && $coluna != 'enderecoGPS') {
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => $this->placa,
                'tabela' => self::getTableSchema()->name,
                'coluna' => $coluna,
                'antes' => isset($atributosAlterados) ? $atributosAlterados[$coluna] : '',
                'depois' => $this->$coluna,
                'key' => 'idVeiculo',
                'id' => $this->id,
            ]);
        }
    }
    private function classAlert() {
        return 'background:#FFC90E;color:#000;padding:10px;border-radius:10px;font-weight:bold;';

    }
    private function classDanger() {
        return 'background:#ED1C24;color:#FFF;padding:10px;border-radius:10px;font-weight:bold;';
    }
    private function addDays($days, $date){
        return date('Y-m-d',(strtotime ( '+'.$days.' day' , strtotime ($date) ) ) );
    }
    private function removeDays($days, $date){
        return date('Y-m-d',(strtotime ( '-'.$days.' day' , strtotime ($date) ) ) );
    }
  public function toDate($date) {
        return Yii::$app->formatter->asDate($date, "php:d/m/Y"); 
    }

    public function crlvAlerta() {
        if(!$this->dataVencimentoCRLV)
            return '-';
        // if($this->cnhValidade > date('Y-m-d'))
            // return $this->toDate($this->cnhValidade);
      

            $dataApos = $this->dataVencimentoCRLV;
            $datetime1 = new \DateTime($dataApos);
    
            $datetime2 = new \DateTime(date('Y-m-d'));
            
            $difference = $datetime1->diff($datetime2);
            $diff = $difference->days;        
               
    
            if($diff > 0 && $this->dataVencimentoCRLV < date('Y-m-d'))
                return '<span style="'.$this->classDanger().'">'.$this->toDate($this->dataVencimentoCRLV).'</span>';

        $data30diasApos = $this->removeDays(30, $this->dataVencimentoCRLV);
      
        if($data30diasApos <= date('Y-m-d'))
            return '<span style="'.$this->classAlert().'">'.$this->toDate($this->dataVencimentoCRLV).'</span>';

        return $this->toDate($this->dataVencimentoCRLV);
    }


    public function vistoriaEstadualAlerta() {
        if(!$this->dataVistoriaEstadual)
            return '-';
        // if($this->cnhValidade > date('Y-m-d'))
            // return $this->toDate($this->cnhValidade);
      

            $dataApos = $this->dataVistoriaEstadual;
            $datetime1 = new \DateTime($dataApos);
    
            $datetime2 = new \DateTime(date('Y-m-d'));
            
            $difference = $datetime1->diff($datetime2);
            $diff = $difference->days;        
               
    
            if($diff > 0 && $this->dataVistoriaEstadual < date('Y-m-d'))
                return '<span style="'.$this->classDanger().'">'.$this->toDate($this->dataVistoriaEstadual).'</span>';
                  
        $data30diasApos = $this->removeDays(10, $this->dataVistoriaEstadual);
      
        if($data30diasApos <= date('Y-m-d'))
            return '<span style="'.$this->classAlert().'">'.$this->toDate($this->dataVistoriaEstadual).'</span>';

        return $this->toDate($this->dataVistoriaEstadual);
    }

    public function seguroAlerta() {
        if(!$this->dataVencimentoSeguro)
            return '-';
        // if($this->cnhValidade > date('Y-m-d'))
            // return $this->toDate($this->cnhValidade);
      

            $dataApos = $this->dataVencimentoSeguro;
            $datetime1 = new \DateTime($dataApos);
    
            $datetime2 = new \DateTime(date('Y-m-d'));
            
            $difference = $datetime1->diff($datetime2);
            $diff = $difference->days;        
               
    
            if($diff > 0 && $this->dataVencimentoSeguro < date('Y-m-d'))
                return '<span style="'.$this->classDanger().'">'.$this->toDate($this->dataVencimentoSeguro).'</span>';
                  
        $data30diasApos = $this->removeDays(10, $this->dataVencimentoSeguro);
      
        if($data30diasApos <= date('Y-m-d'))
            return '<span style="'.$this->classAlert().'">'.$this->toDate($this->dataVencimentoSeguro).'</span>';

        return $this->toDate($this->dataVencimentoSeguro);
    }

    public function textoAnoFabricacao() {
        $diff = (date('Y') - $this->anoFabricacao);
        if($diff == date('Y'))
            $diff = '-';
        else if($diff < 0)
            $diff = '0';
        else
            $diff .= ' ANO(S)';
        
        return $diff ;
    }
    public function anoAlerta($tipo = 0) {
		$diff = (date('Y') - $this->anoFabricacao);
		if($tipo == 1){
			
			if($diff < 0 || !$this->anoFabricacao)
                return $this->textoAnoFabricacao();
			$dozeAnosAtras = date('Y') - 12;

			if($this->capacidade <= 15 && $diff >= 12 && $diff < 13)
				return $this->textoAnoFabricacao();
			if($this->capacidade <= 15 && $diff >= 13)
				return $this->textoAnoFabricacao();
			if($this->capacidade > 15 && $diff == 15)
				return $this->textoAnoFabricacao();
			if($this->capacidade > 15 && $diff >= 16)
				return $this->textoAnoFabricacao();
			return $this->textoAnoFabricacao();
			
		}else{
			
            if($diff < 0 || !$this->anoFabricacao)
                return $this->textoAnoFabricacao();
			$dozeAnosAtras = date('Y') - 12;

			if($this->capacidade <= 15 && $diff >= 12 && $diff < 13)
				return '<span style="'.$this->classAlert().'">'.$this->textoAnoFabricacao().'</span>';
			if($this->capacidade <= 15 && $diff >= 13)
				return '<span style="'.$this->classDanger().'">'.$this->textoAnoFabricacao().'</span>';
			if($this->capacidade > 15 && $diff == 15)
				return '<span style="'.$this->classAlert().'">'.$this->textoAnoFabricacao().'</span>';
			if($this->capacidade > 15 && $diff >= 16)
				return '<span style="'.$this->classDanger().'">'.$this->textoAnoFabricacao().'</span>';
			return $this->textoAnoFabricacao();
		}
       
        
    }
}
 