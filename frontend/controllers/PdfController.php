<?php

namespace frontend\controllers;

use Yii;
use common\models\Usuario;
use common\models\Aluno;
use common\models\Condutor;
use common\models\Escola;
use common\models\ReciboPagamentoAutonomo;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;
use yii\web\UploadedFile;
use common\components\AccessRule;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;
use common\models\Configuracao;

/**
 * CfopController implements the CRUD actions for Cfop model.
 */
class PdfController extends Controller
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
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['rpa', 'carta-apresentacao', 'folha-ponto'],
                'rules' => [
                    [
                        'actions' => ['rpa', 'carta-apresentacao', 'folha-ponto'],
                        'allow' => true,
                        // 'roles' => [
                        //     Usuario::PERFIL_SUPER_ADMIN,
                        // ],
                    ],
                ],
            ]
        ];
    }

    public function actionRpa()
    {
        $get = Yii::$app->request->get();
        
        $model = ReciboPagamentoAutonomo::findOne($get['idRecibo']);
        if ($get['pdf'])
        {
            $this->layout = 'main-pdf';
            $content = $this->render('rpa', [
                'model' => $model,
            ]);

            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'content' => $content,
                'filename' => 'RPA.pdf',
                // 'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                'cssInline' => 'table{display:table;border:#111111 solid 0.1px;margin:0px}td{border:#111111 solid 0.1px;padding: 5px;}',
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_BROWSER,
                'options' => [
                    'subject' => ''
                ],
                'methods' => [
                    
                    'SetFooter' => [''],
                ]
            ]);
            return $pdf->render();
        }
        else
            return $this->render('rpa', [
                'model' => $model,
            ]);
        
        return $this->render('rpa', []);
    }

    public function actionCartaApresentacao($idCondutor, $idEscola)
    {
        $get = Yii::$app->request->get();

        // Query para buscar o aluno
        $condutor = Condutor::findOne($idCondutor);
        $escola = Escola::findOne($idEscola);
//throw new NotFoundHttpException(print_r($escola->nome, true));
        if ($get['pdf'])
        {
            $this->layout = 'main-pdf';
            $content = $this->render('carta-apresentacao', [
                'condutor' => $condutor,
                'escola' => $escola
            ]);

            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'content' => $content,
                'filename' => 'CartaApresentacao.pdf',
                // 'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                'cssInline' => 'table {width:100%;margin-bottom: 50px;} table.recibo { border: 1px solid #222222; padding:3px;} p{font-size: 17px;line-height:30px!importat;} h4{font-size: 17px!important;}',
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_BROWSER,
                'options' => [
                    'title' => ' ',
                    'subject' => ''
                ],
                'methods' => [
                    
                    'SetFooter' => [''],
                ]
            ]);
            return $pdf->render();
        }
        else
            return $this->render('carta-apresentacao', [
                'condutor' => $condutor,
                'escola' => $escola
            ]);
        
        return $this->render('carta-apresentacao', [
                'condutor' => $condutor,
                'escola' => $escola
        ]);
    }

    public function actionFolhaPonto($id)
    {
        $config = Configuracao::setup();
        $this->redirect($config->folhaPonto);
        // $get = Yii::$app->request->get();

        // // Query para buscar o aluno
        // $model = Condutor::findOne($id);
        
        // if ($get['pdf'])
        // {
        //     $this->layout = 'main-pdf';
        //     $content = $this->render('folha-ponto', [
        //         'model' => $model,
        //     ]);

        //     $pdf = new Pdf([
        //         'mode' => Pdf::MODE_UTF8,
        //         'content' => $content,
        //         'filename' => 'FolhaPonto.pdf',
        //         // 'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
        //         'cssInline' => 'table{display:table;border:#111111 solid 0.1px;margin:10px}td{border:#111111 solid 0.1px;padding: 10px;}table.calendario{width: 100%;border-collapse:colapse;}table.calendario .cabecalho{text-align:center;background:#000000;color:#FFFFFF;font-weight:800;padding:30px;}table.calendario td.dia-semana{text-align:center;font-weight:800;background:#CCCCCC;}table.calendario td.dia{text-align:right;font-weight:800;padding:120px 0px 0px 170px;}',
        //         'format' => Pdf::FORMAT_A4,
        //         'orientation' => Pdf::ORIENT_PORTRAIT,
        //         'destination' => Pdf::DEST_BROWSER,
        //         'options' => [
        //             'title' => ' ',
        //             'subject' => ''
        //         ],
        //         'methods' => [
                    
        //             'SetFooter' => [''],
        //         ]
        //     ]);
        //     return $pdf->render();
        // }
        // else
        //     return $this->render('folha-ponto', [
        //         'model' => $model,
        //     ]);
        
        // return $this->render('folha-ponto', []);
    }
}
