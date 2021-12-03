<?php

namespace frontend\controllers;

use Yii;
use common\models\Condutor;
use common\models\GestaoDocumentosSearch;
use common\models\Veiculo;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
/**
 * GestaoDocumentosController implements the CRUD actions for Condutor model.
 */
class GestaoDocumentosController extends Controller
{
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

    private function addDays($days, $date){
        return date('Y-m-d',(strtotime ( '+'.$days.' day' , strtotime ($date) ) ) );
    }

    /**
     * Lists all Condutor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GestaoDocumentosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['status' => Condutor::STATUS_ATIVO]);
        $dataProvider->query->joinWith(['veiculo']);
        $dataProvider->pagination = ['pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20];
        $dataProvider->setSort([
            'attributes' => [
                'veiculo.CRLV' => [
                    'asc' => ['veiculo.dataVencimentoCRLV' => SORT_ASC],
                    'desc' => ['veiculo.dataVencimentoCRLV' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'cnhValidade' => [
                    'asc' => ['cnhValidade' => SORT_ASC],
                    'desc' => ['cnhValidade' => SORT_DESC],
                    'default' => SORT_ASC,
                ],
                'anoFabricacao' => [
                    'asc' => ['anoFabricacao' => SORT_ASC],
                    'desc' => ['anoFabricacao' => SORT_DESC],
                    'default' => SORT_ASC,
                ],
                'nome' => [
                    'asc' => ['nome' => SORT_ASC],
                    'desc' => ['nome' => SORT_DESC],
                    'default' => SORT_ASC,
                ], 
                'veiculo.dataVistoriaEstadual' => [
                    'asc' => ['veiculo.dataVistoriaEstadual' => SORT_ASC],
                    'desc' => ['veiculo.dataVistoriaEstadual' => SORT_DESC],
                    'default' => SORT_ASC,
                ],
                'veiculo.dataVencimentoSeguro' => [
                    'asc' => ['veiculo.dataVencimentoSeguro' => SORT_ASC],
                    'desc' => ['veiculo.dataVencimentoSeguro' => SORT_DESC],
                    'default' => SORT_ASC,
                ],
            ],
        ]);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
 
    private function checkIsAValidDate($myDateString){
        return (bool)strtotime($myDateString);
    }
    public function paint(&$sheet, $model,$alertFn,$field,$position) {
        $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE );

        $alerta = $model->$alertFn();

        if(strpos($alerta, 'background:#ED1C24')) {
          $sheet->getStyle($position)->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setRGB('ED1C24');
          $sheet->getStyle($position)->getFont()->setColor( $colorWhite );

        }

        if(strpos($alerta, 'background:#FFC90E')) {
            $sheet->getStyle($position)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFC90E');

          }
          if(!$model->$field)
            return $sheet->setCellValue($position, '-'); 

          if($model->toDate($model->$field) != '31/12/1969')
            $model->$field = $model->toDate($model->$field); 
          if($field == 'anoFabricacao')
            $model->$field = $model->textoAnoFabricacao();

          return $sheet->setCellValue($position, $model->$field);


    }

	public function actionBuscar(){
		
		 \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
			$post = Yii::$app->request->post();
			
			$id = $post['id'];
			$dadosCnh = Condutor::findOne($id);
			$dadosVeic = Veiculo::findOne($dadosCnh['idVeiculo']);			
			
            return [
				'status' => true,
                'cnhValidade' => $dadosCnh['cnhValidade'],
				'dataVencimentoCRLV' => $dadosVeic['dataVencimentoCRLV'],
				'dataVistoriaEstadual' => $dadosVeic['dataVistoriaEstadual'],
				'dataVencimentoSeguro' => $dadosVeic['dataVencimentoSeguro'],
            ];

        } catch (NotFoundHttpException $e) {
            return [
                'status' => false
            ];
        }
		
		
	
			
	}	 
    
	public function actionSalvarDatas(){
		
		  \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
			$post = Yii::$app->request->post();
			
			
			$idCondutor = $post['idCondutor'];
			$cnhValidade = $post['cnhValidade'];
			$dataVencimentoCRLV = $post['dataVencimentoCRLV'];
			$dataVistoriaEstadual = $post['dataVistoriaEstadual'];
			$dataVencimentoSeguro = $post['dataVencimentoSeguro'];
			
			//$dadosCnh = Condutor::findOne($idCondutor);
			//$dadosVeic = Veiculo::findOne($dadosCnh['idVeiculo']);		

			$condutor = Condutor::findOne($idCondutor);
			$condutor->cnhValidade =$cnhValidade;				
			
			
			$veiculo = Veiculo::findOne($condutor['idVeiculo']);
			$veiculo->dataVencimentoCRLV =$dataVencimentoCRLV;		
			$veiculo->dataVistoriaEstadual =$dataVistoriaEstadual;		
			$veiculo->dataVencimentoSeguro =$dataVencimentoSeguro;		
			
			if ($veiculo->save() && $condutor->save() ){
                $status = true;
            }else{
				$status = false;
			}	
			
			
            return [
				'status' => $status,                
            ];

        } catch (NotFoundHttpException $e) {
            return [
                'status' => false
            ];
        }
		
	
			
	}
	
    public function actionExportar($tipo){
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
  $sheet->getColumnDimension('A')->setWidth(40.14);
  $sheet->getColumnDimension('B')->setWidth(25.5);
  $sheet->getColumnDimension('C')->setWidth(25.34);
  $sheet->getColumnDimension('D')->setWidth(25.14);
  $sheet->getColumnDimension('E')->setWidth(17.57);
  $sheet->getColumnDimension('F')->setWidth(17.57);



  //
  $i = 1;
  // PRÓXIMA LINHA
  $sheet->mergeCells('A'.$i.':B'.($i+4));
  $sheet->mergeCells('C'.$i.':F'.$i);
  $sheet->mergeCells('C'.($i+1).':F'.($i+1));
  $sheet->setCellValue('C'.($i+1), "Secretaria de Educação e Cidadania");
  $sheet->getStyle('C'.($i+1))->applyFromArray($left)->getFont()->setBold(true);

  $sheet->mergeCells('C'.($i+2).':F'.($i+4));
  $sheet->setCellValue('C'.($i+2), "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
  $sheet->getStyle('C'.($i+2))->getAlignment()->setWrapText(true);

  $sheet->getStyle('A'.($i+2).':F'.($i+2))->applyFromArray($left);


  $i+=5;
  $sheet->getStyle('A'.$i.':F'.$i)->applyFromArray($center);

  $sheet->setCellValue('A'.$i, "NOME");
  $sheet->setCellValue('B'.$i, "CNH");
  $sheet->setCellValue('C'.$i, "CRLV");
  $sheet->setCellValue('D'.$i, "Vistoria Semestral");
  $sheet->setCellValue('E'.$i, "Seguro");
  $sheet->setCellValue('F'.$i, "Idade do Veículo");
  $sheet->getStyle('A'.$i.':F'.$i)
  ->getAlignment()->setWrapText(true);
  $sheet->getStyle('A'.$i.':F'.$i)->getFill()
  ->setFillType(Fill::FILL_SOLID)
  ->getStartColor()->setARGB('FF000000');

  $sheet->getStyle('A'.$i.':F'.$i)->getFont()->setBold(true);

  $sheet->getStyle('A'.$i.':F'.$i)->getFont()->setColor( $colorWhite );
  $sheet->setAutoFilter('A'.$i.':F'.$i);

  $query = Condutor::find()->orderBy(['nome' => SORT_ASC]);
  if(isset($_GET['selecionados']) && $_GET['selecionados'] != '') {
      $ids = explode(',',$_GET['selecionados']);
      $query = $query->where(['in', 'id', $ids]);
  }
  foreach($query->all() as $model) {
  $i++;
      if($i % 2 == 0) {
          $sheet->getStyle('A'.$i.':F'.$i)->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setRGB('F6F6F6');
      
      }
      // $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($borderSoft);
      $sheet->getStyle('B'.$i.':F'.$i)->applyFromArray($center);
      $sheet->getStyle('A'.$i.':F'.$i)->applyFromArray($borderSoft);
      $sheet->getStyle('A'.$i.':F'.$i)
      ->getAlignment()->setWrapText(true);
      $sheet->setCellValue('A'.$i, ' '.$model->nome);

    //   public function paintRed(&$sheet, $model,$alertFn,$field,$i) {
        $this->paint($sheet, $model, 'cnhAlerta','cnhValidade', 'B'.$i);

        $position = 'C'.$i;
        $model->veiculo ? $this->paint($sheet, $model->veiculo, 'crlvAlerta','dataVencimentoCRLV', $position) : $sheet->setCellValue($position, '-'); ;
        $position = 'D'.$i;
        $model->veiculo ? $this->paint($sheet, $model->veiculo, 'vistoriaEstadualAlerta','dataVistoriaEstadual', $position) : $sheet->setCellValue($position, '-'); ;
        $position = 'E'.$i;
        $model->veiculo ? $this->paint($sheet, $model->veiculo, 'seguroAlerta','dataVencimentoSeguro', $position) : $sheet->setCellValue($position, '-'); ;
        $position = 'F'.$i;
        $model->veiculo ? $this->paint($sheet, $model->veiculo, 'anoAlerta','anoFabricacao', $position) : $sheet->setCellValue($position, '-'); ;

       

    //   $sheet->setCellValue('B'.$i, $model ? $model->cnhAlerta() : '-');
    //   $sheet->setCellValue('C'.$i, $model->veiculo ? $model->veiculo->crlvAlerta() : '-');
    //   $sheet->setCellValue('D'.$i, $model->veiculo ? $model->veiculo->vistoriaEstadualAlerta() : '-');
    //   $sheet->setCellValue('E'.$i, $model->veiculo ? $model->veiculo->seguroAlerta() : '-');
    //   $sheet->setCellValue('F'.$i, $model->veiculo ? $model->veiculo->anoAlerta() : '-');
 
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
          $query = Condutor::find()->orderBy(['nome' => SORT_ASC]);
          if(isset($_GET['selecionados']) && $_GET['selecionados'] != '') {
              $ids = explode(',',$_GET['selecionados']);
              $query = $query->where(['in', 'id', $ids]);
          }
          foreach($query->all() as $model) {
                  //   $sheet->setCellValue('B'.$i, $model ? $model->cnhAlerta() : '-');
    //   $sheet->setCellValue('C'.$i, $model->veiculo ? $model->veiculo->crlvAlerta() : '-');
    //   $sheet->setCellValue('D'.$i, $model->veiculo ? $model->veiculo->vistoriaEstadualAlerta() : '-');
    //   $sheet->setCellValue('E'.$i, $model->veiculo ? $model->veiculo->seguroAlerta() : '-');
    //   $sheet->setCellValue('F'.$i, $model->veiculo ? $model->veiculo->anoAlerta() : '-');
              $l='';
              $l .= $model->nome;
              $l .= ';'.$model->alvara;
              $l .= ';'.$model->alvara;
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
}

