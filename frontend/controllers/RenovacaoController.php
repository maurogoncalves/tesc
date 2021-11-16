<?php

namespace frontend\controllers;

use Yii;
use common\models\SolicitacaoTransporte;
use yii\helpers\ArrayHelper;
use common\models\SolicitacaoTransporteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use common\models\TipoDocumento;
use common\models\DocumentoAluno;
use yii\web\UploadedFile;
use common\models\Usuario;
use common\models\Escola;
use common\models\UsuarioGrupo;
use common\models\SolicitacaoStatus;
use common\models\Aluno;
use common\models\PontoAluno;
use common\models\EscolaDiretor;
use common\models\EscolaSecretario;
use common\models\Condutor;
use common\models\Veiculo;
use common\models\Ponto;
use yii\filters\AccessControl;
use common\models\DocumentoSolicitacao;
use common\models\SolicitacaoTransporteEscolas;
use common\models\Configuracao;
use common\models\HistoricoMovimentacaoRota;

/**
 * Lista de status para renovacao
 *
 1 - O benefício foi renovado, não houve alteração de período ou endereço
 2 - O benefício na modalidade frete foi renovado com status atendido, o período foi alterado, mas o condutor/rota atende no período
 3 - O benefício na modalidade frete foi solicitado com status recebido porque o endereço foi alterado
 4 - Não houve renovação, algum problema com o cadastro do aluno e atual solicitação
 5 - O benefício na modalidade passe foi renovado com status concedido, e o endereço e/ou o periodo foram alterados 
 6 - O benefício na modalidade passe foi renovado com status concedido, e o endereço e/ou o periodo foram alterados 
 7 - O benefício na modalidade frete foi solicitado com status recebido, não existe condutor para colocar o aluno
 8 - O benefício na modalidade frete foi solicitado com status recebido, não existe o veículo do condutor para colocar o aluno
 9 - O benefício na modalidade frete foi solicitado com status recebido, não existe rota para colocar o aluno
 10 - O benefício na modalidade frete foi solicitado com status recebido, a capacidade do veículo excedeu
 11 - O benefício não foi renovado, a solicitação foi encerrada.
 12 - O benefício na modalidade frete foi solicitado com status recebido, pois o condutor que já o atendia, não atende mais o período desejado.
 13 - Favor revisar o cadastro do aluno, os números de documentos devem ser únicos.
 
 10 e 12 foram alterados para: "O Benefício na modalidade frete foi renovado com sucesso"
 */
 

class RenovacaoController extends Controller
{
    protected $configuracao;

