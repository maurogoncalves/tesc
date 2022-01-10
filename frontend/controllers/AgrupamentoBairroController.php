<?php

namespace frontend\controllers;

error_reporting(0);
ini_set('display_errors', 0);

use Yii;
use common\models\AgrupamentoBairro;
use common\models\AgrupamentoBairroSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Bairro;
use common\models\Escola;
use common\models\Aluno;
use common\models\Configuracao;

use kartik\mpdf\Pdf;
use common\models\SolicitacaoTransporte;
use yii\helpers\Url;;

/**
 * AgrupamentoBairroController implements the CRUD actions for AgrupamentoBairro model.
 */
class AgrupamentoBairroController extends Controller
{
    protected $configuracao;
    public $bairrosUrbanos;
    public $bairrosRurais;
    public $tabela1;
    public $tabela2;
    public $tabela3;
    public $tabela4;
    public $tabela5;
    public $tabela6;
    public $tabela7;

    public $total;

    public function init()
    {
        parent::init();
        
        $this->bairrosUrbanos = AgrupamentoBairro::zonaUrbana();
        $this->bairrosRurais = AgrupamentoBairro::zonaRural();
        $this->tabela1 = [
            'Rede Municipal' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
        ];
        $this->tabela2 = [
            'Rede Municipal - Educação Infantil' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Municipal - Ensino Fundamental' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual - Ensino Fundamental' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual - Ensino Médio' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica - Educação Infantil' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica - Ensino Fundamental' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
        ];


        $this->tabela3 = [
            'Rede Municipal - Frete' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Municipal - Passe Escolar' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual - Frete' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual - Passe Escolar' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica - Frete' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica - Passe Escolar' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
        ];

        $this->tabela4 = [
            'Rede Municipal' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
        ];
        $this->tabela5 = [
            'Rede Municipal' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
        ];

        $this->tabela6 = [
            'Frete' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Passe Escolar' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
        ];

        $this->tabela7 = [
            'Rede Municipal - Educação Infantil' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Municipal - Ensino Fundamental' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual - Ensino Fundamental' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Estadual - Ensino Médio' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica - Educação Infantil' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'Rede Filantrópica - Ensino Fundamental' => ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
        ];

        $this->total = [
            'tabela1' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'tabela2' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'tabela3' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'tabela4' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'tabela5' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'tabela6' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
            'tabela7' =>  ['Rural' => 0, 'Urbana' => 0, 'Total' => 0],
        ];
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AgrupamentoBairro models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new AgrupamentoBairroSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AgrupamentoBairro model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AgrupamentoBairro model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    private function bairrosDisponiveis()
    {
        $bairrosIndisponiveis  = [];
        foreach (AgrupamentoBairro::find()->all() as $bairro) {
            $bairrosIndisponiveis[] = $bairro->idBairro;
        }
        return $bairrosDisponiveis = Bairro::bairrosDisponiveis($bairrosIndisponiveis);
    }
    public function actionBairros()
    {
        $bairros = Bairro::bairrosDisponiveis([]);

        foreach ($bairros as $bairro) {
            print "'" . $bairro->BAIRRO . "',";
        }

        return '';
    }
    private function salvarBairros($post, $agrupamento)
    {

        ini_set('max_execution_time', 300); //300 seconds = 5 minutes

        if (!empty($post['AgrupamentoBairro']['bairrosDisponiveis'])) {
            foreach ($post['AgrupamentoBairro']['bairrosDisponiveis'] as $key => $value) {
                $bairro = Bairro::findOne($value);
                $modelGrupo = new AgrupamentoBairro();
                $modelGrupo->idBairro = $bairro->ID_BAIRRO;
                $modelGrupo->nome =  $bairro->BAIRRO;
                $modelGrupo->agrupamento = $agrupamento;
                $modelGrupo->bairrosDisponiveis = 1;
                // $modelGrupo->idUsuario = $model->id;
                // $modelGrupo->idGrupo = $value;
                if (!$modelGrupo->save()) {
                    // print_r($modelGrupo->getErrors());
                    // \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar bairros');
                }
            }
        }
    }
    protected function td(&$table, $tamanho, $content, $style = '')
    {
        return $table .= '<td  width="' . $tamanho . '%"  style="' . $style . '; padding:5px;">' . $content . '</td>';
    }
    protected function tdBorder(&$table, $tamanho, $content, $style = '')
    {
        return $this->td($table, $tamanho, $content, 'border: 0.7px solid #000;' . $style);
    }
    protected function setHeader($titulo = '-')
    {
        $c = '';
        $c .= '<tr style="background:#0070C0;">';
        $c .= '<th style="border: 0.7px solid #000;color:white; padding:10px">' . $titulo . '</th>';
        $c .= '<th style="border: 0.7px solid #000;color:white; padding:10px">Alunos atendidos - Zona Urbana</th>';
        $c .= '<th style="border: 0.7px solid #000;color:white; padding:10px">Alunos atendidos - Zona Rural</th>';
		$c .= '<th style="border: 0.7px solid #000;color:white; padding:10px">Alunos atendidos - Sem Zona</th>';
        $c .= '<th style="border: 0.7px solid #000;color:white; padding:10px">Total de Alunos Atendidos</th>';

        $c .= '</tr>';
        return $c;
    }
	    

    public function gerarSolicitacoes()
    {
        $this->configuracao = Configuracao::setup();
        // $solicitacoesFrete = [];
        $solicitacoesFrete = SolicitacaoTransporte::find()
		    ->andWhere(['<>', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
            ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_ATENDIDO])
            ->andWhere(['=', 'SolicitacaoTransporte.anoVigente', $this->configuracao->anoVigente])
			->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
            ->innerJoin('Aluno', 'Aluno.id=SolicitacaoTransporte.idAluno')
			->join('LEFT JOIN `CondutorRota` AS `RotaEntrada` ON', '`RotaEntrada`.`id` = `SolicitacaoTransporte`.`idRotaIda`')
            ->join('LEFT JOIN `CondutorRota` AS `RotaSaida` ON', '`RotaSaida`.`id` = `SolicitacaoTransporte`.`idRotaVolta`')
            ->join('LEFT JOIN `Condutor` AS `CondutorEntrada` ON', '`CondutorEntrada`.`id` = `RotaEntrada`.`idCondutor`')
            ->join('LEFT JOIN `Condutor` AS `CondutorSaida` ON', '`CondutorSaida`.`id` = `RotaSaida`.`idCondutor`')
            ->join('LEFT JOIN `AlunoNecessidadesEspeciais` ON', '`AlunoNecessidadesEspeciais`.`idAluno` = `Aluno`.`id`')
			//->join('LEFT JOIN `SolicitacaoStatus` ON', '`SolicitacaoStatus`.`idSolicitacaoTransporte` = `SolicitacaoTransporte`.`id`')			
            ->all();


				
        $solicitacoesPasse = SolicitacaoTransporte::find()
			->andWhere(['<>', 'SolicitacaoTransporte.tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
            ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_CONCEDIDO])			
            ->andWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE])
            ->andWhere(['=', 'SolicitacaoTransporte.anoVigente', $this->configuracao->anoVigente])
			->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
            ->innerJoin('Aluno', 'Aluno.id=SolicitacaoTransporte.idAluno')
            //->join('LEFT JOIN `AlunoNecessidadesEspeciais` ON', '`AlunoNecessidadesEspeciais`.`idAluno` = `Aluno`.`id`')
            //->join('LEFT JOIN `SolicitacaoStatus` ON', '`SolicitacaoStatus`.`idSolicitacaoTransporte` = `SolicitacaoTransporte`.`id`')
            ->all();
			
			
			 // $file="Protesto.xls";
		 
		 
		 // $test="<table border=1>
		// <tr>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// <td></td>
		// </tr>
		// ";
		
