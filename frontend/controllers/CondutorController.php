<?php

namespace frontend\controllers;

use common\models\Aluno;
use common\models\SolicitacaoTransporte;
use Yii;
use common\models\Escola;
use common\models\Condutor;
use common\models\CondutorRegiao;
use common\models\CondutorRota;
use common\models\CondutorSearch;
use common\models\ControleFinanceiroSearch;
use common\models\ControleFinanceiro;
use common\models\HistoricoMovimentacaoRota;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\helpers\Json;
use common\models\Log;
use yii\web\Response;
use common\models\TipoDocumento;
use yii\web\UploadedFile;
use common\models\Usuario;
use common\models\Veiculo;
use common\models\DocumentoCondutor;
use kartik\form\ActiveForm;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
/**
 * CondutorController implements the CRUD actions for Condutor model.
 */
class CondutorController extends Controller
{

    protected $session;
    public function init()
    {
        parent::init();
        $this->session = Yii::$app->session;
        $this->session->open();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // ...
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = ($action->id !== "save-order"); // <-- here
        return parent::beforeAction($action);
    }

    public function actionCondutorJson($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->findModel($id);
    }
    /**
     * Lists all Condutor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CondutorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = ['pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	
    public function actionEscolas()
    {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       if($_POST){		
			
			$sqlEscolas ='select distinct e.nome as nome,e.tipo,e.unidade from SolicitacaoTransporte st  join CondutorEscola  c on c.idCondutor = st.idCondutor join Escola  e on c.idEscola = e.id  where st.`status` = 6 and  c.idCondutor = '.$_POST['condutor'].' order by e.nome' ;
			$dadosEscola = Yii::$app->getDb()->createCommand($sqlEscolas)->queryAll();
			
			$escolas = '';
			$escolas.='<BR>';
			foreach($dadosEscola as $esc){				
				$escolas.= Escola::ARRAY_UNIDADE[$esc['unidade']];
				$escolas.= ' - ';
				$escolas.= Escola::ARRAY_TIPO[$esc['tipo']];
				$escolas.= ' - ';
				$escolas.= '<B>'.$esc['nome'].'</B>';
				
				$escolas.='<BR>';
			}
			
			return $escolas;
			
			
	   }else{
		   echo false;
	   }
    }

	 public function actionBairros()
    {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       if($_POST){		
			
			$sqlBairros ='select distinct a.bairro as bairro from SolicitacaoTransporte st  left join Aluno a on st.idAluno = a.id 	where st.idCondutor = '.$_POST['condutor'].'  and st.`status` = 6 order by a.bairro' ;
			$dadosBairro = Yii::$app->getDb()->createCommand($sqlBairros)->queryAll();
			$bairros = '';
			$bairros.='<BR>';
			foreach($dadosBairro as $esc){				
				$bairros.= '<B>'.$esc['bairro'].'</B>';				
				$bairros.='<BR>';
			}
			
			return $bairros;
			
			
	   }else{
		   echo false;
	   }
    }
	
	public function actionGravarControle()
    {	
		$condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();		
		if($_POST['mes'] and $_POST['ano']){			
			$mes = $_POST['mes'];
			$ano = $_POST['ano'];
			$diasTrabalhados = $_POST['diasTrabalhados'];
			$totalFeito = $_POST['totalFeito'];
			$valor = $_POST['valor'];			
			$sabadosTrabalhados = $_POST['sabadosTrabalhados'];
			$totalFeitoSabado = $_POST['totalFeitoSabado'];
			$valorSabado = $_POST['valorSabado'];			
			$total = $_POST['total'];
			$totalSabado = $_POST['totalSabado'];			
			$totalGeral = $_POST['totalGeral'];
			$empresa = $_POST['empresa'];
			$alvara = $_POST['alvara'];
			$sqlControleFinanceiro ="select count(*) as total from ControleFinanceiro f where f.idCondutor = $condutor->id  and f.mes = $mes and ano = $ano" ;
			$dadosFinanceiro = Yii::$app->getDb()->createCommand($sqlControleFinanceiro)->queryAll();
			if($dadosFinanceiro[0]['total'] <> 0 ){		
				\Yii::$app->db->createCommand("UPDATE ControleFinanceiro
				SET 
				diasTrabalhados=:diasTrabalhados, 
				sabadoLetivo=:sabadosTrabalhados, 
				viagemKm1=:viagemKm1 , 
				viagemKm2=:viagemKm2 ,
				valorDiasUteis=:valorDiasUteis ,
				valorSabado=:valorSabado ,
				valorViagemKm1=:valorViagemKm1 ,
				valorViagemKm2=:valorViagemKm2 ,
				valorNota=:valorNota,
				nomeEmpresa=:empresa ,
				alvara=:alvara				
				WHERE idCondutor=".$condutor->id." and mes=".$mes." and ano=".$ano)
				->bindValue(':diasTrabalhados', $diasTrabalhados)
				->bindValue(':viagemKm1', $totalFeito)
				->bindValue(':sabadosTrabalhados', $sabadosTrabalhados)
				->bindValue(':viagemKm2', $totalFeitoSabado)
				->bindValue(':valorSabado', $totalSabado)
				->bindValue(':valorViagemKm1', $valor)
				->bindValue(':valorViagemKm2', $valorSabado)
				->bindValue(':valorDiasUteis', $total)
				->bindValue(':empresa', $empresa)
				->bindValue(':alvara', $alvara)
				->bindValue(':valorNota', $totalGeral)->execute();				
			}else{
			
				Yii::$app->getDb()->createCommand()->insert('ControleFinanceiro', [
					'idCondutor'=> $condutor->id,
					'ano' => $ano ,
					'mes' => $mes ,
					'diasTrabalhados' => $_POST['diasTrabalhados'] ,
					'sabadoLetivo' => $_POST['sabadosTrabalhados'] ,
					'viagemKm1' => $_POST['totalFeito'] ,
					'viagemKm2' => $_POST['totalFeitoSabado'] ,
					'valorSabado' => $_POST['valorSabado'] ,
					'valorDiasUteis' => $_POST['total'] ,
					'valorNota' => $_POST['totalGeral'] ,
					'valorViagemKm1' => $_POST['valor'] ,
					'valorViagemKm2' => $_POST['valorSabado'] ,
					'nomeEmpresa' => $_POST['empresa'] ,
					'alvara' => $_POST['alvara'] ,
				])->execute();
			}	
			echo 1;
		}else{
			echo 0;
		}		
	}
	
	public function actionFolhaPonto()
    {	
		$mes = date("m");
		if($mes < 10){
			$mes = str_replace("0", "", $mes);
		}		
		$ano = date("Y");
		$primeiroDia = '1';	
		$diaCorrente =  date("d");
		//$diaCorrente =  24;

		$diaCorte =  date("t") - 7;
		if(($diaCorrente <= $diaCorte)){
			$mes = $mes - 1;
		}
		$ultimoDia =  date("t");
		$ultimoDiaMes =  date("t", strtotime($ano.'-'.$mes.'-'.$primeiroDia)) . "\n";	
		$primeiraSemana = array();
		$segundaSemana = array();
		$terceiraSemana = array();
		$quartaSemana = array();
		$quintaSemana = array();
		$sextaSemana = array();
		
		for($i=1;$i<=7;$i++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $i.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			if($diaSemana == 0){
				break;
			}else{
				$primeiraSem[$diaSemana] = $ano.'-'.$mes.'-'.$i;
			}			
		}		
		$ate = $i+6;
		for($j=$i;$j<=$ate;$j++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $j.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$segundaSem[$diaSemana] = $ano.'-'.$mes.'-'.$j;		
		}
		$ate = $j+6;
		for($m=$j;$m<=$ate;$m++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $m.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$terceiraSem[$diaSemana] = $ano.'-'.$mes.'-'.$m;			
		}	
		$ate = $m+6;
		for($n=$m;$n<=$ate;$n++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $n.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$quartaSem[$diaSemana] = $ano.'-'.$mes.'-'.$n;
		}		
		$ate = $n+6;
		for($o=$n;$o<=$ate;$o++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $o.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			if($o<=$ultimoDiaMes){
				$quintaSem[$diaSemana] = $ano.'-'.$mes.'-'.$o;
			}			
		}
		$ate = $ultimoDia;
		for($p=$o;$p<=$ate;$p++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $p.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			if($p<=$ultimoDiaMes){
				$sextaSem[$diaSemana] = $ano.'-'.$mes.'-'.$p;
			}			
		}		
		$cor = '#98FB98';
		$imprime = '0';
		$primeiraSemana = $this->buscaConsultaDiaCondutor($primeiraSem,0,$cor,$imprime);
		$segundaSemana = $this->buscaConsultaDiaCondutor($segundaSem,0,$cor,$imprime);
		$terceiraSemana = $this->buscaConsultaDiaCondutor($terceiraSem,0,$cor,$imprime); 
		$quartaSemana = $this->buscaConsultaDiaCondutor($quartaSem,0,$cor,$imprime);
		$quintaSemana = $this->buscaConsultaDiaCondutor($quintaSem,0,$cor,$imprime);
		$sextaSemana = $this->buscaConsultaDiaCondutor($sextaSem,0,$cor,$imprime);	

		$condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();		
		$sqlEscolas ='select distinct e.nome as nome,e.tipo,e.unidade,e.id from  CondutorEscola  c  join Escola  e on c.idEscola = e.id  where  c.idCondutor  = '.$condutor->id.' order by e.nome' ;
		$dadosEscola = Yii::$app->getDb()->createCommand($sqlEscolas)->queryAll();
	
		$escolas = array();
		$i=1;
		foreach($dadosEscola as $esc){				
			$escolas[$i]['id'] = $esc['id'];
			$escolas[$i]['tipo'] = Escola::ARRAY_TIPO[$esc['tipo']];
			$escolas[$i]['nome'] = $esc['nome'];
			$i++;

		}
		
		$ini = $ano.'-'.$mes.'-'.$primeiroDia;
		$fim = $ano.'-'.$mes.'-'.$ultimoDia;
		$sqlDiasTrabalhados ="select count(*) as total from FolhaPonto f where f.idCondutor = $condutor->id and f.`data` between '$ini' and '$fim' and DAYOFWEEK(f.data) <> 7" ;
		$diasTrabalhados = Yii::$app->getDb()->createCommand($sqlDiasTrabalhados)->queryAll();
		
		$sqlSabadosTrabalhados ="select count(*) as total from FolhaPonto f where f.idCondutor = $condutor->id and f.`data` between '$ini' and '$fim' and DAYOFWEEK(f.data) = 7" ;
		$sabadosTrabalhados = Yii::$app->getDb()->createCommand($sqlSabadosTrabalhados)->queryAll();
		
		$sqlControleFinanceiro ="select f.viagemKm1,f.valorViagemKm1,f.diasTrabalhados,f.valorNota,f.sabadoLetivo,f.viagemKm2,f.valorViagemKm2,f.valorSabado,f.valorDiasUteis,f.nomeEmpresa,f.alvara from ControleFinanceiro f where f.idCondutor = $condutor->id  and f.mes = $mes and ano = $ano" ;
		$dadosFinanceiro = Yii::$app->getDb()->createCommand($sqlControleFinanceiro)->queryAll();
		
		if($dadosFinanceiro[0]['diasTrabalhados']){
			$nomeEmpresa = $dadosFinanceiro[0]['nomeEmpresa'];
		}else{
			$nomeEmpresa = $model->nome;
		}
		
		if($dadosFinanceiro[0]['alvara']){
			$alvara = $dadosFinanceiro[0]['alvara'];
		}else{
			$alvara = $model->alvara;
		}
		
		 return $this->render('folha-ponto', [
            'primeiraSemana' => $primeiraSemana,
			'segundaSemana' => $segundaSemana,
			'terceiraSemana' => $terceiraSemana,
			'quartaSemana' => $quartaSemana,
			'quintaSemana' => $quintaSemana,
			'sextaSemana' => $sextaSemana,            
			'escolas' => $escolas,         
			'diasTrabalhados' => $diasTrabalhados[0]['total'],    
			'sabadosTrabalhados' => $sabadosTrabalhados[0]['total'],    
			'tipoContrato' => $condutor->tipoContrato, 
			'dadosFinanceiro' => $dadosFinanceiro, 
			'ini' => $ini, 
			'fim' => $fim, 	
			'mes' => $mes, 	
			'ano' => $ano, 	
			'alvara' => $alvara, 	
			'nomeEmpresa' => $nomeEmpresa, 	
			'idCondutor' => $condutor->id, 
        ]);		
	}	
	
	public function actionBuscar()
	{	
		if($_POST){
			$condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();			
			if($_POST['buscar']){
				$dia = $_POST['dia'];
				$ini = $_POST['ini'];
				$fim = $_POST['fim'];
				if($dia == 1){
					$sqlDiasTrabalhados ="select count(*) as total from FolhaPonto f where f.idCondutor = $condutor->id and f.`data` between '$ini' and '$fim'  and DAYOFWEEK(f.data) <> 7" ;
				}else{
					$sqlDiasTrabalhados ="select count(*) as total from FolhaPonto f where f.idCondutor = $condutor->id and f.`data` between '$ini' and '$fim'  and DAYOFWEEK(f.data) = 7" ;
				}
				
				$diasTrabalhados = Yii::$app->getDb()->createCommand($sqlDiasTrabalhados)->queryAll();
				\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;	
				return $diasTrabalhados;
			
			}else{
				echo 0 ;
			}			
		}else{
			echo 0 ;
		}		
	}
	
	public function actionGravar()
	{	
		if($_POST){
			$condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();			
			if($_POST['data']){
				$dataGravar = $_POST['data'];
				if($_POST['justificativa']){
					$just = $_POST['justificativa'];
				}else{
					$just = '-';
				}
				if($_POST['idEscola']){
					$idEscola = $_POST['idEscola'];
				}else{
					$idEscola = '0';
				}
				Yii::$app->getDb()->createCommand()->insert('FolhaPonto', [
					'idCondutor'=> $condutor->id,
					'data' => $dataGravar ,
					'justificativa' => $just ,
					'idEscola' => $idEscola
				])->execute();
				echo 1 ;
			}else{
				echo 0 ;
			}			
		}else{
			echo 0 ;
		}		
	}
	public function actionApagar()
	{	
		if($_POST){
			$condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();			
			if($_POST['data']){
				$dataApagar = $_POST['data'];
				$diaSemanaNumero = date('w', strtotime($dataApagar));
				\Yii::$app->db->createCommand()->delete('FolhaPonto', ['data' => $dataApagar])->execute();
				if($diaSemanaNumero == 6){
					echo 2 ;
				}else{
					echo 1 ;
				}
				
			}else{
				echo 0 ;
			}			
		}else{
			echo 0 ;
		}		
	}
	

	public function buscaConsultaDiaCondutor($array,$id = 0,$cor,$imprime )
	{		
		if($id == 0){
			$condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
		}else{
			$condutor->id = $id;
		}
		
		$hoje =  strtotime(date('d-m-Y'));
		$html='';
		for($i=0;$i<=6;$i++){										
			$key = array_key_exists ($i, $array);							
			if($key){
				$dia = array_search($i, $array);
				$diaArr = explode('-',$array[$i]);
				$primeiroDiaMes =  strtotime($array[$i]);
				
				$sqlDiaSupervisor ="select status from DiasFolhaPonto p where p.`data` = '$array[$i]' ";
				$dadosDiaSupervisor = Yii::$app->getDb()->createCommand($sqlDiaSupervisor)->queryAll();						
				if($dadosDiaSupervisor[0]['status'] == '1'){
					$html .='<td id="'.$array[$i].'" class=""  style="text-align:center;color:.;background-color:#dedede">'.$diaArr[2].'</td>';
				}else{
					$sqlDia ="select count(*) as total,justificativa from FolhaPonto p where p.`data` = '$array[$i]' and  p.idCondutor = ".$condutor->id  ;
					$dadosDia = Yii::$app->getDb()->createCommand($sqlDia)->queryAll();
					if($dadosDia[0]['total'] == '1'){
						if($imprime == 1){
							$html .='<td id="'.$array[$i].'" class="apagar" style="font-weight:bold;text-align:center;color:#000;background-color:'.$cor.'">
							<table style="font-weight:bold;width:100%">
							<tr><td></td><td style="width:50%"> x </td><td></td></tr>
							<tr><td></td><td style="width:50%">'.$diaArr[2].'</td><td></td></tr></table> </td>';		
						}else{
							$html .='<td id="'.$array[$i].'" class="apagar" style="font-weight:bold;text-align:center;color:#000;background-color:'.$cor.'">'.$diaArr[2].' </td>';		
						}		
					}else{
						if(($i == 0) or $i == 6){
							if($imprime == 1){
								$html .='<td id="'.$array[$i].'" class="gravarComJustificativa" style="cursor: pointer;text-align:center;font-weight:bold;color:#000;background-color:#dedede"> '.$diaArr[2].' </td>';
							}else{
								$html .='<td id="'.$array[$i].'" class="gravarComJustificativa" style="cursor: pointer;text-align:center;font-weight:bold;color:#000">'.$diaArr[2].'</td>';
							}									
						}else{
							if($imprime == 1){
								$html .='<td id="'.$array[$i].'" class="gravar" style="cursor: pointer;text-align:center;font-weight:bold;color:#000;background-color:#dedede"> '.$diaArr[2].' </td>';
							}else{
								$html .='<td id="'.$array[$i].'" class="gravar" style="cursor: pointer;text-align:center;font-weight:bold;color:#000;">'.$diaArr[2].'</td>';
							}			
						}	
					}			
					
				}					
								
			}else{
				$html .='<td style="text-align:center;font-weight:bold"><br></td>';
			}
		}
		return $html;
	}
	
	public function buscaConsultaDiaSupervisor($array)
	{		

		$hoje =  strtotime(date('d-m-Y'));
		$html='';
		for($i=0;$i<=6;$i++){										
			$key = array_key_exists ($i, $array);							
			if($key){
				$dia = array_search($i, $array);
				$diaArr = explode('-',$array[$i]);
				$primeiroDiaMes =  strtotime($array[$i]);
				
				$sqlDia ="select status,data from DiasFolhaPonto p where p.`data` = '$array[$i]' " ;
				$dadosDia = Yii::$app->getDb()->createCommand($sqlDia)->queryAll();
				
				if($dadosDia[0]['status'] == 1){
					$html .='<td style="cursor: pointer;text-align:center;font-weight:bold;color:#000!important;background-color:#dedede"><input type="checkbox" id="status" name="dia[]" value="'.$array[$i].'|'.$dadosDia[0]['status'].'" > '.$diaArr[2].'</td>'; 
				}else{
					$html .='<td class="ocorrencia" id="'.$array[$i].'" style="cursor: pointer;text-align:center;font-weight:bold;color:#000!important;background-color:#fff"><input type="checkbox" id="status" name="dia[]" value="'.$array[$i].'|'.$dadosDia[0]['status'].'" > '.$diaArr[2].'</td>';
				}		
				
			}else{
				$html .='<td style="text-align:center;font-weight:bold"><br></td>';
			}
		}
		return $html;
	}
	
	public function actionGravarOcorrencia()
	{	
		if($_POST){
			if($_POST['data']){
				$dataGravar = $_POST['data'];
				if($_POST['ocorrencia'] == '-'){
					$ocorrencia = '-';
					$status = 0;
				}else{
					$ocorrencia = $_POST['ocorrencia'];
					$status = 1;
				}
				\Yii::$app->db->createCommand("UPDATE DiasFolhaPonto SET ocorrencia=:ocorrencia,status=:status WHERE data=:data")->bindValue(':data', $dataGravar)->bindValue(':ocorrencia', $ocorrencia)->bindValue(':status', $status)->execute();					
				echo 1 ;
			}else{
				echo 0 ;
			}			
		}else{
			echo 0 ;
		}		
	}
	
	
	
	public function actionBuscarOcorrencia()
	{	
		if($_POST){
			if($_POST['buscar']){		
				$mes = $_POST['mes'];
				$ano = $_POST['ano'];				
				$ultimoDia = date("t", mktime(0,0,0,$mes,'01',$ano)); 				
				$ini =$ano.'-'.$mes.'-01';
				$fim =$ano.'-'.$mes.'-'.$ultimoDia;				
				$sqlOcorrencias ="select d.data,d.ocorrencia,DATE_FORMAT(d.data,'%d-%m-%Y') as dataBr  from DiasFolhaPonto d where d.`data` between '$ini' and '$fim' and d.ocorrencia <> '-'" ;
				$ocorrencias = Yii::$app->getDb()->createCommand($sqlOcorrencias)->queryAll();				
				
				$isArrayLog =  is_array($ocorrencias) ? '1' : '0';
				if($isArrayLog == 1) {
					foreach($ocorrencias as $dado){			
						$arquivo = "<i class='glyphicon glyphicon-trash'></i>";				
						$json .= "<span title='Excluir'  style='cursor: pointer' id=".$dado['data']." class='excluir' >".$dado['dataBr'].' - '.$dado['ocorrencia'].' - '.$arquivo."</span> <BR>";
					}
				}else{
					$json .= "0";
				}
				
	
				
				\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;	
				return $json;			
			}else{
				echo 0 ;
			}			
		}else{
			echo 0 ;
		}			
	}
	
	public function actionGravarSupervisor()
    {
		if($_POST['dia']){
			foreach($_POST['dia'] as $val){
				$arr = explode('|',$val);
				if($arr[1] == '1'){
					$status = 2;
					$ocorrencia = '-';
					\Yii::$app->db->createCommand("UPDATE DiasFolhaPonto SET status=:status,ocorrencia=:ocorrencia WHERE data=:data")->bindValue(':data', $arr[0])->bindValue(':status', $status)->bindValue(':ocorrencia', $ocorrencia)->execute();					
				}else{
					$status = 1;
					\Yii::$app->db->createCommand("UPDATE DiasFolhaPonto SET status=:status WHERE data=:data")->bindValue(':data', $arr[0])->bindValue(':status', $status)->execute();					
				}					
				
			}
			$mes = $_POST['mes'];
			$ano = $_POST['ano'];				

			 \Yii::$app->getSession()->setFlash('success', 'Os dados foram salvos');
			return $this->redirect(['condutor/controle-financeiro', 'ano' => $ano, 'mes' => $mes]);
			
			exit(0);	  
		}else{
			\Yii::$app->getSession()->setFlash('error', 'Escolha m??s e ano');
			return $this->redirect(['condutor/controle-financeiro']);
			exit(0);	  
		}
	}
    public function actionControleFinanceiro()
    {
        $ano = Yii::$app->request->get('ano');
        $mes = Yii::$app->request->get('mes');

        if (isset($ano) && isset($mes))
        {
            /* Quando um ano e m??s estiverem selecionados, vou verificar se todos os condutores j?? tem um registro na tabela ControleFinanceiro... Os que n??o tiverem, eu crio. */
            $condutores = Condutor::find()->joinWith('veiculo', 'Condutor.id=Veiculo.idCondutor')->where(['=', 'alocacao', Veiculo::ALOCACAO_FRETADO])->all();
            
