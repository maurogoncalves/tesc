<?php

namespace frontend\controllers;
use common\models\SolicitacaoTransporte;
use common\models\Aluno;
use common\models\Escola;
use common\models\SolicitacaoCredito;
use yii\helpers\ArrayHelper;
use common\models\ReciboPagamentoAutonomo;
use common\models\Condutor;
use Yii;
use kartik\mpdf\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class PainelIndicadoresController extends \yii\web\Controller
{
  protected $session;
  public function init()
  {
      parent::init();
      $this->session = Yii::$app->session;
      $this->session->open();
  }

    public function actionIndex()
    {
      if ($get = Yii::$app->request->get()) {
        $solicitacoes = SolicitacaoCredito::find()
        ->andWhere(['=', 'status', SolicitacaoTransporte::STATUS_DEFERIDO])
        ->orderBy(['id' => SORT_DESC])
        ;


        if ($get['periodo']) $this->searchSolicitacoesPeriodo($get['periodo'], $solicitacoes);
        if ($get['escola']) $solicitacoes->andWhere(['idEscola' => $get['escola']]);
        if ($get['unidade'])
          $solicitacoes = $solicitacoes
            ->innerJoin('Escola', 'Escola.id=SolicitacaoCredito.idEscola')
            ->andWhere(['Escola.unidade' => $get['unidade']]);

        $escolas = ArrayHelper::map(Escola::find()->all(), 'id', 'nome');
        $unidades = Escola::ARRAY_UNIDADE;
        
        $solicitacoes = $solicitacoes->all();
        $total = 0 ;
        foreach($solicitacoes as $solicitacao) {
          $total += $solicitacao->valorTransferido;
        }
        $this->session->set('cacheReportPainelIndicadores', $solicitacoes);
  
        return $this->render('index', [
          // 'model' => $model,
          // 'data' => $arrayData,
          'solicitacoes' =>  $solicitacoes,
          'escolas' => $escolas,
          'unidades' => $unidades,
          'total' => $total,
          // 'titulo' => 'Painel de indicadores'
        ]);
      }
      $this->redirect(['index']);
    }
    
    public function actionExportPainelIndicadores($tipo) {
          // if(!$GLOBALS['cacheReportPainelIndicadores']) {
          //   return $this->redirect(['index']);
          // }
          // $content = '<table>';
          $solciitacoes = $this->session->get('cacheReportPainelIndicadores');
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
          $sheet->getColumnDimension('A')->setWidth(45.14);
          $sheet->getColumnDimension('B')->setWidth(15.5);
          $sheet->getColumnDimension('C')->setWidth(40.34);
          $sheet->getColumnDimension('D')->setWidth(15.14);
          $sheet->getColumnDimension('E')->setWidth(25.57);
          $sheet->getColumnDimension('F')->setWidth(10.57);
          $sheet->getColumnDimension('G')->setWidth(10.57);

      
      
      
          //
          $i = 1;
          // PRÓXIMA LINHA
          $sheet->mergeCells('A'.$i.':B'.($i+4));
          $sheet->mergeCells('C'.$i.':G'.$i);
          $sheet->mergeCells('C'.($i+1).':G'.($i+1));
          $sheet->setCellValue('C'.($i+1), "Secretaria de Educação e Cidadania");
          $sheet->getStyle('C'.($i+1))->applyFromArray($left)->getFont()->setBold(true);
      
          $sheet->mergeCells('C'.($i+2).':G'.($i+4));
          $sheet->setCellValue('C'.($i+2), "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
          $sheet->getStyle('C'.($i+2))->getAlignment()->setWrapText(true);
      
          $sheet->getStyle('A'.($i+2).':G'.($i+2))->applyFromArray($left);
      
      
          $i+=5;
          $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray($center);
      
          $sheet->setCellValue('A'.$i, "CÓDIGO");
          $sheet->setCellValue('B'.$i, "TIPO DA SOLICITAÇÃO");
          $sheet->setCellValue('C'.$i, "UNIDADE ESCOLAR");
          $sheet->setCellValue('D'.$i, "INÍCIO");
          $sheet->setCellValue('E'.$i, "FIM");
          $sheet->setCellValue('F'.$i, "TOTAL");
          $sheet->setCellValue('G'.$i, "DATA DA TRANSFERÊNCIA");

      
          $sheet->getStyle('A'.$i.':G'.$i)
          ->getAlignment()->setWrapText(true);
          $sheet->getStyle('A'.$i.':G'.$i)->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setARGB('FF000000');
      
          $sheet->getStyle('A'.$i.':G'.$i)->getFont()->setBold(true);
      
          $sheet->getStyle('A'.$i.':G'.$i)->getFont()->setColor( $colorWhite );
          $sheet->setAutoFilter('A'.$i.':G'.$i);
      
          // $response = SolicitacaoTransporte::agruparSolicitacoesPendentesPorEscola();
          // $escolasArr = $response['escolasArr'];
          // $totaisArr = $response['totaisArr'];
          // $escolas = $response['escolas'];
      
          foreach($solciitacoes as $model) {
          $i++;
              if($i % 2 == 0) {
                  $sheet->getStyle('A'.$i.':G'.$i)->getFill()
                  ->setFillType(Fill::FILL_SOLID)
                  ->getStartColor()->setRGB('F6F6F6');
              
              }
              // $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($borderSoft);
              $sheet->getStyle('B'.$i.':G'.$i)->applyFromArray($center);
              $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray($borderSoft);
              $sheet->getStyle('A'.$i.':G'.$i)
              ->getAlignment()->setWrapText(true);
    
              // $sheet->setCellValue('A'.$i, "CÓDIGO");
              // $sheet->setCellValue('B'.$i, "TIPO DA SOLICITAÇÃO");
              // $sheet->setCellValue('C'.$i, "UNIDADE ESCOLAR");
              // $sheet->setCellValue('D'.$i, "INÍCIO");
              // $sheet->setCellValue('E'.$i, "FIM");
              // $sheet->setCellValue('F'.$i, "TOTAL");
              // $sheet->setCellValue('G'.$i, "DATA DA TRANSFERÊNCIA");

              $sheet->setCellValue('A'.$i, ' '.$model->id);
              $sheet->setCellValue('B'.$i,  SolicitacaoCredito::TIPO[$model->tipoSolicitacao]);
              $sheet->setCellValue('C'.$i,  $model->escola->nome);
              $sheet->setCellValue('D'.$i,  ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesInicio]);
              $sheet->setCellValue('E'.$i,  ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesFim]);
              $sheet->setCellValue('F'.$i,  \Yii::$app->formatter::DoubletoReal($model->valorTransferido));
              $sheet->setCellValue('G'.$i,  $model->dataTransferencia ? date("d/m/Y", strtotime($model->dataTransferencia)) : '-' );

      
          }
          $i++;
          if($i % 2 == 0) {
            $sheet->getStyle('A'.$i.':F'.$i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('F6F6F6');
        
        }
        // $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($borderSoft);
        $sheet->getStyle('B'.$i.':G'.$i)->applyFromArray($center);
        $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray($borderSoft);
        $sheet->getStyle('A'.$i.':G'.$i)
        ->getAlignment()->setWrapText(true);
        
        $total = 0 ;
        foreach($solciitacoes as $solicitacao) {
          $total += $solicitacao->valorTransferido;
        }

          $sheet->getStyle('A'.$i.':E'.$i)->applyFromArray($right);
          $sheet->mergeCells('A'.$i.':E'.$i);
          $sheet->setCellValue('A'.$i, 'TOTAL ');
          $sheet->setCellValue('F'.$i,  \Yii::$app->formatter::DoubletoReal($total));

    
          $i++;
          $sheet->mergeCells('A'.$i.':G'.$i);
          $sheet->getStyle('B'.$i.':G'.$i)->applyFromArray($center);
          $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray($borderSoft);
          $sheet->getStyle('A'.$i.':G'.$i)
          ->getAlignment()->setWrapText(true);
          $sheet->setCellValue('A'.$i, 'Emitido em '.date("d/m/Y H:i").'');
          
          $base = "arquivos/_exportacoes/";
              
          switch($tipo){
              case 'PDF':
                  try {
                      $filename = $base."Solicitacoes_Aguardando_Atendimento_".date('d-m-Y-H-i-s').".pdf";
                      
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
      
                  $filename = $base.'Solicitacoes_Aguardando_Atendimento_'.date('d-m-Y-H-i-s').'.txt';
                  $fp = fopen($filename, 'a');
                  $query = SolicitacaoTransporte::find()->orderBy(['nome' => SORT_ASC]);
                
                 
                  foreach($solciitacoes as $model) {
                     $data =  $model->dataTransferencia ? date("d/m/Y", strtotime($model->dataTransferencia)) : '-';
                      $l='';
                      $l .= $model->id;
                      $l .= ';'. SolicitacaoCredito::TIPO[$model->tipoSolicitacao];
                      $l .= ';'. $model->escola->nome;
                      $l .= ';'. ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesInicio];
                      $l .= ';'. ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesFim];
                      $l .= ';'. \Yii::$app->formatter::DoubletoReal($model->valorTransferido);
                      $l .= ';'. $data;
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
    public function actionValorCondutores()
    {
      if ($get = Yii::$app->request->get()) {
        $rpa = ReciboPagamentoAutonomo::find();

        if ($get['periodo']) $this->searchCondutorPeriodo($get['periodo'], $rpa);
        if ($get['condutor']) $rpa->andWhere(['idCondutor' => $get['condutor']]);

        $condutores = ArrayHelper::map(Condutor::find()->all(), 'id', 'nome');

        return $this->render('valor-condutores', [
          'rpa' => $rpa->all(),
          'condutores' => $condutores,
          // 'titulo' => 'Painel de indicadores'
        ]);
      }
      $this->redirect(['index']);
    }

    public function searchCondutorPeriodo($periodo, $model)
    {
      $data = explode('- ', $periodo);

      $data[1] = explode('/', $data[1]);
      $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];
      $data[0] = explode('/', $data[0]);
      $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];
      
      $model->andWhere(['>=', 'data', $data[0]])
        ->andWhere(['<=', 'data', $data[1]]);

      return $model;
    }

    public function searchSolicitacoesPeriodo($periodo, $model)
    {
      $data = explode('- ', $periodo);
    
      $data[1] = explode('/', $data[1]);
      $data[1] = $data[1][2].'-'.$data[1][1].'-'.$data[1][0];
      $data[0] = explode('/', $data[0]);
      $data[0] = trim($data[0][2]).'-'.$data[0][1].'-'.$data[0][0];

      $model->andWhere(['>=', 'dataTransferencia', $data[0]])
        ->andWhere(['<=', 'dataTransferencia', $data[1]]);

      return $model;
    }
}