		 // foreach ($solicitacoesPasse as $model) {
			// $test .= "<tr>";
			// $test .= "<td>".utf8_decode($model->escola->nomeCompleto)."</td>";
			// $test .= "<td>".utf8_decode($model->aluno->nome)."</td>";
			// $test .= "<td>".utf8_decode($model->aluno->RA.'-'.$model->aluno->RAdigito)."</td>";
			// $test .= "<td>".utf8_decode($model->aluno->turma ? Aluno::ARRAY_SERIES[$model->aluno->serie].'/'.Aluno::ARRAY_TURMA[$model->aluno->turma] : '-')."</td>";
			// $test .= "<td>".utf8_decode($model->distanciaEscola . ' KM')."</td>";
			// $test .= "<td>".utf8_decode($model->barreiraFisica == 1 ? 'SIM' : 'NÃO')."</td>";
            // $test .= "<td>".utf8_decode($model->aluno->tipoLogradouro.' '.$model->aluno->endereco.' Nº '.$model->aluno->numeroResidencia)."</td>";
			// $test .= "<td>".utf8_decode($model->aluno->bairro)."</td>";
            
            // $modalidade = '';
            // if($model->aluno->temPasseEscolar()) {
                // $modalidade .= 'Passe Escolar';
            // }
            // if($model->aluno->temPasseEscolar() && $model->aluno->temValeTransporte()) {
                // $modalidade .= ' e ';
            // }
            // if($model->aluno->temValeTransporte()) {
                // $modalidade .= 'Vale Transporte';
            // }
            // $test .= "<td>".utf8_decode($modalidade)."</td>";
			// $test .= "<td>".utf8_decode($model->cartaoPasseEscolar)."</td>";
			// $test .= "<td>".utf8_decode($model->cartaoValeTransporte)."</td>";            
            // $data = $model->recebimento->dataCadastro;
			// $test .= "<td>".utf8_decode($data)."</td>";	
			// $test .= "<td>".utf8_decode($model->escola->unidade)."</td>";			
			// $test .= "</tr>";
	     // }		 
            