            // 
            foreach ($condutores as $condutor)
            {
                // throw new NotFoundHttpException(print_r($condutor->getHistoricoFinanceiro($ano, $mes), true));
                if (!$condutor->getHistoricoFinanceiro($ano, $mes))
                {
                    $historico = new ControleFinanceiro();
                    $historico->ano = $ano;
                    $historico->mes = $mes;
                    $historico->idCondutor = $condutor->id;
                    $historico->saldoAF = $condutor->saldoAFAnterior;
                    //$historico->save();
                }
            }
			


        }
        // $searchModel = new CondutorSearch();
        $searchModel = new ControleFinanceiroSearch();
        $searchModel->alocacao = Veiculo::ALOCACAO_FRETADO;
        $searchModel->ano = $ano;
        $searchModel->mes = $mes;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $historico = ControleFinanceiro::find()->where(['=', 'ano', $ano])->andWhere(['=', 'mes', $mes])->all();
        $arrayHistorico = [];

        foreach ($historico as $registro)
        {
            // $date = \DateTime::createFromFormat( 'Y-m-d', $registro->protocoloTESC);
            // $registro->protocoloTESC = $date ? $date->format('d/m/Y') : null;

            // $date = \DateTime::createFromFormat( 'Y-m-d', $registro->protocoloGC);
            // $registro->protocoloGC = $date ? $date->format('d/m/Y') : null;

            $arrayHistorico[$registro->id] = [
                'diasTrabalhados' => $registro->diasTrabalhados,
                'sabadoLetivo' => $registro->sabadoLetivo,
                'diasExcepcionais1' => $registro->diasExcepcionais1,
                'viagemKm1' => $registro->viagemKm1,
                'diasExcepcionais2' => $registro->diasExcepcionais2,
                'viagemKm2' => $registro->viagemKm2,
                'valorNota' => $registro->valorNota,
                'protocoloTESC' => $registro->protocoloTESC,
                'protocoloGC' => $registro->protocoloGC,
                'lote' => $registro->lote,
                'saldoAF' => $registro->saldoAF,
				'valorViagemKm1' => $registro->valorViagemKm1,
				'valorViagemKm2' => $registro->valorViagemKm2,
				'valorSabado' => $registro->valorSabado,
				'valorDiasUteis' => $registro->valorDiasUteis,
				
            ];
        }
		
		
		if (empty($ano) && empty($mes)){
			$mes = date("m");
			$ano = date("Y");
		}	
		
		$primeiroDia = '1';	
		$ultimoDia = date("t", mktime(0,0,0,$mes,'01',$ano)); 
		$ultimoDiaMes =  date("t", strtotime($ano.'-'.$mes.'-'.$primeiroDia));	
		$primeiraSemana = array();
		$segundaSemana = array();
		$terceiraSemana = array();
		$quartaSemana = array();
		$quintaSemana = array();
		$sextaSemana = array();
		
		for($i=1;$i<=7;$i++){					
			$date = \DateTime::createFromFormat('d/m/Y H:i:s', $i.'/'.$mes.'/'.$ano.' 00:00:00');		
			$diaSemana = $date->format('w');
			if($diaSemana == 0){
				break;
			}else{
				$primeiraSem[$diaSemana] = $ano.'-'.$mes.'-'.$i;
			}			
		}		
		
		$ate = $i+6;
		for($j=$i;$j<=$ate;$j++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $j.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$segundaSem[$diaSemana] = $ano.'-'.$mes.'-'.$j;		
		}
		$ate = $j+6;
		for($m=$j;$m<=$ate;$m++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $m.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$terceiraSem[$diaSemana] = $ano.'-'.$mes.'-'.$m;			
		}	
		$ate = $m+6;
		for($n=$m;$n<=$ate;$n++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $n.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$quartaSem[$diaSemana] = $ano.'-'.$mes.'-'.$n;
		}		
		$ate = $n+6;
		for($o=$n;$o<=$ate;$o++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $o.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			if($o<=$ultimoDiaMes){
				$quintaSem[$diaSemana] = $ano.'-'.$mes.'-'.$o;
			}			
		}
		$ate = $ultimoDia;
		for($p=$o;$p<=$ate;$p++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $p.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			if($p<=$ultimoDiaMes){
				$sextaSem[$diaSemana] = $ano.'-'.$mes.'-'.$p;
			}			
		}		
		$primeiraSemana = $this->buscaConsultaDiaSupervisor($primeiraSem);
		$segundaSemana = $this->buscaConsultaDiaSupervisor($segundaSem);
		$terceiraSemana = $this->buscaConsultaDiaSupervisor($terceiraSem); 
		$quartaSemana = $this->buscaConsultaDiaSupervisor($quartaSem);
		$quintaSemana = $this->buscaConsultaDiaSupervisor($quintaSem);
		$sextaSemana = $this->buscaConsultaDiaSupervisor($sextaSem);
		


        return $this->render('controle-financeiro', [
			'primeiraSemana' => $primeiraSemana,
			'segundaSemana' => $segundaSemana,
			'terceiraSemana' => $terceiraSemana,
			'quartaSemana' => $quartaSemana,
			'quintaSemana' => $quintaSemana,
			'sextaSemana' => $sextaSemana,            
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'historico' => $arrayHistorico,
			'mes' => $mes,
			'ano' => $ano,
			'ocorrencias' => $dadosOco[0]['data']
			
        ]);
    }

    public function actionSaveHistoricoFinanceiro()
    {
        $ano = Yii::$app->request->post('ano');
        $mes = Yii::$app->request->post('mes');
        $condutores = Yii::$app->request->post('condutores');
        $diasTrabalhados = Yii::$app->request->post('diasTrabalhados');
        $sabadoLetivo = Yii::$app->request->post('sabadoLetivo');
        $diasExcepcionais1 = Yii::$app->request->post('diasExcepcionais1');
        $viagemKm1 = Yii::$app->request->post('viagemKm1');
        $diasExcepcionais2 = Yii::$app->request->post('diasExcepcionais2');
        $viagemKm2 = Yii::$app->request->post('viagemKm2');
        $valorNota = Yii::$app->request->post('valorNota');
        $protocoloTESC = Yii::$app->request->post('protocoloTESC');
        $protocoloGC = Yii::$app->request->post('protocoloGC');
        $lote = Yii::$app->request->post('lote');
        $saldoAF = Yii::$app->request->post('saldoAF');

        // throw new NotFoundHttpException(print_r(Yii::$app->request->post(), true));
        if (isset($ano) && isset($mes))
        {
            ControleFinanceiro::deleteAll(['ano' => $ano, 'mes' => $mes]);
            foreach ($condutores as $index => $id)
            {
                // $historico = ControleFinanceiro::find()->where(['=', 'idCondutor', $id])->andWhere(['=', 'ano', $ano])->andWhere(['=', 'mes', $mes])->one();
                $historico = new ControleFinanceiro();
                $historico->ano = $ano;
                $historico->mes = $mes;
                $historico->idCondutor = $id;
                $historico->diasTrabalhados = $diasTrabalhados[$index] ? $diasTrabalhados[$index] : 0;
                $historico->sabadoLetivo = $sabadoLetivo[$index] ? $sabadoLetivo[$index] : 0;
                $historico->diasExcepcionais1 = $diasExcepcionais1[$index] ? $diasExcepcionais1[$index] : 0;
                $historico->viagemKm1 = $viagemKm1[$index] ? $viagemKm1[$index] : 0;
                $historico->diasExcepcionais2 = $diasExcepcionais2[$index] ? $diasExcepcionais2[$index] : 0;
                $historico->viagemKm2 = $viagemKm2[$index] ? $viagemKm2[$index] : 0;
                $historico->valorNota = $valorNota[$index] ? $valorNota[$index] : 0;
                
                // $date = \DateTime::createFromFormat( 'd/m/Y', $protocoloTESC[$index]);
                // $historico->protocoloTESC = $date ? $date->format('Y-m-d') : null;

                // $date = \DateTime::createFromFormat( 'd/m/Y', $protocoloGC[$index]);
                // $historico->protocoloGC = $date ? $date->format('Y-m-d') : null;

                $historico->protocoloTESC = $protocoloTESC[$index];
                $historico->protocoloGC = $protocoloGC[$index];

                $historico->lote = $lote[$index] ? $lote[$index] : 0;
                $historico->saldoAF = $saldoAF[$index] ? $saldoAF[$index] : 0;
                
                $historico->save();
            }
        }
        return $this->redirect(['condutor/controle-financeiro', 'ano' => $ano, 'mes' => $mes]);
    }

    

    // GET de campos usandos no form
    protected function getTelefoneValido($model) {
        $str = '';
        if($model->telefone2 && strlen($model->telefone2) > 5)
        $str .=  ' '.$model->telefone2;
        if($model->celular && strlen($model->celular) > 5)
        $str .= ' '. $model->celular;
        if($model->celular2 && strlen($model->celular2) > 5)
            $str .= ' '. $model->celular2;
        if($model->telefone && strlen($model->telefone) > 5)
            $str .= ' '. $model->telefone;
        if(!$str)
            return '-';
        return $str;
    }

    public function actionExportarEndereco($tipo,$status){
    // $content = '<table>';
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    // $sheet->setCellValue('A1:N5', 'Logo');
    $left = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        )
    );
    $right = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        )
    );
    $center = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        )
    );

    $borderHard = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];
    $borderSoft = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];

    $colorRed =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED );
    $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE );

    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setPath(Yii::getAlias('@webroot').'/img/brasao.png'); // put your path and image here
    $drawing->setCoordinates('A1');
    $drawing->setOffsetX(50);
    $drawing->setOffsetY(15);
    $drawing->setRotation(0);
    $drawing->setWorksheet($sheet);

    $drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing2->setPath(Yii::getAlias('@webroot').'/img/faixa.png'); // put your path and image here
    $drawing2->setCoordinates('C1');
    $drawing2->setOffsetX(0);
    $drawing2->setOffsetY(0);
    $drawing2->setRotation(0);
    $drawing2->setWorksheet($sheet);


    // SETUP DAS COLUNAS
    $sheet->getColumnDimension('A')->setWidth(30);
    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(30);
    $sheet->getColumnDimension('F')->setWidth(40);
    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->getColumnDimension('I')->setWidth(15);
	$sheet->getColumnDimension('J')->setWidth(30);
	$sheet->getColumnDimension('K')->setWidth(10);
	$sheet->getColumnDimension('L')->setWidth(10);
	$sheet->getColumnDimension('M')->setWidth(10);
	$sheet->getColumnDimension('N')->setWidth(10);
	$sheet->getColumnDimension('O')->setWidth(10);
	$sheet->getColumnDimension('P')->setWidth(60);
	$sheet->getColumnDimension('Q')->setWidth(60);
	$sheet->getColumnDimension('R')->setWidth(100);
	$sheet->getColumnDimension('S')->setWidth(50);
	$sheet->getColumnDimension('T')->setWidth(20);
	$sheet->getColumnDimension('U')->setWidth(20);
	$sheet->getColumnDimension('V')->setWidth(20);
	$sheet->getColumnDimension('X')->setWidth(20);
	$sheet->getColumnDimension('Y')->setWidth(20);
	$sheet->getColumnDimension('W')->setWidth(20);
	$sheet->getColumnDimension('Z')->setWidth(20);
	$sheet->getColumnDimension('AA')->setWidth(20);
	$sheet->getColumnDimension('AB')->setWidth(20);
	$sheet->getColumnDimension('AC')->setWidth(20);
	$sheet->getColumnDimension('AD')->setWidth(20);
	$sheet->getColumnDimension('AE')->setWidth(20);
	$sheet->getColumnDimension('AF')->setWidth(20);
	$sheet->getColumnDimension('AG')->setWidth(40);
	$sheet->getColumnDimension('AH')->setWidth(40);
	
    //
    $i = 1;
    // PR??XIMA LINHA
    $sheet->mergeCells('A'.$i.':B'.($i+4));
    $sheet->mergeCells('C'.$i.':AI'.$i);
    $sheet->mergeCells('C'.($i+1).':AI'.($i+1));
    $sheet->setCellValue('C'.($i+1), "Secretaria de Educa????o e Cidadania");
    $sheet->getStyle('C'.($i+1))->applyFromArray($left)->getFont()->setBold(true);

    $sheet->mergeCells('C'.($i+2).':AI'.($i+4));
    $sheet->setCellValue('C'.($i+2), "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
    $sheet->getStyle('C'.($i+2))->getAlignment()->setWrapText(true);

    $sheet->getStyle('A'.($i+2).':AI'.($i+2))->applyFromArray($left);


    $i+=5;
    $sheet->getStyle('A'.$i.':I'.$i)->applyFromArray($center);

    $sheet->setCellValue('A'.$i, "NOME");
	$sheet->setCellValue('B'.$i, "STATUS");
	$sheet->setCellValue('C'.$i, "DATA DE NASCIMENTO");
	$sheet->setCellValue('D'.$i, "CPF DO CONDUTOR");
	$sheet->setCellValue('E'.$i, "RG + ORG??O EMISSOR");
	$sheet->setCellValue('F'.$i, "E-MAIL");
	$sheet->setCellValue('G'.$i, "N??MERO DA CNH");
	$sheet->setCellValue('H'.$i, "VALIDADE DA CNH");	
	$sheet->setCellValue('I'.$i, "NIT");	
	$sheet->setCellValue('J'.$i, "PER??ODO DE COTRATO");	
	$sheet->setCellValue('K'.$i, "TIPO DE CONTRATO");	
	$sheet->setCellValue('L'.$i, "VALOR PAGO");	
    $sheet->setCellValue('M'.$i, "ALVAR??");
	$sheet->setCellValue('N'.$i, "INSCRI????O MUNICIPAL");
    $sheet->setCellValue('O'.$i, "REGI??O DE ATUA????O");
    $sheet->setCellValue('P'.$i, "ENDERE??O COMPLETO");
    $sheet->setCellValue('Q'.$i, "TELEFONE");
	$sheet->setCellValue('R'.$i, "ESCOLAS ATENDIDAS");
    $sheet->setCellValue('S'.$i, "VE??CULO");
	$sheet->setCellValue('T'.$i, "TIPO DO VE??CULO");
	$sheet->setCellValue('U'.$i, "ALOCA????O DO VE??CULO");
    $sheet->setCellValue('V'.$i, "CAPACIDADE");
    $sheet->setCellValue('X'.$i, "VE??CULO ADAPTADO");
	$sheet->setCellValue('Y'.$i, "IDADE DO VE??CULO");
	$sheet->setCellValue('W'.$i, "COMBUST??VEL");
	$sheet->setCellValue('Z'.$i, "N??MERO DA AP??LICE");
	$sheet->setCellValue('AA'.$i, "VENCIMENTO DA AP??LICE");
	$sheet->setCellValue('AB'.$i, "VENCIMENTO DO CRLV");
	$sheet->setCellValue('AC'.$i, "VISTORIA SEMESTRAL");
	$sheet->setCellValue('AD'.$i, "NOME DO MONITOR");
	$sheet->setCellValue('AE'.$i, "CPF DO MONITOR");
	$sheet->setCellValue('AF'.$i, "RG DO MONITOR");
	$sheet->setCellValue('AG'.$i, "TELEFONES DO MONITOR");
	$sheet->setCellValue('AH'.$i, "PEND??NCIAS");
	$sheet->setCellValue('AI'.$i, "BAIRROS ATENDIDOS");

    $sheet->getStyle('A'.$i.':AI'.$i)
    ->getAlignment()->setWrapText(true);
    $sheet->getStyle('A'.$i.':AI'.$i)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF000000');

    $sheet->getStyle('A'.$i.':AI'.$i)->getFont()->setBold(true);

    $sheet->getStyle('A'.$i.':AI'.$i)->getFont()->setColor( $colorWhite );
    $sheet->setAutoFilter('A'.$i.':AI'.$i);

	if(!empty($status)){
		$query = Condutor::find()->andWhere(['status' => $status])->orderBy(['nome' => SORT_ASC]);
	}else{
		$query = Condutor::find()->orderBy(['nome' => SORT_ASC]);
	}
    
	
    if(isset($_GET['selecionados']) && $_GET['selecionados'] != '') {
        $ids = explode(',',$_GET['selecionados']);
        $query = $query->where(['in', 'id', $ids]);
		
    }

	
    foreach($query->all() as $model) {
    $i++;
        if($i % 2 == 0) {
            $sheet->getStyle('A'.$i.':AI'.$i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F6F6F6');
        
        }
        // $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($borderSoft);
        $sheet->getStyle('B'.$i.':AI'.$i)->applyFromArray($center);
        $sheet->getStyle('A'.$i.':AI'.$i)->applyFromArray($borderSoft);
        $sheet->getStyle('A'.$i.':AI'.$i)->getAlignment()->setWrapText(true);
        $sheet->setCellValue('A'.$i, ' '.$model->nome);
		if($model->status == 1){
			$statusCondutor ='ATIVO';
		}else{
			$statusCondutor ='INATIVO';
		}
		$sheet->setCellValue('B'.$i, ' '.$statusCondutor);
		if($model->dataNascimento){
			$dataNascArr = explode("-", $model->dataNascimento);		
			$dataNasc = $dataNascArr[2].'-'.$dataNascArr[1].'-'.$dataNascArr[0];
		}else{
			$dataNasc = '';
		}		
		$sheet->setCellValue('C'.$i, $dataNasc);
		$sheet->setCellValue('D'.$i, $model->cpf);
		$sheet->setCellValue('E'.$i, $model->rg.' - '.$model->orgaoEmissor);
		$sheet->setCellValue('F'.$i, $model->email);
		$sheet->setCellValue('G'.$i, $model->cnhRegistro);
		
		if($model->cnhValidade){
			$dataCnhArr = explode("-", $model->cnhValidade);		
			$dataCnh = $dataCnhArr[2].'-'.$dataCnhArr[1].'-'.$dataCnhArr[0];
		}else{
			$dataCnh = '';
		}	
		
		$sheet->setCellValue('H'.$i, $dataCnh);
		$sheet->setCellValue('I'.$i, $model->nit);
		
		if($model->dataInicioContrato){
			$dtIniContrArr = explode("-", $model->dataInicioContrato);		
			$dataIniContr = $dtIniContrArr[2].'-'.$dtIniContrArr[1].'-'.$dtIniContrArr[0];
		}else{
			$dataIniContr = '';
		}	
		
		if($model->dataFimContrato){
			$dtFinContrArr = explode("-", $model->dataFimContrato);		
			$dtFinContr = $dtFinContrArr[2].'-'.$dtFinContrArr[1].'-'.$dtFinContrArr[0];
		}else{
			$dtFinContr = '';
		}	
		
		$sheet->setCellValue('J'.$i, $dataIniContr.' - '.$dtFinContr);
		$sheet->setCellValue('K'.$i, CONDUTOR::ARRAY_TIPO[$model->tipoContrato]);
		$sheet->setCellValue('L'.$i, $model->valorPagoKmViagem);
        $sheet->setCellValue('M'.$i, $model->alvara);
		$sheet->setCellValue('N'.$i, $model->inscricaoMunicipal);
        $sheet->setCellValue('O'.$i, $model->getRegioesAsString());
        $complemento ='';
        if($model->complementoResidencia){
            $complemento .= ','.$model->complementoResidencia;
        }
        $num = '';
        if($model->numeroResidencia){
            $num .= ', N?? '.$model->numeroResidencia;
        }
        if($model->bairro)
            $model->bairro = ' - '.$model->bairro;

        $sheet->setCellValue('P'.$i, $model->tipoLogradouro.' '.trim($model->endereco).''.$complemento.''.$num.$model->bairro.' '.$model->cep);
        $ano = '';
        if($model->veiculo->anoModelo && $model->veiculo->anoFabricacao)
            $ano = ' Ano: '.$model->veiculo->anoFabricacao.'/'.$model->veiculo->anoModelo;
        $sheet->setCellValue('Q'.$i, $this->getTelefoneValido($model));
		
		$escolas = [];
        foreach ($model->escolas as $escola)
        {
            $escolas[] = $escola->escola->nomeCompleto;
        }

        $sheet->setCellValue('R'.$i,  implode (',', $escolas));
		
        $sheet->setCellValue('S'.$i, $model->veiculo->placa.' / '.$model->veiculo->modelo->marca->nome.' '.$model->veiculo->modelo->nome.$ano);
		$sheet->setCellValue('T'.$i, Veiculo::ARRAY_TIPO_VEICULO[$model->veiculo->tipoVeiculo]);
        $sheet->setCellValue('U'.$i, Veiculo::ARRAY_ALOCACAO[$model->veiculo->alocacao]);
		$sheet->setCellValue('V'.$i, $model->veiculo->capacidade);
        $sheet->setCellValue('X'.$i, Veiculo::ARRAY_ADAPTADO[$model->veiculo->adaptado]);
		$sheet->setCellValue('Y'.$i, $model->veiculo->anoAlerta(1));
		$sheet->setCellValue('W'.$i, Veiculo::ARRAY_TIPO[$model->veiculo->combustivel]);
		
		if($model->veiculo->numApolice == 0){
			$sheet->setCellValue('Z'.$i, 'Pendente');
		}else{
			$sheet->setCellValue('Z'.$i, $model->veiculo->numApolice);
		}
		
		
		if($model->veiculo->dataVencimentoSeguro){
			$dtVencSegArr = explode("-", $model->veiculo->dataVencimentoSeguro);		
			$dtVencSeg = $dtVencSegArr[2].'-'.$dtVencSegArr[1].'-'.$dtVencSegArr[0];
		}else{
			$dtVencSeg = '';
		}			
		$sheet->setCellValue('AA'.$i,$dtVencSeg );		
		
		if($model->veiculo->dataVencimentoCRLV){
			$dtVencCrlvArr = explode("-", $model->veiculo->dataVencimentoCRLV);		
			$dtVencCrlv = $dtVencCrlvArr[2].'-'.$dtVencCrlvArr[1].'-'.$dtVencCrlvArr[0];
		}else{
			$dtVencCrlv = '';
		}			
		$sheet->setCellValue('AB'.$i, $dtVencCrlv);
		
		if($model->veiculo->dataVistoriaEstadual){
			$dtVistEstadualArr = explode("-", $model->veiculo->dataVistoriaEstadual);		
			$dtVistEstadual = $dtVistEstadualArr[2].'-'.$dtVistEstadualArr[1].'-'.$dtVistEstadualArr[0];
		}else{
			$dtVistEstadual = '';
		}			
		
		$sheet->setCellValue('AC'.$i, $dtVistEstadual);
		$sheet->setCellValue('AD'.$i, $model->nomeMonitor);
		$sheet->setCellValue('AE'.$i, $model->cpfMonitor);
		$sheet->setCellValue('AF'.$i, $model->rgMonitor);
		$sheet->setCellValue('AG'.$i, $model->telefoneMonitor.' '.$model->telefoneMonitorWhatsapp);
        $sheet->setCellValue('AH'.$i, $model->pendencias);
		
		$sqlBairros ='select distinct a.bairro as bairro from SolicitacaoTransporte st  left join Aluno a on st.idAluno = a.id 	where st.idCondutor = '.$model->id.'  and st.`status` = 6 order by a.bairro' ;
		$dadosBairro = Yii::$app->getDb()->createCommand($sqlBairros)->queryAll();
		$bairros = '';
		foreach($dadosBairro as $esc){				
			$bairros.=$esc['bairro'];				
			$bairros.=',';
		}

        $sheet->setCellValue('AI'.$i,  $bairros);
    }
    
    $base = "arquivos/_exportacoes/";
        
    switch($tipo){
        case 'PDF':
            try {
                $filename = $base."Condutores_".date('d-m-Y-H-i-s').".pdf";
                
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
                $writer ->setPreCalculateFormulas(false);
                $writer->save($filename);

                header("Content-Disposition: attachment; filename=".$filename);
                $content = file_get_contents($filename);
                unlink($filename);
                exit($content);
                
            } catch(Exception $e) {
                exit($e->getMessage());
            }

        break;
        case 'TXT':

            $filename = $base.'_Condutores_'.date('d-m-Y-H-i-s').'.txt';
            $fp = fopen($filename, 'a');

            //corrige erro de exibi????o em UTF8 para CSV e TXT:
        fwrite($fp, pack("CCC",0xef,0xbb,0xbf));                  

            $query = Condutor::find()->orderBy(['nome' => SORT_ASC]);
            if(isset($_GET['selecionados']) && $_GET['selecionados'] != '') {
                $ids = explode(',',$_GET['selecionados']);
                $query = $query->where(['in', 'id', $ids]);
            }
            foreach($query->all() as $model) {
                $l='';
                $l .= $model->nome;
				
				if($model->status == 1){
					$statusCondutor ='ATIVO';
				}else{
					$statusCondutor ='INATIVO';
				}
				$l .= $statusCondutor;
				if($model->dataNascimento){
					$dataNascArr = explode("-", $model->dataNascimento);		
					$dataNasc = $dataNascArr[2].'-'.$dataNascArr[1].'-'.$dataNascArr[0];
				}else{
					$dataNasc = '';
				}
				$l .= $dataNasc;	
				$l .= ';'.$model->cpf;
				$l .= ';'.$model->rg.' - '.$model->orgaoEmissor;
				$l .= ';'.$model->email;
				$l .= ';'.$model->cnhRegistro;
				
				if($model->cnhValidade){
					$dataCnhArr = explode("-", $model->cnhValidade);		
					$dataCnh = $dataCnhArr[2].'-'.$dataCnhArr[1].'-'.$dataCnhArr[0];
				}else{
					$dataCnh = '';
				}	
				$l .= ';'.$dataCnh;
				$l .= ';'.$model->nit;
				
				if($model->dataInicioContrato){
					$dtIniContrArr = explode("-", $model->dataInicioContrato);		
					$dataIniContr = $dtIniContrArr[2].'-'.$dtIniContrArr[1].'-'.$dtIniContrArr[0];
				}else{
					$dataIniContr = '';
				}	
				
				if($model->dataFimContrato){
					$dtFinContrArr = explode("-", $model->dataFimContrato);		
					$dtFinContr = $dtFinContrArr[2].'-'.$dtFinContrArr[1].'-'.$dtFinContrArr[0];
				}else{
					$dtFinContr = '';
				}	
				$l .= ';'.$dataIniContr.' - '.$dtFinContr;
				$l .= ';'.CONDUTOR::ARRAY_TIPO[$model->tipoContrato];
				$l .= ';'.$model->valorPagoKmViagem;
				$l .= ';'.$model->alvara;
				$l .= ';'.$model->inscricaoMunicipal;
				$l .= ';'.$model->getRegioesAsString();
				
				$complemento ='';
				if($model->complementoResidencia){
					$complemento .= ','.$model->complementoResidencia;
				}
				$num = '';
				if($model->numeroResidencia){
					$num .= ', N?? '.$model->numeroResidencia;
				}
				if($model->bairro)
					$model->bairro = ' - '.$model->bairro;
				
				$l .= ';'.$model->tipoLogradouro.' '.trim($model->endereco).''.$complemento.''.$num.$model->bairro.' '.$model->cep;

				$ano = '';
				if($model->veiculo->anoModelo && $model->veiculo->anoFabricacao)
					$ano = ' Ano: '.$model->veiculo->anoFabricacao.'/'.$model->veiculo->anoModelo;

				
				$l .= ';'.$this->getTelefoneValido($model);
				
				$escolas = [];
				foreach ($model->escolas as $escola)
				{
					$escolas[] = $escola->escola->nomeCompleto;
				}

				$l .= ';'.implode (',', $escolas);
		
				$sheet->setCellValue('R'.$i,  implode (',', $escolas));
				
				$l .= ';'.$model->veiculo->placa.' / '.$model->veiculo->modelo->marca->nome.' '.$model->veiculo->modelo->nome.$ano;
				$l .= ';'.Veiculo::ARRAY_TIPO_VEICULO[$model->veiculo->tipoVeiculo];
				$l .= ';'.Veiculo::ARRAY_ALOCACAO[$model->veiculo->alocacao];
				$l .= ';'.$model->veiculo->capacidade;
				$l .= ';'.Veiculo::ARRAY_ADAPTADO[$model->veiculo->adaptado];
				$l .= ';'.$model->veiculo->anoAlerta(1);
				$l .= ';'.Veiculo::ARRAY_TIPO[$model->veiculo->combustivel];
				
				
				if($model->veiculo->numApolice == 0){
					$l .= ';Pendente';
				}else{
					$l .= ';'.$model->veiculo->numApolice;
				}
				
				
				if($model->veiculo->dataVencimentoSeguro){
					$dtVencSegArr = explode("-", $model->veiculo->dataVencimentoSeguro);		
					$dtVencSeg = $dtVencSegArr[2].'-'.$dtVencSegArr[1].'-'.$dtVencSegArr[0];
				}else{
					$dtVencSeg = '';
				}			
				$l .= ';'.$dtVencSeg;
			
				if($model->veiculo->dataVencimentoCRLV){
					$dtVencCrlvArr = explode("-", $model->veiculo->dataVencimentoCRLV);		
					$dtVencCrlv = $dtVencCrlvArr[2].'-'.$dtVencCrlvArr[1].'-'.$dtVencCrlvArr[0];
				}else{
					$dtVencCrlv = '';
				}			
				$l .= ';'.$dtVencCrlv;
				
				if($model->veiculo->dataVistoriaEstadual){
					$dtVistEstadualArr = explode("-", $model->veiculo->dataVistoriaEstadual);		
					$dtVistEstadual = $dtVistEstadualArr[2].'-'.$dtVistEstadualArr[1].'-'.$dtVistEstadualArr[0];
				}else{
					$dtVistEstadual = '';
				}			
				$l .= ';'.$dtVistEstadual;
				$l .= ';'.$model->nomeMonitor;
				$l .= ';'.$model->cpfMonitor;
				$l .= ';'.$model->rgMonitor;
				$l .= ';'.$model->telefoneMonitor.' '.$model->telefoneMonitorWhatsapp;
				$sqlBairros ='select distinct a.bairro as bairro from SolicitacaoTransporte st  left join Aluno a on st.idAluno = a.id 	where st.idCondutor = '.$model->id.'  and st.`status` = 6 order by a.bairro' ;
				$dadosBairro = Yii::$app->getDb()->createCommand($sqlBairros)->queryAll();
				$bairros = '';
				foreach($dadosBairro as $esc){				
					$bairros.=$esc['bairro'];				
					$bairros.=',';
				}
				$l .= ';'.$bairros;			
				$l .= ';'.$model->pendencias;				
				$l .= '
';			
                fwrite($fp,$l);
            }
            fclose($fp);
            try {
                // $writer = new Xlsx($spreadsheet);
                header("Content-Disposition: attachment; filename=".$filename);
                $content = file_get_contents($filename);
                unlink($filename);
                exit($content);
             
            } catch(Exception $e) {
                exit($e->getMessage());
            }
        break;
		 case 'CSV':

            $filename = $base.'_Condutores_'.date('d-m-Y-H-i-s').'.csv';
            $fp = fopen($filename, 'a');

            //corrige erro de exibi????o em UTF8 para CSV e TXT:
            fwrite($fp, pack("CCC",0xef,0xbb,0xbf));                  

            $query = Condutor::find()->orderBy(['nome' => SORT_ASC]);
            if(isset($_GET['selecionados']) && $_GET['selecionados'] != '') {
                $ids = explode(',',$_GET['selecionados']);
                $query = $query->where(['in', 'id', $ids]);
            }
            foreach($query->all() as $model) {
                $l='';
                $l .= $model->nome;
				
				if($model->status == 1){
					$statusCondutor ='ATIVO';
				}else{
					$statusCondutor ='INATIVO';
				}
				$l .= $statusCondutor;
				if($model->dataNascimento){
					$dataNascArr = explode("-", $model->dataNascimento);		
					$dataNasc = $dataNascArr[2].'-'.$dataNascArr[1].'-'.$dataNascArr[0];
				}else{
					$dataNasc = '';
				}
				$l .= $dataNasc;	
				$l .= ';'.$model->cpf;
				$l .= ';'.$model->rg.' - '.$model->orgaoEmissor;
				$l .= ';'.$model->email;
				$l .= ';'.$model->cnhRegistro;
				
				if($model->cnhValidade){
					$dataCnhArr = explode("-", $model->cnhValidade);		
					$dataCnh = $dataCnhArr[2].'-'.$dataCnhArr[1].'-'.$dataCnhArr[0];
				}else{
					$dataCnh = '';
				}	
				$l .= ';'.$dataCnh;
				$l .= ';'.$model->nit;
				
				if($model->dataInicioContrato){
					$dtIniContrArr = explode("-", $model->dataInicioContrato);		
					$dataIniContr = $dtIniContrArr[2].'-'.$dtIniContrArr[1].'-'.$dtIniContrArr[0];
				}else{
					$dataIniContr = '';
				}	
				
				if($model->dataFimContrato){
					$dtFinContrArr = explode("-", $model->dataFimContrato);		
					$dtFinContr = $dtFinContrArr[2].'-'.$dtFinContrArr[1].'-'.$dtFinContrArr[0];
				}else{
					$dtFinContr = '';
				}	
				$l .= ';'.$dataIniContr.' - '.$dtFinContr;
				$l .= ';'.CONDUTOR::ARRAY_TIPO[$model->tipoContrato];
				$l .= ';'.$model->valorPagoKmViagem;
				$l .= ';'.$model->alvara;
				$l .= ';'.$model->inscricaoMunicipal;
				$l .= ';'.$model->getRegioesAsString();
				
				$complemento ='';
				if($model->complementoResidencia){
					$complemento .= ','.$model->complementoResidencia;
				}
				$num = '';
				if($model->numeroResidencia){
					$num .= ', N?? '.$model->numeroResidencia;
				}
				if($model->bairro)
					$model->bairro = ' - '.$model->bairro;
				
				$l .= ';'.$model->tipoLogradouro.' '.trim($model->endereco).''.$complemento.''.$num.$model->bairro.' '.$model->cep;

				$ano = '';
				if($model->veiculo->anoModelo && $model->veiculo->anoFabricacao)
					$ano = ' Ano: '.$model->veiculo->anoFabricacao.'/'.$model->veiculo->anoModelo;
				
				
				$l .= ';'.$this->getTelefoneValido($model);
				
				$escolas = [];
				foreach ($model->escolas as $escola)
				{
					$escolas[] = $escola->escola->nomeCompleto;
				}

				$l .= ';'.implode (',', $escolas);
		
				$sheet->setCellValue('R'.$i,  implode (',', $escolas));
				
				$l .= ';'.$model->veiculo->placa.' / '.$model->veiculo->modelo->marca->nome.' '.$model->veiculo->modelo->nome.$ano;
				$l .= ';'.Veiculo::ARRAY_TIPO_VEICULO[$model->veiculo->tipoVeiculo];
				$l .= ';'.Veiculo::ARRAY_ALOCACAO[$model->veiculo->alocacao];
				$l .= ';'.$model->veiculo->capacidade;
				$l .= ';'.Veiculo::ARRAY_ADAPTADO[$model->veiculo->adaptado];
				$l .= ';'.$model->veiculo->anoAlerta(1);
				$l .= ';'.Veiculo::ARRAY_TIPO[$model->veiculo->combustivel];
				
				
				if($model->veiculo->numApolice == 0){
					$l .= ';Pendente';
				}else{
					$l .= ';'.$model->veiculo->numApolice;
				}
				
				
				if($model->veiculo->dataVencimentoSeguro){
					$dtVencSegArr = explode("-", $model->veiculo->dataVencimentoSeguro);		
					$dtVencSeg = $dtVencSegArr[2].'-'.$dtVencSegArr[1].'-'.$dtVencSegArr[0];
				}else{
					$dtVencSeg = '';
				}			
				$l .= ';'.$dtVencSeg;
			
				if($model->veiculo->dataVencimentoCRLV){
					$dtVencCrlvArr = explode("-", $model->veiculo->dataVencimentoCRLV);		
					$dtVencCrlv = $dtVencCrlvArr[2].'-'.$dtVencCrlvArr[1].'-'.$dtVencCrlvArr[0];
				}else{
					$dtVencCrlv = '';
				}			
				$l .= ';'.$dtVencCrlv;
				
				if($model->veiculo->dataVistoriaEstadual){
					$dtVistEstadualArr = explode("-", $model->veiculo->dataVistoriaEstadual);		
					$dtVistEstadual = $dtVistEstadualArr[2].'-'.$dtVistEstadualArr[1].'-'.$dtVistEstadualArr[0];
				}else{
					$dtVistEstadual = '';
				}			
				$l .= ';'.$dtVistEstadual;
				$l .= ';'.$model->nomeMonitor;
				$l .= ';'.$model->cpfMonitor;
				$l .= ';'.$model->rgMonitor;
				$l .= ';'.$model->telefoneMonitor.' '.$model->telefoneMonitorWhatsapp;
				
				
				$sqlBairros ='select distinct a.bairro as bairro from SolicitacaoTransporte st  left join Aluno a on st.idAluno = a.id 	where st.idCondutor = '.$model->id.'  and st.`status` = 6 order by a.bairro' ;
				$dadosBairro = Yii::$app->getDb()->createCommand($sqlBairros)->queryAll();
				$bairros = '';
				foreach($dadosBairro as $esc){				
					$bairros.=$esc['bairro'];				
					$bairros.=',';
				}
				$l .= ';'.$bairros;	
				$l .= ';'.$model->pendencias;
				$l .= '
';			
                fwrite($fp,$l);
            }
            fclose($fp);
            try {
                // $writer = new Xlsx($spreadsheet);
                header("Content-Disposition: attachment; filename=".$filename);
                $content = file_get_contents($filename);
                unlink($filename);
                exit($content);
             
            } catch(Exception $e) {
                exit($e->getMessage());
            }
        break;
        case 'EXCEL':
            try {
                $writer = new Xlsx($spreadsheet);
                $filename = $base."Condutores_".date('d-m-Y-H-i-s').".xlsx";
                $writer->save($filename);
                header("Content-Disposition: attachment; filename=".$filename);
                $content = file_get_contents($filename);
                unlink($filename);
                exit($content);
             
            } catch(Exception $e) {
                exit($e->getMessage());
            }
        break;
    }
   
    }

    private function sentidoIda($model) {
            foreach($model->meusPontos as $alunoPonto) {
                if($alunoPonto->ponto->condutorRota->sentido == CondutorRota::SENTIDO_IDA)
                        return $alunoPonto->ponto->condutorRota->viagem;
            }
    }
    private function sentidoVolta($model) {
        foreach($model->meusPontos as $alunoPonto) {
            if($alunoPonto->ponto->condutorRota->sentido == CondutorRota::SENTIDO_VOLTA)
                    return $alunoPonto->ponto->condutorRota->viagem;
        }
    }
    public function actionExportar($tipo) {
            // public function actionExportarEndereco($tipo){
    // $content = '<table>';
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

    // $sheet->setCellValue('A1:N5', 'Logo');
    $left = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        )
    );
    $right = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        )
    );
    $center = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        )
    );

    $borderHard = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];
    $borderSoft = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];

    $colorRed =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED );
    $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE );

    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setPath(Yii::getAlias('@webroot').'/img/brasaoPdf.png'); // put your path and image here
    $drawing->setCoordinates('A1');
    $drawing->setOffsetX(50);
    $drawing->setOffsetY(15);
    $drawing->setRotation(0);
    $drawing->setWorksheet($sheet);

    $drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing2->setPath(Yii::getAlias('@webroot').'/img/faixa.png'); // put your path and image here
    $drawing2->setCoordinates('C1');
    $drawing2->setOffsetX(0);
    $drawing2->setOffsetY(0);
    $drawing2->setRotation(0);
    $drawing2->setWorksheet($sheet);


    // SETUP DAS COLUNAS
    $sheet->getColumnDimension('A')->setWidth(50.14);
    $sheet->getColumnDimension('B')->setWidth(14.5);
    $sheet->getColumnDimension('C')->setWidth(20.34);
    $sheet->getColumnDimension('D')->setWidth(25.14);
    $sheet->getColumnDimension('E')->setWidth(12.57);
    $sheet->getColumnDimension('F')->setWidth(12.57);
    $sheet->getColumnDimension('G')->setWidth(16);
    $sheet->getColumnDimension('H')->setWidth(13);
    $sheet->getColumnDimension('I')->setWidth(32);



    //
    $i = 1;
    // PR??XIMA LINHA
    $sheet->mergeCells('A'.$i.':B'.($i+4));
    $sheet->mergeCells('I'.$i.':N'.($i+4));

    $sheet->mergeCells('C'.$i.':I'.$i);
    $sheet->mergeCells('C'.($i+1).':I'.($i+1));
    $sheet->setCellValue('C'.($i+1), "Secretaria de Educa????o e Cidadania");
    $sheet->getStyle('C'.($i+1))->applyFromArray($left)->getFont()->setBold(true);

    $sheet->mergeCells('C'.($i+2).':I'.($i+4));
    $sheet->setCellValue('C'.($i+2), "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
    $sheet->getStyle('C'.($i+2))->getAlignment()->setWrapText(true);

    $sheet->getStyle('A'.($i+2).':I'.($i+2))->applyFromArray($left);


    $i+=5;
    // $objSheet->getStyle('A2:B2')->getProtection()
// ->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
    $sheet->getStyle('A'.$i.':N'.$i)->applyFromArray($center);

    $sheet->setCellValue('A'.$i, "ESCOLA");
    $sheet->setCellValue('B'.$i, "NOME");
    $sheet->setCellValue('C'.$i, "RA");
    $sheet->setCellValue('D'.$i, "ANO/S??RIE/TURMA");
    $sheet->setCellValue('E'.$i, "HOR??RIO DE ENTRADA");
    $sheet->setCellValue('F'.$i, "HOR??RIO DE SA??DA");
    $sheet->setCellValue('G'.$i, "NOME DA M??E");
    $sheet->setCellValue('H'.$i, "NOME DO PAI");
    $sheet->setCellValue('I'.$i, "ENDERE??O");
    $sheet->setCellValue('J'.$i, "BAIRRO");
    $sheet->setCellValue('K'.$i, "TELEFONES");
    $sheet->setCellValue('L'.$i, "IN??CIO");
    $sheet->setCellValue('M'.$i, "VIAGEM IDA");
    $sheet->setCellValue('N'.$i, "VIAGEM VOLTA");

    $sheet->getStyle('A'.$i.':N'.$i)
    ->getAlignment()->setWrapText(true);
    $sheet->getStyle('A'.$i.':N'.$i)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF000000');

    $sheet->getStyle('A'.$i.':N'.$i)->getFont()->setBold(true);

    $sheet->getStyle('A'.$i.':N'.$i)->getFont()->setColor( $colorWhite );
    $sheet->setAutoFilter('A'.$i.':N'.$i);

    $condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
            
    foreach($condutor->alunos as $aluno) {
    $i++;
        if($i % 2 == 0) {
            $sheet->getStyle('A'.$i.':N'.$i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F6F6F6');
        
        }
        
     $sheet->setCellValue('A'.$i, $aluno->escola->nomeCompleto);
     $sheet->setCellValue('B'.$i, $aluno->nome);
     $sheet->setCellValue('C'.$i, $aluno->RA.' '.$aluno->RAdigito);
     $sheet->setCellValue('D'.$i, Aluno::ARRAY_SERIES[$aluno->serie].'/'.Aluno::ARRAY_TURMA[$aluno->turma]);
    $sheet->setCellValue('E'.$i, $aluno->horarioEntrada);
    $sheet->setCellValue('F'.$i, $aluno->horarioSaida);
    $sheet->setCellValue('G'.$i, $aluno->nomeMae);
    $sheet->setCellValue('H'.$i, $aluno->nomePai);
    $sheet->setCellValue('I'.$i, $aluno->enderecoCompleto());
    $sheet->setCellValue('J'.$i, $aluno->bairro);
    // To tired
    $model = $aluno; 
    $tel='';
    if($model->telefoneCelular && strlen($model->telefoneCelular) > 5)
         $tel .= $model->telefoneCelular.'/';
    if($model->telefoneCelular2 && strlen($model->telefoneCelular2) > 5)
         $tel .= $model->telefoneCelular2.'/';
    if($model->telefoneResidencial && strlen($model->telefoneResidencial) > 5)
         $tel .= $model->telefoneResidencial.'/';
    if($model->telefoneResidencial2 && strlen($model->telefoneResidencial2) > 5)
         $tel .= $model->telefoneResidencial2.'/';

    $sheet->setCellValue('K'.$i,  substr($tel, 0, -1));
    $sheet->setCellValue('L'.$i,  $model->entradaRota());
    $sheet->setCellValue('M'.$i,  $this->sentidoIda($aluno));
    $sheet->setCellValue('N'.$i,  $this->sentidoVolta($aluno));

    }
    $sheet->getStyle('A5:N'.$i)->applyFromArray($borderSoft);

    $i++;
    $sheet->mergeCells('A'.$i.':N'.$i);
    $sheet->setCellValue('A'.$i, "Relat??rio gerado em ".date('d/m/y H:i'));
    $sheet->getStyle('A'.$i.':N'.$i)->applyFromArray($center);

    $base = "arquivos/_exportacoes/";
        
    switch($tipo){
        case 'PDF':
            try {
                $filename = $base."Condutores_".date('d-m-Y-H-i-s').".pdf";
                
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
                $writer ->setPreCalculateFormulas(false);
                $writer->save($filename);

                header("Content-Disposition: attachment; filename=".$filename);
                $content = file_get_contents($filename);
                unlink($filename);
                exit($content);
                
            } catch(Exception $e) {
                exit($e->getMessage());
            }

        break;
        case 'EXCEL':
            try {
                $writer = new Xlsx($spreadsheet);
                $filename = $base."Condutores_".date('d-m-Y-H-i-s').".xlsx";
                $writer->save($filename);
                header("Content-Disposition: attachment; filename=".$filename);
                $content = file_get_contents($filename);
                unlink($filename);
                exit($content);
             
            } catch(Exception $e) {
                exit($e->getMessage());
            }
        break;
    }
   
    
    }

    public function actionAlunos()
    {

        $model = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
        $condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
		
		$hoje = date("d-m-Y");
		$hojeMenosDez =  date('d-m-Y', strtotime('-10 days', strtotime($hoje)));
		$strHojeMenosDez = strtotime($hojeMenosDez);
		$strHoje = strtotime($hoje);
		$alunoNovo = '0';
        $arrayData = [];
        $titulo = 'Relac??a??o de alunos transportados';

        // Quando o pdf for solicitado via post, faremos dessa forma
        $post = Yii::$app->request->post();

        $get = Yii::$app->request->get();
        if (!$get) {
            // \Yii::$app->getSession()->setFlash('error', 'Selecione um per??odo para a consulta.');
        } else {

            // print 'ok';
            // $datas = explode(' - ', $get['periodo']);
            // $dtInicial = \DateTime::createFromFormat ( 'd/m/Y', $datas[0]);
            // $dtFinal = \DateTime::createFromFormat ( 'd/m/Y', $datas[1]);
            // throw new NotFoundHttpException(print_r($datas, true));

            $idsAluno = [];
			if($condutor){
				foreach ($condutor->alunos as $aluno){
					$idsAluno[] = $aluno->id;		
					
					
					$result = Aluno::find()->where(['in', 'Aluno.id', $idsAluno]);
					
					if (isset($get['escola']) && $get['escola'] != '')
						$result->andFilterWhere(['=', 'idEscola', $get['escola']]);

					if (isset($get['aluno']) && $get['aluno'] != '')
						$result->andFilterWhere(['like', 'nome', $get['aluno']]);

					$result->orderBy([
						'idEscola' => SORT_ASC,
						'nome' => SORT_ASC,
						'horarioEntrada'=>SORT_ASC
					]);				  
				
					$sqlSol ="select a.*,e.nome as escola, DATE_FORMAT(st.data,'%d-%m-%Y') as dataSol,st.id as idSol,st.idRotaIda,st.idRotaVolta,st.idCondutor,DATE_FORMAT(st.data - INTERVAL 10 DAY,'%d/%m/%Y')  as hoje_menos_dez  from SolicitacaoTransporte st  join Aluno a on a.id = st.idAluno  join Escola e on e.id = st.idEscola where st.idCondutor = ".$condutor->id." and st.idAluno = ".$aluno->id."  and st.`status` = 6  and st.idRotaIda is not null and st.idRotaVolta is not null order by st.data"; 
					$dadosSol = Yii::$app->getDb()->createCommand($sqlSol)->queryAll();
					
					$strDataSol = strtotime($dadosSol[0]['dataSol']);
					
					if(!empty($dadosSol[0]['nome'])){						
						if( $dadosSol[0]['cienteCondutor'] == 0 ){
							$dadosSol[0]['alunoNovo']  = $alunoNovo = 1;							
							
						}					
						$arrayData[] = $dadosSol[0];						
					}
											

					// if( ($strDataSol <= $strHoje) and ($strDataSol >= $strHojeMenosDez) and ($dadosSol[0]['cienteCondutor'] == 0 )){
						// $dadosSol[0]['alunoNovo']  = $alunoNovo = 1;							
					// }					
					//$arrayData = $result->all();
					
					
					
				}
					
				
			}else{
				$arrayData ='';
			}
            if (!$arrayData)
                \Yii::$app->getSession()->setFlash('error', 'Nenhum resultado encontrado.');
        }
		
		
	
        $this->session->set('alunos', $arrayData);
        return $this->render('alunos', [
            'model' => $condutor,
			'alunoNovo' => $alunoNovo,
            'data' => $arrayData,
            'titulo' => $titulo,
            'get' => $get
        ]);
		
    }

    /**
     * Lists all Condutor models.
     * @return mixed
     */
    public function actionAoVivo()
    {


        return $this->render('ao-vivo');
    }
	
	 /**
     * salvar pendencia.
     * @return mixed
     */
    public function actionSalvar()
    {
		$this->enableCsrfValidation = false;
		if($_POST){
			$condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
			
			
			if($_POST['tipo'] == '1'){
				$antes = $condutor->pendencias;
				$depois ='O condutor '.$condutor->nome.' est?? ciente das pend??ncias';
				
				if($_POST['idCondutor'] == $condutor->id){
					Yii::$app->getDb()->createCommand()->insert('Log', [
					'acao'=>1,
					 'referencia' => 'Condutor-' . \Yii::$app->User->identity->id,
					 'tabela' => 'Condutor',
					 'coluna' => 'Condutor - Pend??ncias',
					 'antes' => $antes,
					 'depois' => $depois,
					 'idUsuario' =>  \Yii::$app->User->identity->id,
					])->execute();
					echo true;
				}else{
					echo false;
				}
				
			}else{
				
				$antes = 'Aluno novo na rota';
				$depois ='O condutor '.$condutor->nome.' est?? ciente que deve entrar em contato com o respons??vel';
				if($_POST['idCondutor'] == $condutor->id){
					
					Yii::$app->getDb()->createCommand()->insert('Log', [
					'acao'=>1,
					 'referencia' => 'Condutor-' . \Yii::$app->User->identity->id,
					 'tabela' => 'Condutor',
					 'coluna' => 'Condutor',
					 'antes' => $antes,
					 'depois' => $depois,
					 'idUsuario' =>  \Yii::$app->User->identity->id,
					])->execute();
				
					$aluno = Aluno::findOne($_POST['aluno']);

					if(!$aluno){
						echo false;
					}else{										
						$aluno->cienteCondutor = 1;
						$aluno->save();
						echo true;	
					}

				}else{
					echo false;
				}
				
			}		

			
			
		}else{
			echo false;
		}
    }


    public function actionAoVivoAjax()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $output = [];
        $veiculos = Veiculo::find()->all();

        foreach ($veiculos as $veiculo) {
            $output[] = [
                'veiculo' => $veiculo,
                'condutor' => $veiculo->condutor,
                'rotas' => $veiculo->condutor->vinculo,
            ];
        }
        return $output;
    }


    /**
     * Displays a single Condutor model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (Yii::$app->request->get('ajax')) {
            return $this->renderPartial('view-ajax', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }


    private function loadCheckbox($model)
    {
        if ($model->telefoneWhatsapp == 'on')
            $model->telefoneWhatsapp = 'checked';
        else
            $model->telefoneWhatsapp = '';

        if ($model->telefoneWhatsapp2 == 'on')
            $model->telefoneWhatsapp2 = 'checked';
        else
            $model->telefoneWhatsapp2 = '';

        if ($model->celularWhatsapp == 'on')
            $model->celularWhatsapp = 'checked';
        else
            $model->celularWhatsapp = '';

        if ($model->celularWhatsapp2 == 'on')
            $model->celularWhatsapp2 = 'checked';
        else
            $model->celularWhatsapp2 = '';


        if ($model->telefoneMonitorWhatsapp == 'on')
            $model->telefoneMonitorWhatsapp = 'checked';
        else
            $model->telefoneMonitorWhatsapp = '';


        if ($model->celularMonitorWhatsapp == 'on')
            $model->celularMonitorWhatsapp = 'checked';
        else
            $model->celularMonitorWhatsapp = '';

        return $model;
    }

    private function criarUsuario($model, $novaSenha)
    {
        $usuario = Usuario::findOne(['cpf' => $model->cpf]);
        if (!$usuario) {
            $usuario = new Usuario();
        }
        $usuario->nome = $model->nome;
        $usuario->username = $model->cpf;
        $usuario->cpf = $model->cpf;
        $usuario->email = $model->email;
        $usuario->status = $model->status;
        $usuario->setPassword($novaSenha);
        $usuario->idPerfil = Usuario::PERFIL_CONDUTOR;
        $usuario->generateAuthKey();
        $usuario->generatePasswordResetToken();
        $usuario->save();


        $model->idUsuario = $usuario->id;
        $model->save();
        return $model;
    }
    public function actionCreate()
    {
        $model = new Condutor();
        
        if ($model->load(Yii::$app->request->post())) {
            $usuario = Usuario::findOne(['cpf' => Usuario::limparCPF($model->cpf)]);
            $cadastroOK = true;
            $condutorExistente = Condutor::findOne(['cpf' => Usuario::limparCPF($model->cpf)]);
            if($condutorExistente){
                \Yii::$app->getSession()->setFlash('error', 'Este CPF pertence ao condutor "' . $condutorExistente->nome . '" portanto n??o ?? poss??vel utiliz??-lo.');
                $cadastroOK = false;
            }
            if ($usuario && $usuario->idPerfil != Usuario::PERFIL_CONDUTOR) {
                \Yii::$app->getSession()->setFlash('error', 'Este CPF pertence ao usu??rio "' . $usuario->nome . '" portanto n??o ?? poss??vel utiliz??-lo.');
                $cadastroOK = false;
            }
            $novaSenha = str_replace('/', '', $model->dataNascimento);
            $model = $this->getDates($model);
            if ($cadastroOK && $model->save()) {
                $model = $this->loadCheckbox($model);
                $this->uploadMultiple($model);
                $usuario = $this->criarUsuario($model, $novaSenha);
                $this->salvarRegioes(Yii::$app->request->post(), $model);

                if ($model->anexoFotoMotorista) {
                    $this->uploadSingleFile($model, 'anexoFotoMotorista', 'fotoMotorista');
                }
                //10082001
                // $veiculo = new Veiculo();
                // $veiculo->idCondutor = $model->id;
                // $veiculo->save();

                // $model->idVeiculo = $veiculo->id;
                // $model->save();


                return $this->redirect(['condutor/view', 'id' => $model->id]);
            }
        }
        // if($model->getErrors()){
        //     \Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header'=>'Erro ao salvar o condutor.']));
        // }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionCreateAjax()
    {
        $model = new Condutor();
        if (Yii::$app->request->get('idEmpresa')) {
            $model->idEmpresa = Yii::$app->request->get('idEmpresa');
        }
        if ($model->load(Yii::$app->request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $usuario = Usuario::findOne(['cpf' => Usuario::limparCPF($model->cpf)]);
            $cadastroOK = true;

            if ($usuario && $usuario->idPerfil != Usuario::PERFIL_CONDUTOR) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['status' => false, 'validation' => ['cpf' => 'Este CPF pertence ao usu??rio "' . $usuario->nome . '" portanto n??o ?? poss??vel utiliz??-lo']];
                $cadastroOK = false;
            }
            $novaSenha = str_replace('/', '', $model->dataNascimento);
            $model = $this->getDates($model);
            if ($cadastroOK && $model->save()) {
                $model = $this->loadCheckbox($model);
                $this->criarUsuario($model, $novaSenha);
                $this->salvarRegioes(Yii::$app->request->post(), $model);


                if ($model->anexoFotoMotorista) {
                    $this->uploadSingleFile($model, 'anexoFotoMotorista', 'fotoMotorista');
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['status' => true];
                }
            } else {
                return ActiveForm::validate($model);
            }
        } else {
            // if($model->getErrors())
            //     Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header'=>'Erro ao salvar Ve??culo.']));
            return $this->renderAjax('_formAjax', [
                'model' => $model,

                'action' => 'condutor/create-ajax',
            ]);
        }
    }


    public function actionSearchEscolasAjax($idCondutor){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $condutor = Condutor::findOne($idCondutor);
        $escolas = [];
        $listaIds = [];
        foreach($condutor->escolas as $escola){
            $escolas[] = $escola->escola; 
            $listaIds[] = $escola->escola->id;
        }
        return ['escolas' => $escolas, 'ids' => $listaIds];
      }

    public function actionViewAjax($id)
    {
        return $this->renderAjax('view', [
            'model' => $this->findModel($id),
            'ajax' => true,
        ]);
    }

    private function getDates($model)
    {

        $data = \DateTime::createFromFormat('d/m/Y', $model->dataNascimento);
        if ($data)
            $model->dataNascimento = $data->format('Y-m-d');

        $data = \DateTime::createFromFormat('d/m/Y', $model->cnhValidade);
        if ($data)
            $model->cnhValidade = $data->format('Y-m-d');

        $data = \DateTime::createFromFormat('d/m/Y', $model->dataInicioContrato);
        if ($data)
            $model->dataInicioContrato = $data->format('Y-m-d');

        $data = \DateTime::createFromFormat('d/m/Y', $model->dataFimContrato);
        if ($data)
            $model->dataFimContrato = $data->format('Y-m-d');

        return $model;
    }

    private function getDatesBr($model)
    {



        $data = \DateTime::createFromFormat('Y-m-d', $model->dataNascimento);
        if ($data)
            $model->dataNascimento = $data->format('d/m/Y');

        $data = \DateTime::createFromFormat('Y-m-d', $model->cnhValidade);
        if ($data)
            $model->cnhValidade = $data->format('d/m/Y');

        $data = \DateTime::createFromFormat('Y-m-d', $model->dataInicioContrato);
        if ($data)
            $model->dataInicioContrato = $data->format('d/m/Y');

        $data = \DateTime::createFromFormat('Y-m-d', $model->dataFimContrato);
        if ($data)
            $model->dataFimContrato = $data->format('d/m/Y');

        return $model;
    }
	 
    private function uploadSingleFile($model, $file, $dbColumn)
    {
        $arquivos = UploadedFile::getInstances($model, $file);

        if ($arquivos) {
            $dirBase = Yii::getAlias('@webroot') . '/';
            $dir = 'arquivos/' . $file . '/';

            if (!file_exists($dirBase . $dir))
                mkdir($dir, 0777, true);

            $i = 1;
            foreach ($arquivos as $arquivo) {
                $i++;
                $nomeArquivo = $file . '_' . time() . '_' . $model->id . '.' . $arquivo->extension;
                $arquivo->saveAs($dirBase . $dir . $nomeArquivo);
                $model->$dbColumn = $dir . $nomeArquivo;
                $model->save();
            }

            //Atualiza tabela de logs
            $this->salvarLog(Log::ACAO_ATUALIZAR, $dbColumn, $model->id);

        }
    }

    private function actionUploadFile($model, $file, $idTipoDocumento)
    {
        $arquivos = UploadedFile::getInstances($model, $file);

        if ($arquivos) {
			
            //DocumentoCondutor::deleteAll(['idCondutor' => $model->id, 'idTipo' => $idTipoDocumento]);   
            $documentos = DocumentoCondutor::find()->andWhere(['idCondutor' => $model->id])->andWhere(['idTipo' => $idTipoDocumento])->all();
            foreach ($documentos as $documento) {
                $documento->delete();
            }
            $dirBase = Yii::getAlias('@webroot') . '/';
            $dir = 'arquivos/' . $idTipoDocumento . '/';

            if (!file_exists($dirBase . $dir))
                mkdir($dir, 0777, true);

            $i = 1;
            foreach ($arquivos as $arquivo) {
                $nomeArquivo = $idTipoDocumento . '_' . time() . '_' . $i . '.' . $arquivo->extension;
                $arquivo->saveAs($dirBase . $dir . $nomeArquivo);

                $modelDocumento = new DocumentoCondutor();
                $modelDocumento->nome = $nomeArquivo;
                $modelDocumento->idCondutor = $model->id;
                $modelDocumento->arquivo = $dir . $nomeArquivo;
                $modelDocumento->idTipo = $idTipoDocumento;
                $modelDocumento->dataCadastro = date('Y-m-d H:i:s');
                $modelDocumento->save();

                $i++;
            }

            //Atualiza tabela de logs
            $this->salvarLog(Log::ACAO_ATUALIZAR, $file, $model->id);
        }
    }

    private function uploadMultiple($model)
    {
        $this->actionUploadFile($model, 'documentoCRLV', TipoDocumento::TIPO_CRLV);
        $this->actionUploadFile($model, 'documentoApoliceSeguro', TipoDocumento::TIPO_APOLICE_SEGURO);
        $this->actionUploadFile($model, 'documentoAutorizacaoEscolar', TipoDocumento::TIPO_AUTORIZACAO_ESCOLAR);
        $this->actionUploadFile($model, 'documentoProntuarioCNH', TipoDocumento::TIPO_PRONTUARIO_CNH);

        $this->actionUploadFile($model, 'documentoComprovanteEndereco', TipoDocumento::TIPO_COMPROVANTE_ENDERECO);
        $this->actionUploadFile($model, 'documentoCNHCondutor', TipoDocumento::TIPO_CNH);
        $this->actionUploadFile($model, 'documentoContrato', TipoDocumento::TIPO_CONTRATO);
        $this->actionUploadFile($model, 'documentoMonitorRG', TipoDocumento::TIPO_RG_MONITOR);
        $this->actionUploadFile($model, 'documentoMonitorCPF', TipoDocumento::TIPO_CPF_MONITOR);
        $this->actionUploadFile($model, 'documentoMonitorContratoTrabalho', TipoDocumento::TIPO_CONTRATO_TRABALHO);
        $this->actionUploadFile($model, 'documentoMonitorCertidaoAntecedentesCriminais', TipoDocumento::TIPO_CERTIDAO_ANTECEDENTES_CRIMINAIS);

        $this->actionUploadFile($model, 'documentoCertidaoInscricaoMunicipal', TipoDocumento::TIPO_CERTIDAO_INSCRICAO_MUNICIPAL);

        $this->actionUploadFile($model, 'documentoDebitosMunicipais', TipoDocumento::TIPO_CERTIDAO_NEGATIVA_DEBITOS_MUNICIPAIS);

        $this->actionUploadFile($model, 'documentoCertidaoNegativaAcoesCiveis', TipoDocumento::TIPO_CERTIDAO_NEGATIVA_ACOES_CIVEIS);
    }


    private function salvarRegioes($post,$model){
        CondutorRegiao::deleteAll(['idCondutor' => $model->id]);
        if( !empty($post['Condutor']['inputRegiao']) ) {
            foreach ($post['Condutor']['inputRegiao'] as $key => $value) {
                $modelGrupo = new CondutorRegiao();
                $modelGrupo->idCondutor = $model->id;
                $modelGrupo->regiao = $value;
                if (!$modelGrupo->save())
                {
                    \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar grupos');
                }
            }
        }
    }

    /**
     * get ultima modifcacao do condutor
     * @param int $id
     * @return mixed
     */
    public function getDataUltimaModif($id)
    {
        $sql = "SELECT MAX(data) FROM escolarsjc.Log WHERE tabela='Condutor' AND idCondutorTable=$id
            AND coluna<>'lat' AND coluna<>'lng'";
        
        $queryResult = Yii::$app->getDb()->createCommand($sql)->queryAll();

        return $queryResult;
    }

    /**
     * get ultima modificacao do monitor
     * @param int $id
     * @return mixed
     */
    public function getDataUltimaModifMonitor($id)
    {
        $sql = "SELECT MAX(data) FROM escolarsjc.Log WHERE tabela='Condutor' AND idCondutorTable=$id
            AND (coluna='nomeMonitor' OR coluna='rgMonitor' OR coluna='cpfMonitor' OR coluna='telefoneMonitor')";
        
        $queryResult = Yii::$app->getDb()->createCommand($sql)->queryAll();

        return $queryResult;
    }

    /**
     * get ultima modificacao de dados compostos do Cndutor
     * @param int $id
     * @return mixed
     */
    public function getDataUltimaModifCompostos($id)
    {

        $sql = "SELECT MAX(data) FROM escolarsjc.Log WHERE tabela='Condutor' AND idCondutorTable=$id
                    AND (coluna='telefone' OR coluna='telefone2' OR coluna='celular' 
                            OR coluna='celular2' OR coluna='folhaPonto' OR coluna='pesquisaRota'
                        )";
        
        $queryResult = Yii::$app->getDb()->createCommand($sql)->queryAll();

        return $queryResult;
        
    }


    /**
     * get ultima modifcacao do condutor por coluna
     * @param int $id
     * @param string $coluna
     * @return mixed
     */
    public function getDataUltimaModifColuna($id, $coluna)
    {
        $searchResult = Log::find()->select('data')
                                ->andWhere([
                                    'tabela' => 'Condutor', 
                                    'idCondutorTable' => $id,
                                    'coluna' => $coluna
                                ])
                                ->orderBy('data desc')
                                ->one();

        return $searchResult;
    }

    /**
     * Converte string de data para formato correto
     * @param string $coluna
     * @return string
     */
    public function formatPadraoDataBR($stringData)
    {
        if($stringData){
            $dataFormatada = "(Alterado em: " . date("d/m/Y H:i", strtotime($stringData)) . ")";
        }else{
            $dataFormatada = '';
        }

        return $dataFormatada;
    }

    private function salvarLog($acao, $coluna, $id)
    {
        if ($coluna) {
            Log::salvarLog([
                'acao' => $acao,
                'referencia' => 'Condutor-' . $id,
                'tabela' => 'Condutor',
                'coluna' => $coluna,
                'antes' => 'Arquivo antigo',
                'depois' => 'Arquivo atualizado',
                'key' => 'idCondutor',
                'id' => $id,
            ]);
        }
    }

    /**
     * Updates an existing Condutor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		
        // 
        // $this->criarUsuario($model);
        //Pass em formato BT
        // print  $_POST['Condutor[dataNascimento]'];
        // print '<br><br><br>';
        // $novaSenha = str_replace('/', '', Yii::$app->request->post('dataNascimento'));
        $logData = array();
        $arrayBusca = array('telefone', 'fotoMotorista', 'documentoCNHCondutor', 'documentoCRLV', 
                        'documentoApoliceSeguro', 'documentoAutorizacaoEscolar', 
                        'documentoProntuarioCNH', 'documentoMonitorRG', 'documentoMonitorContratoTrabalho',
                        'documentoMonitorCertidaoAntecedentesCriminais'
                    );
                    
        $dataUltimoLog = $this->getDataUltimaModif($id);
        $logData['dataUltimoLog'] = $this->formatPadraoDataBR($dataUltimoLog[0]["MAX(data)"]);

        $dataModifMonitor = $this->getDataUltimaModifMonitor($id);
        $logData['dataModifMonitor'] = $this->formatPadraoDataBR($dataModifMonitor[0]["MAX(data)"]);

        $dataDadosCompCondut = $this->getDataUltimaModifCompostos($id);
        $logData['dataDadosCompCondut'] = $this->formatPadraoDataBR($dataDadosCompCondut[0]["MAX(data)"]);

        foreach ($arrayBusca as $k => $value) {
            $buscaResult = $this->getDataUltimaModifColuna($id, $value);
            $logData[$value] = $this->formatPadraoDataBR($buscaResult["data"]);
        }            

        if ($model->load(Yii::$app->request->post())) {
            $model = $this->loadCheckbox($model);
            $this->uploadMultiple($model);
            // $novaSenha = str_replace('/', '', $model->dataNascimento);
            $model = $this->getDates($model);
            $model->save();
            if ($model->anexoFotoMotorista) {
                $this->uploadSingleFile($model, 'anexoFotoMotorista', 'fotoMotorista');										
            }
			Yii::$app->db->createCommand()->update('Condutor', ['fotoMotorista' => $model->fotoMotorista], 'id = '.$model->id)->execute();			

            $this->actionUploadFile($model, 'documentoCRLV', TipoDocumento::TIPO_CRLV);
            $this->actionUploadFile($model, 'documentoApoliceSeguro', TipoDocumento::TIPO_APOLICE_SEGURO);
            $this->actionUploadFile($model, 'documentoAutorizacaoEscolar', TipoDocumento::TIPO_AUTORIZACAO_ESCOLAR);
            $this->actionUploadFile($model, 'documentoProntuarioCNH', TipoDocumento::TIPO_PRONTUARIO_CNH);
            
            $usuario = Usuario::findOne(['cpf' => Usuario::limparCPF($model->cpf)]);
            
            if ($usuario)
            {
                if($model->novaSenha)
                {
                    $usuario->status = $model->status;
                    $usuario->setPassword($model->novaSenha);
                }
    
                if ($model->status == Condutor::STATUS_INATIVO)
                    $usuario->status = Usuario::STATUS_INATIVO;
    
                if ($model->status == Condutor::STATUS_ATIVO)
                    $usuario->status = Usuario::STATUS_ATIVO;
    
                $usuario->save();
            }
			
			
           
            $this->salvarRegioes(Yii::$app->request->post(), $model);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model = $this->getDatesBr($model);
            return $this->render('update', [
                'model' => $model,
                'logData' => $logData,
            ]);
        }
    }

    public function actionUpdatePwd($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post())) {

            $usuario = Usuario::findOne(['cpf' => Usuario::limparCPF($model->cpf)]);
            
            if($model->novaSenha)
            {
                $usuario->status = $model->status;
                $usuario->setPassword($model->novaSenha);
                $usuario->save();
            } 
            return $this->goBack(Yii::$app->request->referrer);
        }

        return $this->renderAjax('update-pwd', [
            'model' => $model,
        ]);

    }

    public function actionTrataRegiao($max)
    {
        $condutores = Condutor::find()->where('id', '<', $max)->all();

        foreach($condutores as $model)
        {
            CondutorRegiao::deleteAll(['idCondutor' => $model->id]);

            $modelGrupo = new CondutorRegiao();
            if($model->regiao){
                $modelGrupo->idCondutor = $model->id;
                $modelGrupo->regiao = $model->regiao;
                $modelGrupo->save(false);
            }
        }
    }
    /**
     * Deletes an existing Condutor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		/** 
		* mauro dia 17/08/2021
		* altera????o para n??o dar erro para o usu??rio, quando excluir o condutor
		*/
		
  	   $dadosCondutor = HistoricoMovimentacaoRota::find()->andWhere(['idCondutorAtual' => $id])->orWhere(['idCondutorAnterior' => $id])->one();
		  
				
		if($dadosCondutor->id){
			\Yii::$app->getSession()->setFlash('error', 'O condutor n??o pode ser exclu??do, por ter hist??rico de movimenta????o.');
		}else{
			\Yii::$app->getSession()->setFlash('success', 'O condutor foi exclu??do.');
			$this->findModel($id)->delete();
		}
        

        return $this->redirect(['index']);
    }

    /**
     * Finds the Condutor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Condutor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Condutor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionArquivos($id, $tipo)
    {
        DocumentoCondutor::deleteAll(['idCondutor' => $id, 'idTipo' => $tipo]);
        return $this->redirect(['view', 'id' => $id]);
    }


    public function actionDeleteDoc($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = DocumentoCondutor::findOne($id);
        $arquivo = $model->arquivo;
        if ($model->delete())
        {
            return [
                'status' => true,
                'message' => 'Documento exclu??do da base. '. ((!unlink(Yii::$app->basePath . "/web/" . $arquivo))?'Arquivo n??o exclu??do.':''),
            ];
        }
        else
        {
            return [
                'status' => false,
                'message' => 'Erro ao excluir documento',
            ];
        }
    }

    public function actionBeneficioAluno() {
        $this->enableCsrfValidation = false;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $aluno = Aluno::findOne($_POST['idAluno']);

        if(!$aluno)
            throw new NotFoundHttpException('The requested page does not exist.');
        $aluno->naoEstaUtilizando = $_POST['marcado'];
        if(isset($_POST['justificativa'])) {
            $aluno->justificativaNaoUtilizando = $_POST['justificativa'];
        }
        $aluno->save();
        return ['status' => true];
    }

    private function cabecalho(&$sheet, &$i, $data) {
        $start = $i;
        $left = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            )
        );
        $right = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            )
        );
        $center = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            )
        );
        $fontSize12 = [
            'font' => [
                'size' => 12
            ]
        ];
        $fontSize14 = [
            'font' => [
                'size' => 14
            ]
        ];
        $fontSize16 = [
            'font' => [
                'size' => 16
            ]
        ];
        $borderHard = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $borderSoft = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
    
        $colorRed =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED );
        $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE );
       
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath(Yii::getAlias('@webroot').'/img/logoRelatorioCondutor.png'); // put your path and image here
        $drawing->setCoordinates('A'.($i+2));
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(6);
        $drawing->setRotation(0);
        $drawing->setWorksheet($sheet);
    

    
        // SETUP DAS COLUNAS
        $sheet->getColumnDimension('A')->setWidth(5.29);
        $sheet->getColumnDimension('B')->setWidth(12.29);
        $sheet->getColumnDimension('C')->setWidth(52.71);
        $sheet->getColumnDimension('D')->setWidth(15.86);
        $sheet->getColumnDimension('E')->setWidth(9.71);
        $sheet->getColumnDimension('F')->setWidth(4.71);
        $sheet->getColumnDimension('G')->setWidth(4.29);
        $sheet->getColumnDimension('H')->setWidth(9.86);
        $sheet->getColumnDimension('I')->setWidth(9.86);
        $sheet->getColumnDimension('J')->setWidth(15.86);
        $sheet->getColumnDimension('K')->setWidth(28.29);
        $sheet->getColumnDimension('L')->setWidth(10);
        $sheet->getColumnDimension('M')->setWidth(16.86);
        $sheet->getProtection()->setPassword('t3sc');

        $sheet->getProtection()->setSheet(true);

    
    
        //HEADER DA TABELA
        $sheet->mergeCells('A'.$i.':M'.$i);
        $sheet->getStyle('A'.($i))->applyFromArray($right)->getFont()->setBold(true);
        $sheet->setCellValue('A'.$i, "5.3.04.00.07 - Lista de Alunos Transportados");
        $sheet->getStyle('A'.($i))->applyFromArray($fontSize12);

        $sheet->getRowDimension($i)->setRowHeight(15.75);

        
        $sheet->getStyle('A'.$i.':M'.($i+1))->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
   

        $i++;
        
        $sheet->mergeCells('A'.$i.':M'.$i);
        $sheet->getStyle('A'.($i))->applyFromArray($center)->getFont()->setBold(true);
        $sheet->setCellValue('A'.$i, "1 (UMA) PLANILHA POR ESCOLA/PER??ODO");
        $sheet->getStyle('A'.($i))->applyFromArray($fontSize16);
        $sheet->getRowDimension($i)->setRowHeight(21.75);



        $i++;
        // lOGO FIX
        $sheet->getStyle('A'.($i))->applyFromArray($right)->getFont()->setBold(true);

        $sheet->mergeCells('B'.$i.':K'.$i);
        $sheet->setCellValue('B'.$i, "Secretaria de Educa????o e Cidadania - Rua Fel??cio Savastano, 240 - Vila Industrial - 3901 2000 Transporte Escolar - 3901 2165 / 2065 - transporte.escolar@sjc.sp.gov.br");
        $sheet->getStyle('B'.($i))->applyFromArray($center)->getFont()->setBold(true);
        // $sheet->getStyle('A'.$i.':K'.$i)
        // ->getAlignment()->setWrapText(true);

        
        $sheet->mergeCells('L'.$i.':M'.$i);
        $sheet->setCellValue('L'.$i, "DATA: ".date('d/m/Y'));
        $sheet->getStyle('L'.($i))->applyFromArray($fontSize12);

        $sheet->getStyle('L'.($i))->applyFromArray($center)->getFont()->setBold(true);
        $sheet->getRowDimension($i)->setRowHeight(43.50);

        $i++;
        $sheet->mergeCells('A'.$i.':M'.$i);
        $sheet->getStyle('A'.($i))->applyFromArray($center)->getFont()->setBold(true);
        $sheet->setCellValue('A'.$i, 'DADOS DO CONDUTOR');
        $sheet->getStyle('A'.($i))->applyFromArray($fontSize14);

        $sheet->getRowDimension($i)->setRowHeight(20.25);

        $i++;
        $sheet->mergeCells('A'.$i.':B'.$i);
        $sheet->setCellValue('A'.$i, 'NOME COMPLETO:');
        $sheet->getStyle('A'.($i))->applyFromArray($right)->getFont()->setBold(true);
        $sheet->getRowDimension($i)->setRowHeight(20.25);


        $sheet->mergeCells('C'.$i.':F'.$i);
        $sheet->getStyle('C'.$i.':F'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('C'.$i.':F'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('C'.$i, $data['nomeCompleto']);

        $sheet->mergeCells('G'.$i.':I'.$i);
        $sheet->setCellValue('G'.$i, 'E-MAIL:');
        $sheet->getStyle('G'.($i))->applyFromArray($right)->getFont()->setBold(true);
       
        $sheet->mergeCells('J'.$i.':K'.$i);
        $sheet->getStyle('J'.$i.':K'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('J'.$i.':K'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('J'.$i, $data['email']);


        $sheet->setCellValue('L'.$i, 'ALVAR??:');
        $sheet->getStyle('L'.($i))->applyFromArray($left)->getFont()->setBold(true);

        $sheet->getStyle('M'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('M'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('M'.$i, $data['alvara']);

        $i++;
        $sheet->mergeCells('A'.$i.':B'.$i);
        $sheet->setCellValue('A'.$i, 'ENDERE??O:');
        $sheet->getStyle('A'.($i))->applyFromArray($right)->getFont()->setBold(true);
        $sheet->getRowDimension($i)->setRowHeight(20.25);



        $sheet->mergeCells('C'.$i.':K'.$i);
        $sheet->getStyle('C'.$i.':K'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('C'.$i.':K'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('C'.$i, $data['endereco']);
        
        $sheet->setCellValue('L'.$i, 'N??:');
        $sheet->getStyle('L'.($i))->applyFromArray($left)->getFont()->setBold(true);

        $sheet->getStyle('M'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('M'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('M'.$i, $data['numeroEndereco']);

        $i++;
        $sheet->mergeCells('A'.$i.':B'.$i);
        $sheet->setCellValue('A'.$i, 'BAIRRO:');
        $sheet->getStyle('A'.($i))->applyFromArray($right)->getFont()->setBold(true);
        $sheet->getRowDimension($i)->setRowHeight(20.25);

        $sheet->mergeCells('C'.$i.':E'.$i);
        $sheet->getStyle('C'.$i.':E'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('C'.$i.':E'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('C'.$i, $data['bairro']);

        $sheet->setCellValue('F'.$i, 'CEP:');
        $sheet->getStyle('F'.($i))->applyFromArray($left)->getFont()->setBold(true);

        $sheet->mergeCells('G'.$i.':I'.$i);
        $sheet->getStyle('G'.$i.':I'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('G'.$i.':I'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('G'.$i, $data['cep']);

        $sheet->setCellValue('J'.$i, 'TELS P/ CONTATO:');
        $sheet->getStyle('J'.($i))->applyFromArray($left)->getFont()->setBold(true);


        $sheet->mergeCells('K'.$i.':M'.$i);
        $sheet->getStyle('K'.$i.':M'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('K'.$i.':M'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('K'.$i, $data['telefones']);

        $i++;
        $sheet->mergeCells('A'.$i.':C'.$i);
        $sheet->setCellValue('A'.$i, 'UNIDADE ESCOLAR ATENDIDA:');
        $sheet->getStyle('A'.($i))->applyFromArray($fontSize12);
        $sheet->getStyle('A'.($i))->applyFromArray($right)->getFont()->setBold(true);
        $sheet->getRowDimension($i)->setRowHeight(20.25);

        $sheet->mergeCells('D'.$i.':M'.$i);
        $sheet->getStyle('D'.$i.':M'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('D'.$i.':M'.$i)->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('D'.$i, $data['escolaNome']);

        $i++;
        $sheet->mergeCells('A'.$i.':C'.$i);
        $sheet->setCellValue('A'.$i, 'DIST??NCIAS OCIOSAS (KM) - CASA DO CONDUTOR/1?? ALUNO:');
        $sheet->getStyle('A'.($i))->applyFromArray($fontSize12);
        $sheet->getStyle('A'.($i))->applyFromArray($right)->getFont()->setBold(true);
        $sheet->getRowDimension($i)->setRowHeight(20.25);

        // $sheet->mergeCells('D'.$i);
        $sheet->getStyle('D'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('D'.$i)->getProtection()->setLocked( \PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

        $sheet->mergeCells('E'.$i.':I'.$i);
        $sheet->setCellValue('E'.$i, '??LTIMO ALUNO/CASA CONDUTOR:');
        $sheet->getStyle('E'.($i))->applyFromArray($fontSize12);
        $sheet->getStyle('E'.($i))->applyFromArray($right)->getFont()->setBold(true);



        // $sheet->mergeCells('J'.$i);
        $sheet->getStyle('J'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('J'.$i)->getProtection()->setLocked( \PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

        $sheet->mergeCells('K'.$i.':L'.$i);
        $sheet->setCellValue('K'.$i, 'PER??ODO (MANH??/TARDE/NOITE):');
        $sheet->getStyle('K'.($i))->applyFromArray($fontSize12);
        $sheet->getStyle('K'.($i))->applyFromArray($right)->getFont()->setBold(true);
   

        // $sheet->mergeCells('M'.$i);
        $sheet->getStyle('M'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('00F6F6F6');
        $sheet->getStyle('M'.($i))->applyFromArray($left)->getFont()->setBold(true);
        $sheet->setCellValue('M'.$i, $data['periodo']);

        $i++;
        $sheet->mergeCells('A'.$i.':M'.$i);
        $sheet->getStyle('A'.($i))->applyFromArray($center)->getFont()->setBold(true);
        $sheet->setCellValue('A'.$i, "DADOS DOS ALUNOS");
        $sheet->getStyle('A'.($i))->applyFromArray($fontSize14);
        $sheet->getRowDimension($i)->setRowHeight(20.25);


        // COLUNAS DO CABE??ALHO
        $i++;
        $sheet->getRowDimension($i)->setRowHeight(60);
        $sheet->getStyle('A'.$i.':M'.$i)->applyFromArray($center)->getFont()->setBold(true);

        $sheet->setCellValue('A'.$i, "N?? ORDEM");
        $sheet->getStyle('A'.$i)
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.$i)->getAlignment()->setTextRotation(90);
        // $sheet->setAutoFilter('A'.$i);


        $sheet->mergeCells('B'.$i.':C'.$i);
        $sheet->setCellValue('B'.$i, 'NOME DO ALUNO');
        // $sheet->setAutoFilter('B'.$i);

        $sheet->setCellValue('D'.$i, "RA");

        $sheet->mergeCells('E'.$i.':G'.$i);
        $sheet->setCellValue('E'.$i, "TURMA");        
        // $sheet->setAutoFilter('E'.$i);

        $sheet->setCellValue('H'.$i, "VIAGEM ENTRADA");
        $sheet->getStyle('H'.$i)
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('H'.$i)->getAlignment()->setTextRotation(90);
        // $sheet->setAutoFilter('H'.$i);

        $sheet->setCellValue('I'.$i, "VIAGEM SA??DA");
        $sheet->getStyle('I'.$i)
        ->getAlignment()->setWrapText(true);
        $sheet->getStyle('I'.$i)->getAlignment()->setTextRotation(90);
        // $sheet->setAutoFilter('I'.$i);

        $sheet->mergeCells('J'.$i.':K'.$i);
        $sheet->setCellValue('J'.$i, "ENDERE??O COMPLETO");
        // $sheet->setAutoFilter('J'.$i);

        $sheet->mergeCells('L'.$i.':M'.$i);
        $sheet->setCellValue('L'.$i, "BAIRRO");
        // $sheet->setAutoFilter('L'.$i);

        // FOREACH DE DADOS

        // aplica borda
        $sheet->getStyle('A'.$start.':M'.$i)->applyFromArray($borderSoft);


    }

    private function addAluno(&$i, $aluno, &$cont, &$sheet, $condutor) {
                // $sheet->setCellValue('A1:N5', 'Logo');
        $left = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            )
        );
        $right = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            )
        );
        $center = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            )
        );
        $fontSize12 = [
            'font' => [
                'size' => 12
            ]
        ];
        $fontSize14 = [
            'font' => [
                'size' => 14
            ]
        ];
        $fontSize16 = [
            'font' => [
                'size' => 16
            ]
        ];
        $borderHard = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $borderSoft = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
    
        $colorRed =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED );
        $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE );
          $sheet->getRowDimension($i)->setRowHeight(20);
                        $sheet->getStyle('A'.$i.':M'.$i)->applyFromArray($borderSoft);
                        if($aluno->naoEstaUtilizando > 0) {
                            $sheet->getRowDimension($i)->setRowHeight(40);
                            $sheet->getStyle('A'.$i.':M'.$i)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('00FFCCCC');
                        }
                        $sheet->setCellValue('A'.$i, $cont);

                        $sheet->setCellValue('B'.$i, $aluno->nome);
                        $sheet->mergeCells('B'.$i.':C'.$i);

                        $sheet->setCellValue('D'.$i, $aluno->RACompleto);
                        
                        $sheet->setCellValue('E'.$i, Aluno::ARRAY_SERIES[$aluno->serie].'/'.Aluno::ARRAY_TURMA[$aluno->turma]);
                        $sheet->mergeCells('E'.$i.':G'.$i);


                            foreach($aluno->meusPontos as $alunoPonto) {

                                if($alunoPonto->ponto->condutorRota->idCondutor == $condutor->id) {
                                    if($alunoPonto->ponto->condutorRota->sentido == CondutorRota::SENTIDO_IDA)
                                        $sheet->setCellValue('H'.$i, $alunoPonto->ponto->condutorRota->viagem);    
                                    else 
                                        $sheet->setCellValue('I'.$i, $alunoPonto->ponto->condutorRota->viagem);    
                                }
                            }


                        $sheet->mergeCells('J'.$i.':K'.$i);
                        $end = $aluno->enderecoCompleto();
                        if($aluno->naoEstaUtilizando > 0)
                            $end = mb_strtoupper($aluno->justificativaNaoUtilizando,'utf-8');
                        $sheet->setCellValue('J'.$i, $end);
                        $sheet->getStyle('J'.$i)->getAlignment()->setWrapText(true);

                        $sheet->mergeCells('L'.$i.':M'.$i);
                        $sheet->setCellValue('L'.$i, $aluno->bairro);
    }

    public function actionExportarAlunosTransportados($tipo){
        $condutor = Condutor::find()->where(['idUsuario' => \Yii::$app->User->identity->id])->one();
        $idEscolas = array_column($condutor->escolas,'idEscola');
        $idRotas = array_column($condutor->vinculo,'id');;

        // $content = '<table>';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    
        // $sheet->setCellValue('A1:N5', 'Logo');
        $left = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            )
        );
        $right = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            )
        );
        $center = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            )
        );
        $fontSize12 = [
            'font' => [
                'size' => 12
            ]
        ];
        $fontSize14 = [
            'font' => [
                'size' => 14
            ]
        ];
        $fontSize16 = [
            'font' => [
                'size' => 16
            ]
        ];
        $borderHard = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $borderSoft = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
    
        $colorRed =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED );
        $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE );
        $i = 1;
        
        
        $data = [
            'nomeCompleto' => $condutor->nome,
            'endereco' => $condutor->enderecoCompleto(),
            'email' => $condutor->email,
            'alvara' => $condutor->alvara,
            'bairro' => $condutor->bairro,
            'cep' => $condutor->cep,
            'numeroEndereco' => $condutor->numeroResidencia,
            'telefones' => $this->getTelefoneValido($condutor),
        ];        
        $folha = 1;
        foreach($condutor->escolas as $condutorEscola) {

            foreach(CondutorRota::ARRAY_TURNOS as $valorTurno=>$labelTurno) {
                $i=1;
                $alunos = [];
                $alunosIds = [];
                $rotas = CondutorRota::find()->where(['turno' => $valorTurno])->andWhere(['idCondutor' => $condutor->id])->all(); 
                foreach($rotas as $rota) {
                    foreach($rota->alunoPonto as $alunoPonto) {
                        $aluno = $alunoPonto->aluno;
                        if($aluno->idEscola == $condutorEscola->idEscola && !in_array($aluno->id, $alunosIds)) {
                            $alunos[] = $alunoPonto->aluno;
                            $alunosIds[] = $aluno->id;
                        }
                    }
                }
                $data['escolaNome'] = $condutorEscola->escola->nomeCompleto;
                $data['periodo'] = $labelTurno;
                
               
               
               
                if(count($alunos) > 0) {
                    if($folha > 1)
                        $sheet = $spreadsheet->createSheet();
                    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                    $sheet->setTitle('FOLHA '.$folha);
                    $this->cabecalho($sheet,$i, $data);
                    $i+=1;
                    $cont = 1;
                    // $sheet->setCellValue('A'.$i, $cont);
                    // $sheet->setCellValue('B'.$i, 'XXXXXX');

                    // Adciona alunos normais usando o beneficios
                    foreach($alunos as $aluno) {
                        if($aluno->naoEstaUtilizando == 0 || $aluno->naoEstaUtilizando == null ){
                            $this->addAluno($i, $aluno, $cont, $sheet, $condutor);
                        $i++;
                        $cont++;
                        }
                    }
                    // Adciona alunos que n??o est??o usando o beneficios
                    foreach($alunos as $aluno) {
                        if($aluno->naoEstaUtilizando > 0){
                        $this->addAluno($i, $aluno, $cont, $sheet, $condutor);
                        $i++;
                        $cont++;
                        }
                   
                    }
                    $folha++;                    
                }

            
            }
          
        }
        // die(1);
        // $this->cabecalho($sheet,$i);
        
        $base = "arquivos/_exportacoes/";
            
        switch($tipo){
            case 'PDF':
                try {
                    $filename = $base."Condutores_".date('d-m-Y-H-i-s').".pdf";
                    
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
                    $writer ->setPreCalculateFormulas(false);
                    $writer->writeAllSheets();  //This allow export multiple sheets

                    $writer->save($filename);
    
                    header("Content-Disposition: attachment; filename=".$filename);
                    $content = file_get_contents($filename);
                    unlink($filename);
                    exit($content);
                    
                } catch(Exception $e) {
                    exit($e->getMessage());
                }
    
            break;
            case 'TXT':
    
                $filename = $base.'_Condutores_'.date('d-m-Y-H-i-s').'.txt';
                $fp = fopen($filename, 'a');
                $query = Condutor::find()->orderBy(['nome' => SORT_ASC]);
                if(isset($_GET['selecionados']) && $_GET['selecionados'] != '') {
                    $ids = explode(',',$_GET['selecionados']);
                    $query = $query->where(['in', 'id', $ids]);
                }
                foreach($query->all() as $model) {
                    $l='';
                    $l .= $model->nome;
                    $l .= ';'.$model->alvara;
                    $l .= ';'.Condutor::ARRAY_REGIAO[$model->regiao];
                    $complemento ='';
                    if($model->complementoResidencia){
                        $complemento .= ','.$model->complementoResidencia;
                    }
                    $num = '';
                    if($model->numeroResidencia){
                        $num .= ', N?? '.$model->numeroResidencia;
                    }
                    if($model->bairro)
                        $model->bairro = ' - '.$model->bairro;
                    $l .= ';'.$model->tipoLogradouro.' '.trim($model->endereco).''.$complemento.''.$num.$model->bairro.' '.$model->cep;
                    $l .= ';'.$model->veiculo->modelo->marca->nome.' '.$model->veiculo->modelo->nome.' '.Veiculo::ARRAY_TIPO[$model->veiculo->combustivel];
                    $l .= ';'.$model->veiculo->capacidade;
                    $l .= ';'.Veiculo::ARRAY_ADAPTADO[$model->veiculo->adaptado];
        
                    $escolas = [];
                    foreach ($model->escolas as $escola)
                    {
                        $escolas[] = $escola->escola->nomeCompleto;
                    }
                    $l .= ';'.implode (',', $escolas);
                    $l .= '';

                    fwrite($fp,$l);
                }
                fclose($fp);
                try {
                    // $writer = new Xlsx($spreadsheet);
                    header("Content-Disposition: attachment; filename=".$filename);
                    $content = file_get_contents($filename);
                    unlink($filename);
                    exit($content);
                 
                } catch(Exception $e) {
                    exit($e->getMessage());
                }
            break;
            case 'EXCEL':
                try {
                    $writer = new Xlsx($spreadsheet);
                    $filename = $base."Condutores_".date('d-m-Y-H-i-s').".xlsx";
                    $writer->save($filename);
                    header("Content-Disposition: attachment; filename=".$filename);
                    $content = file_get_contents($filename);
                    unlink($filename);
                    exit($content);
                 
                } catch(Exception $e) {
                    exit($e->getMessage());
                }
            break;
        }
       
    }

    public function actionGerenciarExportacao()
    {
        $dados = [];
        $condutor = $this->session->get('condutor');
        $multipleOrder = $this->session->get('sort');
        $alunosSessao =  $this->session->get('alunos');
        $dados =  $this->session->get('dados');

        if (isset($_GET['selecionados']) && $_GET['selecionados']) {
            $dados = [];
            $listaSelecionados = explode(',', $_GET['selecionados']);
            foreach ($alunosSessao as $aluno) {
                if (in_array($aluno->id, $listaSelecionados))
                    $dados[] = $aluno;
            }
            $this->session->set('alunos2', $dados);
        }
        $this->layout = 'main-pdf';

        return $this->render('gerenciar-exportacao', [
            'dados' => $dados,
        ]);
    }

    public function actionRemoverFotoMotorista($id){
        if (($model = Condutor::findOne($id)) !== null) {
            $model->fotoMotorista = '';
            $model->save();
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return Json::encode([
                'error' => false
            ]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionReportFinanceiroPdf()
    {
        $ano = Yii::$app->request->get('ano');
        $mes = Yii::$app->request->get('mes');
        $keys = explode(',', str_replace('[','',str_replace(']', '', Yii::$app->request->get('keys'))));

        // throw new NotFoundHttpException(print_r($keys, true));

        $searchModel = new ControleFinanceiroSearch();
        $searchModel->ano = $ano;
        $searchModel->mes = $mes;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $content = '';
        
        $content .= '<table border="0" width="100%" class="table">';
        $content .= '
        <tr>
            <th style="text-align:center;"><b>Condutor</b></th>
            <th style="text-align:center;"><b>NIT</b></th>
            <th style="text-align:center;"><b>Dias<br>Trabalhados</b></th>
            <th style="text-align:center;"><b>S??bado<br>Letivo</b></th>
            <th style="text-align:center;"><b>Dia(s)<br>Excepcional(is)<br>(1)</b></th>
            <th style="text-align:center;"><b>Viagem/Km<br>(1)</b></th>
            <th style="text-align:center;"><b>Dia(s)<br>Excepcional(is)<br>(2)</b></th>
            <th style="text-align:center;"><b>Viagem/Km<br>(2)</b></th>
            <th style="text-align:center;"><b>Valor<br>Nota</b></th>
            <th style="text-align:center;"><b>Protocolo<br>TESC</b></th>
            <th style="text-align:center;"><b>Protocolo<br>GC</b></th>
            <th style="text-align:center;"><b>Lote</b></th>
            <th style="text-align:center;"><b>Saldo<br>AF</b></th>
        </tr>';
        
        foreach ($dataProvider->getModels() as $model) {
            if (!in_array($model->id, $keys))
                continue;

            $content .= '<tr>';
            $content .= $this->td(20, $model->condutor->nome);
            $content .= $this->td(10, $model->condutor->nit);
            $content .= $this->tdCenter(8, $model->diasTrabalhados);
            $content .= $this->tdCenter(8, $model->sabadoLetivo);
            $content .= $this->tdCenter(8, $model->diasExcepcionais1);
            $content .= $this->tdCenter(8, $model->viagemKm1);
            $content .= $this->tdCenter(8, $model->diasExcepcionais2);
            $content .= $this->tdCenter(8, $model->viagemKm2);
            $content .= $this->td(10, $model->valorNota);

            $date = \DateTime::createFromFormat( 'Y-m-d', $model->protocoloTESC);
            $content .= $this->tdCenter(8, $date ? $date->format('d/m/Y') : ' - ');

            $date = \DateTime::createFromFormat( 'Y-m-d', $model->protocoloGC);
            $content .= $this->tdCenter(8, $date ? $date->format('d/m/Y') : ' - ');
            $content .= $this->tdCenter(8, $model->lote);
            $content .= $this->td(10, $model->saldoAF);
            $content .= '</tr>';
        }
        $content .= '</table>';

        $pdf = new Pdf([
            'mode' => 'c',
            'marginTop' => 50,
            'marginBottom' => 20,
            'marginLeft' => 5,
            'marginRight' => 5,
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .table table { border-collapse: collapse; } .table table, .table th, .table td { border: 1px solid black;} .table th td { padding-left: 3px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ['
                <table width="100%">
                <tr>
                  <Td align="center">
                  <img src="img/brasaoFull.png">
                  </Td>
                </tr>
              </table>'],
                'SetFooter' => ['Emitido em ' . date('d/m/Y') . '|| {PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }
	
	public function actionExportFolhaPonto()
    {
		$ARRAY_MESES = ['','JANEIRO','FEVEREIRO','MAR??O','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBRO','OUTUBRO','NOVEMBRO','DEZEMBRO'];		
		$ano = Yii::$app->request->get('ano');
        $mes = Yii::$app->request->get('mes');
		$id = Yii::$app->request->get('idCondutor');
		
		$mesCompleto = $ARRAY_MESES[$mes];
		
		$model = Condutor::findOne($id);
		
		$primeiroDia = '1';	
		$ultimoDiaMes =  date("t", strtotime($ano.'-'.$mes.'-'.$primeiroDia));
		$primeiraSemana = array();
		$segundaSemana = array();
		$terceiraSemana = array();
		$quartaSemana = array();
		$quintaSemana = array();
		$sextaSemana = array();
		
		$primDia =$ano.'-'.$mes.'-'.$primeiroDia;
		$ultiDia =$ano.'-'.$mes.'-'.$ultimoDiaMes;
		
		for($i=1;$i<=7;$i++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $i.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			if($diaSemana == 0){
				break;
			}else{
				$primeiraSem[$diaSemana] = $ano.'-'.$mes.'-'.$i;
			}			
		}		
		$ate = $i+6;
		for($j=$i;$j<=$ate;$j++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $j.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$segundaSem[$diaSemana] = $ano.'-'.$mes.'-'.$j;		
		}
		$ate = $j+6;
		for($m=$j;$m<=$ate;$m++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $m.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$terceiraSem[$diaSemana] = $ano.'-'.$mes.'-'.$m;			
		}	
		$ate = $m+6;
		for($n=$m;$n<=$ate;$n++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $n.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			$quartaSem[$diaSemana] = $ano.'-'.$mes.'-'.$n;
		}		
		$ate = $n+6;
		for($o=$n;$o<=$ate;$o++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $o.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			if($o<=$ultimoDiaMes){
				$quintaSem[$diaSemana] = $ano.'-'.$mes.'-'.$o;
			}			
		}
		$ate = $ultimoDia;
		for($p=$o;$p<=$ate;$p++){			
			$date = \DateTime::createFromFormat('d/m/Y H:i', $p.'/'.$mes.'/'.$ano.' 00:00');		
			$diaSemana = $date->format('w');
			if($p<=$ultimoDiaMes){
				$sextaSem[$diaSemana] = $ano.'-'.$mes.'-'.$p;
			}			
		}		
		$cor = '#fff';
		$imprime ='1';
		$primeiraSemana = $this->buscaConsultaDiaCondutor($primeiraSem,$id,$cor,$imprime);
		$segundaSemana = $this->buscaConsultaDiaCondutor($segundaSem,$id,$cor,$imprime);
		$terceiraSemana = $this->buscaConsultaDiaCondutor($terceiraSem,$id,$cor,$imprime); 
		$quartaSemana = $this->buscaConsultaDiaCondutor($quartaSem,$id,$cor,$imprime);
		$quintaSemana = $this->buscaConsultaDiaCondutor($quintaSem,$id,$cor,$imprime);
		$sextaSemana = $this->buscaConsultaDiaCondutor($sextaSem,$id,$cor,$imprime);	

		
		$sqlJust ='select p.justificativa,DATE_FORMAT(p.data,"%d/%m/%Y") as data,e.nome,p.idEscola from FolhaPonto p left join Escola e on e.id = p.idEscola where p.idCondutor = '.$id.' and p.`data` between "'.$primDia.'" and "'.$ultiDia.'" and DAYOFWEEK(data) = 7 or DAYOFWEEK(data) = 1  order by p.data' ;
		$dadosJust = Yii::$app->getDb()->createCommand($sqlJust)->queryAll();
		
		$ocorrencia = '-';
		$sqlOco ='select DATE_FORMAT(p.data,"%d/%m/%Y") as data,p.ocorrencia from DiasFolhaPonto p  where  p.`data` between "'.$primDia.'" and "'.$ultiDia.'" and p.ocorrencia <> "'.$ocorrencia.'" order by p.data' ;
		$dadosOco = Yii::$app->getDb()->createCommand($sqlOco)->queryAll();
		
		$sqlControleFinanceiro ="select * from ControleFinanceiro f where f.idCondutor = $id  and f.mes = $mes and ano = $ano" ;
		$dadosFinanceiro = Yii::$app->getDb()->createCommand($sqlControleFinanceiro)->queryAll();

		$isArrayLog =  is_array($dadosOco) ? '1' : '0';
		if($isArrayLog == 1) {
			foreach($dadosOco as $dado){			
				$ocor .= "<tr><td style='border-top: 1px solid white;  border-color:#000;width:100%'>".$dado['data'].' - '.$dado['ocorrencia']."</td><tr>";
			}
		}else{
			$ocor .= "<tr><td style='border-top: 1px solid white;  border-color:#000;width:100%'></td></tr>";
		}
		
		if($dadosFinanceiro[0]['diasTrabalhados']){
			$nomeEmpresa = strtoupper($dadosFinanceiro[0]['nomeEmpresa']);
		}else{
			$nomeEmpresa = strtoupper($model->nome);
		}
		
		if($dadosFinanceiro[0]['alvara']){
			$alvara = strtoupper($dadosFinanceiro[0]['alvara']);
		}else{
			$alvara = strtoupper($model->alvara);
		}
		
		$valorViagemKm1Arr = explode(".",$dadosFinanceiro[0]['valorViagemKm1']);
		$valorViagemKm1Arr[1] = str_replace("0","",$valorViagemKm1Arr[1]);
		if($valorViagemKm1Arr[1] <= 0) {
			$dadosFinanceiro[0]['valorViagemKm1']= $valorViagemKm1Arr[0];
		}
		
		$viagemKm1Arr = explode(".",$dadosFinanceiro[0]['viagemKm1']);
		$viagemKm1Arr[1] = str_replace("0","",$viagemKm1Arr[1]);
		if($viagemKm1Arr[1] <= 0) {
			$dadosFinanceiro[0]['viagemKm1']= $viagemKm1Arr[0];
		}
		
		$valorViagemKm2Arr = explode(".",$dadosFinanceiro[0]['valorViagemKm2']);
		$valorViagemKm2Arr[1] = str_replace("0","",$valorViagemKm2Arr[1]);
		if($valorViagemKm2Arr[1] <= 0) {
			$dadosFinanceiro[0]['valorViagemKm2']= $valorViagemKm2Arr[0];
		}
		
		$viagemKm2Arr = explode(".",$dadosFinanceiro[0]['viagemKm2']);
		$viagemKm2Arr[1] = str_replace("0","",$viagemKm2Arr[1]);
		if($viagemKm2Arr[1] <= 0) {
			$dadosFinanceiro[0]['viagemKm2']= $viagemKm2Arr[0];
		}
		
								
		$html = '
		<table id="" style="width:100%;font-size:70%">
		<tr>
		<td  ><b>CONTRATADO (A) </b>: '.$nomeEmpresa.'</td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ><b>ALVAR?? </b>: '.$alvara.'</td>
		<tr>
		<td > <b> *?? Srs. Condutores favor assinalar com um ???X??? no calend??rio somente os dias trabalhados.</b></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		</tr>
		<tr>
		<td > <b> *?? Se houver s??bado letivo, ser?? obrigat??rio justificar o evento ocorrido.</b></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		</tr>
		<tr>
		<td > <b> *?? Entregar no PRIMEIRO dia letivo do PR??XIMO m??s,  com ocorr??ncias se houver.</b></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		<td  ></td>
		</tr>
		</table>
		<table id="" class="display" style="width:100%;font-size:70%">
							<thead>
								<tr>
									<th colspan=7 style="text-align:center;color:#FFF;background-color:#000">'.$mesCompleto.' - '.$ano.'</th>									
								</tr>
								<tr>
									<th style="text-align:center;color:#000">DOM</th>
									<th style="text-align:center;color:#000">SEG</th>
									<th style="text-align:center;color:#000">TER</th>
									<th style="text-align:center;color:#000">QUA</th>
									<th style="text-align:center;color:#000">QUI</th>
									<th style="text-align:center;color:#000">SEX</th>
									<th style="text-align:center;color:#000">SAB</th>
								</tr>
							</thead>
							<tbody>
								<tr><td colspan=7 style="border-top: 1px solid white;  border-color:#dedede;" ><br><br></td></tr>				
								<tr>	
									'.$primeiraSemana.'																						
								</tr>	
								<tr><td colspan=7 style="border-top: 1px solid white;  border-color:#dedede;" ><BR><BR></td></tr>
								<tr>									
									'.$segundaSemana.'	
								</tr>

								<tr><td colspan=7 style="border-top: 1px solid white;  border-color:#dedede;" ><BR><BR></td></tr>
								<tr>		
									'.$terceiraSemana.'																															
								</tr>
								
								<tr><td colspan=7 style="border-top: 1px solid white;  border-color:#dedede;" ><BR><BR></td></tr>
								<tr>
									'.$quartaSemana.'
								</tr>								
								<tr><td colspan=7 style="border-top: 1px solid white;  border-color:#dedede;" ><BR><BR></td></tr>
								<tr>
									'.$quintaSemana.'
								</tr>				
									<tr><td colspan=7 style="border-top: 1px solid white;  border-color:#dedede;" ><BR><BR></td></tr>							
									<tr>
									'.$sextaSemana.'	
								</tr>
								</tbody>					
								</table>
								<table id="" class="display" style="width:100%;font-size:70%">
								<tr>
								<td style="border-top: 1px solid white;  border-color:#000;">Dias Letivos <br> Trabalhados</td>
								<td style="border-top: 1px solid white;  border-color:#000;">Kms rodado por dia  / <br> N?? de viagens por dia	</td>
								<td style="border-top: 1px solid white;  border-color:#000;">Valor por Km  /  Valor <br> por Viagem		</td>
								<td style="border-top: 1px solid white;  border-color:#000;" >Total (R$)</td>
								<tr>		
								<tr>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['diasTrabalhados'].'</td>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['valorViagemKm1'].'</td>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['viagemKm1'].'</td>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['valorDiasUteis'].'</td>
								<tr>		
								<tr>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['sabadoLetivo'].'</td>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['valorViagemKm2'].'</td>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['viagemKm2'].'</td>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['valorSabado'].'</td>
								<tr>		
								<tr>
								<td colspan="3" style="text-align:right;font-weight:bold!important;border-top: 1px solid white;  border-color:#000;"><b>Valor Total</b></td>
								<td style="border-top: 1px solid white;  border-color:#000;">'.$dadosFinanceiro[0]['valorNota'].'</td>
								<tr>		
								</table>
								
								<table id="" class="display" style="width:100%">
								<tr>
								<td  style="border-top: 1px solid white;  border-color:#000;width:100"><br></td>
								</tr>	
								</table>
								
								<table id="" class="display" style="width:100%;font-size:70%">
								<tr>
								<td colspan=3 style="border-top: 1px solid white;  border-color:#000;width:100%;font-weight:bold">Srs. Condutores - Por favor preencher as informa????es referentes ao(s) s??bado(s) trabalhado(s).</td>
								<tr>		
								<tr>
								<td  style="border-top: 1px solid white;  border-color:#000;width:5%">Data</td>
								<td  style="border-top: 1px solid white;  border-color:#000;width:45%">Unidade Escolar</td>
								<td  style="border-top: 1px solid white;  border-color:#000;width:50%">Justificativa</td>
								</tr>	
								';
								
								foreach($dadosJust as $just){	
									if($just['idEscola'] == '0'){
										$just['nome'] ='TODAS ESCOLAS';
									}elseif($just['idEscola'] == '1'){
										$just['nome'] ='OUTRA UNIDADE';
									}
									$html.='<tr>';
									$html.='<td  style="border-top: 1px solid white;  border-color:#000;width:20%">'.$just['data'].'</td>';
									$html.='<td  style="border-top: 1px solid white;  border-color:#000;width:40%">'.$just['nome'].'</td>';
									$html.='<td  style="border-top: 1px solid white;  border-color:#000;width:40%">'.$just['justificativa'].'</td>';
									$html.='</tr>';			
								}
								$html.='</table>
								<table id="" class="display" style="width:100%;font-size:70%">
								<tr>
								<td  style="border-top: 1px solid white;  border-color:#000;width:100"><br></td>
								</tr>	
								</table>
								
								<table id="" class="display" style="width:100%;font-size:70%">
								<tr>
								<td  style="border-top: 1px solid white;  border-color:#000;width:100%"><b>Ocorr??ncias:</b></td>
								</tr>		
								'.$ocor.'
								<tr>
								<td  style="border-top: 1px solid white;  border-color:#000;width:100"><br></td>
								</tr>	
								</table>	
								<table id="" class="display" style="width:100%;font-size:70%">
								<tr>
								<td  style="border-top: 1px solid white;  border-color:#000;width:100"><br><br><br><br><br><br></td>
								</tr>	
								</table>
								<table id="" class="display" style="width:100%;font-size:70%">
								<tr>
								<td  style="border-top: 1px solid white;  border-color:#000;width:30%;text-align:center">Data</td>
								<td  style="width:40%"></td>
								<td  style="border-top: 1px solid white;  border-color:#000;width:30%;text-align:center">Assinatura do Condutor</td>
								<tr>		
								</table>		
		
					';	
			
			
		 $pdf = new Pdf([
            'mode' => 'c',
            'marginTop' => 50,
            'marginBottom' => 20,
            'marginLeft' => 5,
            'marginRight' => 5,
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $html,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:10px} .table table { border-collapse: collapse; } .table table, .table th, .table td { border: 1px solid black;} .table th td { padding-left: 1px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ['
                <table width="100%">
                <tr>
                  <Td align="center">
                  <img src="img/brasaoFull.png">
                  </Td>
                </tr>
              </table>'],
                'SetFooter' => ['Emitido em ' . date('d/m/Y') . '|| {PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
		
		
	}	
    public function actionReportFinanceiroXls()
    {
        $ano = Yii::$app->request->get('ano');
        $mes = Yii::$app->request->get('mes');
        $keys = explode(',', str_replace('[','',str_replace(']', '', Yii::$app->request->get('keys'))));

        $searchModel = new ControleFinanceiroSearch();
        $searchModel->ano = $ano;
        $searchModel->mes = $mes;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);    
        // $sheet = $spreadsheet->createSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('CONTROLE FINANCEIRO');
        // $this->cabecalho($sheet,$i, $data);
        $i = 1;

        $sheet->setCellValue('A'.$i, 'Condutor');
        $sheet->setCellValue('B'.$i, 'NIT');
        $sheet->setCellValue('C'.$i, 'Dias Trabalhados');
        $sheet->setCellValue('D'.$i, 'S??bado Letivo');
        $sheet->setCellValue('E'.$i, 'Dia(s) Excepcional(is) (1)');
        $sheet->setCellValue('F'.$i, 'Viagem/Km (1)');
        $sheet->setCellValue('G'.$i, 'Dia(s) Excepcional(is) (2)');
        $sheet->setCellValue('H'.$i, 'Viagem/Km (2)');
        $sheet->setCellValue('I'.$i, 'Valor Nota');
        $sheet->setCellValue('J'.$i, 'Protocolo TESC');
        $sheet->setCellValue('K'.$i, 'Protocolo GC');
        $sheet->setCellValue('L'.$i, 'Lote');
        $sheet->setCellValue('M'.$i, 'Saldo AF');
        
        foreach ($dataProvider->getModels() as $model) {
            if (!in_array($model->id, $keys))
                continue;

            $i++;        
            $sheet->setCellValue('A'.$i, $model->condutor->nome);
            $sheet->setCellValue('B'.$i, $model->condutor->nit);
            $sheet->setCellValue('C'.$i, $model->diasTrabalhados);
            $sheet->setCellValue('D'.$i, $model->sabadoLetivo);
            $sheet->setCellValue('E'.$i, $model->diasExcepcionais1);
            $sheet->setCellValue('F'.$i, $model->viagemKm1);
            $sheet->setCellValue('G'.$i, $model->diasExcepcionais2);
            $sheet->setCellValue('H'.$i, $model->viagemKm2);
            $sheet->setCellValue('I'.$i, $model->valorNota);

            $date = \DateTime::createFromFormat( 'Y-m-d', $model->protocoloTESC);
            $sheet->setCellValue('J'.$i, $date ? $date->format('d/m/Y') : ' - ');

            $date = \DateTime::createFromFormat( 'Y-m-d', $model->protocoloGC);
            $sheet->setCellValue('K'.$i, $date ? $date->format('d/m/Y') : ' - ');
            $sheet->setCellValue('L'.$i, $model->lote);
            $sheet->setCellValue('M'.$i, $model->saldoAF);
        }

        $base = "arquivos/_exportacoes/";

        $writer = new Xlsx($spreadsheet);
        $filename = $base."Controle_financeiro".date('d-m-Y-H-i-s').".xlsx";
        $writer->save($filename);
        header("Content-Disposition: attachment; filename=".$filename);
        $content = file_get_contents($filename);
        unlink($filename);
        exit($content);
    }

    protected function tdCenter($tamanho, $content, $style = '')
    {
        return '<td width="' . $tamanho . '%" align="center">' . $content . '</td>';
    }
    protected function td($tamanho, $content, $style = '')
    {
        return '<td width="' . $tamanho . '%" >' . $content . '</td>';
    }
}
