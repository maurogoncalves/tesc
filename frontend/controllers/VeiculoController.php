<?php

namespace frontend\controllers;

use Yii;
use common\models\Veiculo;
use common\models\VeiculoSearch;
use common\models\TipoDocumento;
use yii\web\UploadedFile;
use common\models\DocumentoVeiculo;
use common\models\Condutor;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * VeiculoController implements the CRUD actions for Veiculo model.
 */
class VeiculoController extends Controller
{
    const left = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        )
    );
    const right = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        )
    );
    const center = array(
        'alignment' => array(
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        )
    );
    const fontSize12 = [
        'font' => [
            'size' => 12
        ]
    ];
    const fontSize14 = [
        'font' => [
            'size' => 14
        ]
    ];
    const fontSize16 = [
        'font' => [
            'size' => 16
        ]
    ];
    const borderHard = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];
    const borderSoft = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'],
            ],
        ],
    ];
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


    private function uploadMultiple($model){
            $this->actionUploadFile($model, 'documentoCRLV', TipoDocumento::TIPO_CRLV);
            $this->actionUploadFile($model, 'documentoVistoriaEstadual', TipoDocumento::TIPO_VISTORIA_ESTADUAL);
            $this->actionUploadFile($model, 'documentoVistoriaMunicipal', TipoDocumento::TIPO_VISTORIA_MUNICIPAL);
            $this->actionUploadFile($model, 'documentoApoliceSeguro', TipoDocumento::TIPO_APOLICE);
            $this->actionUploadFile($model, 'documentoDPVAT', TipoDocumento::TIPO_DPVAT);
    }


    private function uploadSingleFile($model,$file,$dbColumn){
        $arquivos = UploadedFile::getInstances($model, $file);

        if ($arquivos)
        {
            $dirBase = Yii::getAlias('@webroot').'/';
            $dir = 'arquivos/'.$file.'/';

            if (!file_exists($dirBase.$dir))
            mkdir($dir, 0777, true);

            $i = 1;
            foreach ($arquivos as $arquivo)
            {
                $i++;
                $nomeArquivo = $file.'_'.time().'_'.$model->id.'.'.$arquivo->extension;
                $arquivo->saveAs($dirBase.$dir.$nomeArquivo);
                $model->$dbColumn = $dir.$nomeArquivo;
                $model->save();
            }
        }
    }
        

      private function getDates($model){
    
        $data = \DateTime::createFromFormat ( 'd/m/Y', $model->dataVencimentoSeguro);
        if ($data)
            $model->dataVencimentoSeguro = $data->format('Y-m-d');

        $data = \DateTime::createFromFormat ( 'd/m/Y', $model->dataVistoriaMunicipal);
        if ($data)
            $model->dataVistoriaMunicipal = $data->format('Y-m-d');

        $data = \DateTime::createFromFormat ( 'd/m/Y', $model->dataVistoriaEstadual);
        if ($data)
            $model->dataVistoriaEstadual = $data->format('Y-m-d');

        $data = \DateTime::createFromFormat ( 'd/m/Y', $model->dataVencimentoCRLV);
        if ($data)
            $model->dataVencimentoCRLV = $data->format('Y-m-d');

        $model->save();
        return $model;
      }

      private function getDatesBr($model){
        
        $data = \DateTime::createFromFormat ( 'Y-m-d', $model->dataVencimentoSeguro);
        if ($data)
            $model->dataVencimentoSeguro = $data->format('d/m/Y');

        $data = \DateTime::createFromFormat ( 'Y-m-d', $model->dataVistoriaMunicipal);
        if ($data)
            $model->dataVistoriaMunicipal = $data->format('d/m/Y');
        
        $data = \DateTime::createFromFormat ( 'Y-m-d', $model->dataVistoriaEstadual);
        if ($data)
            $model->dataVistoriaEstadual = $data->format('d/m/Y');

        $data = \DateTime::createFromFormat ( 'Y-m-d', $model->dataVencimentoCRLV);
        if ($data)
            $model->dataVencimentoCRLV = $data->format('d/m/Y');
        return $model;
    }



    /**
     * Lists all Veiculo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VeiculoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = ['pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    private function actionUploadFile($model,$file, $idTipoDocumento){
        
        $arquivos = UploadedFile::getInstances($model, $file);

        if ($arquivos)
        {
            //print 'DELETED '.$idTipoDocumento;
            DocumentoVeiculo::deleteAll(['idVeiculo' => $model->id, 'idTipo' => $idTipoDocumento]);

            $dirBase = Yii::getAlias('@webroot').'/';
            $dir = 'arquivos/'.$idTipoDocumento.'/';

            if (!file_exists($dirBase.$dir))
              mkdir($dir, 0777, true);

            $i = 1;
            foreach ($arquivos as $arquivo)
            {
              $nomeArquivo = $idTipoDocumento.'_'.time().'_'.$i.'.'.$arquivo->extension;
              $arquivo->saveAs($dirBase.$dir.$nomeArquivo);

              $modelDocumento = new DocumentoVeiculo();
              $modelDocumento->nome = $nomeArquivo;
              $modelDocumento->idVeiculo = $model->id;
              $modelDocumento->arquivo = $dir.$nomeArquivo;
              $modelDocumento->idTipo = $idTipoDocumento;
              $modelDocumento->dataCadastro = date('Y-m-d H:i:s');
              $modelDocumento->save();

              $i++;
          }
        } 
    }
    /**
     * Displays a single Veiculo model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    private function setCondutor($model){
      if($model->idCondutor){
         $condutor = Condutor::findOne($model->idCondutor);
          $condutor->idVeiculo = $model->id;
          $condutor->save(false);
      }
    }
    /**
     * Creates a new Veiculo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Veiculo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->uploadMultiple($model);
            $this->setCondutor($model);
            $this->getDates($model);
            if($model->anexoFotoPlaca){
                $this->uploadSingleFile($model,'anexoFotoPlaca','fotoPlaca');
            }
            if($model->anexoFotoVeiculo){
                $this->uploadSingleFile($model,'anexoFotoVeiculo','fotoVeiculo');
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreateAjax()   
    {
        $model = new Veiculo();
        if(Yii::$app->request->get('idProprietarioEmpresa')){
            $model->idProprietarioEmpresa = Yii::$app->request->get('idProprietarioEmpresa');
        }
        if($model->load(Yii::$app->request->post())){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($model->save()) {
                $this->uploadMultiple($model);
                $this->setCondutor($model);
                $this->getDates($model);
                if($model->anexoFotoPlaca){
                    $this->uploadSingleFile($model,'anexoFotoPlaca','fotoPlaca');
                }
                if($model->anexoFotoVeiculo){
                    $this->uploadSingleFile($model,'anexoFotoVeiculo','fotoVeiculo');
                }
                return ['status' => true];
            } else {
                return ActiveForm::validate($model);
 
                // return ['status' => false, 'validation' => $model->getErrors()];
            }
        } else {
            // if($model->getErrors())
            //     Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header'=>'Erro ao salvar Veículo.']));
            return $this->renderAjax('_formAjax', [
                'model' => $model,
           
                'action' => 'veiculo/create-ajax',
            ]);
        }
    }

    public function actionViewAjax($id){
           return $this->renderAjax('view', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Updates an existing Veiculo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->uploadMultiple($model);
            $this->setCondutor($model);
              $this->getDates($model);
                  
            if($model->anexoFotoPlaca){
                $this->uploadSingleFile($model,'anexoFotoPlaca','fotoPlaca');
            }
            if($model->anexoFotoVeiculo){
                $this->uploadSingleFile($model,'anexoFotoVeiculo','fotoVeiculo');
            }
            return $this->redirect(['veiculo/index']);
        } else {
            $model = $this->getDatesBr($model);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionReportPdf()
    {
        $searchModel = new VeiculoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $arrayTipoVeiculo = [];
        foreach (Veiculo::ARRAY_TIPO_VEICULO as $key => $tipo) {
            $arrayTipoVeiculo[$tipo] = 0;
        }

        $arrayAlocacao = [];
        foreach (Veiculo::ARRAY_ALOCACAO as $key => $alocacao) {
            $arrayAlocacao[$alocacao] = 0;
        }

        $tr = '';
        foreach ($dataProvider->getModels() as $model) {
            $tr .= '<tr>';
            $tr .= $this->td(30, $model->condutor->nome);
            $tr .= $this->td(20, $model->modelo->nome);
            $tr .= $this->td(5, $model->placa);
            $tr .= $this->td(10, Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo]);
            $tr .= $this->td(10, Veiculo::ARRAY_ALOCACAO[$model->alocacao]);
            $tr .= $this->td(10, $model->capacidade);
            $tr .= $this->td(15, Veiculo::ARRAY_TIPO[$model->combustivel]);
            $tr .= '</tr>';

            $arrayTipoVeiculo[Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo]]++;
            $arrayAlocacao[Veiculo::ARRAY_ALOCACAO[$model->alocacao]]++;
        }

        $content = '';

        $content .= '<center><h3>Quantidades por tipo de veículo e alocação</h3></center>';
        $content .= '<div class="row">';
        $content .= '<div class="col-md-6 col-xs-6 col-lg-6">';
        $content .= '<table border="0" class="table">';
        $content .= '
        <tr>
            <th><b>Tipo de veículo</b></th>
            <th><b>Quantidade</b></th>
        </tr>';

        foreach ($arrayTipoVeiculo as $key=>$value) {
            $content .= '<tr>';
            $content .= $this->td(50, $key);
            $content .= $this->td(50, $value);
            $content .= '</tr>';
        }
        $content .= '</table>';
        $content .= '</div>';

        $content .= '<div class="col-md-6">';
        $content .= '<table border="0" class="table">';
        $content .= '
        <tr>
            <th><b>Alocação do Veículo</b></th>
            <th><b>Quantidade</b></th>
        </tr>';

        foreach ($arrayAlocacao as $key=>$value) {
            $content .= '<tr>';
            $content .= $this->td(50, $key);
            $content .= $this->td(50, $value);
            $content .= '</tr>';
        }
        $content .= '</table>';

        $content .= '</div>';
        $content .= '</div>';


        $content .= '<table border="0" width="100%" class="table">';
        $content .= '
        <tr>
            <th><b>Condutor</b></th>
            <th><b>Modelo</b></th>
            <th><b>Placa</b></th>
            <th><b>Tipo de veículo</b></th>
            <th><b>Alocação do Veículo</b></th>
            <th><b>Capacidade</b></th>
            <th><b>Combustível</b></th>
        </tr>';
        
        $content .= $tr;
        // foreach ($dataProvider->getModels() as $model) {
        //     $content .= '<tr>';
        //     $content .= $this->td(30, $model->condutor->nome);
        //     $content .= $this->td(20, $model->modelo->nome);
        //     $content .= $this->td(5, $model->placa);
        //     $content .= $this->td(10, Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo]);
        //     $content .= $this->td(10, Veiculo::ARRAY_ALOCACAO[$model->alocacao]);
        //     $content .= $this->td(10, $model->capacidade);
        //     $content .= $this->td(15, Veiculo::ARRAY_TIPO[$model->combustivel]);
        //     $content .= '</tr>';

        //     $arrayTipoVeiculo[Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo]]++;
        //     $arrayAlocacao[Veiculo::ARRAY_ALOCACAO[$model->alocacao]]++;
        // }
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

    public function actionReportXls()
    {
        $searchModel = new VeiculoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $arrayTipoVeiculo = [];
        foreach (Veiculo::ARRAY_TIPO_VEICULO as $key => $tipo) {
            $arrayTipoVeiculo[$tipo] = 0;
        }

        $arrayAlocacao = [];
        foreach (Veiculo::ARRAY_ALOCACAO as $key => $alocacao) {
            $arrayAlocacao[$alocacao] = 0;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);    
        // $sheet = $spreadsheet->createSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('CONTROLE FINANCEIRO');
        $i = 1;
        $this->cabecalho($sheet,$i, $data);

        foreach ($dataProvider->getModels() as $model) {
            $arrayTipoVeiculo[Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo]]++;
            $arrayAlocacao[Veiculo::ARRAY_ALOCACAO[$model->alocacao]]++;
        }

        $i+=9;
        $sheet->getRowDimension($i)->setRowHeight(60);
        $sheet->getStyle('A'.$i.':B'.$i)->applyFromArray(self::center)->getFont()->setBold(true);
        $sheet->getStyle('A'.$i.':B'.$i)->applyFromArray(self::borderSoft);
        $sheet->setCellValue('A'.$i, "TIPO DE VEÍCULO");
        $sheet->setCellValue('B'.$i, 'QUANTIDADE');

        $i2 = $i+1;
        foreach ($arrayTipoVeiculo as $key=>$value) {
            $sheet->setCellValue('A'.$i2, $key);
            $sheet->setCellValue('B'.$i2, $value);
            $sheet->getStyle('B'.$i2)->applyFromArray(self::center);
            $i2++;
        }
        
        $sheet->getStyle('D'.$i.':E'.$i)->applyFromArray(self::center)->getFont()->setBold(true);
        $sheet->getStyle('D'.$i.':E'.$i)->applyFromArray(self::borderSoft);
        $sheet->setCellValue('D'.$i, "ALOCAÇÃO DO VEÍCULO");
        $sheet->setCellValue('E'.$i, "QUANTIDADE");

        $i3 = $i+1;
        foreach ($arrayAlocacao as $key=>$value) {
            $sheet->setCellValue('D'.$i3, $key);
            $sheet->setCellValue('E'.$i3, $value);
            $sheet->getStyle('E'.$i3)->applyFromArray(self::center);
            $i3++;
        }

        $i = ($i2>$i3) ? $i2 : $i3;
        
        // COLUNAS DO CABEÇALHO
        $i+=7;
        $sheet->getRowDimension($i)->setRowHeight(60);
        $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray(self::center)->getFont()->setBold(true);
        $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray(self::borderSoft);
        $sheet->setCellValue('A'.$i, "CONDUTOR");
        $sheet->setCellValue('B'.$i, 'MODELO');
        $sheet->setCellValue('C'.$i, "PLACA");
        $sheet->setCellValue('D'.$i, "TIPO DE VEÍCULO");        
        $sheet->setCellValue('E'.$i, "ALOCAÇÃO DO VEÍCULO");
        $sheet->setCellValue('F'.$i, "CAPACIDADE");
        $sheet->setCellValue('G'.$i, "COMBUSTÍVEL");        
        
        foreach ($dataProvider->getModels() as $model) {
            $i++;        
            $sheet->setCellValue('A'.$i, $model->condutor->nome);
            $sheet->setCellValue('B'.$i, $model->modelo->nome);
            $sheet->setCellValue('C'.$i, $model->placa);
            $sheet->setCellValue('D'.$i, Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo]);
            $sheet->setCellValue('E'.$i, Veiculo::ARRAY_ALOCACAO[$model->alocacao]);
            $sheet->setCellValue('F'.$i, $model->capacidade);
            $sheet->getStyle('F'.$i)->applyFromArray(self::center);
            $sheet->setCellValue('G'.$i, Veiculo::ARRAY_TIPO[$model->combustivel]);
            $sheet->getStyle('G'.$i)->applyFromArray(self::center);

            $arrayTipoVeiculo[Veiculo::ARRAY_TIPO_VEICULO[$model->tipoVeiculo]]++;
            $arrayAlocacao[Veiculo::ARRAY_ALOCACAO[$model->alocacao]]++;
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

    private function cabecalho(&$sheet, &$i, $data) {
        $start = $i;
    
        $colorRed =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED );
        $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE );

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath(Yii::getAlias('@webroot').'/img/brasao.png'); // put your path and image here
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(100);
        $drawing->setOffsetY(15);
        $drawing->setRotation(0);
        $drawing->setWorksheet($sheet);

        $drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing2->setPath(Yii::getAlias('@webroot').'/img/faixa.png'); // put your path and image here
        $drawing2->setCoordinates('B1');
        $drawing2->setOffsetX(1);
        $drawing2->setOffsetY(1);
        $drawing2->setRotation(0);
        $drawing2->setWorksheet($sheet);

        // SETUP DAS COLUNAS
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(24);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(16);
        $sheet->getColumnDimension('H')->setWidth(13);
        $sheet->getColumnDimension('I')->setWidth(32);

        //
        $i = 1;
        // PRÓXIMA LINHA
        $sheet->mergeCells('A'.$i.':A'.($i+9));
        $sheet->getStyle('A'.$i.':A'.($i+9))->applyFromArray(self::borderSoft);
        $sheet->mergeCells('B'.$i.':G'.$i);
        
        $i++;
        $sheet->mergeCells('B'.$i.':G'.($i+1));
        $sheet->getStyle('B'.$i.':G'.($i+1))->applyFromArray(self::borderSoft);
        $sheet->setCellValue('B'.$i, "Secretaria de Educação e Cidadania");
        $sheet->getStyle('B'.$i)->applyFromArray(self::left)->getFont()->setBold(true);

        $i+=2;
        $sheet->mergeCells('B'.$i.':G'.($i+6));
        $sheet->getStyle('B'.$i.':G'.($i+6))->applyFromArray(self::borderSoft);
        $sheet->setCellValue('B'.$i, "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
        $sheet->getStyle('B'.$i)->getAlignment()->setWrapText(true);
        $sheet->getStyle('B'.$i)->applyFromArray(self::left);

        // $sheet->getStyle('A'.($i+2).':G'.($i+2))->applyFromArray($left);

        // $i+=5;
        // $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray($center);
    }

    public function actionSearchAjax($idCondutor){
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $condutor = Condutor::findOne($idCondutor);

      return Veiculo::findOne($condutor->idVeiculo);
    }
    /**
     * Deletes an existing Veiculo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Veiculo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Veiculo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Veiculo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionArquivos($id, $tipo){

        DocumentoVeiculo::deleteAll(['idVeiculo' => $id, 'idTipo' => $tipo]);   
        return $this->redirect(['view', 'id' => $id]);
    }
}
