<?php

namespace frontend\controllers;

use Yii;
use common\models\SolicitacaoCredito;
use common\models\SolicitacaoCreditoAluno;
use common\models\SolicitacaoCreditoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Aluno;
use common\models\AlunoCurso;
use common\models\Configuracao;
use common\models\SolicitacaoCreditoStatus;
use common\models\SolicitacaoTransporte;
use yii\helpers\BaseHtml;
use yii\filters\AccessControl;
use common\models\Usuario;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
/**
 * SolicitacaoCreditoController implements the CRUD actions for SolicitacaoCredito model.
 */
class SolicitacaoCreditoController extends Controller
{
   
    public $config;
    public function init() {
        $this->config = Configuracao::setup();
        date_default_timezone_set('America/Sao_Paulo');
        parent::init();
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
 
    public function actionExportar($id,$tipo){
        $model = $this->findModel($id);
        
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
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(48.71);
        $sheet->getColumnDimension('C')->setWidth(11.71);
        $sheet->getColumnDimension('D')->setWidth(8.43);
        $sheet->getColumnDimension('E')->setWidth(12.57);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(13);
        $sheet->getColumnDimension('H')->setWidth(17);
        $sheet->getColumnDimension('I')->setWidth(17.14);
        $sheet->getColumnDimension('J')->setWidth(17);
        $sheet->getColumnDimension('K')->setWidth(9.14);
        $sheet->getColumnDimension('L')->setWidth(13);
        $sheet->getColumnDimension('M')->setWidth(12.71);
        $sheet->getColumnDimension('N')->setWidth(13);

        //
        $i = 1;
        // PRÓXIMA LINHA
        $sheet->mergeCells('A'.$i.':B'.($i+4));
        $sheet->mergeCells('C'.$i.':N'.$i);
        $sheet->mergeCells('C'.($i+1).':N'.($i+1));
        $sheet->setCellValue('C'.($i+1), "Secretaria de Educação e Cidadania");
        $sheet->getStyle('C'.($i+1))->applyFromArray($left)->getFont()->setBold(true);

        $sheet->mergeCells('C'.($i+2).':N'.($i+4));
        $sheet->setCellValue('C'.($i+2), "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
        $sheet->getStyle('C'.($i+2))->getAlignment()->setWrapText(true);

        $sheet->getStyle('A'.($i+2).':N'.($i+2))->applyFromArray($left);
        
        
        // Próxima linha
        $i += 5;
        $sheet->getRowDimension($i)->setRowHeight(25);
        $sheet->mergeCells('A'.$i.':N'.$i);
        $sheet->setCellValue('A'.$i, "SOLICITAÇÃO DE CRÉDITOS - PASSE ESCOLAR GRATUITO");
        $sheet->getStyle('A'.$i.':N'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFFFC000');
        $sheet->getStyle('A'.$i)->getFont()->setSize(18);
        $sheet->getStyle('A'.$i.':N'.$i)->applyFromArray($borderHard);

        $sheet->getStyle('A'.$i)->applyFromArray($center)->getFont()->setBold(true);
        // PRÓXIMA LINHA
        $i+=1;
        $colunasHeader = [
            [
                'leftLabel' => 'UNIDADE ESCOLAR: ',
                'rightLabel' => $model->escola->nomeCompleto,
            ],
            [
                'leftLabel' => 'CRÉDITOS PARA O MÊS:',
                'rightLabel' =>'',
            ],
            [
                'leftLabel' => 'QUANTIDADE DE DIAS LETIVOS NO MÊS DE:',
                'rightLabel' => $model->diasLetivosMes,
            ],
            [
                'leftLabel' => 'QUANTIDADE DE ALUNOS BENEFICIADOS:',
                'rightLabel' => count($model->solicitacaoCreditoAlunos),
            ],
            [
                'leftLabel' => 'VALOR NECESSÁRIO:',
                'rightLabel' => $model->valorNecessarioTotal,
            ],
            [
                'leftLabel' => 'SALDO RESTANTE NA ESCOLA:',
                'rightLabel' => $model->saldoRestante,
            ],
            [
                'leftLabel' => 'DIAS LETIVOS PARA ENCERRAR O MÊS ATUAL:',
                'rightLabel' => $model->diasLetivosRestantes,
            ],
            [
                'leftLabel' => 'SALDO RESTANTE NOS CARTÕES (AO ENCERRAR O MÊS):',
                'rightLabel' => $model->saldoRestanteCartoes,
            ],
            [
                'leftLabel' => 'VALOR A SER CREDITADO:',
                'rightLabel' => $model->valorCreditado,
            ],
        ];
        foreach($colunasHeader as $col) {    
            // BLOCO ESQUERDO
            $sheet->mergeCells('A'.$i.':B'.$i.'');
            $sheet->setCellValue('A'.$i.'', $col['leftLabel']);
            $sheet->getStyle('A'.$i)->applyFromArray($right)->getFont()->setBold(true);
            $sheet->getStyle('A'.$i.':N'.$i)->applyFromArray($borderHard);
            // BLOCO DIREITO
            $sheet->mergeCells('C'.$i.':N'.$i);
            $sheet->setCellValue('C'.$i, $col['rightLabel']);
            $sheet->getStyle('C'.$i)->applyFromArray($left)->getFont()->setBold(true);
            $sheet->getStyle('C'.$i.':N'.$i)->getFont()->setColor( $colorRed );
            $i++;
        }
        // PRÓX LINHA
        $sheet->mergeCells('A'.$i.':B'.$i.'');
        
        // PRÓX LINHA
        $i++;
        $sheet->getRowDimension($i)->setRowHeight(25);
        $sheet->mergeCells('A'.$i.':N'.$i);
        $sheet->setCellValue('A'.$i, "RELAÇÃO DE ALUNOS BENEFICIADOS PELO TRANSPORTE ESCOLAR - MODALIDADE PASSE");
        $sheet->getStyle('A'.$i)->applyFromArray($center)->getFont()->setBold(true);
        $sheet->getStyle('A'.$i)->getFont()->setSize(16);
        $sheet->getStyle('A'.$i.':N'.$i)->applyFromArray($borderHard);
        $sheet->getStyle('A'.$i.':N'.$i)->getFont()->setColor( $colorRed );


        // $sheet->getStyle('A1:N5')->applyFromArray($styleArray);

        // PRÓX LINHA
        $i++;
        $sheet->getRowDimension($i)->setRowHeight(25);
        $sheet->mergeCells('A'.$i.':N'.$i);
        $sheet->getStyle('A'.$i.':N'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FF000000');

        // PRÓX 
        $i++;
        $sheet->setCellValue('A'.$i, "QTD");
        $sheet->setCellValue('B'.$i, "NOME COMPLETO");
        $sheet->setCellValue('C'.$i, "RA");
        $sheet->setCellValue('D'.$i, "TURMA");
        $sheet->setCellValue('E'.$i, "Nº CARTÃO");
        $sheet->setCellValue('F'.$i, "SALDO ATUAL");
        $sheet->setCellValue('G'.$i, "FUNDHAS");
        $sheet->setCellValue('H'.$i, "DIAS LETIVOS PARA FECHAR O MÊS (R$)");
        $sheet->setCellValue('I'.$i, "DIAS LETIVOS PARA FECHAR O MÊS FUNDHAS");
        $sheet->getStyle('I'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFBDD7EE');

        $sheet->setCellValue('J'.$i, "SALDO DESCONTADO");
        $sheet->getStyle('J'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFFF6161');

        $sheet->setCellValue('K'.$i, "ANTI-U.E.");

        $sheet->setCellValue('L'.$i, "SALDO NO FINAL DO MÊS");
        $sheet->setCellValue('M'.$i, "NECESSIDADE DE CRÉDITOS?");
        $sheet->setCellValue('N'.$i, "VALOR A SER CREDITADO");
        
        $sheet->getStyle('L'.$i.':N'.$i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFD6DCE4');

        $sheet->getStyle('A'.$i.':N'.$i)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.$i.':N'.$i)->getFont()->setBold(true);
        $sheet->getStyle('A'.$i.':N'.$i)->applyFromArray($center);
        $sheet->getStyle('A'.$i.':N'.$i)->applyFromArray($borderHard);
        $sheet->getRowDimension($i)->setRowHeight(50);

        // PRÓX LINHA
        $i++;
        $contAluno = 1;
        foreach($model->solicitacaoCreditoAlunos as $solAluno) {
            $sheet->getStyle('A'.$i.':N'.$i)->applyFromArray($borderSoft);
            $sheet->getStyle('F'.$i.':L'.$i)->applyFromArray($right);
            $sheet->getStyle('C'.$i.':F'.$i)->applyFromArray($center);

            $sheet->setCellValue('A'.$i, $contAluno);
            $sheet->getStyle('A'.$i)->applyFromArray($center);
            $sheet->setCellValue('B'.$i, $solAluno->aluno->nome);
            $sheet->setCellValue('C'.$i, $solAluno->aluno->RACompleto);
            $sheet->setCellValue('D'.$i, Aluno::ARRAY_TURMA[$solAluno->aluno->turma]);
            $sheet->setCellValue('E'.$i, $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? $solAluno->aluno->solicitacaoAtivaPasse->cartaoPasseEscolar : $solAluno->aluno->solicitacaoAtivaPasse->cartaoValeTransporte);
            $sheet->setCellValue('F'.$i, $solAluno->saldo);
            $sheet->setCellValue('G'.$i, $solAluno->fundhas == 'on' ? 'X' : '');
            $sheet->setCellValue('H'.$i, $solAluno->diasLetivosFecharMes / 2);
            $sheet->setCellValue('I'.$i, $solAluno->diasLetivosFecharMes / 2);
            $sheet->getStyle('I'.$i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFBDD7EE');

            $sheet->setCellValue('J'.$i, $solAluno->diasLetivosFecharMes);
            $sheet->getStyle('J'.$i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF6161');
            
            $sheet->setCellValue('K'.$i, $solAluno->antiUe);
            $sheet->setCellValue('L'.$i, $solAluno->saldoFinalMes);
            $sheet->getStyle('L'.$i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD6DCE4');

            $sheet->setCellValue('M'.$i, $solAluno->valor > 0 ? 'SIM': 'NÃO');
            $sheet->getStyle('M'.$i)->applyFromArray($center);
            $sheet->getStyle('M'.$i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD6DCE4');

            $sheet->setCellValue('N'.$i, $solAluno->valor);
            $sheet->getStyle('N'.$i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD6DCE4');


            $contAluno++;
            $i++;
        }
        $base = "arquivos/_exportacoes/";
        
        switch($tipo){
            case 'PDF':
                try {
                    $filename = $base."Solicitacao_Credito_".date('d-m-Y-H-i-s').".pdf";
                    
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
                    $filename = $base."Solicitacao_Credito_".date('d-m-Y-H-i-s').".xlsx";
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

                $filename = $base.'_Solicitacao_Credito_'.date('d-m-Y-H-i-s').'.txt';
                $fp = fopen($filename, 'a'); 
          
                foreach($model->solicitacaoCreditoAlunos as $solAluno) {
                    if($model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR){
                        $cartao = $solAluno->aluno->solicitacaoAtivaPasse->cartaoPasseEscolar;
                    } else {
                        $cartao = $solAluno->aluno->solicitacaoAtivaPasse->cartaoValeTransporte;
                    }
                    if($solAluno->valor > 0 )
                        fwrite($fp, $cartao.';'.\Yii::$app->formatter::DoubletoReal($solAluno->valor).'
');


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
        }

    
    }
  
    public function actionAlteracaoStatusAjax($status){
        $model = new SolicitacaoCreditoStatus();
        $id =  Yii::$app->request->get('id');
        $status =  Yii::$app->request->get('status');
        if($id){
          $model->idSolicitacaoCredito = $id;
          $solicitacao = $this->findModel($id);
        }
        if($status){
         $model->status = $status;
        }
        
        
        // if(Usuario::permissao(Usuario::PERFIL_DIRETOR) && $model->status == SolicitacaoTransporte::STATUS_DEFERIDO_DIRETOR){
        //    $model->justificativa = 'Eu '.\Yii::$app->User->identity->nome.', portador do RG '.\Yii::$app->User->identity->rg.', declaro, nesta data, ter ciência e estar de acordo com os procedimentos realizados quanto a solicitação de Transporte Público Escolar do (a) aluno (a) à luz dos critérios de elegibilidade com base na Lei Municipal nº 8.107, de 03 de maio de 2010 e Lei Federal nº 12.796, de 04 de abril de 2013. Comprometo-me a respeitá-los e cumpri-los plena e integralmente, além de manter sempre verossímeis os dados da instituição e de minha área de competência. Respondendo administrativa, civil e penalmente, pela inclusão de informações inadequadas, se comprovada a omissão ou comissão, dolo ou culpa, nos termos da Lei Federal nº 8.429, de 02 de junho de 1992, que dispõe sobre as sanções aplicáveis aos agentes públicos no exercício de mandato, cargo, emprego ou função na administração pública direta, indireta ou funcional.';
        // }
       if ($model->load(Yii::$app->request->post())) {
  
        $model->idUsuario = \Yii::$app->User->identity->id;
        $model->dataCadastro = date('Y-m-d');
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
  
        if(!$model->save()){
          return ['status' => false, 'errors' => $model->getErrors()];
        }
        $solicitacao->status = $status;
        $solicitacao->save(false);
  
      
  
        return ['status' => true];
      } else {
       return $this->renderAjax('statusAjax', [
        'model' => $model,
        'solicitacao' => $solicitacao,
        'action' => 'solicitacao-credito/alteracao-status-ajax',
        'status' => $status,
        ]);
     }
  
   }
    /**
     * Lists all SolicitacaoCredito models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SolicitacaoCreditoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SolicitacaoCredito model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $model =  $this->findModel($id);
        
        $passeEscolar = SolicitacaoCreditoAluno::find()
                      ->where(['=','tipo', SolicitacaoCreditoAluno::TIPO_PASSE_ESCOLAR])
                      ->andWhere(['=','idSolicitacao', $id])
                      ->all();

        $valeTransporte = SolicitacaoCreditoAluno::find()
                          ->where(['=','tipo', SolicitacaoCreditoAluno::TIPO_VALE_TRANSPORTE])
                          ->andWhere(['=','idSolicitacao', $id])
                          ->all();
        
        return $this->render('view', [
            'model' => $model,
            'passeEscolar' => $passeEscolar,
            "valeTransporte" => $valeTransporte,

        ]);
    }
    private function getDates($model){
   
        $data = \DateTime::createFromFormat( 'd/m/Y', $model->inicio);
        if ($data)
            $model->inicio = $data->format('Y-m-d');

        $data = \DateTime::createFromFormat( 'd/m/Y', $model->fim);
        if ($data)
            $model->fim = $data->format('Y-m-d');

        return $model;
    }

    private function getDatesBr($model){
        
        $data = \DateTime::createFromFormat ( 'Y-m-d', $model->inicio);
        if ($data)
            $model->inicio = $data->format('d/m/Y');

        $data = \DateTime::createFromFormat ( 'Y-m-d', $model->fim);
        if ($data)
            $model->fim = $data->format('d/m/Y');

        return $model;
    }

    public function actionRelatorio($id){
          $model = $this->findModel($id);
          if($model->status == SolicitacaoCredito::STATUS_EFETIVADA){
            return $this->redirect(['view', 'id' => $model->id]);
        }
            
          if(Yii::$app->request->post()){
            $alunos = Yii::$app->request->post();
            
            // A $key nesse caso é o $idAluno  
            // Foreach responsável pelo passeEscolar
            foreach ($alunos['CheckboxPasseEscolar'] as $idAluno => $value) {
                //print $idAluno;
                $modelAluno = new SolicitacaoCreditoAluno();
                $modelAluno->tipo = SolicitacaoCreditoAluno::TIPO_PASSE_ESCOLAR;
                $modelAluno->idSolicitacao = $model->id;
                $modelAluno->idAluno = $idAluno;

                // print_r(SolicitacaoCredito::workingDays($model->inicio,$model->fim, $model->escola->calendario));
                // exit(1);
                $modelAluno->valor = SolicitacaoCreditoAluno::toDouble($alunos['passeEscolar'][$idAluno]['Valor']) * SolicitacaoCredito::workingDays($model->inicio,$model->fim, $model->escola->calendario);
                // print $modelAluno->valor;
          
                $modelAluno->saldo = $alunos['passeEscolar'][$idAluno]['Saldo'];
                $modelAluno->justificativa = $alunos['passeEscolar'][$idAluno]['Justificativa']; 
                if(!$modelAluno->save())
                    \Yii::$app->getSession()->setFlash('error', BaseHtml::errorSummary($modelAluno, ['header'=>'Erro ao salvar a solicitação de crédito do aluno.']));
            }

            
            // A $key nesse caso é o $idAluno
            // Foreach responsável pelo valeTransporte
            foreach ($alunos['CheckboxValeTransporte'] as $idAluno => $value) {
                $modelAluno = new SolicitacaoCreditoAluno();
                $modelAluno->tipo = SolicitacaoCreditoAluno::TIPO_VALE_TRANSPORTE;
                $modelAluno->idSolicitacao = $model->id;
                $modelAluno->idAluno = $idAluno;
                $modelAluno->valor = $alunos['valeTransporte'][$idAluno]['Valor'];
                $modelAluno->saldo = $alunos['valeTransporte'][$idAluno]['Saldo'];
                $modelAluno->justificativa = $alunos['valeTransporte'][$idAluno]['Justificativa']; 
                if(!$modelAluno->save())
                    \Yii::$app->getSession()->setFlash('error', BaseHtml::errorSummary($modelAluno, ['header'=>'Erro ao salvar a solicitação de crédito do aluno.']));

            }

            $model->status = SolicitacaoCredito::STATUS_EFETIVADA;
            $model->criado = date('Y-m-d H:i:s');
            $model->creditoAdministrativo = $alunos['creditoAdministrativo']; 
            $model->save();
            // print '<pre>';
            // print_r(Yii::$app->request->post());
            // print '</pre>';
            
            
            return $this->redirect(['view', 'id' => $model->id]);
          }
          
       
         
     
          $alunos = Aluno::find()
                        ->where(['=','SolicitacaoTransporte.idEscola', $model->idEscola])
                        ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_DEFERIDO])
                        //->andWhere(['>', 'dataAprovacao', $model->inicio])
                        ->andWhere(['=', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
                        ->andWhere(['=', 'SolicitacaoTransporte.modalidadeBeneficio', Aluno::MODALIDADE_PASSE])
                        ->joinWith('solicitacao')
                        ->orderBy(['nome'=>SORT_ASC])
                        ->all();
         
        $alunosValeTransporte = [];
        foreach ($alunos as $aluno) {
            $aluno->valeTransporte = 0;
            $aluno->passeEscolar =  SolicitacaoCreditoAluno::toDouble($this->config->passeEscolar) * 2;
            if($aluno->alunoCurso){
                // $endDate = $model->fim;
                // $startDate = $model->inicio;
                // $endDate = $endDate;
                $interval = \DateInterval::createFromDateString('1 day');
                $period = new \DatePeriod( new \DateTime($model->inicio), $interval, new \DateTime($model->fim));

                $cont = 0;
                foreach (Aluno::ARRAY_DIAS_CURSO as $dia => $texto) {
              
                   switch ($dia) {
                       case Aluno::SEGUNDA: $diaString = 'Monday'; break;
                       case Aluno::TERCA: $diaString = 'Tuesday'; break;
                       case Aluno::QUARTA: $diaString = 'Wednesday'; break;
                       case Aluno::QUINTA: $diaString = 'Thursday'; break;
                       case Aluno::SEXTA: $diaString = 'Friday'; break;
                       case Aluno::SABADO: $diaString = 'Saturday'; break;
                       case Aluno::DOMINGO: $diaString = 'Sunday'; break;
                   }
                  
               
                   if($aluno->cursoDia($dia)){
                    foreach ($period as $dt) {
                          // print $dt->format("l").'-'.$diaString;
                          // print '<Br>';
                          if($dt->format("l") == $diaString) {
                             $aluno->valeTransporte += ($this->config->valeTransporte * 2);
                          }
                      }
                   }
                }
                $aluno->valeTransporte = number_format($aluno->valeTransporte, 2);
                array_push($alunosValeTransporte, $aluno);   
            }
           
        }
          // foreach($alunosValeTransporte as $a){
          //   print $a->valeTransporte;
          //   print '<br>';
          // }
          return $this->render('relatorio', [
            'model' => $model,
            'alunos' => $alunos,
            'alunosValeTransporte' => $alunosValeTransporte,
        ]); 
    }
    public function actionRelatorioFinal($id)
    {   
        $model =  $this->findModel($id);
        
        // print_r($model->solicitacaoCreditoAlunos);
        if($model->load(Yii::$app->request->post()) && $model->save() ){
               if($model->tipoSolicitacao > 0){
                    $this->redirect(['relatorio-final', 'id' => $id]);
                }
                else {
                                $this->redirect(['view', 'id' => $id]);

                }
                                

        }
        return $this->render('relatorio-final', [
            'model' => $model,
            'solicitacoesAlunos' => $model->solicitacaoCreditoAlunos,
            'configuracao' => Configuracao::setup()

        ]);
    }
    /**
     * Creates a new SolicitacaoCredito model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreate()
    // {
    //     $model = new SolicitacaoCredito();

    //     if ( $model->load(Yii::$app->request->post()) ) {
    //         \Yii::$app->getSession()->setFlash('success', 'Criado com sucesso.');
    //         $model = $this->getDates($model);
    //         if( $model->save() ){
    //             return $this->redirect(['relatorio', 'id' => $model->id]);
    //         }
            
    //     } else {
    //         if($model->getErrors())
    //             \Yii::$app->getSession()->setFlash('error', BaseHtml::errorSummary($model, ['header'=>'Erro ao salvar o usuário.']));
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }
    
    public function actionCriarCredito($tipo){
        $model = new SolicitacaoCredito();
        $model->tipoSolicitacao = $tipo;
        $model->criado = date('Y-m-d H:i:s');
        if ( $model->load(Yii::$app->request->post()) ) {			
			
		
            \Yii::$app->getSession()->setFlash('success', 'Criado com sucesso.');
            $model = $this->getDates($model);
            if( $model->save() ){
                if($model->tipoSolicitacao == SolicitacaoCredito::TIPO_CREDITO_ADMINISTRATIVO){
                    $model->status = SolicitacaoCredito::STATUS_EFETIVADA;
                    $model->save();
                    return $this->redirect(['credito-administrativo', 'id' => $model->id]);
                }
                return $this->redirect(['credito-preenchimento', 'id' => $model->id]);
            }
        } else {
            return $this->render('criar-credito', [
                'model' => $model,
                
            ]); 
        }
    }

 
    
    public function actionCreditoPreenchimento($id){
        $model = $this->findModel($id);
		
        if ($post = Yii::$app->request->post() ) {
			
            if($model->status != SolicitacaoCredito::STATUS_EFETIVADA){
				
				$pkCount = (is_array($post['aluno']) ? count($post['aluno']) : 0);
				
				if($pkCount == 0){					
					\Yii::$app->getSession()->setFlash('error', 'Essa escola não tem alunos e não pode receber solicitações de crédito.');
					return $this->redirect(['index']);
					exit;
				}
				\Yii::$app->db->createCommand()->delete('SolicitacaoCreditoAluno', ['idSolicitacao'=>$model->id])->execute();
				
				//print_r($post['aluno']);exit;
                foreach ($post['aluno'] as $index => $idAluno) {
					
					
					
                    $modelAluno = new SolicitacaoCreditoAluno();
                    $modelAluno->tipo = SolicitacaoCreditoAluno::TIPO_PASSE_ESCOLAR;
                    $modelAluno->idSolicitacao = $model->id;
                    $modelAluno->idAluno = $idAluno;
                    $modelAluno->valor = \Yii::$app->formatter::BRLtoDouble($post['valorNecessario'][$idAluno]);
                    $modelAluno->saldo = \Yii::$app->formatter::BRLtoDouble($post['saldoRestante'][$idAluno]);
                    $modelAluno->justificativa = $post['justificativa'][$idAluno]; 
                    $modelAluno->fundhas = $post['fundhas'][$idAluno];
                    
                    $modelAluno->diasLetivosFecharMes = $post['diasLetivosFecharMes'][$idAluno];
                    $modelAluno->antiUe = $post['AntiUe'][$idAluno];
                    $modelAluno->saldoFinalMes = $post['saldoFinalMes'][$idAluno];
                    
                    if(!$modelAluno->save())
                        \Yii::$app->getSession()->setFlash('error', BaseHtml::errorSummary($modelAluno, ['header'=>'Erro ao salvar a solicitação de crédito do aluno.'])); 
                }

				if ($post['statusProgresso'] == 1){
					$model->status = SolicitacaoCredito::STATUS_EM_ANDAMENTO;
				}else{
					$model->status = SolicitacaoCredito::STATUS_EFETIVADA;
				}
                
                $model->saldoRestante = \Yii::$app->formatter::BRLtoDouble($post['saldoRestanteEscola']);
                $model->valorNecessarioTotal = \Yii::$app->formatter::BRLtoDouble($post['valorNecessarioTotal']);
				$model->valorNecessarioAluno = \Yii::$app->formatter::BRLtoDouble($post['valorNecessarioAluno']);
                $model->diasLetivosRestantes = \Yii::$app->formatter::BRLtoDouble($post['diasLetivosRestantes']);
                $model->saldoRestanteCartoes = \Yii::$app->formatter::BRLtoDouble($post['saldoRestanteCartoes']);
                $model->valorCreditado = \Yii::$app->formatter::BRLtoDouble($post['valorCreditado']);
                $model->diasLetivosMes = $post['diasLetivosMes'];
                
                $model->criado = date('Y-m-d H:i:s');
                $model->save();
                return $this->redirect(['index']);
            }
        }

        if($model->tipoSolicitacao == SolicitacaoCredito::TIPO_VALE_TRANSPORTE){
			
            $idsAlunoCurso = AlunoCurso::find()->cache(60)->select('idAluno')->all();
            $alunos = [];
            foreach($idsAlunoCurso as $aluno) {
                $alunos[] = $aluno->idAluno;
            }
            $alunos = Aluno::find()
            ->where(['=','SolicitacaoTransporte.idEscola', $model->idEscola])
            ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_CONCEDIDO])
            // ->andWhere(['>', 'dataAprovacao', $model->inicio])
            ->andWhere(['=', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
            ->andWhere(['=', 'SolicitacaoTransporte.modalidadeBeneficio', Aluno::MODALIDADE_PASSE])
            ->andWhere(['in', 'Aluno.id', $alunos])
            ->joinWith('solicitacao')
            ->orderBy(['nome'=>SORT_ASC])
            ->all();
			
			 return $this->render('credito-preenchimento', [
				'model' => $model,
				// 'alunos' => $alunos,
				'alunos' => $alunos,
				'configuracao' => Configuracao::setup()
			]); 
			
		
        } else {
			
            // $alunos = Aluno::find()
            // ->where(['=','SolicitacaoTransporte.idEscola', $model->idEscola])
            // ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_CONCEDIDO])
            // ->andWhere(['>', 'dataAprovacao', $model->inicio])
            // ->andWhere(['=', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_BENEFICIO])
            // ->andWhere(['=', 'SolicitacaoTransporte.modalidadeBeneficio', Aluno::MODALIDADE_PASSE])
            // ->joinWith('solicitacao')
            // ->orderBy(['nome'=>SORT_ASC])
            // ->all();
			
			
			$sql = "select *,a.id as idAl,sca.id as idSolicitacaoCredAl,(select count(*) from SolicitacaoCreditoAluno scca where scca.idSolicitacao = ".$id.") as temSolCred from Aluno a  join SolicitacaoTransporte st on st.idAluno = a.id left join SolicitacaoCreditoAluno sca on sca.idAluno = a.id and sca.idSolicitacao = ".$id." where st.`status` = ".SolicitacaoTransporte::STATUS_CONCEDIDO." and st.tipoSolicitacao = ".SolicitacaoTransporte::SOLICITACAO_BENEFICIO." and st.modalidadeBeneficio = ".Aluno::MODALIDADE_PASSE." and st.idEscola = ".$model->idEscola ;			
			$alunos = Yii::$app->getDb()->createCommand($sql)->queryAll();					
			

			$sql = "select * from SolicitacaoCredito sc where sc.`id` = ".$id;			
			$solCred = Yii::$app->getDb()->createCommand($sql)->queryAll();					
			
			 return $this->render('credito-preenchimento-passe', [
				'model' => $model,
				// 'alunos' => $alunos,
				'alunos' => $alunos,
				'solCred' => $solCred,
				'temSolCred' => $alunos[0]['temSolCred'],
				'configuracao' => Configuracao::setup()
			]); 
        }
       
       
    }

    /**
     * Updates an existing SolicitacaoCredito model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
              $model =$this->getDates($model);
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model = $this->getDatesBr($model);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SolicitacaoCredito model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionCreditoAdministrativo($id){
        $model = $this->findModel($id);
        if($model->load(Yii::$app->request->post())){
            $model->save(false);
        }
        return $this->render('credito-administrativo', [
            'model' => $model,
        ]);
    }
    /**
     * Finds the SolicitacaoCredito model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SolicitacaoCredito the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SolicitacaoCredito::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