        // $test .= '</table>';
		// header("Content-type: application/vnd.ms-excel");
		// header("Content-Disposition: attachment; filename=$file");
		// echo $test;	
		// exit;
		
        $solicitacoes = array_merge($solicitacoesFrete, $solicitacoesPasse);

        return $solicitacoes;
    }
    public function actionTratamento()
    {
        print '<h1> Alunos com bairros divergentes </h1>';
        $solicitacoes = $this->gerarSolicitacoes();
        print '<table>';


        foreach ($solicitacoes as $solicitacao) {

            $zona = '';
            if (in_array($solicitacao->aluno->bairro, $this->bairrosUrbanos)) {
                $zona = 'Urbana';
            } else if (in_array($solicitacao->aluno->bairro, $this->bairrosRurais)) {
                $zona = 'Rural';
            }


            if (!$zona) {
                print '<tr>';
                print '<td>' . $solicitacao->idAluno . '</td>';
                print '<td>' . $solicitacao->aluno->nome . '</td>';
                print '<td>' . $solicitacao->id . '</td>';
                print '<td>' . $solicitacao->escola->nomeCompleto . '</td>';
                print '<td>' . $solicitacao->aluno->RA . '-' . $solicitacao->aluno->RAdigito . '</td>';
                print '<td>' . $solicitacao->aluno->cep . '</td>';
                print '<td>' . $solicitacao->aluno->cidade . '</td>';
                print '<td>' . $solicitacao->aluno->tipoLogradouro . '</td>';
                print '<td>' . $solicitacao->aluno->endereco . '</td>';
                print '<td>' . $solicitacao->aluno->bairro . '</td>';
                print '<td>https://escolarsjc.ipplan.org.br' . Url::toRoute(['aluno/view', 'id' =>  $solicitacao->aluno->id]) . '</td>';



                print '</tr>';
            }
        }
        print '</table>';
        print '<h1>Alunos sem tipo de ensino</h1>';
        foreach ($solicitacoes as $solicitacao) {
            if (!$solicitacao->aluno->ensino) {
                print 'Cód Aluno: ' . $solicitacao->idAluno . '<br>';
                print 'Cód Escola: ' . $solicitacao->idEscola . '<br>';
                print 'Cód Solicitação: ' . $solicitacao->id . '<br>';
                print 'Nome: ' . $solicitacao->aluno->nome . '<br>';
                print 'Escola: ' . $solicitacao->escola->nomeCompleto . '<br>';
                print 'RA: ' . $solicitacao->aluno->RA . '-' . $solicitacao->aluno->RAdigito . '<br>';

                print '<a target="_new" href="' . Url::toRoute(['aluno/update', 'id' =>  $solicitacao->aluno->id]) . '">Editar</a>';
                print '<hr>';
            }
        }
    }
    protected function somarZona($key, $solicitacao)
    {
		
        $zona = '';
        if (in_array($solicitacao->aluno->bairro, $this->bairrosUrbanos)) {
            $zona = 'Urbana';
			
        } else if (in_array($solicitacao->aluno->bairro, $this->bairrosRurais)) {
            $zona = 'Rural';
        }
        if ($zona == '') {
			$zona = '-';
            /*
            print 'Aluno onde o bairro não está cadastrado no agrupamento';
            // Pega alunos com problemas de bairro
            // print_r($solicitacao);
            print $solicitacao->aluno->nome;
            print '<br/>';
            print 'Frete:' . $solicitacao->id;
            print '<br/>';
            print 'Bairro: ' . @$solicitacao->aluno->bairro;
            print '<br/>';
            print '<a target="_new" href="' . Url::toRoute(['aluno/update', 'id' =>  $solicitacao->aluno->id]) . '">Editar</a>';
            print '<br/><br/>';
            return true;
            */
        }
        // Tabela 1
        $this->tabela1[$key][$zona]++;
        $this->total['tabela1'][$zona]++;
        $this->tabela1[$key]['Total']++;
        $this->total['tabela1']['Total']++;
        //

        //Tabela 2
        // print 'Escolas sem atendimentoÇbr>';
        $atendimentos = $solicitacao->escola->atendimento;
        if (!$atendimentos) {

            // Pega escolas sem atendimento
            print $solicitacao->escola->id;
            print '<hr>';
        }
		
        switch ($solicitacao->aluno->ensino) {

            case Escola::ENSINO_INFANTIL:
                // Tabela 2
                $this->tabela2[$key . ' - Educação Infantil'][$zona]++;
                $this->total['tabela2'][$zona]++;
                $this->tabela2[$key . ' - Educação Infantil']['Total']++;
                $this->total['tabela2']['Total']++;

                //              //$solicitacao->modalidadeBeneficio == Aluno::MODALIDADE_PASSE && Tabela 7
                if ($solicitacao->aluno->necessidades) {
                    $this->tabela7[$key . ' - Educação Infantil'][$zona]++;
                    $this->tabela7[$key . ' - Educação Infantil']['Total']++;
                    $this->total['tabela7'][$zona]++;
                    $this->total['tabela7']['Total']++;
                }
                //
                break;
            case Escola::ENSINO_FUNDAMENTAL:
                // Tabela 2
                $this->tabela2[$key . ' - Ensino Fundamental'][$zona]++;
                $this->total['tabela2'][$zona]++;

                $this->tabela2[$key . ' - Ensino Fundamental']['Total']++;
                $this->total['tabela2']['Total']++;
                //$solicitacao->modalidadeBeneficio == Aluno::MODALIDADE_PASSE &&
                if ($solicitacao->aluno->necessidades) {
                    $this->tabela7[$key . ' - Ensino Fundamental'][$zona]++;
                    $this->tabela7[$key . ' - Ensino Fundamental']['Total']++;
                    $this->total['tabela7'][$zona]++;
                    $this->total['tabela7']['Total']++;
                }
                //

                break;
            case Escola::ENSINO_MEDIO:
                // Tabela 2
                $this->tabela2[$key . ' - Ensino Médio'][$zona]++;
                $this->total['tabela2'][$zona]++;

                $this->tabela2[$key . ' - Ensino Médio']['Total']++;
                $this->total['tabela2']['Total']++;
                //$solicitacao->modalidadeBeneficio == Aluno::MODALIDADE_PASSE && 
                if ($solicitacao->aluno->necessidades) {
                    $this->tabela7[$key . ' - Ensino Médio'][$zona]++;
                    $this->tabela7[$key . ' - Ensino Médio']['Total']++;

                    $this->total['tabela7'][$zona]++;
                    $this->total['tabela7']['Total']++;
                }
                //
                break;
            default:
                break;
        }
        //

        // Tabela 3, 4, 5,
        if ($solicitacao->modalidadeBeneficio == Aluno::MODALIDADE_FRETE) {
            // Tabela 3
            $this->tabela3[$key . ' - Frete'][$zona]++;
            $this->total['tabela3'][$zona]++;
			

            $this->tabela3[$key . ' - Frete']['Total']++;
            $this->total['tabela3']['Total']++;
            //
            // Tabela 4
            $this->tabela4[$key][$zona]++;
            $this->total['tabela4'][$zona]++;

            $this->tabela4[$key]['Total']++;
            $this->total['tabela4']['Total']++;

            //
            //Tabela 6
            $this->tabela6['Frete'][$zona]++;
            $this->tabela6['Frete']['Total']++;
            $this->total['tabela6']['Total']++;
            $this->total['tabela6'][$zona]++;
        } else if ($solicitacao->modalidadeBeneficio == Aluno::MODALIDADE_PASSE) {
            // Tabela 3
            $this->tabela3[$key . ' - Passe Escolar'][$zona]++;
            $this->total['tabela3'][$zona]++;
            $this->tabela3[$key . ' - Passe Escolar']['Total']++;
            $this->total['tabela3']['Total']++;
            //
            // Tabela 5 
            $this->tabela5[$key][$zona]++;
            $this->total['tabela5'][$zona]++;

            $this->tabela5[$key]['Total']++;
            $this->total['tabela5']['Total']++;
            //

            //Tabela 6
            $this->tabela6['Passe Escolar'][$zona]++;
            $this->tabela6['Passe Escolar']['Total']++;
            $this->total['tabela6']['Total']++;
            $this->total['tabela6'][$zona]++;
        }
        //


    }
    protected function hasValue(&$field, $column)
    {
        if ($field && $field[$column]) {
            return $field[$column];
        }
        return '0';
    }

    protected function gerarTotal($tabela)
    {


        $c = '';
        $RIGHT = 'text-align:right;padding-right:7px;';
        $LEFT = 'text-align:left;padding-left:7px;';

        $CENTER = 'text-align:center';
        $c .= '<tr>';
        $this->tdBorder($c, 50, 'Total Geral', $LEFT);
        $this->tdBorder($c, 25, $this->total[$tabela]['Urbana'], $CENTER);
        $this->tdBorder($c, 25, $this->total[$tabela]['Rural'], $CENTER);
		$this->tdBorder($c, 25, $this->total[$tabela]['-'], $CENTER);
		//$this->tdBorder($c, 25, $this->total[$tabela]['Total'], $CENTER);
		//alteracao em 04/10/2021 - as somas não batiam com o total
		$totalGeral = $this->total[$tabela]['Urbana'] + $this->total[$tabela]['Rural'] + $this->total[$tabela]['-'];
		$this->tdBorder($c, 25, $totalGeral, $CENTER);
        

        return $c .= '</tr>';
    }
    protected function montarPdf(&$pdf)
    {
        // print_r($this->tabela6);
        // exit(1);
        $mpdf = $pdf->api;
        $mpdf->setHTMLHeader('
        <table width="100%">
        <tr>
          <Td align="center">
          <img src="img/brasaoFull.png">
          </Td>
        </tr>
        <tr>
          <td align="center">
              <i>Emitido em ' . date("d/m/Y H:i") . '</i>
          </td>
        </tr>
      </table>');


        $c = '';
        $RIGHT = 'text-align:right;padding-right:7px;';
        $LEFT = 'text-align:left;padding-left:7px;';

        $CENTER = 'text-align:center';
        $c .= '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><div style="text-align:center;">';
        $c .= '<b>RELATÓRIO</b><br>';
        $c .= '<b>Agrupamento de bairros</b>';
        $c .= '</div>';
        // $mpdf->WriteHTML($c);
        // $mpdf->addPage();



        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        //  TABELA 1
        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
	
        foreach ($this->gerarSolicitacoes() as $solicitacao) {
            switch ($solicitacao->escola->unidade) {
                case Escola::UNIDADE_MUNICIPAL:
                    // Tabela 1
                    $this->somarZona('Rede Municipal', $solicitacao);

                    // Rede Municipal não possui Ensino Médio e estão sendo exibidos dados.
                    //  if(isset($_GET['teste'])) {
                    //     print '<pre>';
                    //     if($solicitacao->aluno->ensino == Escola::ENSINO_MEDIO){
                    //         print '<table>';
                    //         print '<tr>';
                    //         print '<td>'.$solicitacao->aluno->nome.'</td>';
                    //         print '<td>'.$solicitacao->escola->nome.'</td>';
                    //         print '<td>'.$solicitacao->id.'</td>';
                    //         print '<td><a target="_new" href="?r=solicitacao-transporte%2Fview&id='.$solicitacao->id.'">Visualizar Solicitação de Transporte</a></td>';                
                    //         print '<td><a target="_new" href="?r=aluno%2Fupdate&id='.$solicitacao->aluno->id.'">Editar Aluno</a></td>';                
                    //         print '<td><a target="_new" href="?r=escola%2Fview&id='.$solicitacao->escola->id.'">Visualizar Escola</a></td>';
                    //         print '</tr>';
                    //         print '</table>';

                    //         // print $solicitacao->aluno->nome.' |<a>'.$solicitacao->id.'</a>|'.$solicitacao->status.'| '.$solicitacao->idAluno.' |'.$solicitacao->escola->nome;
                    //         // print '<Br>';
                    //     }
                    //     print '</pre>';
                    // }
                    break;
                case Escola::UNIDADE_ESTADUAL:
                    // Tabela 1

                    //Rede Estadual não possui Educação Infantil e estão sendo exibidos dados
                    // UPDATE Aluno SET ensino = 2 WHERE id = 12986;
                    //  if(isset($_GET['teste'])) {
                    //     print '<pre>';
                    //     if($solicitacao->aluno->ensino == Escola::ENSINO_INFANTIL){
                    //         print '<table>';
                    //         print '<tr>';
                    //         print '<td>'.$solicitacao->aluno->nome.'</td>';
                    //         print '<td>'.$solicitacao->escola->nome.'</td>';
                    //         print '<td>'.$solicitacao->id.'</td>';
                    //         print '<td><a target="_new" href="?r=solicitacao-transporte%2Fview&id='.$solicitacao->id.'">Visualizar Solicitação de Transporte</a></td>';                
                    //         print '<td><a target="_new" href="?r=aluno%2Fupdate&id='.$solicitacao->aluno->id.'">Editar Aluno</a></td>';                
                    //         print '<td><a target="_new" href="?r=escola%2Fview&id='.$solicitacao->escola->id.'">Visualizar Escola</a></td>';
                    //         print '</tr>';
                    //         print '</table>';

                    //         // print $solicitacao->aluno->nome.' |<a>'.$solicitacao->id.'</a>|'.$solicitacao->status.'| '.$solicitacao->idAluno.' |'.$solicitacao->escola->nome;
                    //         // print '<Br>';
                    //     }
                    //     print '</pre>';
                    // }
					
                    $this->somarZona('Rede Estadual', $solicitacao);
                    break;
                case Escola::UNIDADE_FILANTROPICA:
                    // Tabela 1
                    $this->somarZona('Rede Filantrópica', $solicitacao);
                    break;
                default:



                    break;
            }
        }


        $c = '<table style=" border-collapse:collapse;margin:20px;padding:0;width:100%;">';
        $c .= $this->setHeader();
        ksort($this->tabela1);

	
        foreach ($this->tabela1 as $key => $value) {
            $c .= '<tr>';
			$total = $this->hasValue($this->tabela1[$key], 'Urbana') +$this->hasValue($this->tabela1[$key], 'Rural')+$this->hasValue($this->tabela1[$key], '-');
            $this->tdBorder($c, 50, $key, $LEFT);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela1[$key], 'Urbana'), $CENTER);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela1[$key], 'Rural'), $CENTER);
			$this->tdBorder($c, 16.66, $this->hasValue($this->tabela1[$key], '-'), $CENTER);
            //$this->tdBorder($c, 16.66, $this->hasValue($this->tabela1[$key], 'Total'), $CENTER);
			//alteracao feita em 04/10/2021 as somas não batiam com o total
			$this->tdBorder($c, 16.66, $total, $CENTER);
            $c .= '</tr>';
        }

        $c .= $this->gerarTotal('tabela1');


        $c .= '</table>';
        $c .= '<div style="text-align:center">Tabela 1 - Relatório Simplificado</div>';
        $mpdf->WriteHTML($c, 0);
        $mpdf->addPage();
        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        //  TABELA 2
        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////


        $c = '<table style=" border-collapse:collapse;margin:20px;padding:0;width:100%;">';
        $c .= $this->setHeader();
        // if(isset($_GET['teste'])) {
        //     print '<pre>';
        //     print_r($this->tabela2);
        //     print '</pre>';
        // }
        // print '<pre>';
        // print_r($this->tabela2);
        // print '</pre>';
        ksort($this->tabela2);
        foreach ($this->tabela2 as $key => $value) {
			$total = $this->hasValue($this->tabela2[$key], 'Urbana') +$this->hasValue($this->tabela2[$key], 'Rural')+$this->hasValue($this->tabela2[$key], '-');
            $c .= '<tr>';
            $this->tdBorder($c, 50, $key, $LEFT);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela2[$key], 'Urbana'), $CENTER);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela2[$key], 'Rural'), $CENTER);
			$this->tdBorder($c, 16.66, $this->hasValue($this->tabela2[$key], '-'), $CENTER);
            //$this->tdBorder($c, 16.66, $this->hasValue($this->tabela2[$key], 'Total'), $CENTER);
			//alteracao feita em 04/10/2021 as somas não batiam com o total
			$this->tdBorder($c, 16.66, $total, $CENTER);
            $c .= '</tr>';
        }

        $c .= $this->gerarTotal('tabela2');

        $c .= '</table>';
        $c .= '<div style="text-align:center">Tabela 2 - Relatório por Tipo de Rede/Ensino</div>';

        $mpdf->WriteHTML($c, 0);

        $c = '<br><br>';

        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        //  TABELA 3
        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        $c = '<table style=" border-collapse:collapse;margin:20px;padding:0;width:100%;">';
        $c .= $this->setHeader();
        ksort($this->tabela3);
        foreach ($this->tabela3 as $key => $value) {
			$total = $this->hasValue($this->tabela3[$key], 'Urbana') +$this->hasValue($this->tabela3[$key], 'Rural') + $this->hasValue($this->tabela3[$key], '-');
            $c .= '<tr>';
            $this->tdBorder($c, 50, $key, $LEFT);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela3[$key], 'Urbana'), $CENTER);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela3[$key], 'Rural'), $CENTER);
			$this->tdBorder($c, 16.66, $this->hasValue($this->tabela3[$key], '-'), $CENTER);
            //$this->tdBorder($c, 16.66, $this->hasValue($this->tabela3[$key], 'Total'), $CENTER);
			//alteracao feita em 04/10/2021 as somas não batiam com o total
			$this->tdBorder($c, 16.66, $total, $CENTER);
            $c .= '</tr>';
        }
        $c .= $this->gerarTotal('tabela3');
        $c .= '</table>';
        $c .= '<div style="text-align:center">Tabela 3 - Relatório por Tipo de Rede/Modalidade Benefício</div>';
        $mpdf->WriteHTML($c, 2);
        $mpdf->addPage();
        // $c = '<br><br>';


        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        //  TABELA 4
        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        $c = '<table style=" border-collapse:collapse;margin:20px;padding:0;width:100%;">';
        $c .= $this->setHeader('Modalidade Frete');
        ksort($this->tabela4);
        foreach ($this->tabela4 as $key => $value) {
			$total = $this->hasValue($this->tabela4[$key], 'Urbana') +$this->hasValue($this->tabela4[$key], 'Rural')+$this->hasValue($this->tabela4[$key], '-');	
            $c .= '<tr>';
            $this->tdBorder($c, 50, $key, $LEFT);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela4[$key], 'Urbana'), $CENTER);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela4[$key], 'Rural'), $CENTER);
			$this->tdBorder($c, 16.66, $this->hasValue($this->tabela4[$key], '-'), $CENTER);
            //$this->tdBorder($c, 16.66, $this->hasValue($this->tabela4[$key], 'Total'), $CENTER);
			//alteracao feita em 04/10/2021 as somas não batiam com o total
			$this->tdBorder($c, 16.66, $total, $CENTER);
            $c .= '</tr>';
        }

        $c .= $this->gerarTotal('tabela4');
        $c .= '</table>';
        $c .= '<div style="text-align:center">Tabela 4 - Relatório de Atendimento Modalidade Frete</div>';

        $mpdf->WriteHTML($c, 0);
        $c = '<br><br>';


        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        //  TABELA 5
        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        $c = '<table style=" border-collapse:collapse;margin:20px;padding:0;width:100%;">';
        $c .= $this->setHeader('Modalidade Passe Escolar');
        ksort($this->tabela5);
        foreach ($this->tabela5 as $key => $value) {
			$total = $this->hasValue($this->tabela5[$key], 'Urbana') +$this->hasValue($this->tabela5[$key], 'Rural')+$this->hasValue($this->tabela5[$key], '-');
            $c .= '<tr>';
            $this->tdBorder($c, 50, $key, $LEFT);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela5[$key], 'Urbana'), $CENTER);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela5[$key], 'Rural'), $CENTER);
			$this->tdBorder($c, 16.66, $this->hasValue($this->tabela5[$key], '-'), $CENTER);
            //$this->tdBorder($c, 16.66, $this->hasValue($this->tabela5[$key], 'Total'), $CENTER);
			//alteracao feita em 04/10/2021 as somas não batiam com o total
			$this->tdBorder($c, 16.66, $total, $CENTER);
            $c .= '</tr>';
        }

        $c .= $this->gerarTotal('tabela5');
        $c .= '</table>';
        $c .= '<div style="text-align:center">Tabela 5 - Relatório de Atendimento Modalidade Passe Escolar</div>';

        $mpdf->WriteHTML($c, 2);
        $c = '<br><br>';


        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        //  TABELA 6
        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        $c = '<table style=" border-collapse:collapse;margin:20px;padding:0;width:100%;">';
        $c .= $this->setHeader('Transporte Público Escolar');
        ksort($this->tabela6);
        foreach ($this->tabela6 as $key => $value) {
			$total = $this->hasValue($this->tabela6[$key], 'Urbana') +$this->hasValue($this->tabela6[$key], 'Rural')+$this->hasValue($this->tabela6[$key], '-');
            $c .= '<tr>';
            $this->tdBorder($c, 50, $key, $LEFT);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela6[$key], 'Urbana'), $CENTER);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela6[$key], 'Rural'), $CENTER);
			$this->tdBorder($c, 16.66, $this->hasValue($this->tabela6[$key], '-'), $CENTER);
            //$this->tdBorder($c, 16.66, $this->hasValue($this->tabela6[$key], 'Total'), $CENTER);
			//alteracao feita em 04/10/2021 as somas não batiam com o total
			$this->tdBorder($c, 16.66, $total, $CENTER);
            $c .= '</tr>';
        }
        $c .= $this->gerarTotal('tabela6');
        $c .= '</table>';
        $c .= '<div style="text-align:center">Tabela 6 - Relatório Simplificado por Modalidade de Atendimento</div>';

        $mpdf->WriteHTML($c, 0);
        $c = '<br><br>';

        $mpdf->addPage();

        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        //  TABELA 7
        ////////////////////////////////////////
        ////////////////////////////////////////
        ////////////////////////////////////////
        $c = '<table style=" border-collapse:collapse;margin:20px;padding:0;width:100%;">';
        $c .= $this->setHeader('Atendimento Alunos Especiais');
        ksort($this->tabela7);
        foreach ($this->tabela7 as $key => $value) {
			$total = $this->hasValue($this->tabela7[$key], 'Urbana') +$this->hasValue($this->tabela7[$key], 'Rural')+$this->hasValue($this->tabela7[$key], '-');
            $c .= '<tr>';
            $this->tdBorder($c, 50, $key, $LEFT);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela7[$key], 'Urbana'), $CENTER);
            $this->tdBorder($c, 16.66, $this->hasValue($this->tabela7[$key], 'Rural'), $CENTER);
			$this->tdBorder($c, 16.66, $this->hasValue($this->tabela7[$key], '-'), $CENTER);
            //$this->tdBorder($c, 16.66, $this->hasValue($this->tabela7[$key], 'Total'), $CENTER);
			//alteracao feita em 04/10/2021 as somas não batiam com o total
			$this->tdBorder($c, 16.66, $total, $CENTER);
            $c .= '</tr>';
        }
        $c .= $this->gerarTotal('tabela7');
        $c .= '</table>';
        $c .= '<div style="text-align:center">Tabela 7 - Atendimento Alunos Especiais</div>';

        $mpdf->WriteHTML($c, 2);
        $c = '<br><br>';

        return $c;
    }




    public function actionRelatorio()
    {
        $pdf = new Pdf([
            'marginTop' => 55,
            'marginBottom' => 20,
            // 'marginLeft' => 10,
            // 'marginRight' => 10,
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            // 'content' => $this->montarPdf(), 
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
             'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
             'cssInline' => '.kv-heading-1{font-size:18px} .table table { border-collapse: collapse; } .table table, .table th, .table td { border: 1px solid black;} .table th td { padding-left: 3px;} .thpref {
                 background:#0070C0;
                 color:#fff;
             }',
            // set mPDF properties on the fly
            'options' => ['title' => 'Relatório - Agrupamento de bairros'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ['
                  <table width="100%">
                  <tr>
                    <Td align="center">
                    <img src="img/brasaoFull.png">
                    </Td>
                  </tr>
                  <tr>
                    <td align="center">
                        <i>Emitido em ' . date("d/m/Y H:i") . '</i>
                    </td>
                  </tr>
                </table>'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        $this->montarPdf($pdf);
        if (!isset($_GET['teste'])) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_RAW; 
            Yii::$app->response->headers->add('Content-Type', 'application/pdf');
            return $pdf->render();
        }
    }





    public function actionCreate()
    {
        // print_r($bairrosDisponiveis);
        $model = new AgrupamentoBairro();
        $model->scenario = 'create';

        if (Yii::$app->request->post()) {
            $this->salvarBairros($_POST,  $_POST['AgrupamentoBairro']['agrupamento']);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'bairrosDisponiveis' => $this->bairrosDisponiveis()
            ]);
        }
    }

    /**
     * Updates an existing AgrupamentoBairro model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // print $model->agrupamento;
            // AgrupamentoBairro::deleteAll(['agrupamento' => $model->agrupamento]);
            // print $_POST['AgrupamentoBairro']['agrupamento'];
            // $this->salvarBairros($_POST,  $model->agrupamento);
            return $this->redirect(['index']);
        } else {

            return $this->render('update', [
                'model' => $model,
                'bairrosDisponiveis' => $this->bairrosDisponiveis(),
                'bairrosPorZona' => $model->bairrosPorZona($model->agrupamento),
            ]);
        }
    }

    /**
     * Deletes an existing AgrupamentoBairro model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AgrupamentoBairro model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AgrupamentoBairro the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AgrupamentoBairro::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
