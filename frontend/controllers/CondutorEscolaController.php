<?php

namespace frontend\controllers;

use Yii;
use common\models\CondutorEscola;
use common\models\CondutorEscolaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * CondutorEscolaController implements the CRUD actions for CondutorEscola model.
 */
class CondutorEscolaController extends Controller
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
    /**
     * Lists all CondutorEscola models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CondutorEscolaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateAjax()
    {
        $model = new CondutorEscola();
        if(Yii::$app->request->get('idCondutor')){
            $model->idCondutor = Yii::$app->request->get('idCondutor');
        }
        $post = Yii::$app->request->post();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            //$this->salvarRota($post,$post['CondutorRota']['idEscola'],$post['CondutorRota']['idCondutor']);
            // print '<pre>';
            // print_r($post['CondutorRota']['configuracao']);
            // print '</pre>';
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['status' => true];
        } else {
            return $this->renderAjax('_formAjax', [
                'model' => $model,
                'action' => 'condutor-escola/create-ajax',
            ]);
        }
    }

    /**
     * Displays a single CondutorEscola model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CondutorEscola model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CondutorEscola();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CondutorEscola model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CondutorRota model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['condutor/view', 'id' => $model->idCondutor]);
    }
    /**
     * Finds the CondutorEscola model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CondutorEscola the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CondutorEscola::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
