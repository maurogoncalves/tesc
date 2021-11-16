<?php

namespace frontend\controllers;

use Yii;
use common\models\Configuracao;
use common\models\ConfiguracaoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/**
 * ConfiguracaoController implements the CRUD actions for Configuracao model.
 */
class ConfiguracaoController extends Controller
{
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
    private function getDates($model)
    {

        $data = \DateTime::createFromFormat('d/m/Y', $model->dataVigente);
        if ($data)
            $model->dataVigente = $data->format('Y-m-d');


        return $model;
    }

    private function getDatesBr($model)
    {
        $data = \DateTime::createFromFormat('Y-m-d', $model->dataVigente);
        if ($data)
            $model->dataVigente = $data->format('d/m/Y');
        return $model;
    }

    /**
     * Lists all Configuracao models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ConfiguracaoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Configuracao model.
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
     * Displays a single Configuracao model.
     * @param integer $id
     * @return mixed
     */
    public function actionViewAjax()
    { 
        date_default_timezone_set('America/Sao_Paulo');
        $model = $this->findModel(1);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model->anoVigente = $model->calcularAno();
        $model = $this->getDatesBr($model);
        
        return $model;
    }

    private function actionUploadFile($model, $file)
    {

        $idTipoDocumento = 'folhaPonto';
        $arquivos = UploadedFile::getInstances($model, $file);
        // print_r($arquivos);
        if ($arquivos) {
            $dirBase = Yii::getAlias('@webroot') . '/';
            $dir = 'arquivos/' . $idTipoDocumento . '/';

            if (!file_exists($dirBase . $dir))
                mkdir($dir, 0777, true);

            $i = 1;
            foreach ($arquivos as $arquivo) {
                $nomeArquivo = $idTipoDocumento . '_' . time() . '_' . $i . '.' . $arquivo->extension;
                $arquivo->saveAs($dirBase . $dir . $nomeArquivo);
                $model->folhaPonto =  $dir . $nomeArquivo;
                $model->save();
                $i++;
            }
        }
    }

    /**
     * Updates an existing Configuracao model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        $model = $this->findModel(1);
        
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model = $this->getDates($model);
            // if($model->documentoFolhaPonto) {
                $this->actionUploadFile($model, 'documentoFolhaPonto');
            // }
            $model->save();
            //return $this->redirect(['view', 'id' => $model->id]);
            return $this->goHome();
        } else {
            $model = $this->getDatesBr($model);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Configuracao model.
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
     * Finds the Configuracao model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Configuracao the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Configuracao::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
