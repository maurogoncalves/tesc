<?php

namespace frontend\controllers;

use common\models\SolicitacaoTransporte;
use common\models\SolicitacaoStatus;
use common\models\Aluno;
use common\models\Escola;
use common\models\Veiculo;
use common\models\Condutor;
use common\models\Log;
use common\models\HistoricoMovimentacaoRota;
use common\models\CondutorRota;
use common\models\AgrupamentoBairro;
use common\models\SolicitacaoTransporteSearch;
use common\models\Bairro;
use common\models\SolicitacaoCredito;
use common\models\Usuario;
use Exception;
use yii\helpers\ArrayHelper;
use Yii;
use kartik\mpdf\Pdf;
use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class PainelAtendimentoController extends \yii\web\Controller
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
    protected $session;
    public function init()
    {
        parent::init();
        $this->session = Yii::$app->session;
        $this->session->open();
    }
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
    
    public function actionReportHistoricoAtendimento()
    {
        $contentBefore = '';
        $dados = $this->session->get('historicos');
        $condutor = $this->session->get('condutor');
        $periodoInicio = $this->session->get('periodoInicio');
        $periodoFim = $this->session->get('periodoFim');

        // print_r($dados);
        $this->sortDadosHistoricoAtendimento($dados);
        if (!$dados)
            $dados = [];
        if ($periodoInicio) {
            $d = $this->explodeData($periodoInicio);
            $periodoInicio = date("d/m/Y", strtotime($d[0])) . ' - ' . date("d/m/Y", strtotime($d[1]));
        } else {
            $periodoInicio = '-';
        }
        if ($periodoFim) {
            $d = $this->explodeData($periodoFim);
            $periodoFim = date("d/m/Y", strtotime($d[0])) . ' - ' . date("d/m/Y", strtotime($d[1]));
        } else {
            $periodoFim = '-';
        }
        //         ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        // print $_GET['escola'];
        // print_r(Escola::find()->where(['id' => $_GET['escola']])->one() );
        $content = '';
        $content =  '<table width="100%">
      <tr>
        <td><b>CONDUTOR: </b>' . $condutor->nome . '</td>
        <td><b>ALVARÁ: </b>' . $condutor->alvara . '</td>
        <td><b>TELEFONE: </b>' . $condutor->telefone . '</td>
      </tr>
      <tr>
      <td><b>PERÍODO INÍCIO: </b>' . $periodoInicio . '</td>
      <td><b>PERÍODO FIM: </b>' . $periodoFim . '</td>

    </tr>
      </table>';
        $content .= '<table border="0" width="100%" class="table">';
        $content .= '
        <tr>
            <th align="center"><b>ESCOLA</b></th>
            <th align="center"><b>NOME DO ALUNO</b></th>
            <th align="center"><b>RA</b></th>
            <th align="center"><b>ENT</b></th>
            <th align="center"><b>SAI</b></th>
            <th align="center"><b>ANO/SÉRIE E TURMA</b></th>
            <th align="center"><b>ENDEREÇO</b></th>
            <th align="center"><b>BAIRRO</b></th>
            <th align="center"><b>TEL.</b></th>
            <th align="center"><b>NECESSIDADES</b></th>
            <th align="center"><b>INÍCIO DO ATENDIMENTO</b></th>
            <th align="center"><b>FIM DO ATENDIMENTO</b></th>
        </tr>';

        foreach ($dados as $model) {
            $content .= '<tr>';
            $content .= $this->td(15, $model->aluno->escola->nomeCompleto);
            $content .= $this->td(20, $model->aluno->nome);
            $content .= $this->td(5, $model->aluno->RA . '-' . $model->aluno->RAdigito);
            $content .= $this->tdCenter(7, $model->entrada);
            $content .= $this->tdCenter(7, $model->saida);
            $content .= $this->tdCenter(7, Aluno::ARRAY_SERIES[$model->aluno->serie] . '/' . Aluno::ARRAY_TURMA[$model->aluno->turma]);
            $content .= $this->td(20, $model->aluno->tipoLogradouro . ' ' . $model->aluno->endereco . ' Nº ' . $model->aluno->numeroResidencia);
            $content .= $this->td(10, $model->aluno->bairro);
            $content .= $this->td(7, Yii::$app->formatter->asTelefone($this->getTelefoneValido($model->aluno)));
            // $content .= $this->td(7, $this->getTelefoneValido($model->aluno));
            $content .= $this->tdCenter(10, $this->getNecessidades($model->aluno));
            $content .= $this->tdCenter(10, $model->inicioAtendimento);
            $content .= $this->tdCenter(10, $model->fimAtendimento);


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

    public function actionReportPesquisaAtendimento()
    {
		
        $contentBefore = '';
        $dados = $this->session->get('historicos');
        $condutor = $this->session->get('condutor');
        $periodo = $this->session->get('periodo');
		$get = Yii::$app->request->get();
		
		$tipo = $get['tipo'];

        //sort feito pela query
        // $this->sortDadosHistoricoAtendimento($dados);
        if (!$dados)
            $dados = [];
        if ($periodo) {
            $d = $this->explodeData($periodo);
            $periodo = date("d/m/Y", strtotime($d[0])) . ' - ' . date("d/m/Y", strtotime($d[1]));
        } else {
            $periodo = '-';
        }
		
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
		$drawing->setOffsetY(20);
		$drawing->setRotation(0);
		$drawing->setHeight(120);
		$drawing->setWidth(150);
		$drawing->setWorksheet($sheet);

		$drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$drawing2->setPath(Yii::getAlias('@webroot').'/img/faixa.png'); // put your path and image here
		$drawing2->setCoordinates('C1');
		$drawing2->setOffsetX(0);
		$drawing2->setOffsetY(0);
		$drawing2->setRotation(0);
		$drawing2->setWorksheet($sheet);


		// SETUP DAS COLUNAS
		$sheet->getColumnDimension('A')->setWidth(10);
		$sheet->getColumnDimension('B')->setWidth(40);
		$sheet->getColumnDimension('C')->setWidth(15);
		$sheet->getColumnDimension('D')->setWidth(15);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(50);
		$sheet->getColumnDimension('I')->setWidth(50);
		$sheet->getColumnDimension('J')->setWidth(50);
		
		
		
		//
		$i = 1;
		// PRÓXIMA LINHA
		
		$content =  ' CONDUTOR: ' . $condutor->nome . ' - ALVARÁ: ' . $condutor->alvara . ' - TELEFONE: ' . $condutor->telefone . ' - PERÍODO: ' . $periodo;
	    $sheet->mergeCells('A'.$i.':B'.($i+6));
		$sheet->mergeCells('C'.$i.':J'.$i);
		$sheet->mergeCells('C'.($i+1).':J'.($i+1));
		$sheet->setCellValue('C'.($i+1), "Secretaria de Educação e Cidadania");
		$sheet->getStyle('C'.($i+1))->applyFromArray($left)->getFont()->setBold(true);

		$sheet->mergeCells('C'.($i+2).':J'.($i+6));
		$sheet->setCellValue('C'.($i+2), "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165\n\n".$content);
		$sheet->getStyle('C'.($i+2))->getAlignment()->setWrapText(true);

		$sheet->getStyle('A'.($i+2).':J'.($i+2))->applyFromArray($left);
		
		
		$i=8;
		// $sheet->mergeCells('A'.$i.':B'.($i));
		// $sheet->mergeCells('C'.$i.':J'.$i);
		// $sheet->mergeCells('C'.($i+1).':J'.($i+1));
		// $sheet->setCellValue('C'.($i), 'teste');
		
		// $sheet->getStyle('A'.($i).':J'.($i))->applyFromArray($left);
		
		// $i+=2;
		
		$sheet->getStyle('A'.$i.':I'.$i)->applyFromArray($center);  
		$sheet->setCellValue('A'.$i, "Id");
		$sheet->setCellValue('B'.$i, "Aluno");
		$sheet->setCellValue('C'.$i, "RA");
		$sheet->setCellValue('D'.$i, "Data");
		$sheet->setCellValue('E'.$i, "Entrada");
		$sheet->setCellValue('F'.$i, "Saída");
		$sheet->setCellValue('G'.$i, "Ano/Série e Turma");
		$sheet->setCellValue('H'.$i, "Endereço");	
		$sheet->setCellValue('I'.$i, "Bairro");	
		$sheet->setCellValue('J'.$i, "Escola");	
		

		$sheet->getStyle('A'.$i.':J'.$i)
		->getAlignment()->setWrapText(true);
		$sheet->getStyle('A'.$i.':J'.$i)->getFill()
		->setFillType(Fill::FILL_SOLID)
		->getStartColor()->setARGB('FF000000');

		$sheet->getStyle('A'.$i.':J'.$i)->getFont()->setBold(true);

		$sheet->getStyle('A'.$i.':J'.$i)->getFont()->setColor( $colorWhite );
		$sheet->setAutoFilter('A'.$i.':J'.$i);
			
			foreach ($dados as $model) {
				  $i++;
				if($i % 2 == 0) {
					$sheet->getStyle('A'.$i.':J'.$i)->getFill()
					->setFillType(Fill::FILL_SOLID)
					->getStartColor()->setRGB('F6F6F6');
				
				}
				$sheet->getStyle('B'.$i.':J'.$i)->applyFromArray($center);
				$sheet->getStyle('A'.$i.':J'.$i)->applyFromArray($borderSoft);
				$sheet->getStyle('A'.$i.':J'.$i)->getAlignment()->setWrapText(true);
				$sheet->setCellValue('A'.$i, $model['id_aluno']);
				$sheet->setCellValue('B'.$i, $model['aluno']);
				$sheet->setCellValue('C'.$i, $model['RA'].' '.$model['RAdigito']);
				$sheet->setCellValue('D'.$i, $model['criacao']);
				$sheet->setCellValue('E'.$i, $model['horarioEntrada']);
				$sheet->setCellValue('F'.$i, $model['horarioSaida']);
				$sheet->setCellValue('G'.$i, Aluno::ARRAY_SERIES[$model['serie']].'/'.Aluno::ARRAY_TURMA[$model['turma']]);
				$sheet->setCellValue('H'.$i, $model['tipoLogradouro'].' '.$model['endereco'].', '.$model['numeroResidencia']);
				$sheet->setCellValue('I'.$i, $model['bairro']);
				$sheet->setCellValue('J'.$i, $model['escola']);
			}
			
			
		$base = "arquivos/_exportacoes/";
		switch($tipo){		 
			case 'EXCEL':
                try {
                    $writer = new Xlsx($spreadsheet);
                    $filename = $base."Pesquisa_Atendimento_".date('d-m-Y-H-i-s').".xlsx";
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
                    $this->actionReportPesquisaAtendimentoTxtECsv($base, $dados, $tipo, $condutor, $periodo);
                break;	
                case 'CSV':
                    $this->actionReportPesquisaAtendimentoTxtECsv($base, $dados, $tipo, $condutor, $periodo);
                break;
		}	
		
    }

    public function actionReportPesquisaAtendimentoTxtECsv($base, $dados, $tipo, $condutor, $periodo)
    {
        
        $filename = $base . 'Pesquisa_Atendimento_' . date('d-m-Y-H-i-s') . '.'. strtolower($tipo);
        $fp = fopen($filename, 'a');  

        //corrige erro de exibição em UTF8 para CSV e TXT:
        fwrite($fp, pack("CCC",0xef,0xbb,0xbf));                  

        foreach ($dados as $model) {
            $l = '';
            $l .= $model['id_aluno'];
            $l .= ';' . $model['aluno'];
            $l .= ';' . $model['RA'].' '.$model['RAdigito'];
            $l .= ';' . $model['criacao'];
            $l .= ';' . $model['horarioEntrada'];
            $l .= ';' . $model['horarioSaida'];
            $l .= ';' . Aluno::ARRAY_SERIES[$model['serie']].'/'.Aluno::ARRAY_TURMA[$model['turma']];
            $l .= ';' . $model['tipoLogradouro'].' '.$model['endereco'].', '.$model['numeroResidencia'];
            $l .= ';' . $model['bairro'];
            $l .= ';' . $model['escola'];
            $l .= ';' . $condutor["nome"];
            $l .= ';' . $periodo;
            $l .= '
';//espaço acima é a quebra linha

            fwrite($fp, $l);
      }
       
        fclose($fp);
        try {
            // $writer = new Xlsx($spreadsheet);
            header("Content-Disposition: attachment; filename=" . $filename);
            $content = file_get_contents($filename);
            unlink($filename);
            exit($content);
        } catch (\Exception $e) {
            exit($e->getMessage());
        }

    }
    
	public function actionReportPesquisaAtendimentoPdf()
    {
		
        $contentBefore = '';
        $dados = $this->session->get('historicos');
        $condutor = $this->session->get('condutor');
        $periodo = $this->session->get('periodo');

        //sort feito pela query
        // $this->sortDadosHistoricoAtendimento($dados);
        if (!$dados)
            $dados = [];
        if ($periodo) {
            $d = $this->explodeData($periodo);
            $periodo = date("d/m/Y", strtotime($d[0])) . ' - ' . date("d/m/Y", strtotime($d[1]));
        } else {
            $periodo = '-';
        }

        $content = '';
        $content =  '<table width="100%">
      <tr>
        <td><b>CONDUTOR: </b>' . $condutor->nome . '</td>
        <td><b>ALVARÁ: </b>' . $condutor->alvara . '</td>
        <td><b>TELEFONE: </b>' . $condutor->telefone . '</td>
      </tr>
      <tr>
      <td><b>PERÍODO: </b>' . $periodo . '</td>

    </tr>
      </table>';
        $content .= '<table border="0" width="100%" class="table">';
        $content .= '
        <tr>			
            <th align="center"><b>Id</b></th>
            <th align="center"><b>Aluno</b></th>
            <th align="center"><b>RA</b></th>
            <th align="center"><b>Data</b></th>
            <th align="center"><b>Entrada</b></th>
            <th align="center"><b>Saída</b></th>
			<th align="center"><b>Ano/Série e Turma</b></th>
            <th align="center"><b>Endereço</b></th>
            <th align="center"><b>Bairro</b></th>
            <th align="center"><b>Escola</b></th>
        </tr>';

        foreach ($dados as $model) {
            $content .= '<tr>';
			$content .= $this->td(6, $model['id_aluno']);
			$content .= $this->td(34, $model['aluno']);
			$content .= $this->td(10, $model['RA'].' '.$model['RAdigito']);
			$content .= $this->td(7, $model['criacao']);
			$content .= $this->td(7, $model['horarioEntrada']);
			$content .= $this->td(7, $model['horarioSaida']);
			$content .= $this->td(15, Aluno::ARRAY_SERIES[$model['serie']].'/'.Aluno::ARRAY_TURMA[$model['turma']]);
            $content .= $this->td(31,$model['tipoLogradouro'].' '.$model['endereco'].', '.$model['numeroResidencia']);
			$content .= $this->td(20, $model['bairro']);
			$content .= $this->td(20, $model['escola']);
			

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
			 'filename' => 'Pesquisa_Atendimento',
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
            'options' => ['title' => 'Pesquisa de Atendimento'],
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
		$mpdf->Output('filename.pdf', \Mpdf\Output\Destination::FILE);

    }
	
    public function actionIndex()
    {
        $camposOrdenacao = [
            'Escola.nome A-Z' => 'Escola A-Z',
            'Escola.nome Z-A' => 'Escola Z-A',
            'Aluno.nome Z-A' => 'Nome Z-A',
            'Aluno.nome A-Z' => 'Nome A-Z',
            'Aluno.horarioEntrada A-Z' => 'Horário de entrada - A-Z',
            'Aluno.horarioEntrada Z-A' => 'Horário de entrada - Z-A',
            'Aluno.horarioSaida A-Z' => 'Horário de saída - A-Z',
            'Aluno.horarioSaida Z-A' => 'Horário de saída - Z-A',
            'Aluno.horarioSaida A-Z' => 'Horário de saída - A-Z',
            'Aluno.horarioSaida Z-A' => 'Horário de saída - Z-A',
            'Aluno.RA A-Z' => 'RA - A-Z',
            'Aluno.RA Z-A' => 'RA - Z-A',
            'Aluno.endereco A-Z' => 'Endereço - A-Z',
            'Aluno.endereco Z-A' => 'Endereço - Z-A',
            'Aluno.bairro A-Z' => 'Bairro - A-Z',
            'Aluno.bairro Z-A' => 'Bairro - Z-A',
            'Aluno.serie A-Z' => 'Ano/Série - A-Z',
            'Aluno.serie Z-A' => 'Ano/Série - Z-A',
            'Aluno.turma A-Z' => 'Turma - A-Z',
            'Aluno.turma Z-A' => 'Turma - Z-A',
            'CondutorEntrada.nome A-Z' => 'Condutor Entrada - A-Z',
            'CondutorEntrada.nome Z-A' => 'Condutor Entrada - Z-A',
            'CondutorSaida.nome A-Z' => 'Condutor Saída - A-Z',
            'CondutorSaida.nome Z-A' => 'Condutor Saída - Z-A',
            'SolicitacaoStatus.dataCadastro A-Z' => 'Início - A-Z',
            'SolicitacaoStatus.dataCadastro Z-A' => 'Início - Z-A',
            'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) A-Z' => 'Necessidade Especial COM-SEM',
            'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) Z-A' => 'Necessidade Especial SEM-COM',
        ];

        if ($get = Yii::$app->request->get()) {


            $st = SolicitacaoTransporte::find()
                ->andWhere(['<>', 'SolicitacaoTransporte.tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
                ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_ATENDIDO])
                ->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
                ->innerJoin('Aluno', 'Aluno.id=SolicitacaoTransporte.idAluno')
                ->join('LEFT JOIN `CondutorRota` AS `RotaEntrada` ON', '`RotaEntrada`.`id` = `SolicitacaoTransporte`.`idRotaIda`')
                ->join('LEFT JOIN `CondutorRota` AS `RotaSaida` ON', '`RotaSaida`.`id` = `SolicitacaoTransporte`.`idRotaVolta`')
                ->join('LEFT JOIN `Condutor` AS `CondutorEntrada` ON', '`CondutorEntrada`.`id` = `RotaEntrada`.`idCondutor`')
                ->join('LEFT JOIN `Condutor` AS `CondutorSaida` ON', '`CondutorSaida`.`id` = `RotaSaida`.`idCondutor`')
                ->join('LEFT JOIN `AlunoNecessidadesEspeciais` ON', '`AlunoNecessidadesEspeciais`.`idAluno` = `Aluno`.`id`')
                //->join('LEFT JOIN `SolicitacaoStatus` ON', '`SolicitacaoStatus`.`idSolicitacaoTransporte` = `SolicitacaoTransporte`.`id`')
                //->andWhere(['=', 'SolicitacaoStatus.status', SolicitacaoTransporte::STATUS_ATENDIDO])
				;

            if ($get['tipoFrete']) $st->andWhere(['SolicitacaoTransporte.tipoFrete' => $get['tipoFrete']]);
            if ($get['unidade']) $st->andWhere(['Escola.unidade' => $get['unidade']]);
            if ($get['escola']) {
                $st->andWhere(['SolicitacaoTransporte.idEscola' => $get['escola']]);
            } else {
                $escolas = [];
                switch (\Yii::$app->User->identity->idPerfil) {
                    case Usuario::PERFIL_SECRETARIO:
                        foreach (\Yii::$app->User->identity->secretarios as $registro)
                            array_push($escolas, $registro->escola);
                        break;
                    case Usuario::PERFIL_DIRETOR:
                        foreach (\Yii::$app->User->identity->diretores as $registro)
                            array_push($escolas, $registro->escola);
                        break;
                    case Usuario::PERFIL_DRE:
                        foreach (Escola::find()->where(['Escola.unidade' => Escola::UNIDADE_ESTADUAL])->all() as $registro)
                            array_push($escolas, $registro);
                        break;
                    default:
                        $escolas = Escola::find()->rightJoin('Aluno', 'Aluno.idEscola=Escola.id')->all();
                        break;
                }
                $ids = array_column($escolas, 'id');
                $st->andWhere(['in', 'SolicitacaoTransporte.idEscola', $ids]);
            }
            if ($get['periodo']) $this->searchPeriodo($get['periodo'], $st);
            if ($get['regiao']) $st->andWhere(['Escola.regiao' => $get['regiao']]);
            if ($get['bairro']) $st->andWhere(['Aluno.bairro' => $get['bairro']]);
            if ($get['horarioEntrada']) $st->andWhere(['Aluno.horarioEntrada' => $get['horarioEntrada'] . ':00']);
            if ($get['horarioSaida']) $st->andWhere(['Aluno.horarioSaida' => $get['horarioSaida'] . ':00']);

            $multipleOrder = $this->applySort($get);
            if (!$multipleOrder) {
                $multipleOrder['Aluno.nome'] = SORT_ASC;
            }
			
            $st->orderBy($multipleOrder);



            if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE) {
                $st->andWhere(['Escola.tipo' => Escola::TIPO_EE]);
            }
            $st = $st->distinct(true);
            $st = $st->all();
    
	
            $solicitacoes = $st;
			
            if ($get['idCondutor']) {
                $solicitacoes = [];
                $rotas = CondutorRota::find()->where(['idCondutor' => $get['idCondutor']])->all();
                $idsRotas = array_column($rotas, 'id');
                foreach ($st as $solicitacao) {
                    if (in_array($solicitacao->idRotaVolta, $idsRotas) || in_array($solicitacao->idRotaIda, $idsRotas))
                        $solicitacoes[] = $solicitacao;
                }
            }

            $escolas = [];
            switch (\Yii::$app->User->identity->idPerfil) {
                case Usuario::PERFIL_SECRETARIO:
                    foreach (\Yii::$app->User->identity->secretarios as $registro)
                        array_push($escolas, $registro->escola);
                    break;
                case Usuario::PERFIL_DIRETOR:
                    foreach (\Yii::$app->User->identity->diretores as $registro)
                        array_push($escolas, $registro->escola);
                    break;
                case Usuario::PERFIL_DRE:
                    foreach (Escola::find()->where(['Escola.unidade' => Escola::UNIDADE_ESTADUAL])->all() as $registro)
                        array_push($escolas, $registro);
                    break;
                default:
                    $escolas = Escola::find()->rightJoin('Aluno', 'Aluno.idEscola=Escola.id')->all();
                    break;
            }

            if (Yii::$app->request->get('report') == 'pdf')
                return $this->alunosAtendidosFretePdf($solicitacoes);

            if (Yii::$app->request->get('report') == 'xls')
                return $this->alunosAtendidosFreteXls($solicitacoes);

			
            return $this->render('index', [
                'solicitacoesTransporte' => $solicitacoes,
                'bairros' => ArrayHelper::map(AgrupamentoBairro::find()->all(), 'nome', 'nome'),
                'condutores' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'),
                'tiposFrete' => SolicitacaoTransporte::ARRAY_TIPO_FRETE,
                'unidades' => Escola::ARRAY_UNIDADE,
                'escolas' =>  ArrayHelper::map($escolas, 'id', 'nome'),
                'regioes' => Escola::ARRAY_REGIAO,
                'camposOrdenacao' => $camposOrdenacao,
            ]);
        }
        $this->redirect(['index']);
    }
    public function applySort($get = null)
    {
        $multipleOrder = [];

        for ($i = 0; $i <= 9; $i++) {
            if ($get && isset($get['order-' . $i]) && $get['order-' . $i] != '') {
                $mySort = $get['order-' . $i];

                $sort = explode(' ', $mySort);
                if ($sort[1] == 'A-Z')
                    $order = SORT_ASC;
                else
                    $order = SORT_DESC;
                $multipleOrder[$sort[0]] = $order;
            }
        }

        return $multipleOrder;
    }
    public function actionAlunosAtendidosPasseEscolar()
    {
        $camposOrdenacao = [
            'Escola.nome A-Z' => 'Escola A-Z',
            'Escola.nome Z-A' => 'Escola Z-A',
            'Aluno.nome Z-A' => 'Nome Z-A',
            'Aluno.nome A-Z' => 'Nome A-Z',
            'Aluno.horarioEntrada A-Z' => 'Horário de entrada - A-Z',
            'Aluno.horarioEntrada Z-A' => 'Horário de entrada - Z-A',
            'Aluno.horarioSaida A-Z' => 'Horário de saída - A-Z',
            'Aluno.horarioSaida Z-A' => 'Horário de saída - Z-A',
            'Aluno.horarioSaida A-Z' => 'Horário de saída - A-Z',
            'Aluno.horarioSaida Z-A' => 'Horário de saída - Z-A',
            'Aluno.RA A-Z' => 'RA - A-Z',
            'Aluno.RA Z-A' => 'RA - Z-A',
            'Aluno.endereco A-Z' => 'Endereço - A-Z',
            'Aluno.endereco Z-A' => 'Endereço - Z-A',
            'Aluno.bairro A-Z' => 'Bairro - A-Z',
            'Aluno.bairro Z-A' => 'Bairro - Z-A',
            'Aluno.serie A-Z' => 'Ano/Série - A-Z',
            'Aluno.serie Z-A' => 'Ano/Série - Z-A',
            'Aluno.turma A-Z' => 'Turma - A-Z',
            'Aluno.turma Z-A' => 'Turma - Z-A',
            'SolicitacaoStatus.dataCadastro A-Z' => 'Início - A-Z',
            'SolicitacaoStatus.dataCadastro Z-A' => 'Início - Z-A',
            'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) A-Z' => 'Necessidade Especial COM-SEM',
            'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) Z-A' => 'Necessidade Especial SEM-COM',
        ];

        if ($get = Yii::$app->request->get()) {


            $st = SolicitacaoTransporte::find()
                ->andWhere(['<>', 'SolicitacaoTransporte.tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
                ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_CONCEDIDO])
                ->andWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE])
                ->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
                ->innerJoin('Aluno', 'Aluno.id=SolicitacaoTransporte.idAluno')
                //->join('LEFT JOIN `AlunoNecessidadesEspeciais` ON', '`AlunoNecessidadesEspeciais`.`idAluno` = `Aluno`.`id`')
                //->join('LEFT JOIN `SolicitacaoStatus` ON', '`SolicitacaoStatus`.`idSolicitacaoTransporte` = `SolicitacaoTransporte`.`id`')
				;

			
            //   if ($get['modalidadeBeneficio'] == 1){
            //     $st->andWhere(['<>','SolicitacaoTransporte.cartaoPasseEscolar', '']);
            //   }
            //   if ($get['modalidadeBeneficio'] == 2){
            //     $st->andWhere(['<>','SolicitacaoTransporte.cartaoValeTransporte', '']);
            //   }

            if ($get['unidade']) $st->andWhere(['Escola.unidade' => $get['unidade']]);
            if ($get['escola']) {				
                $st->andWhere(['SolicitacaoTransporte.idEscola' => $get['escola']]);
            } else {
                $escolas = [];
                switch (\Yii::$app->User->identity->idPerfil) {
                    case Usuario::PERFIL_SECRETARIO:
                        foreach (\Yii::$app->User->identity->secretarios as $registro)
                            array_push($escolas, $registro->escola);
                        break;
                    case Usuario::PERFIL_DIRETOR:
                        foreach (\Yii::$app->User->identity->diretores as $registro)
                            array_push($escolas, $registro->escola);
                        break;
                    case Usuario::PERFIL_DRE:
                        foreach (Escola::find()->where(['Escola.unidade' => Escola::UNIDADE_ESTADUAL])->all() as $registro)
                            array_push($escolas, $registro);
                        break;
                    default:
                        $escolas = Escola::find()->rightJoin('Aluno', 'Aluno.idEscola=Escola.id')->all();
                        break;
                }
                $ids = array_column($escolas, 'id');
                $st->andWhere(['in', 'SolicitacaoTransporte.idEscola', $ids]);
            }
            if ($get['periodo']) $this->searchPeriodo($get['periodo'], $st);
            if ($get['regiao']) $st->andWhere(['Escola.regiao' => $get['regiao']]);
            if ($get['bairro']) $st->andWhere(['Aluno.bairro' => $get['bairro']]);
            if ($get['horarioEntrada']) $st->andWhere(['Aluno.horarioEntrada' => $get['horarioEntrada'] . ':00']);
            if ($get['horarioSaida']) $st->andWhere(['Aluno.horarioSaida' => $get['horarioSaida'] . ':00']);

            $multipleOrder = $this->applySort($get);
            if (!$multipleOrder) {
                $multipleOrder['Aluno.nome'] = SORT_ASC;
            }
            $st->orderBy($multipleOrder);

            $st = $st->all();

			
            $solicitacoes = $st;
			
            if ($get['modalidadeBeneficio'] && $get['modalidadeBeneficio'] != '') {
                $solicitacoes = [];
                // [1 => 'Passe Escolar', 2 => 'Vale Transporte']
                foreach ($st as $solicitacao) {
                    if ($get['modalidadeBeneficio'] == 1 && $solicitacao->aluno->temPasseEscolar())
                        $solicitacoes[] = $solicitacao;
                    if ($get['modalidadeBeneficio'] == 2 && $solicitacao->aluno->temValeTransporte())
                        $solicitacoes[] = $solicitacao;
                }
            }

            $escolas = [];
            switch (\Yii::$app->User->identity->idPerfil) {
                case Usuario::PERFIL_SECRETARIO:
                    foreach (\Yii::$app->User->identity->secretarios as $registro)
                        array_push($escolas, $registro->escola);
                    break;
                case Usuario::PERFIL_DIRETOR:
                    foreach (\Yii::$app->User->identity->diretores as $registro)
                        array_push($escolas, $registro->escola);
                    break;
                case Usuario::PERFIL_DRE:
                    foreach (Escola::find()->where(['Escola.unidade' => Escola::UNIDADE_ESTADUAL])->all() as $registro)
                        array_push($escolas, $registro);
                    break;
                default:
                    $escolas = Escola::find()->rightJoin('Aluno', 'Aluno.idEscola=Escola.id')->all();
                    break;
            }

            if (Yii::$app->request->get('report') == 'pdf')
                return $this->alunosAtendidosPassePdf($solicitacoes);

            if (Yii::$app->request->get('report') == 'xls')
                return $this->alunosAtendidosPasseXls($solicitacoes);
            

            return $this->render('indexAlunosAtendidosPasseEscolar', [
                'solicitacoesTransporte' => $solicitacoes,
                'bairros' => ArrayHelper::map(AgrupamentoBairro::find()->all(), 'nome', 'nome'),
                'condutores' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'),
                'modalidadeBeneficios' => Aluno::ARRAY_MODALIDADE,
                'unidades' => Escola::ARRAY_UNIDADE,
                'escolas' =>  ArrayHelper::map($escolas, 'id', 'nome'),
                'regioes' => Escola::ARRAY_REGIAO,
                'camposOrdenacao' => $camposOrdenacao,
            ]);
        }
        $this->redirect(['index']);
    }

    // RELATÓRIO PASSE ESCOLAR
    private function alunosAtendidosPassePdf($data)
    {
        ini_set("pcre.backtrack_limit", "5000000");

        ini_set("max_execution_time", "300");        $content .= '<table border="0" width="100%" class="table">';
        $content .= '
        <tr>
            <th><b>Escola</b></th>
            <th><b>Nome</b></th>
            <th><b>RA</b></th>
            <th><b>Ano/Série</b></th>
            <th><b>Distância da escola (Metros)</b></th>
            <th><b>Barreira física</b></th>
            <th><b>Endereço</b></th>
            <th><b>Bairro</b></th>
            <th><b>Modalidade</b></th>
            <th><b>Cartão de passe escolar</b></th>
            <th><b>Cartão de vale transporte</b></th>
            <th><b>Início</b></th>        
        </tr>';

        foreach ($data as $model) {
            $content .= '<tr>';
            $content .= $this->td(15, $model->escola->nomeCompleto);
            $content .= $this->td(15, $model->aluno->nome);
            $content .= $this->td(5, $model->aluno->RA.'-'.$model->aluno->RAdigito);
            $content .= $this->td(5, $model->aluno->turma ? Aluno::ARRAY_SERIES[$model->aluno->serie].'/'.Aluno::ARRAY_TURMA[$model->aluno->turma] : '-');
            $content .= $this->td(5, $model->distanciaEscola . ' KM');
            $content .= $this->td(5, $model->barreiraFisica == 1 ? 'SIM' : 'NÃO');
            $content .= $this->td(15, $model->aluno->tipoLogradouro.' '.$model->aluno->endereco.' Nº '.$model->aluno->numeroResidencia);
            $content .= $this->td(10, $model->aluno->bairro);                            
            
            $modalidade = '';
            if($model->aluno->temPasseEscolar()) {
                $modalidade .= 'Passe Escolar';
            }
            if($model->aluno->temPasseEscolar() && $model->aluno->temValeTransporte()) {
                $modalidade .= ' e ';
            }
            if($model->aluno->temValeTransporte()) {
                $modalidade .= 'Vale Transporte';
            }
            
            $content .= $this->td(10, $modalidade);
            $content .= $this->td(5, $model->cartaoPasseEscolar);
            $content .= $this->td(5, $model->cartaoValeTransporte);
            
            $data = $model->recebimento->dataCadastro;
            $content .= $this->td(5, ($data != '0000-00-00')?Yii::$app->formatter->asDate($data, 'dd/MM/Y'):'-');
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

    private function alunosAtendidosPasseXls($data)
    {
        $content = '';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
		
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);    
        // $sheet = $spreadsheet->createSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // $sheet->setTitle('CONTROLE FINANCEIRO');
        $i = 1;
        $this->cabecalhoPasse($sheet,$i, $data);

        $i+=9;
        
        // COLUNAS DO CABEÇALHO
        $sheet->getRowDimension($i)->setRowHeight(60);
        $sheet->getStyle('A'.$i.':L'.$i)->applyFromArray(self::center)->getFont()->setBold(true);
        $sheet->getStyle('A'.$i.':L'.$i)->applyFromArray(self::borderSoft);
        $sheet->setCellValue('A'.$i, "Escola");
        $sheet->setCellValue('B'.$i, "Nome");
        $sheet->setCellValue('C'.$i, "RA");
        $sheet->setCellValue('D'.$i, "Ano/Série");
        $sheet->setCellValue('E'.$i, "Distância da escola (Metros)");
        $sheet->setCellValue('F'.$i, "Barreira física");
        $sheet->setCellValue('G'.$i, "Endereço");
        $sheet->setCellValue('H'.$i, "Bairro");
        $sheet->setCellValue('I'.$i, "Modalidade");
        $sheet->setCellValue('J'.$i, "Cartão de passe escolar");
        $sheet->setCellValue('K'.$i, "Cartão de vale transporte");
        $sheet->setCellValue('L'.$i, "Início");       
		//$sheet->setCellValue('M'.$i, "Unidade");   
        
        foreach ($data as $model) {
            $i++;        
            $sheet->setCellValue('A'.$i, $model->escola->nomeCompleto);
            $sheet->setCellValue('B'.$i, $model->aluno->nome);
            $sheet->setCellValue('C'.$i, $model->aluno->RA.'-'.$model->aluno->RAdigito);
            $sheet->setCellValue('D'.$i, $model->aluno->turma ? Aluno::ARRAY_SERIES[$model->aluno->serie].'/'.Aluno::ARRAY_TURMA[$model->aluno->turma] : '-');
            $sheet->setCellValue('E'.$i, $model->distanciaEscola . ' KM');
            $sheet->setCellValue('F'.$i, $model->barreiraFisica == 1 ? 'SIM' : 'NÃO');
            $sheet->setCellValue('G'.$i, $model->aluno->tipoLogradouro.' '.$model->aluno->endereco.' Nº '.$model->aluno->numeroResidencia);
            $sheet->setCellValue('H'.$i, $model->aluno->bairro);                            
            
            $modalidade = '';
            if($model->aluno->temPasseEscolar()) {
                $modalidade .= 'Passe Escolar';
            }
            if($model->aluno->temPasseEscolar() && $model->aluno->temValeTransporte()) {
                $modalidade .= ' e ';
            }
            if($model->aluno->temValeTransporte()) {
                $modalidade .= 'Vale Transporte';
            }
            
            $sheet->setCellValue('I'.$i, $modalidade);
            $sheet->setCellValue('J'.$i, $model->cartaoPasseEscolar);
            $sheet->setCellValue('K'.$i, $model->cartaoValeTransporte);
            
            $data = $model->recebimento->dataCadastro;
            $sheet->setCellValue('L'.$i, ($data != '0000-00-00')?Yii::$app->formatter->asDate($data, 'dd/MM/Y'):'-');
			//$sheet->setCellValue('M'.$i, $model->escola->unidade);
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

    private function cabecalhoPasse(&$sheet, &$i, $data) {
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
        $sheet->mergeCells('B'.$i.':L'.$i);
        
        $i++;
        $sheet->mergeCells('B'.$i.':L'.($i+1));
        $sheet->getStyle('B'.$i.':L'.($i+1))->applyFromArray(self::borderSoft);
        $sheet->setCellValue('B'.$i, "Secretaria de Educação e Cidadania");
        $sheet->getStyle('B'.$i)->applyFromArray(self::left)->getFont()->setBold(true);

        $i+=2;
        $sheet->mergeCells('B'.$i.':L'.($i+6));
        $sheet->getStyle('B'.$i.':L'.($i+6))->applyFromArray(self::borderSoft);
        $sheet->setCellValue('B'.$i, "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
        $sheet->getStyle('B'.$i)->getAlignment()->setWrapText(true);
        $sheet->getStyle('B'.$i)->applyFromArray(self::left);

        // $sheet->getStyle('A'.($i+2).':G'.($i+2))->applyFromArray($left);

        // $i+=5;
        // $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray($center);
    }

    // RELATÓRIO FRETE
    private function alunosAtendidosFretePdf($data)
    {
        ini_set("pcre.backtrack_limit", "5000000");
        ini_set("max_execution_time", "300");

        $content = '<table border="0" width="100%" class="table">';
        $content .= '
        <tr>
        <th><b>Escola</b></th>
        <th><b>Nome</b></th>
        <th><b>RA</b></th>
        <th><b>Horário de entrada</b></th>
        <th><b>Horário de saída</b></th>
        <th><b>Ano/Série</b></th>
        <th><b>Distância da escola (Metros)</b></th>
        <th><b>Barreira física</b></th>
        <th><b>Endereço</b></th>
        <th><b>Bairro</b></th>
        <th><b>Condutor Entrada</b></th>';
        
        if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
                \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
                \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
            $content .= '<th><b>Telefone Condutor Entrada</b></th>';
        
        $content .= '<th><b>Condutor Saída</b></th>';

        if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
                \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
                \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
            $content .= '<th><b>Telefone Condutor Saída</b></th>';

        $content .= '
        <th><b>Necessidades</b></th> 
        <th><b>Início do atendimento</b></th>
        </tr>';

        foreach ($data as $model) {
            $content .= '<tr>';
            $content .= $this->td(10, $model->escola->nomeCompleto);
            $content .= $this->td(10, $model->aluno->nome);
            $content .= $this->td(5, $model->aluno->RA.'-'.$model->aluno->RAdigito);
            $content .= $this->td(5, $model->aluno->horarioEntrada);
            $content .= $this->td(5, $model->aluno->horarioSaida);
            $content .= $this->td(5, $model->aluno->turma ? Aluno::ARRAY_SERIES[$model->aluno->serie].'/'.Aluno::ARRAY_TURMA[$model->aluno->turma] : '-');
            $content .= $this->td(5, $model->distanciaEscola . ' KM');
            $content .= $this->td(5, $model->barreiraFisica == 1 ? 'SIM' : 'NÃO');
            $content .= $this->td(10, $model->aluno->tipoLogradouro.' '.$model->aluno->endereco.' Nº '.$model->aluno->numeroResidencia);
            $content .= $this->td(5, $model->aluno->bairro);
            $content .= $this->td(5, $model->rotaIda ? $model->rotaIda->condutor->nome : '-');
            
            if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
                    \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
                    \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
                $content .= $this->td(5, $model->rotaIda ? $model->rotaIda->condutor->celular : '-');
            
            $content .= $this->td(5, $model->rotaVolta ? $model->rotaVolta->condutor->nome : '-');
            
            if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
                    \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
                    \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
                $content .= $this->td(5, $model->rotaIda ? $model->rotaIda->condutor->celular : '-');
            
            $necessidades = $model->aluno->necessidades;
            $redes = [];
            foreach ($necessidades as $necessidade)
            {
                $redes[] = $necessidade->necessidadesEspeciais->nome;
            }
            $content .= $this->td(5, implode (', ', $redes));
            
            $data = $model->atendimento->dataCadastro;
            $content .= $this->td(5, ($data != '0000-00-00')?Yii::$app->formatter->asDate($data, 'dd/MM/Y'):'-');
            
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

    private function alunosAtendidosFreteXls($data)
    {
        $content = '';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);    
        // $sheet = $spreadsheet->createSheet();
        // $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // $sheet->setTitle('CONTROLE FINANCEIRO');
        $i = 1;
        $this->cabecalhoFrete($sheet,$i, $data);

        $i+=9;
        
        // COLUNAS DO CABEÇALHO
        $sheet->getRowDimension($i)->setRowHeight(60);

        $col = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
        $colNum = 0;

        $sheet->setCellValue($col[$colNum].$i, "Escola");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Nome");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "RA");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Horário de entrada");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Horário de saída");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Ano/Série");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Distância da escola (Metros)");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Barreira física");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Endereço");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Bairro");
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Condutor Entrada");
        $colNum++;
        
        if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
                \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
                \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
                {
                    $sheet->setCellValue($col[$colNum].$i, "Telefone Condutor Entrada");
                    $colNum++;
                }
        
        $sheet->setCellValue($col[$colNum].$i, "Condutor Saída");
        $colNum++;

        if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
                \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
                \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
                {
                    $sheet->setCellValue($col[$colNum].$i, "Telefone Condutor Saída");
                    $colNum++;
                }

        $sheet->setCellValue($col[$colNum].$i, "Necessidades"); 
        $colNum++;
        $sheet->setCellValue($col[$colNum].$i, "Início do atendimento");

        $sheet->getStyle('A'.$i.':'.$col[$colNum].$i)->applyFromArray(self::center)->getFont()->setBold(true);
        $sheet->getStyle('A'.$i.':'.$col[$colNum].$i)->applyFromArray(self::borderSoft);    
        
        foreach ($data as $model) {
            $i++;
            $colNum = 0;
            
            $sheet->setCellValue($col[$colNum].$i, $model->escola->nomeCompleto);
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->aluno->nome);
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->aluno->RA.'-'.$model->aluno->RAdigito);
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->aluno->horarioEntrada);
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->aluno->horarioSaida);
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->aluno->turma ? Aluno::ARRAY_SERIES[$model->aluno->serie].'/'.Aluno::ARRAY_TURMA[$model->aluno->turma] : '-');
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->distanciaEscola . ' KM');
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->barreiraFisica == 1 ? 'SIM' : 'NÃO');
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->aluno->tipoLogradouro.' '.$model->aluno->endereco.' Nº '.$model->aluno->numeroResidencia);
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->aluno->bairro);
            $colNum++;
            $sheet->setCellValue($col[$colNum].$i, $model->rotaIda ? $model->rotaIda->condutor->nome : '-');
            $colNum++;
            
            if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
                    \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
                    \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
                    {
                        $sheet->setCellValue($col[$colNum].$i, $model->rotaIda ? $model->rotaIda->condutor->celular : '-');
                        $colNum++;
                    }
            
            $sheet->setCellValue($col[$colNum].$i, $model->rotaVolta ? $model->rotaVolta->condutor->nome : '-');
            $colNum++;
            
            if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_SECRETARIO ||
                    \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DIRETOR ||
                    \Yii::$app->User->identity->idPerfil == Usuario::PERFIL_DRE)
                    {
                        $sheet->setCellValue($col[$colNum].$i, $model->rotaIda ? $model->rotaIda->condutor->celular : '-');
                        $colNum++;
                    }
            
            $necessidades = $model->aluno->necessidades;
            $redes = [];
            foreach ($necessidades as $necessidade)
            {
                $redes[] = $necessidade->necessidadesEspeciais->nome;
            }
            $sheet->setCellValue($col[$colNum].$i, implode (', ', $redes));
            $colNum++;
            
            $data = $model->atendimento->dataCadastro;
            $sheet->setCellValue($col[$colNum].$i, ($data != '0000-00-00')?Yii::$app->formatter->asDate($data, 'dd/MM/Y'):'-');
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

    private function cabecalhoFrete(&$sheet, &$i, $data) {
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
        $sheet->mergeCells('B'.$i.':O'.$i);
        
        $i++;
        $sheet->mergeCells('B'.$i.':O'.($i+1));
        $sheet->getStyle('B'.$i.':O'.($i+1))->applyFromArray(self::borderSoft);
        $sheet->setCellValue('B'.$i, "Secretaria de Educação e Cidadania");
        $sheet->getStyle('B'.$i)->applyFromArray(self::left)->getFont()->setBold(true);

        $i+=2;
        $sheet->mergeCells('B'.$i.':O'.($i+6));
        $sheet->getStyle('B'.$i.':O'.($i+6))->applyFromArray(self::borderSoft);
        $sheet->setCellValue('B'.$i, "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
        $sheet->getStyle('B'.$i)->getAlignment()->setWrapText(true);
        $sheet->getStyle('B'.$i)->applyFromArray(self::left);

        // $sheet->getStyle('A'.($i+2).':G'.($i+2))->applyFromArray($left);

        // $i+=5;
        // $sheet->getStyle('A'.$i.':G'.$i)->applyFromArray($center);
    }

    public function actionHistoricoAtendimento()
    {
        if ($get = Yii::$app->request->get()) {
            $condutor = Condutor::findOne($get['idCondutor']);
            $alunos = [];


            if ($get['idCondutor']) {

                $sql = "SELECT 
                            `HistoricoMovimentacaoRota`.`id`
                        FROM
                            `HistoricoMovimentacaoRota`";
                
                $sql .= " LEFT JOIN `HistoricoMovimentacaoRota` AS `saida` ON `saida`.`id` = `HistoricoMovimentacaoRota`.`idHistoricoMovimentacaoAssociado`";

                $sql .= " WHERE
                            (
                                (`HistoricoMovimentacaoRota`.`idCondutorAnterior` = {$condutor->id}) OR (`HistoricoMovimentacaoRota`.`idCondutorAtual` = {$condutor->id})
                            )
                            AND 
                            (
                                (`HistoricoMovimentacaoRota`.`idAluno` > 0) AND (`HistoricoMovimentacaoRota`.`tipo` = ".HistoricoMovimentacaoRota::STATUS_ALUNO_INSERIDO.")
                            )";

                if ($get['periodo_inicio']) {

                    $data = $this->explodeData($get['periodo_inicio']);
                    $sql .= " 
                            AND
                            (
                                (`HistoricoMovimentacaoRota`.`criacao` >= '{$data[0]}' AND `HistoricoMovimentacaoRota`.`criacao` <= '{$data[1]}')
                                OR 
                                (
                                    (`HistoricoMovimentacaoRota`.`criacao` >= '{$data[0]}')
                                    AND
                                    (`HistoricoMovimentacaoRota`.`geradoviasistema` = 1)
                                )
                            )";
                }

                if ($get['periodo_fim']) { 
                    $data = $this->explodeData($get['periodo_fim']);
                    $sql .= " 
                            AND
                            (
                                (`saida`.`criacao` >= '{$data[0]}' AND `saida`.`criacao` <= '{$data[1]}')
                            )";
                }

                $sql .= " ORDER BY `HistoricoMovimentacaoRota`.`id` DESC";
                // die($sql);
                $idHistoricos = Yii::$app->getDb()->createCommand($sql)->queryAll();

                $historicos = HistoricoMovimentacaoRota::find()->where(['in','HistoricoMovimentacaoRota.id',array_column($idHistoricos, 'id')])->all();
                
                $regs = [];
                $regDatas = [];
                foreach ($historicos as $historico) {
                    $key = $historico->criacao . ' ' . $historico->idAluno . ' ' . $historico->sentido;

                    if (!in_array($key, $regDatas)) {
                        $regDatas[] = $key;
                        $regs[] = $historico;
                    }
                }

                $this->session->set('historicos', $regs);
                $this->session->set('condutor', $condutor);
                if (isset($_GET['sort'])) {
                    $sort = $_GET['sort'];
                } else {
                    $sort = 'aluno.nome';
                }
                $this->session->set('sort', $sort);
                $this->session->set('periodoInicio', $_GET['periodo_inicio']);
                $this->session->set('periodoFim', $_GET['periodo_fim']);
            } else {
                \Yii::$app->getSession()->setFlash('error', 'Selecione um condutor');
            }

            return $this->render('historico-atendimento', [
                'historicos' => $regs,
                'condutor' => $condutor,
                'condutores' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'),
                'get' => Yii::$app->request->get()
            ]);
        }
    }

    public function actionPesquisaAtendimento()
    {
        if ($get = Yii::$app->request->get()) {
            $data = $this->explodeData($get['periodo']);
            $condutor = Condutor::findOne($get['idCondutor']);
            $alunos = [];
			
			
            if ($get['idCondutor']) {
				$ano =   date("Y");
				$primeiraDataArr = explode("-",$data[0]);
				if($primeiraDataArr[0]){
					$anoAnterior =  $primeiraDataArr[0] - 1;
					$primeiroDiaAno = $primeiraDataArr[0].'-01-01';
				}else{
					$anoAnterior =   date("Y") - 1;
					$primeiroDiaAno = $anoAnterior.'-01-01';
				}
				
				$ultimaDataArr = explode("-",$data[1]);
				if($ultimaDataArr[0]){
					$ultimoDiaAno = $primeiraDataArr[0].'-12-31';
				}else{
					
					$primeiroDiaAno = $ano.'-12-21';
				}
				
				$sql="select h.id,al.id as id_aluno,al.nome as aluno,al.RA,al.RAdigito,al.horarioEntrada,al.horarioSaida,al.serie,al.turma,al.tipoLogradouro,al.endereco,al.bairro,al.numeroResidencia,e.nome as escola,c.nome as condutor,DATE_FORMAT(h.criacao,'%d/%m/%Y') as criacao
							from HistoricoMovimentacaoRota h 
							join Aluno al on h.idAluno = al.id
							join Escola e on e.id = h.idEscola
							join Condutor c on c.id = h.idCondutorAtual
							where h.idSolicitacaoTransporte in (
							select sta.idSolicitacaoTransporte from SolicitacaoStatus sta where sta.idSolicitacaoTransporte in (
								select st.id from SolicitacaoTransporte st where st.idCondutor = {$condutor->id}  and  (st.`data` between '{$data[0]}' and '{$data[1]}' or st.ultimaMovimentacao between '{$data[0]}' and '{$data[1]}')
									union 
								select id from SolicitacaoTransporte st where st.`status` =6 and st.idCondutor = {$condutor->id} and anoVigente = {$ano})
							and sta.`status` = 6) group by al.nome";

				
				
                $dados = Yii::$app->getDb()->createCommand($sql)->queryAll();

                				
				
                // throw new NotFoundHttpException(print_r($array));
                $this->session->set('historicos', $dados);
                $this->session->set('condutor', $condutor);
                if (isset($_GET['sort'])) {
                    $sort = $_GET['sort'];
                } else {
                    $sort = 'aluno.nome';
                }
                $this->session->set('sort', $sort);
                $this->session->set('periodo', $_GET['periodo']);
            } else {
                \Yii::$app->getSession()->setFlash('error', 'Selecione um condutor');
            }

            return $this->render('pesquisa-atendimento', [
                'historicos' => $dados,
				 'periodo' => $get['periodo'],
                'condutores' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'),
                'get' => Yii::$app->request->get()
            ]);
        }
    }

    protected function tdCenter($tamanho, $content, $style = '')
    {
        return '<td width="' . $tamanho . '%" align="center">' . $content . '</td>';
    }
    protected function td($tamanho, $content, $style = '')
    {
        return '<td width="' . $tamanho . '%" >' . $content . '</td>';
    }

    protected function getTelefoneValido($model)
    {
        if ($model->telefoneCelular && strlen($model->telefoneCelular) > 5)
            return $model->telefoneCelular;
        if ($model->telefoneCelular2 && strlen($model->telefoneCelular2) > 5)
            return $model->telefoneCelular2;
        if ($model->telefoneResidencial && strlen($model->telefoneResidencial) > 5)
            return $model->telefoneResidencial;
        if ($model->telefoneResidencial2 && strlen($model->telefoneResidencial2) > 5)
            return $model->telefoneResidencial2;
        return '-';
    }
	
	 protected function getTelefoneValidoAux($model)
    {
        if ($model['telefoneCelular'] && strlen($model['telefoneCelular']) > 5){
			return $model['telefoneCelular'];
		}            
        if ($model['telefoneCelular2'] && strlen($model['telefoneCelular2']) > 5){
			return $model['telefoneCelular2'];
		}            
        if ($model['telefoneResidencial'] && strlen($model['telefoneResidencial']) > 5){
			return $model['telefoneResidencial'];
		}            
        if ($model['telefoneResidencial2'] && strlen($model['telefoneResidencial2']) > 5){
			return $model['telefoneResidencial2'];
		}
        return '-';
    }
	
    protected function getNecessidades($model)
    {
        $necessidades = $model->necessidades;
        $redes = [];
        foreach ($necessidades as $necessidade) {
            $redes[] = $necessidade->necessidadesEspeciais->nome;
        }
        return implode(', ', $redes);
    }
    protected function montarPdfCondutor($condutor)
    {
		
	$rotas = CondutorRota::find()->where(['idCondutor' => $condutor])->all();
    $idsRotas = array_column($rotas, 'id');
	
	
	$rotas = implode(", ", $idsRotas);

			
	$sql="select a.nome as aluno,e.nome as escola,a.RA,a.RAdigito,a.serie,a.turma,a.turno,a.horarioEntrada,a.horarioSaida,a.tipoLogradouro,a.endereco,a.numeroResidencia, a.bairro,a.telefoneResidencial,a.telefoneResidencial2,a.telefoneCelular,a.telefoneCelular2,n.nome as necessidade from  SolicitacaoTransporte st  join Escola e on st.idEscola = e.id join Aluno a on a.id = st.idAluno left join AlunoNecessidadesEspeciais al on a.id = al.idAluno left join NecessidadesEspeciais n on n.id = al.idNecessidadesEspeciais where st.`status` = 6 and st.`status` <> 2 and st.idRotaIda in ($rotas) or st.idRotaVolta in ($rotas) and st.idRotaIda is not null and st.idRotaVolta is not null order by e.nome,a.turno,a.horarioEntrada,a.horarioSaida";
	
    $dados = Yii::$app->getDb()->createCommand($sql)->queryAll();
	
	$sqlCondutor="select * from Condutor c where c.id = $condutor";
    $condutor = Yii::$app->getDb()->createCommand($sqlCondutor)->queryAll();
				
        $content =  '<table width="100%">
      <tr>
        <td><b>CONDUTOR: </b>' . $condutor[0]['nome'] . '</td>
        <td><b>ALVARÁ: </b>' . $condutor[0]['alvara'] . '</td>
        <td><b>TELEFONE: </b>' . $condutor[0]['telefone'] . '</td>
      </tr>
    </table>';
        $content .= '<table border="0" width="100%" class="table" style="font-size:10px!important">';
        $content .= '
      <tr>
	     <th align="center"><b></b></th>
        <th align="center"><b>NOME</b></th>
        <th align="center"><b>ESCOLA</b></th>
        <th align="center"><b>RA</b></th>
        <th align="center"><b>ANO/SÉRIE E TURMA</b></th>
		<th align="center"><b>TURNO</b></th>
        <th align="center"><b>ENTRADA</b></th>
        <th align="center"><b>SAÍDA</b></th>
        <th align="center"><b>ENDEREÇO</b></th>
        <th align="center"><b>COMPLEMENTO</b></th>
        <th align="center"><b>BAIRRO</b></th>
        <th align="center"><b>TEL.</b></th>
        <th align="center"><b>NECESSIDADES</b></th>
      </tr>';
		$i=1;
        foreach ($dados as $model) {
			
			
            $content .= '<tr>';
			$content .= $this->td(5, $i);
            $content .= $this->td(20, $model['aluno']);
            $content .= $this->td(15, $model['escola']);
            $content .= $this->td(5, $model['RA'] . '-' . $model['RAdigito']);
            $content .= $this->td(7, Aluno::ARRAY_SERIES[$model['serie']] . '/' . Aluno::ARRAY_TURMA[$model['turma']]);
			$content .= $this->td(7, Aluno::ARRAY_TURNO[$model['turno']]);
            $content .= $this->td(7, $model['horarioEntrada']);
            $content .= $this->td(7, $model['horarioSaida']);
            $content .= $this->td(20, $model['tipoLogradouro'] . ' ' . $model['endereco'] . ' Nº ' . $model['numeroResidencia']);
            $content .= $this->td(20, $model['complementoResidencia']);
            $content .= $this->td(10, $model['bairro']);
            $content .= $this->td(7, Yii::$app->formatter->asTelefone($this->getTelefoneValidoAux($model)));
            $content .= $this->td(10, $model['necessidade']);

            $content .= '</tr>';
			$i++;
        }
        $content .= '</table>';

        return $content;
    }
    protected function montarPdfEscola($dados, $condutor, $escola)
    {
        $content =  '<table width="100%">
      <tr>
        <td><b>UNIDADE ESCOLAR: </b>' . $escola->nomeCompleto . '</td>
    
      </tr>
      <tr>
        <td><b>CONDUTOR: </b>' . $condutor->nome . '</td>
        <td><b>ALVARÁ: </b>' . $condutor->alvara . '</td>
        <td><b>TELEFONE: </b>' . $condutor->telefone . '</td>
      </tr>
    </table>';
        $content .= '<table border="0" width="100%"  class="table">';
        $content .= '
      <tr>
        <th align="center"><b>NOME</b></th>
        <th align="center"><b>RA</b></th>
        <th align="center"><b>ANO/SÉRIE E TURMA</b></th>
        <th align="center"><b>ENTRADA</b></th>
        <th align="center"><b>SAÍDA</b></th>
        <th align="center"><b>ENDEREÇO</b></th>
        <th align="center"><b>BAIRRO</b></th>
        <th align="center"><b>TEL.</b></th>
        <th align="center"><b>NECESSIDADES</b></th>
      </tr>';


        foreach ($dados as $model) {
            $content .= '<tr>';
            $content .= $this->td(20, $model->nome);
            $content .= $this->td(3, $model->RA . '-' . $model->RAdigito);
            $content .= $this->td(3, Aluno::ARRAY_SERIES[$model->serie] . '/' . Aluno::ARRAY_TURMA[$model->turma]);
            $content .= $this->td(7, $model->horarioEntrada);
            $content .= $this->td(7, $model->horarioSaida);
            $content .= $this->td(20, $model->tipoLogradouro . ' ' . $model->endereco . ' Nº ' . $model->numeroResidencia);
            $content .= $this->td(10, $model->bairro);
            $content .= $this->td(7, Yii::$app->formatter->asTelefone($this->getTelefoneValido($model)));
            $content .= $this->td(10, $this->getNecessidades($model));
            $content .= '</tr>';
        }
        $content .= '</table>';
        return $content;
    }
    private function sortArr(&$array, $subfield, $ascDesc, $subfield2 = '')
    {
        $sortarray = array();
        foreach ($array as $key => $row) {
            if ($subfield2)
                $sortarray[$key] = $row[$subfield][$subfield2];
            else
                $sortarray[$key] = $row[$subfield];
        }

        array_multisort($sortarray, $ascDesc, $array);
    }
    public function sortDados(&$dados)
    {
        $sort = $this->session->get('sort');
        switch ($sort) {
            case '-nome':
                $this->sortArr($dados, 'nome', SORT_DESC);
                break;
            case 'nome':
                $this->sortArr($dados, 'nome', SORT_ASC);
                break;
            case 'RA':
                $this->sortArr($dados, 'RA', SORT_ASC);
                break;
            case '-RA':
                $this->sortArr($dados, 'RA', SORT_DESC);
                break;
            case 'horarioEntrada':
                $this->sortArr($dados, 'horarioEntrada', SORT_ASC);
                break;
            case '-horarioEntrada':
                $this->sortArr($dados, 'horarioEntrada', SORT_DESC);
                break;
            case '-horarioSaida':
                $this->sortArr($dados, 'horarioSaida', SORT_DESC);
                break;
            case 'horarioSaida':
                $this->sortArr($dados, 'horarioSaida', SORT_ASC);
                break;
            case 'escola.nome':
                $this->sortArr($dados, 'escola', SORT_ASC, 'nome');
                break;
            case '-escola.nome':
                $this->sortArr($dados, 'escola', SORT_DESC, 'nome');
                break;

            default:
                break;
        }
    }
    public function sortDadosHistoricoAtendimento(&$dados)
    {
        $sort = $this->session->get('sort');
        switch ($sort) {
            case '-aluno.nome':
                $this->sortArr($dados, 'aluno', SORT_DESC, 'nome');
                break;
            case 'aluno.nome':
                $this->sortArr($dados, 'aluno', SORT_ASC, 'nome');
                break;

            default:
                break;
        }
    }
    public function actionReport()
    {
        $contentBefore = '';
        $dadosAux = [];
        
        if ($this->session->get('alunos2')) {
            $dados = $this->session->get('alunos2');
        } else {
            $dados = $this->session->get('alunos');
        }

        if (\Yii::$app->User->identity->idPerfil == Usuario::PERFIL_CONDUTOR)
            $condutor = \Yii::$app->User->identity->condutor;
        else
            $condutor = $this->session->get('condutor');

        // print_r($dados);
        $this->sortDados($dados);
        if (!$dados)
            $dados = [];
        //         ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        // print $_GET['escola'];
        // print_r(Escola::find()->where(['id' => $_GET['escola']])->one() );
        if (isset($_GET['escola'])) {
            $escola = Escola::findOne($_GET['escola']);
            $content = $this->montarPdfEscola($dados, $condutor, $escola);
        } else {
            $content = $this->montarPdfCondutor($_GET['idCondutor']);
        }


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
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }
    public function actionAlunosCondutor()
    {
        $camposOrdenacao = [
            'Escola.nome A-Z' => 'Escola - A-Z',
            'Escola.nome Z-A' => 'Escola - Z-A',
            'Aluno.nome Z-A' => 'Nome - Z-A',
            'Aluno.nome A-Z' => 'Nome - A-Z',
            'Aluno.horarioEntrada A-Z' => 'Horário de entrada - A-Z',
            'Aluno.horarioEntrada Z-A' => 'Horário de entrada - Z-A',
            'Aluno.horarioSaida A-Z' => 'Horário de saída - A-Z',
            'Aluno.horarioSaida Z-A' => 'Horário de saída - Z-A',
            'Aluno.RA A-Z' => 'RA - A-Z',
            'Aluno.RA Z-A' => 'RA - Z-A',
            'Aluno.endereco A-Z' => 'Endereço - A-Z',
            'Aluno.endereco Z-A' => 'Endereço - Z-A',
            'Aluno.bairro A-Z' => 'Bairro - A-Z',
            'Aluno.bairro Z-A' => 'Bairro - Z-A',
            'Aluno.serie A-Z' => 'Ano/Série - A-Z',
            'Aluno.serie Z-A' => 'Ano/Série - Z-A',
            'Aluno.turma A-Z' => 'Turma - A-Z',
            'Aluno.turma Z-A' => 'Turma - Z-A',
            'CondutorEntrada.nome A-Z' => 'Condutor Entrada - A-Z',
            'CondutorEntrada.nome Z-A' => 'Condutor Entrada - Z-A',
            'CondutorEntrada.alvara A-Z' => 'Alvará Entrada - A-Z',
            'CondutorEntrada.alvara Z-A' => 'Alvará Entrada - Z-A',
            'CondutorSaida.nome A-Z' => 'Condutor Saída - A-Z',
            'CondutorSaida.nome Z-A' => 'Condutor Saída - Z-A',
            'CondutorSaida.alvara A-Z' => 'Alvará Saída - A-Z',
            'CondutorSaida.alvara Z-A' => 'Alvará Saída - Z-A',
            'SolicitacaoStatus.dataCadastro A-Z' => 'Início - A-Z',
            'SolicitacaoStatus.dataCadastro Z-A' => 'Início - Z-A',
            'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) A-Z' => 'Necessidade Especial COM-SEM',
            'ISNULL(`AlunoNecessidadesEspeciais`.`idNecessidadesEspeciais`) Z-A' => 'Necessidade Especial SEM-COM',
        ];

        if (isset($_GET['pageSize']) && $_GET['pageSize']) {
            $pageSize = $_GET['pageSize'];
        } else {
            $pageSize = 200;
        }
        // $get['']
        if (isset($_GET['_tog7f728364']) && $_GET['_tog7f728364']) {
            $pageSize = 10000;
        }
        $this->session->set('alunos2', null);
        if ($get = Yii::$app->request->get()) {
            $condutor = Condutor::findOne($get['idCondutor']);
            $alunos = [];


            if ($get['idCondutor']) {
                $rotas = CondutorRota::find()->where(['idCondutor' => $get['idCondutor']])->all();
                $idsRotas = array_column($rotas, 'id');
                $multipleOrder = $this->applySort($get);

                // print_r($multipleOrder);
                // exit(1);
                $solicitacoes = SolicitacaoTransporte::find()
                    ->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
                    ->innerJoin('Aluno', 'Aluno.id=SolicitacaoTransporte.idAluno')
                    ->join('LEFT JOIN `CondutorRota` AS `RotaEntrada` ON', '`RotaEntrada`.`id` = `SolicitacaoTransporte`.`idRotaIda`')
                    ->join('LEFT JOIN `CondutorRota` AS `RotaSaida` ON', '`RotaSaida`.`id` = `SolicitacaoTransporte`.`idRotaVolta`')
                    ->join('LEFT JOIN `Condutor` AS `CondutorEntrada` ON', '`CondutorEntrada`.`id` = `RotaEntrada`.`idCondutor`')
                    ->join('LEFT JOIN `Condutor` AS `CondutorSaida` ON', '`CondutorSaida`.`id` = `RotaSaida`.`idCondutor`')
                    ->join('LEFT JOIN `SolicitacaoStatus` ON', '`SolicitacaoStatus`.`idSolicitacaoTransporte` = `SolicitacaoTransporte`.`id`')
                    //->join('LEFT JOIN `AlunoNecessidadesEspeciais` ON', '`AlunoNecessidadesEspeciais`.`id` = (SELECT id FROM `AlunoNecessidadesEspeciais` AS an WHERE an.`idAluno` = Aluno.`id` LIMIT 1)
                    // ->join('LEFT JOIN `AlunoNecessidadesEspeciais` ON', '`AlunoNecessidadesEspeciais`.`idAluno` = `Aluno`.`id`')
                    ->join('LEFT JOIN `AlunoNecessidadesEspeciais` ON', '`AlunoNecessidadesEspeciais`.`idAluno` = `Aluno`.`id`')
                    ->andWhere(['<>', 'tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
                    ->andWhere(['=', 'SolicitacaoTransporte.status', SolicitacaoTransporte::STATUS_ATENDIDO])
                    ->andFilterWhere(['or', ['idRotaIda' => $idsRotas], ['idRotaVolta' => $idsRotas]])

                    // ->andWhere(['=', 'bb', SolicitacaoTransporte::STATUS_ATENDIDO]);               
                ;


                if ($get['escola']) {
                    $solicitacoes->andWhere(['SolicitacaoTransporte.idEscola' => $get['escola']]);
                }

                if (!$multipleOrder) {
                    $multipleOrder['Aluno.nome'] = SORT_ASC;
                }
                $solicitacoes = $solicitacoes->orderBy($multipleOrder);
                $solicitacoes = $solicitacoes->all();

                foreach ($solicitacoes as $solicitacao) {
                    $aluno  = $solicitacao->aluno;
                    if ($aluno->solicitacaoAtiva->rotaIda->idCondutor != $condutor->id) {
                        $aluno->horarioEntrada = '-';
                    }

                    if ($aluno->solicitacaoAtiva->rotaVolta->idCondutor != $condutor->id) {
                        $aluno->horarioSaida = '-';
                    }

                    $alunos[] = $solicitacao->aluno;
                }
            } else {
                \Yii::$app->getSession()->setFlash('error', 'Selecione um condutor');
            }

            // print_r($alunos);
            $this->session->set('alunos', $alunos);
            $this->session->set('condutor', $condutor);

            // print 'sort'.$sort;
            $this->session->set('sort', $multipleOrder);
            return $this->render('alunos-condutor', [
                'pageSize' => $pageSize,
                'alunos' => $alunos,
                'condutor' => $condutor,
                'condutores' => ArrayHelper::map(Condutor::find()->all(), 'id', 'nome'),
                'escolas' => ArrayHelper::map(Escola::find()->all(), 'id', 'nome'),
                'get' => Yii::$app->request->get(),
                'camposOrdenacao' => $camposOrdenacao
            ]);
        }
        $this->redirect(['index']);
    }

    // public function actionRenderPdf(){
    //   $content ='';
    //   $titulo = '';
    //   $pdf = new Pdf([
    //     'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
    //     'content' => $content,
    //     'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.css',
    //     // 'cssFile' => '@vendor/almasaeed2010/adminlte/dist/css/AdminLTE.css',
    //     'cssInline' => 'td{font-size: 11px;}th{font-size: 11px!important;} h5{font-size:14px!important;}h4{font-size: 18px!important;}',
    //     'format' => Pdf::FORMAT_A4,
    //     'orientation' => Pdf::ORIENT_LANDSCAPE,
    //     'destination' => Pdf::DEST_STRING,
    //     'options' => [
    //         'title' => $titulo,
    //         'subject' => ''
    //     ],
    //     'methods' => [

    //         'SetFooter' => ['|Página {PAGENO}|'],
    //     ]
    // ]);
    // return base64_encode($pdf->render());
    // }
    public function explodeData($periodo)
    {
        $data = explode('- ', $periodo);

        $data[1] = explode('/', $data[1]);
        $data[1] = $data[1][2] . '-' . $data[1][1] . '-' . $data[1][0];
        $data[0] = explode('/', $data[0]);
        $data[0] = trim($data[0][2]) . '-' . $data[0][1] . '-' . $data[0][0];
        return $data;
    }
    public function searchPeriodo($periodo, $model)
    {
        $data = $this->explodeData($periodo);

        $model->andWhere(['>=', 'data', $data[0]])
            ->andWhere(['<=', 'data', $data[1]]);

        return $model;
    }

    // Passe escolar passescolar PASSE
    public function actionEmitirRelatorioAgrupado($tipo)
    {
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

        $colorRed =  new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        $colorWhite =  new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath(Yii::getAlias('@webroot') . '/img/brasaoPdf.png'); // put your path and image here
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(50);
        $drawing->setOffsetY(15);
        $drawing->setRotation(0);
        $drawing->setWorksheet($sheet);

        $drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing2->setPath(Yii::getAlias('@webroot') . '/img/faixa.png'); // put your path and image here
        $drawing2->setCoordinates('C1');
        $drawing2->setOffsetX(0);
        $drawing2->setOffsetY(0);
        $drawing2->setRotation(0);
        $drawing2->setWorksheet($sheet);


        // SETUP DAS COLUNAS
        $sheet->getColumnDimension('A')->setWidth(50.14);
        $sheet->getColumnDimension('B')->setWidth(30.5);
        $sheet->getColumnDimension('C')->setWidth(30.5);
        $sheet->getColumnDimension('D')->setWidth(25.14);


        // $sheet->getColumnDimension('A')->setWidth(60.14);
        // $sheet->getColumnDimension('B')->setWidth(14.5);
        // $sheet->getColumnDimension('C')->setWidth(20.34);
        // $sheet->getColumnDimension('D')->setWidth(25.14);
        // $sheet->getColumnDimension('E')->setWidth(12.57);
        // $sheet->getColumnDimension('F')->setWidth(20);
        // $sheet->getColumnDimension('G')->setWidth(20);
        // $sheet->getColumnDimension('H')->setWidth(32);


        //
        $i = 1;
        // PRÓXIMA LINHA
        $sheet->mergeCells('A' . $i . ':B' . ($i + 4));
        $sheet->mergeCells('C' . $i . ':D' . $i);
        $sheet->mergeCells('C' . ($i + 1) . ':D' . ($i + 1));
        $sheet->setCellValue('C' . ($i + 1), "Secretaria de Educação e Cidadania");
        $sheet->getStyle('C' . ($i + 1))->applyFromArray($left)->getFont()->setBold(true);

        $sheet->mergeCells('C' . ($i + 2) . ':D' . ($i + 4));
        $sheet->setCellValue('C' . ($i + 2), "Setor de Transporte Escolar\nE-mail: transporte.escolar@sjc.sp.gov.br\nTelefone: (12) 3901-2165");
        $sheet->getStyle('C' . ($i + 2))->getAlignment()->setWrapText(true);

        $sheet->getStyle('A' . ($i + 2) . ':D' . ($i + 2))->applyFromArray($left);


        $i += 5;
        $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($center);

        $sheet->mergeCells('A' . $i . ':D' . $i);
        $sheet->setCellValue('A' . $i, "Quantidade de Alunos Atendidos por Escola (Vale Transporte e Passe Escolar)");
        $sheet->getStyle('A' . $i)->applyFromArray($center);

        $sheet->getStyle('A' . $i . ':D' . $i)
            ->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $i . ':D' . $i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00F6F6F6');

        $sheet->getStyle('A' . $i)->getFont()->setBold(true);

        // $sheet->getStyle('A'.$i)->getFont()->setColor( $colorWhite );

        $i++;

        $sheet->setCellValue('A' . $i, "ESCOLA");
        $sheet->setCellValue('B' . $i, "ATENDIDOS - PASSE ESCOLAR");
        $sheet->setCellValue('C' . $i, "ATENDIDOS - VALE TRANSPORTE");
        $sheet->setCellValue('D' . $i, "ATENDIDOS");

        $sheet->getStyle('A' . $i . ':D' . $i)
            ->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $i . ':D' . $i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF000000');
        $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($center);

        $sheet->getStyle('A' . $i . ':D' . $i)->getFont()->setBold(true);

        $sheet->getStyle('A' . $i . ':D' . $i)->getFont()->setColor($colorWhite);
        $sheet->setAutoFilter('A' . $i . ':D' . $i);


        $st = SolicitacaoTransporte::find()
            ->andWhere(['<>', 'SolicitacaoTransporte.tipoSolicitacao', SolicitacaoTransporte::SOLICITACAO_CANCELAMENTO])
            ->andWhere(['SolicitacaoTransporte.status' => SolicitacaoTransporte::STATUS_DEFERIDO])
            ->andWhere(['SolicitacaoTransporte.modalidadeBeneficio' => Aluno::MODALIDADE_PASSE])
            ->innerJoin('Escola', 'Escola.id=SolicitacaoTransporte.idEscola')
            ->innerJoin('Aluno', 'Aluno.id=SolicitacaoTransporte.idAluno')
            ->join('LEFT JOIN `AlunoNecessidadesEspeciais` ON', '`AlunoNecessidadesEspeciais`.`idAluno` = `Aluno`.`id`')
            ->join('LEFT JOIN `SolicitacaoStatus` ON', '`SolicitacaoStatus`.`idSolicitacaoTransporte` = `SolicitacaoTransporte`.`id`')
            ->andWhere(['=', 'SolicitacaoStatus.status', SolicitacaoTransporte::STATUS_DEFERIDO]);
        $escolas = [];
        switch (\Yii::$app->User->identity->idPerfil) {
            case Usuario::PERFIL_SECRETARIO:
                foreach (\Yii::$app->User->identity->secretarios as $registro)
                    array_push($escolas, $registro->escola);
                break;
            case Usuario::PERFIL_DIRETOR:
                foreach (\Yii::$app->User->identity->diretores as $registro)
                    array_push($escolas, $registro->escola);
                break;
            case Usuario::PERFIL_DRE:
                foreach (Escola::find()->where(['Escola.unidade' => Escola::UNIDADE_ESTADUAL])->all() as $registro)
                    array_push($escolas, $registro);
                break;
            default:
                $escolas = Escola::find()->rightJoin('Aluno', 'Aluno.idEscola=Escola.id')->all();
                break;
        }
        $ids = array_column($escolas, 'id');
        $st->andWhere(['in', 'SolicitacaoTransporte.idEscola', $ids]);
        $st = $st->all();

        $escolas = [];
        foreach ($st as $solicitacao) {
            $key = $solicitacao->escola->nomeCompleto;

            if (!isset($escolas[$key]))
                $escolas[$key] = [
                    'passeEscolar' => 0,
                    'valeTransporte' => 0,
                    'atendidos' => 0
                ];

            // Se não tem VT então soma ao passe
            if (!$solicitacao->aluno->alunoCurso)
                $escolas[$key]['passeEscolar'] += 1;
            // Se tem VT então soma ao VT
            if ($solicitacao->aluno->alunoCurso)
                $escolas[$key]['valeTransporte'] += 1;

            $escolas[$key]['atendidos'] += 1;
        }
        // print_r($escolas);
        // die(1);
        $totalPasseEscolar = 0;
        $totalvaleTransporte = 0;
        $totalAtendidos = 0;
        foreach ($escolas as $key => $value) {
            $totalPasseEscolar += $value['passeEscolar'];
            $totalvaleTransporte += $value['valeTransporte'];
            $totalAtendidos += $value['atendidos'];
            $i++;
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':D' . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('00F6F6F6');
            }
            // $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($borderSoft);
            $sheet->getStyle('B' . $i . ':D' . $i)->applyFromArray($center);
            $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($borderSoft);
            $sheet->getStyle('A' . $i . ':D' . $i)
                ->getAlignment()->setWrapText(true);
            $sheet->setCellValue('A' . $i, ' ' . $key);

            $sheet->setCellValue('B' . $i, $value['passeEscolar']);
            $sheet->setCellValue('C' . $i, $value['valeTransporte']);
            $sheet->setCellValue('D' . $i, $value['atendidos']);
        }
        $i++;
        if ($i % 2 == 0) {
            $sheet->getStyle('A' . $i . ':D' . $i)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00F6F6F6');
        }
        $sheet->getStyle('B' . $i . ':D' . $i)->applyFromArray($center);
        $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($borderSoft);
        $sheet->getStyle('A' . $i . ':D' . $i)
            ->getAlignment()->setWrapText(true);
        $sheet->setCellValue('A' . $i, ' Total');
        $sheet->setCellValue('B' . $i, $totalPasseEscolar);
        $sheet->setCellValue('C' . $i, $totalvaleTransporte);
        $sheet->setCellValue('D' . $i, $totalAtendidos);

        ///////////////////////////////////////////////
        ///////////////////////////////////////////////
        ///////////////////////////////////////////////
        // SEGUNDA TABELA
        ///////////////////////////////////////////////
        ///////////////////////////////////////////////
        ///////////////////////////////////////////////
        $i += 2;
        $redes = [
            'Rede Municipal' => [
                'passeEscolar' => 0,
                'valeTransporte' => 0,
                'atendidos' => 0
            ],
            'Rede Estadual' => [
                'passeEscolar' => 0,
                'valeTransporte' => 0,
                'atendidos' => 0
            ],
            'Rede Filantrópica' => [
                'passeEscolar' => 0,
                'valeTransporte' => 0,
                'atendidos' => 0
            ],
        ];
        foreach ($st as $solicitacao) {
            switch ($solicitacao->escola->unidade) {
                case Escola::UNIDADE_MUNICIPAL:
                    $key = 'Rede Municipal';
                    break;
                case Escola::UNIDADE_ESTADUAL:

                    $key = 'Rede Estadual';
                    break;
                case Escola::UNIDADE_FILANTROPICA:
                    $key = 'Rede Filantrópica';
                    break;
                default:
                    break;
            }



            // Se não tem VT então soma ao passe
            if (!$solicitacao->aluno->alunoCurso)
                $redes[$key]['passeEscolar'] += 1;
            // Se tem VT então soma ao VT
            if ($solicitacao->aluno->alunoCurso)
                $redes[$key]['valeTransporte'] += 1;

            $redes[$key]['atendidos'] += 1;
        }


        $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($center);

        $sheet->mergeCells('A' . $i . ':D' . $i);
        $sheet->setCellValue('A' . $i, "Alunos Atendidos por Rede (Estadual, Municipal, Filantrópica) por tipo (Vale Transporte e Passe Escolar)");
        $sheet->getStyle('A' . $i)->applyFromArray($center);

        $sheet->getStyle('A' . $i . ':D' . $i)
            ->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $i . ':D' . $i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('00F6F6F6');

        $sheet->getStyle('A' . $i)->getFont()->setBold(true);

        // $sheet->getStyle('A'.$i)->getFont()->setColor( $colorWhite );
        $i++;

        $sheet->setCellValue('A' . $i, "ESCOLA");
        $sheet->setCellValue('B' . $i, "ATENDIDOS - PASSE ESCOLAR");
        $sheet->setCellValue('C' . $i, "ATENDIDOS - VALE TRANSPORTE");
        $sheet->setCellValue('D' . $i, "ATENDIDOS");

        $sheet->getStyle('A' . $i . ':D' . $i)
            ->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $i . ':D' . $i)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF000000');
        $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($center);

        $sheet->getStyle('A' . $i . ':D' . $i)->getFont()->setBold(true);

        $sheet->getStyle('A' . $i . ':D' . $i)->getFont()->setColor($colorWhite);
        $sheet->setAutoFilter('A' . $i . ':D' . $i);
        $totalPasseEscolar = 0;
        $totalvaleTransporte = 0;
        $totalAtendidos = 0;
        foreach ($redes as $key => $value) {
            $totalPasseEscolar += $value['passeEscolar'];
            $totalvaleTransporte += $value['valeTransporte'];
            $totalAtendidos += $value['atendidos'];
            $i++;
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':D' . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('00F6F6F6');
            }
            // $sheet->getStyle('A'.$i.':H'.$i)->applyFromArray($borderSoft);
            $sheet->getStyle('B' . $i . ':D' . $i)->applyFromArray($center);
            $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($borderSoft);
            $sheet->getStyle('A' . $i . ':D' . $i)
                ->getAlignment()->setWrapText(true);
            $sheet->setCellValue('A' . $i, ' ' . $key);

            $sheet->setCellValue('B' . $i, $value['passeEscolar']);
            $sheet->setCellValue('C' . $i, $value['valeTransporte']);
            $sheet->setCellValue('D' . $i, $value['atendidos']);
        }
        $i++;
        if ($i % 2 == 0) {
            $sheet->getStyle('A' . $i . ':D' . $i)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('00F6F6F6');
        }
        $sheet->getStyle('B' . $i . ':D' . $i)->applyFromArray($center);
        $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray($borderSoft);
        $sheet->getStyle('A' . $i . ':D' . $i)
            ->getAlignment()->setWrapText(true);
        $sheet->setCellValue('A' . $i, ' Total');
        $sheet->setCellValue('B' . $i, $totalPasseEscolar);
        $sheet->setCellValue('C' . $i, $totalvaleTransporte);
        $sheet->setCellValue('D' . $i, $totalAtendidos);

        $base = "arquivos/_exportacoes/";

        switch ($tipo) {
            case 'PDF':
                try {
                    $filename = $base . "Relatorio_Atendimento_Passe_Escolar_" . date('d-m-Y-H-i-s') . ".pdf";

                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
                    $writer->setPreCalculateFormulas(false);
                    $writer->save($filename);

                    header("Content-Disposition: attachment; filename=" . $filename);
                    $content = file_get_contents($filename);
                    unlink($filename);
                    exit($content);
                } catch (Exception $e) {
                    exit($e->getMessage());
                }

                break;
            case 'EXCEL':
                try {
                    $writer = new Xlsx($spreadsheet);
                    $filename = $base . "Relatorio_Atendimento_Passe_Escolar_" . date('d-m-Y-H-i-s') . ".xlsx";
                    $writer->save($filename);
                    header("Content-Disposition: attachment; filename=" . $filename);
                    $content = file_get_contents($filename);
                    unlink($filename);
                    exit($content);
                } catch (Exception $e) {
                    exit($e->getMessage());
                }
                break;
        }
    }
}