    public function init()
    {
        parent::init();
        $this->configuracao = Configuracao::setup();
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

	public function actionSalvar(){	

		if($_POST){
			
	

			$idAluno = $_POST['id_aluno'];
			$idSol = $_POST['id_solicitacao'];
			$motivo = $_POST['motivo_renova'];
			$atualizaEnd = $_POST['atualizar_end'];
			$turno = $_POST['turno'];
			$ensino = $_POST['ensino'];
			$serie = $_POST['serie'];
			$turma = $_POST['turma'];
			$entrada = $_POST['entrada'];
			$saida = $_POST['saida'];
			$telefone = $_POST['telefone'];	
			$renovar = $_POST['renovar'];
			
			
			$solicitacao = SolicitacaoTransporte::findOne($idSol);			
            $solicitacao->motivoNaoRenova =$motivo;
            if ($solicitacao->save()){
                $atualizouSol = 1;
            }else{
				$atualizouSol = 0;
			}

			$aluno = Aluno::findOne($idAluno);
			$turnoAntigo = $aluno->turno;
			$aluno->turno =$turno;
			$aluno->ensino =$ensino;
			$aluno->turma =$turma;
			$aluno->serie =$serie;
			$aluno->horarioEntrada =$entrada;
			$aluno->horarioSaida =$saida;
			$aluno->telefoneResidencial =$telefone;
			
			
			//pegar tipo da escola
			$escola = Escola::findOne($aluno->idEscola);			
			if ($aluno->save()){
                $atualizouAl = 1;
            }else{
				$atualizouAl = 0;
			}			
			//verificar se alterou endereço
			//Os dados atualizados já deverão constar na página de RENOVAÇÃO e, após o usuário salvar as novas informações (BOTÃO VERDE)
			//o status do benefício do aluno deverá retornar para “RECEBIDO”, referente ao NOVO ANO LETIVO, ficando os campos do condutor entrada e saída em branco/vazios.
			//criar uma solicitação para o próximo ano letivo, com staus RECEBIDO
		
			if(($atualizouAl == 1)){	
		
				$status = 1;
				//se nada mudou, renovar o beneficio, criando uma nova solicitação para o proximo ano letivo

				//se o periodo mudou, será verificado:
				//se o condutor existe, se não existe - status recebido
				//se o veiculo existe, se não existe - status recebido
				//se a rota existe para o periodo verificar 
				//se na rota já excedeu a capacidade do veiculo, se excedeu status recebido
				//senao atende

					//verificar se existe condutor e rota no novo turno
					//renovar beneficio com status de recebido
					$status = 2;
					$dadosCondutor = Condutor::findOne($solicitacao->idCondutor);
					if(!$solicitacao->idCondutor){
						$status = 7;
					}
					$dadosVeiculo = Veiculo::findOne($dadosCondutor->idVeiculo);					
					if(!$dadosCondutor->idVeiculo){
						$status = 8;
					}					
					
					if($solicitacao->modalidadeBeneficio == 2){
						if(($renovar <> 'N') and ($atualizaEnd <> '1') ){
							if($turno <> $turnoAntigo){
								$status = 6;
								//renovar beneficio com status de deferido	
								$this->gravar($solicitacao,$escola,$idSol,$status);	
							}else{
								$status = 5;
								//renovar beneficio com status de deferido	
								$this->gravar($solicitacao,$escola,$idSol,$status);	
							}	
							
						}	
					}else{
						if(($renovar <> 'N') and ($atualizaEnd <> 1) ){
							$temRota = '0';
							//verifica se o turno escolhido tem rotas para a escola
							if($turno <> $turnoAntigo){
								$sqlVerificaRota ='select count(*) as tem from CondutorRota cr join Ponto p on p.idCondutorRota = cr.id join PontoEscola pe on pe.idPonto = p.id  where cr.idCondutor = '.$solicitacao->idCondutor.' and cr.turno = '.$turno.'  and pe.idEscola = '.$aluno->idEscola.' and ( p.idCondutorRota <> '.$solicitacao->idRotaIda.'  ) and ( p.idCondutorRota <> '.$solicitacao->idRotaVolta.'  )' ;
								$verificaRota = Yii::$app->getDb()->createCommand($sqlVerificaRota)->queryAll();
							}else{
								$sqlVerificaRota ='select count(*) as tem from CondutorRota cr join Ponto p on p.idCondutorRota = cr.id join PontoEscola pe on pe.idPonto = p.id  where cr.idCondutor = '.$solicitacao->idCondutor.' and cr.turno = '.$turno.'  and pe.idEscola = '.$aluno->idEscola.' and ( p.idCondutorRota = '.$solicitacao->idRotaIda.'  ) or ( p.idCondutorRota = '.$solicitacao->idRotaVolta.'  )' ;
								$verificaRota = Yii::$app->getDb()->createCommand($sqlVerificaRota)->queryAll();
							}
							
							//$sqlVerificaRota ='select count(*) as tem from CondutorRota cr join Ponto p on p.idCondutorRota = cr.id join PontoEscola pe on pe.idPonto = p.id  where cr.idCondutor = '.$solicitacao->idCondutor.' and cr.turno = 1  and pe.idEscola = '.$aluno->idEscola ;
							//$verificaRota = Yii::$app->getDb()->createCommand($sqlVerificaRota)->queryAll();
								
							if((empty($verificaRota[0]['tem'])) || ($verificaRota[0]['tem'] == 0)){	
								//criar a solicitação, mas colocar em recebido
								$status = 12;
								PontoAluno::removerTodasRotas($aluno->id);
							}else{
								//selecionar id condutor rota
								//selecionar ponto e verificar capacidade
								//inserir ponto aluno com sentido ida e volta		
								$sqlRotaIdaTotAl ='select c.id,c.turno,(select count(*)+2 from Ponto p join PontoAluno pa on p.id = pa.idPonto  where p.idCondutorRota = c.id and p.tipo = 3) as alunos from CondutorRota c  join Ponto p on p.idCondutorRota = c.id join PontoEscola pe on pe.idPonto = p.id where c.idCondutor = '.$solicitacao->idCondutor.' and c.turno = '.$turno.' and c.rotaAtiva = 1 and c.sentido = 1  and pe.idEscola = '.$aluno->idEscola ;
								$rotaIda = Yii::$app->getDb()->createCommand($sqlRotaIdaTotAl)->queryAll();

								$sqlRotaVoltaTotAl ='select c.id,c.turno,(select count(*)+2 from Ponto p join PontoAluno pa on p.id = pa.idPonto where p.idCondutorRota = c.id and p.tipo = 3) as alunos from CondutorRota c join Ponto p on p.idCondutorRota = c.id join PontoEscola pe on pe.idPonto = p.id where c.idCondutor = '.$solicitacao->idCondutor.' and c.turno = '.$turno.' and c.rotaAtiva = 1 and c.sentido = 2  and pe.idEscola = '.$aluno->idEscola; 
								$rotaVolta = Yii::$app->getDb()->createCommand($sqlRotaVoltaTotAl)->queryAll();
								
								
								if((empty($rotaVolta[0]['id'])) || (empty($rotaIda[0]['id']))){						
									//criar a solicitação, mas colocar em recebido
									$status = 9;
									
									$sqlTodosPontos ='select idPonto from PontoAluno p where p.idAluno  = '.$idAluno; 
									$todosPontos = Yii::$app->getDb()->createCommand($sqlTodosPontos)->queryAll();

									foreach($todosPontos as $pont){									
										if($pont['idPonto']){	
											\Yii::$app->db->createCommand()->delete('PontoAluno', ['idPonto' => $pont['idPonto']])->execute();
											\Yii::$app->db->createCommand()->delete('Ponto', ['id' => $pont['idPonto']])->execute();
										}
									}
										
								}else{		
									
									if($rotaVolta[0]['alunos'] >= $dadosVeiculo->capacidade){
										//regra do transbordo é considerar o total de alunos mais 2 (condutor e auxiliar), 
										//se o total de alunos for maior que a capacidade do veiculo, coloca em recebido
										//criar/renovar a solicitação, a capacidade excedeu , mas colocar em recebido
										$status = 10;
										
										$sqlTodosPontos ='select idPonto from PontoAluno p where p.idAluno  = '.$idAluno; 
										$todosPontos = Yii::$app->getDb()->createCommand($sqlTodosPontos)->queryAll();

										foreach($todosPontos as $pont){									
											if($pont['idPonto']){	
												\Yii::$app->db->createCommand()->delete('PontoAluno', ['idPonto' => $pont['idPonto']])->execute();
												\Yii::$app->db->createCommand()->delete('Ponto', ['id' => $pont['idPonto']])->execute();
											}
										}
										//PontoAluno::removerTodasRotas($aluno->id);
								
									}
									if( $rotaIda[0]['alunos'] >= $dadosVeiculo->capacidade){
										//regra do transbordo é considerar o total de alunos mais 2 (condutor e auxiliar), 
										//se o total de alunos for maior que a capacidade do veiculo, coloca em recebido
										//criar/renovar a solicitação, a capacidade excedeu , mas colocar em recebido
										$status = 10;
										// $sqlTodosPontos ='select idPonto from PontoAluno p where p.idAluno  = '.$idAluno; 
										// $todosPontos = Yii::$app->getDb()->createCommand($sqlTodosPontos)->queryAll();
										
										// foreach($todosPontos as $pont){
											
											// $sqlTodosPontos ='delete from Ponto  where id = '.$pont['idPonto']; 
											// $todosPontos = Yii::$app->getDb()->createCommand($sqlTodosPontos)->queryAll();
										
										// }

										// PontoAluno::removerTodasRotas($aluno->id);
										
										
									}						
									if($status <> 10){
										$sqlTodosPontos ='select idPonto from PontoAluno p where p.idAluno  = '.$idAluno; 
										$todosPontos = Yii::$app->getDb()->createCommand($sqlTodosPontos)->queryAll();
										
										foreach($todosPontos as $pont){									
											if($pont['idPonto']){	
												\Yii::$app->db->createCommand()->delete('PontoAluno', ['idPonto' => $pont['idPonto']])->execute();
												\Yii::$app->db->createCommand()->delete('Ponto', ['id' => $pont['idPonto']])->execute();
											}
										}
										
										//inserir rota ida							
										$modelPontoIda = new Ponto();					
										$modelPontoIda->idCondutorRota = $rotaIda[0]['id'];
										$modelPontoIda->tipo =3;
										$modelPontoIda->lat =$aluno->lat;
										$modelPontoIda->lng =$aluno->lng;
										$modelPontoIda->confirmacaoPassagem =0;
										$modelPontoIda->sentido =1;						
										$modelPontoIda->save();							
										//inserir rota volta
										$modelPontoVolta = new Ponto();					
										$modelPontoVolta->idCondutorRota = $rotaVolta[0]['id'];
										$modelPontoVolta->tipo =3;
										$modelPontoVolta->lat =$aluno->lat;
										$modelPontoVolta->lng =$aluno->lng;
										$modelPontoVolta->confirmacaoPassagem =0;
										$modelPontoVolta->sentido =2;						
										$modelPontoVolta->save();													
										PontoAluno::removerTodasRotas($aluno->id);							
										//inserir aluno rota ida						
										$modelPontoAlunoIda = new PontoAluno();					
										$modelPontoAlunoIda->idPonto = $modelPontoIda->id;
										$modelPontoAlunoIda->idAluno =$aluno->id;						
										$modelPontoAlunoIda->sentido =1;						
										$modelPontoAlunoIda->save();							
										//inserir aluno rota ida
										$modelPontoAlunoVolta = new PontoAluno();					
										$modelPontoAlunoVolta->idPonto = $modelPontoVolta->id;
										$modelPontoAlunoVolta->idAluno =$aluno->id;				
										$modelPontoAlunoVolta->sentido =2;												
										$modelPontoAlunoVolta->save();
										
										$solicitacao->idRotaIda  = $rotaIda[0]['id'];
										$solicitacao->idRotaVolta   = $rotaVolta[0]['id'];
										$temRota = '1';

									}
								}
							}
							//criar/renovar a solicitação, inserir na rota
							$this->gravar($solicitacao,$escola,$idSol,$status);	
						}						
					}
								
				if($atualizaEnd == '1'){
					if($renovar == 'N'){
						$status = 11;
						$this->gravar($solicitacao,$escola,$idSol,$status);	
					}else{		
						$status = 3;
						if($solicitacao->modalidadeBeneficio == 2){
							if($escola->unidade == 1){
								//se escola municipal salvar com status deferido pelo diretor	
								$status = 6;			
							}else{
								//se escola municipal salvar com status deferido pelo dre
								$status = 6;		
							}						
							//renovar beneficio com status de deferido	
							$this->gravar($solicitacao,$escola,$idSol,$status);	
						}
						if($status == 3){
							//renovar beneficio com status de recebido	
							$this->gravar($solicitacao,$escola,$idSol,$status);		
						}
					}	
				}				
				
				
				if($renovar == 'N'){
					$status = 11;
					$this->gravar($solicitacao,$escola,$idSol,$status);	
				}else{
					//modalidade 1 = frete
					if(($status == 1 ) and ($solicitacao->modalidadeBeneficio == 1)){					
						$this->gravar($solicitacao,$escola,$idSol,$status);				
					}
					//modalidade 2 = passe escolar				
					if(($status == 1 ) and ($solicitacao->modalidadeBeneficio == 2)){
						$this->gravar($solicitacao,$escola,$idSol,$status);	
					}
				}				
				
			}else{
				$status=13;				
			}
		}else{
			$status=4;
		}
        echo json_encode($status);
    }
	
	private function gravar($solicitacao,$escola,$idSol,$status){
		$model = new SolicitacaoTransporte();
		$model->idAluno =$solicitacao->idAluno;
		$model->idEscola =$solicitacao->idEscola;
		$model->anoVigente =$solicitacao->anoVigente+1; //alterar quando mexer na configuracao
		$model->idDiretor =$solicitacao->idDiretor; 					
		$model->turno=$solicitacao->turno;
		$model->dataStatusDiretor=$solicitacao->dataStatusDiretor;
		$model->dataStatusDre=$solicitacao->dataStatusDre;
		$model->data=date('Y-m-d');
		$model->ultimaMovimentacao=date('Y-m-d');
		$model->justificativaBarreiraFisica=$solicitacao->justificativaBarreiraFisica;
		$model->motivoBarreiraFisica=$solicitacao->motivoBarreiraFisica;
		$model->modalidadeBeneficio=$solicitacao->modalidadeBeneficio;
		$model->tipoFrete=$solicitacao->tipoFrete;
		$model->cartaoPasseEscolar=$solicitacao->cartaoPasseEscolar;
		$model->cartaoValeTransporte=$solicitacao->cartaoValeTransporte;
		$model->barreiraFisica=$solicitacao->barreiraFisica;
		$model->distanciaEscola=$solicitacao->distanciaEscola;
		$model->tipoSolicitacao=$solicitacao->tipoSolicitacao;
		$model->idCondutor=$solicitacao->idCondutor;
		$model->checkForm=$solicitacao->checkForm;
		$model->checkInex=$solicitacao->checkInex;
		$model->checkEnd=$solicitacao->checkEnd;
		$model->checkSed=$solicitacao->checkSed;
		$model->checkMemorando=$solicitacao->checkMemorando;
		$model->checkVizinho=$solicitacao->checkVizinho;		
		$model->checkLaudoMedico=$solicitacao->checkLaudoMedico;
		$model->checkSolicitacaoEspecial=$solicitacao->checkSolicitacaoEspecial;
		
		$novaRotaIda = $model->idRotaIda = $solicitacao->idRotaIda;
		$novaRotaVolta =  $model->idRotaVolta =$solicitacao->idRotaVolta;
		
		
		$model->motivoNaoRenova=$solicitacao->motivoNaoRenova;	
		
		
		if($solicitacao->modalidadeBeneficio == 1){
			if($status == 3){
				//se modalidade de beneficio for frete, e houve alteração de endereço salvar com  status é recebido
				$model->novaSolicitacao=SolicitacaoTransporte::NOVA_SOLICITACAO;
				$model->status=SolicitacaoTransporte::STATUS_DEFERIDO;	
				$model->save();		
				
				$modelStatus = new SolicitacaoStatus();
				$modelStatus->idUsuario = \Yii::$app->User->identity->id;
				$modelStatus->dataCadastro = date('Y-m-d');
				$modelStatus->status = SolicitacaoTransporte::STATUS_DEFERIDO;
				$modelStatus->idSolicitacaoTransporte = $model->id;
				$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
				$modelStatus->justificativa = 'BENEFÍCIO RENOVADO, MAS AGUARDANDO NOVA ROTA PARA O NOVO ENDEREÇO.';
				$modelStatus->save();		
			
			}elseif($status == 2){		
			
				//se modalidade de beneficio for frete, e não houve alteração periodo
				$model->novaSolicitacao=SolicitacaoTransporte::RENOVACAO;
				$model->status=SolicitacaoTransporte::STATUS_ATENDIDO;
				$model->save();	
				
				$modelStatus = new SolicitacaoStatus();
				$modelStatus->idUsuario = \Yii::$app->User->identity->id;
				$modelStatus->dataCadastro = date('Y-m-d');
				$modelStatus->status = SolicitacaoTransporte::STATUS_ENCERRADA;
				$modelStatus->idSolicitacaoTransporte = $idSol;
				$modelStatus->justificativa = 'ENCERRADO PELO SISTEMA. NOVA SOLICITAÇÃO VIGENTE #' . $model->id;
				$modelStatus->save();	
				
			}elseif($status == 9){		
				//se modalidade de beneficio for frete, e não existe rotas, status recebido
				$model->status=SolicitacaoTransporte::STATUS_DEFERIDO;
				$model->novaSolicitacao=SolicitacaoTransporte::RENOVACAO;
				$model->save();		
				
				$modelStatus = new SolicitacaoStatus();
				$modelStatus->idUsuario = \Yii::$app->User->identity->id;
				$modelStatus->dataCadastro = date('Y-m-d');
				$modelStatus->status = SolicitacaoTransporte::STATUS_DEFERIDO;
				$modelStatus->idSolicitacaoTransporte = $model->id;
				$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
				$modelStatus->justificativa = 'BENEFÍCIO SOLICITADO COM STATUS RECEBIDO, MAS AGUARDANDO NOVA ROTA, O CONDUTOR NÃO ATENDE O PERÍODO DESEJADO.';
				$modelStatus->save();	
				
				
			}elseif($status == 10){		
				//se modalidade de beneficio for frete, e excedeu a capacidade do veiculo, status recebido
				$model->status=SolicitacaoTransporte::STATUS_DEFERIDO;
				$model->novaSolicitacao=SolicitacaoTransporte::NOVA_SOLICITACAO;
				$model->save();		
				
				$modelStatus = new SolicitacaoStatus();
				$modelStatus->idUsuario = \Yii::$app->User->identity->id;
				$modelStatus->dataCadastro = date('Y-m-d');
				$modelStatus->status = SolicitacaoTransporte::STATUS_DEFERIDO;
				$modelStatus->idSolicitacaoTransporte = $model->id;
				$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
				$modelStatus->justificativa = 'BENEFÍCIO SOLICITADO COM STATUS RECEBIDO, MAS AGUARDANDO NOVA ROTA, A CAPACIDADE DO VEÍCULO EXCEDEU.';
				$modelStatus->save();	
			
			}elseif($status == 12){		
				//BENEFÍCIO SOLICITADO COM STATUS RECEBIDO, MAS AGUARDANDO NOVA ROTA, O CONDUTOR NÃO ATENDE O PERÍODO DESEJADO
				$model->status=SolicitacaoTransporte::STATUS_DEFERIDO;
				$model->novaSolicitacao=SolicitacaoTransporte::NOVA_SOLICITACAO;
				$model->save();		
				
				$modelStatus = new SolicitacaoStatus();
				$modelStatus->idUsuario = \Yii::$app->User->identity->id;
				$modelStatus->dataCadastro = date('Y-m-d');
				$modelStatus->status = SolicitacaoTransporte::STATUS_DEFERIDO;
				$modelStatus->idSolicitacaoTransporte = $model->id;
				$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
				$modelStatus->justificativa = 'BENEFÍCIO SOLICITADO COM STATUS RECEBIDO, MAS AGUARDANDO NOVA ROTA, O CONDUTOR NÃO ATENDE O PERÍODO DESEJADO.';
				$modelStatus->save();	
			}elseif($status == 1){	
				//modalidade 1 = frete, sem alteração de endereço/periodo status atendido
				$model->novaSolicitacao=SolicitacaoTransporte::NOVA_SOLICITACAO;
				$model->status=SolicitacaoTransporte::STATUS_ATENDIDO;	
				$model->novaSolicitacao=SolicitacaoTransporte::RENOVACAO;
				$model->save();		
			}
				
		}elseif(($status == 5 ) && ($solicitacao->modalidadeBeneficio == 2)){
			//status 5 e status 6		
			//se escola municipal salvar com status deferido pelo diretor	
			//se escola municipal salvar com status deferido pelo dre
			$model->modalidadeBeneficio=2;
			$model->novaSolicitacao=SolicitacaoTransporte::RENOVACAO;
			$model->status=SolicitacaoTransporte::STATUS_CONCEDIDO;	
			$model->save();		
		}
		
		$solicitacao = SolicitacaoTransporte::findOne($idSol);
        $solicitacao->status =SolicitacaoTransporte::STATUS_ENCERRADA;    
		$solicitacao->save();
		
		
		if(($status == 1 ) && ($solicitacao->modalidadeBeneficio == 1)){
			$sqlCondutor ='select c.nome from Condutor c where c.id = '.$solicitacao->idCondutor ;
			$dadosCondutorIda = Yii::$app->getDb()->createCommand($sqlCondutor)->queryAll();
						
			$modelStatus = new SolicitacaoStatus();
			$modelStatus->idUsuario = \Yii::$app->User->identity->id;
			$modelStatus->dataCadastro = date('Y-m-d');
			$modelStatus->status = SolicitacaoTransporte::STATUS_ATENDIDO;
			$modelStatus->idSolicitacaoTransporte = $model->id;
			$modelStatus->idCondutorRota = $solicitacao->idRotaIda;
			$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
			$modelStatus->mostrar = 1;							
			$modelStatus->justificativa = 'ATRIBUÍDO EM ROTA. ROTA #'.$novaRotaIda.' DO CONDUTOR '.$dadosCondutorIda[0]->nome;
			$modelStatus->save();
								
			$modelStatus = new SolicitacaoStatus();
			$modelStatus->idUsuario = \Yii::$app->User->identity->id;
			$modelStatus->dataCadastro = date('Y-m-d');
			$modelStatus->status = SolicitacaoTransporte::STATUS_ATENDIDO;
			$modelStatus->idSolicitacaoTransporte = $model->id;
			$modelStatus->idCondutorRota = $solicitacao->idRotaVolta;
			$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
			$modelStatus->mostrar = 1;							
			$modelStatus->justificativa = 'ATRIBUÍDO EM ROTA. ROTA #'.$novaRotaVolta.' DO CONDUTOR '.$dadosCondutorIda[0]->nome;
			$modelStatus->save();
		}
		
		if(($status == 2 ) && ($solicitacao->modalidadeBeneficio == 1)){
			$sqlCondutor ='select c.nome from Condutor c where c.id = '.$solicitacao->idCondutor ;
			$dadosCondutorIda = Yii::$app->getDb()->createCommand($sqlCondutor)->queryAll();
						
			$modelStatus = new SolicitacaoStatus();
			$modelStatus->idUsuario = \Yii::$app->User->identity->id;
			$modelStatus->dataCadastro = date('Y-m-d');
			$modelStatus->status = SolicitacaoTransporte::STATUS_ATENDIDO;
			$modelStatus->idSolicitacaoTransporte = $model->id;
			$modelStatus->idCondutorRota = $solicitacao->idRotaIda;
			$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
			$modelStatus->mostrar = 1;							
			$modelStatus->justificativa = 'ATRIBUÍDO EM ROTA. ROTA #'.$novaRotaIda.' DO CONDUTOR '.$dadosCondutorIda[0]->nome;
			$modelStatus->save();
								
			$modelStatus = new SolicitacaoStatus();
			$modelStatus->idUsuario = \Yii::$app->User->identity->id;
			$modelStatus->dataCadastro = date('Y-m-d');
			$modelStatus->status = SolicitacaoTransporte::STATUS_ATENDIDO;
			$modelStatus->idSolicitacaoTransporte = $model->id;
			$modelStatus->idCondutorRota = $solicitacao->idRotaVolta;
			$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
			$modelStatus->mostrar = 1;							
			$modelStatus->justificativa = 'ATRIBUÍDO EM ROTA. ROTA #'.$novaRotaVolta.' DO CONDUTOR '.$dadosCondutorIda[0]->nome;
			$modelStatus->save();
		}
		
		if($solicitacao->modalidadeBeneficio == 2){
			
			if($status == 11){
				$modelStatus = new SolicitacaoStatus();
				$modelStatus->idUsuario = \Yii::$app->User->identity->id;
				$modelStatus->dataCadastro = date('Y-m-d');
				$modelStatus->status = SolicitacaoTransporte::STATUS_ENCERRADA;
				$modelStatus->idSolicitacaoTransporte = $idSol;
				$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
				$modelStatus->mostrar = 1;							
				$modelStatus->justificativa = 'BENEFÍCIO ENCERRADO VIA SISTEMA';
				$modelStatus->save();			
			}else{
				//modalidade 1 = frete, sem alteração de endereço/periodo status atendido
				$model->modalidadeBeneficio=2;
				$model->novaSolicitacao=SolicitacaoTransporte::RENOVACAO;
				$model->status=SolicitacaoTransporte::STATUS_CONCEDIDO;	
				$model->save();	
					
				$modelStatus = new SolicitacaoStatus();
				$modelStatus->idUsuario = \Yii::$app->User->identity->id;
				$modelStatus->dataCadastro = date('Y-m-d');
				$modelStatus->status = SolicitacaoTransporte::STATUS_CONCEDIDO;
				$modelStatus->idSolicitacaoTransporte = $model->id;
				$modelStatus->tipo = SolicitacaoStatus::TIPO_INSERIDO;
				$modelStatus->mostrar = 1;							
				$modelStatus->justificativa = 'BENEFÍCIO RENOVADO MODALIDADE PASSE';
				$modelStatus->save();
			}				
			
		}				
		
		
		return true;
		
			
	}
    
    /**
     * Lists all SolicitacaoTransporte models.
     * @return mixed
     */
    public function actionIndex($ra=false,$idAluno=false)
    {

		$configuracao = Configuracao::findOne(1);

		if (Usuario::permissao(Usuario::PERFIL_DIRETOR)) {
            $ids = EscolaDiretor::listaEscolas();
            $ids[] = 999999;
        }

        if (Usuario::permissao(Usuario::PERFIL_SECRETARIO)) {
            $ids = EscolaSecretario::listaEscolas();
            $ids[] = 999999;
        }
		
		if(!empty($ids)){
			$sqlAtendido = "select st.id,a.id as idAluno, a.nome,a.RA,a.RAdigito,a.turno,a.ensino,a.serie,a.turma,a.horarioEntrada,a.horarioSaida,a.telefoneResidencial,a.endereco, a.complementoResidencia,a.bairro,st.idCondutor,st.idRotaIda,st.idRotaVolta,a.idEscola,
				(select GROUP_CONCAT(nome SEPARATOR '<br>') from AlunoNecessidadesEspeciais al  join NecessidadesEspeciais n on al.idNecessidadesEspeciais = n.id where al.idAluno = a.id) as necessidades,st.anoVigente,ci.nome as condutor_ida,cv.nome as condutor_volta,st.modalidadeBeneficio,a.numeroResidencia,
				(select count(*) from SolicitacaoTransporte sta where sta.idAluno = a.id and sta.`status` <> 6 and sta.id > st.id) as tem_outra_solicitacao
				from SolicitacaoTransporte st 
				join Aluno a on st.idAluno = a.id				
				join CondutorRota cri on cri.id = st.idRotaIda
				join CondutorRota crv on crv.id = st.idRotaVolta
				join Condutor ci on ci.id = cri.idCondutor
				join Condutor cv on cv.id = crv.idCondutor
				where st.`status` = ".SolicitacaoTransporte::STATUS_ATENDIDO." and a.serie not in (16,17,18,19,20,21,22,23,24,25,26,27) and st.anoVigente = $configuracao->anoVigente and a.idEscola in  (".implode (',', $ids).")";
				//  as series entre 16 e 27 são ensino EJA e são renovados a cada 6 meses
				$sqlDeferidoDre = "select st.id,a.id as idAluno, a.nome,a.RA,a.RAdigito,a.turno,a.ensino,a.serie,a.turma,a.horarioEntrada,a.horarioSaida,a.telefoneResidencial,a.endereco, a.complementoResidencia,a.bairro,st.idCondutor,st.idRotaIda,st.idRotaVolta,a.idEscola,
				(select GROUP_CONCAT(nome SEPARATOR '<br>') from AlunoNecessidadesEspeciais al  join NecessidadesEspeciais n on al.idNecessidadesEspeciais = n.id where al.idAluno = a.id) as necessidades,st.anoVigente,ci.nome as condutor_ida,cv.nome as condutor_volta,st.modalidadeBeneficio,a.numeroResidencia,
				(select count(*) from SolicitacaoTransporte sta where sta.idAluno = a.id and sta.`status` <> 6 and sta.id > st.id) as tem_outra_solicitacao
				from SolicitacaoTransporte st 
				join Aluno a on st.idAluno = a.id				
				left join CondutorRota cri on cri.id = st.idRotaIda
				left join CondutorRota crv on crv.id = st.idRotaVolta
				left join Condutor ci on ci.id = cri.idCondutor
				left join Condutor cv on cv.id = crv.idCondutor
				left join Escola e on e.id = a.idEscola
				where st.`status` = ".SolicitacaoTransporte::STATUS_CONCEDIDO." and a.serie not in (16,17,18,19,20,21,22,23,24,25,26,27) and st.anoVigente = $configuracao->anoVigente  and e.unidade in ('1','2') and st.modalidadeBeneficio = 2 and a.idEscola in  (".implode (',', $ids).")";
				//  as series entre 16 e 27 são ensino EJA e são renovados a cada 6 meses
				$sql = $sqlAtendido.' union '.$sqlDeferidoDre;
        				
		}else{
			$sqlAtendido = "select st.id,a.id as idAluno, a.nome,a.RA,a.RAdigito,a.turno,a.ensino,a.serie,a.turma,a.horarioEntrada,a.horarioSaida,a.telefoneResidencial,a.endereco, a.complementoResidencia,a.bairro,st.idCondutor,st.idRotaIda,st.idRotaVolta,a.idEscola,
				(select GROUP_CONCAT(nome SEPARATOR '<br>') from AlunoNecessidadesEspeciais al  join NecessidadesEspeciais n on al.idNecessidadesEspeciais = n.id where al.idAluno = a.id) as necessidades,st.anoVigente,ci.nome as condutor_ida,cv.nome as condutor_volta,st.modalidadeBeneficio,a.numeroResidencia,
				(select count(*) from SolicitacaoTransporte sta where sta.idAluno = a.id and sta.`status` <> 6 and sta.id > st.id) as tem_outra_solicitacao
				from SolicitacaoTransporte st 
				join Aluno a on st.idAluno = a.id				
				join CondutorRota cri on cri.id = st.idRotaIda
				join CondutorRota crv on crv.id = st.idRotaVolta
				join Condutor ci on ci.id = cri.idCondutor
				join Condutor cv on cv.id = crv.idCondutor
				where st.`status` = ".SolicitacaoTransporte::STATUS_ATENDIDO." and a.serie not in (16,17,18,19,20,21,22,23,24,25,26,27) and st.anoVigente = $configuracao->anoVigente" ;
				//  as series entre 16 e 27 são ensino EJA e são renovados a cada 6 meses
				
				$sqlDeferidoDre = "select st.id,a.id as idAluno, a.nome,a.RA,a.RAdigito,a.turno,a.ensino,a.serie,a.turma,a.horarioEntrada,a.horarioSaida,a.telefoneResidencial,a.endereco, a.complementoResidencia,a.bairro,st.idCondutor,st.idRotaIda,st.idRotaVolta,a.idEscola,
				(select GROUP_CONCAT(nome SEPARATOR '<br>') from AlunoNecessidadesEspeciais al  join NecessidadesEspeciais n on al.idNecessidadesEspeciais = n.id where al.idAluno = a.id) as necessidades,st.anoVigente,ci.nome as condutor_ida,cv.nome as condutor_volta,st.modalidadeBeneficio,a.numeroResidencia,
				(select count(*) from SolicitacaoTransporte sta where sta.idAluno = a.id and sta.`status` <> 6 and sta.id > st.id) as tem_outra_solicitacao
				from SolicitacaoTransporte st 
				join Aluno a on st.idAluno = a.id				
				left join CondutorRota cri on cri.id = st.idRotaIda
				left join CondutorRota crv on crv.id = st.idRotaVolta
				left join Condutor ci on ci.id = cri.idCondutor
				left join Condutor cv on cv.id = crv.idCondutor
				left join Escola e on e.id = a.idEscola
				where st.`status` = ".SolicitacaoTransporte::STATUS_CONCEDIDO." and a.serie not in (16,17,18,19,20,21,22,23,24,25,26,27) and st.anoVigente = $configuracao->anoVigente  and e.unidade in ('1','2') and st.modalidadeBeneficio = 2 ";
				//  as series entre 16 e 27 são ensino EJA e são renovados a cada 6 meses
				$sql = $sqlAtendido.' union '.$sqlDeferidoDre;
		}
		
		$dadosAlunos = Yii::$app->getDb()->createCommand($sql)->queryAll();
		
		
        return $this->render('index', [
			'alunos' => $dadosAlunos,
			'ra' => $ra,
			'idAluno' => $idAluno,
        ]);
    }

  
   
}
