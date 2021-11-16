<?php

namespace frontend\controllers;

use Yii;
use common\models\AlunoRota;
use common\models\AlunoRotaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Condutor;
use common\models\CondutorRota;
/**
 * AlunoRotaController implements the CRUD actions for AlunoRota model.
 */
class AlunoRotaController extends Controller
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

    /**
     * Lists all AlunoRota models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlunoRotaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,

        ]);
    }

    public function actionIndexAjax($idCondutorRota)
    {
        $condutorRota = CondutorRota::findOne($idCondutorRota);
        $model = new AlunoRota();

       
        // $model->idCondutorRota = $idCondutorRota;
        if(Yii::$app->request->get('idCondutorRota')){
            $model->idCondutorRota = Yii::$app->request->get('idCondutorRota');
        }
        
       
        $alunos = AlunoRota::find()->where(['idCondutorRota' => $idCondutorRota])->all();

       if ($model->load(Yii::$app->request->post()) && $model->save()) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['status' => true];
        } else {
                return $this->renderAjax('indexAjax', [
                    'alunos' => $alunos,
                    'model' => $model,
                    'action' => 'aluno-rota/index-ajax',
                    'escolas' => $condutorRota ? $condutorRota->condutor->escolas : [],
                    'idCondutorRota' => $idCondutorRota     
                ]);   
        }
        
           
    }
    public function actionViewAjax($id)
    {
        return $this->renderAjax('viewAjax', [
            'model' => $this->findModel($id),
        ]);
 
    }

    public function actionCreateAjax($idCondutor)
    {
        $condutor = Condutor::findOne($idCondutor);
        $model = new AlunoRota();
    

        if(Yii::$app->request->get('idCondutorRota')){
            $model->idCondutorRota = Yii::$app->request->get('idCondutorRota');
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['status' => true];
        } else {
       
            return $this->renderAjax('_formAjax', [
                'model' => $model,
                'action' => 'aluno-rota/create-ajax',
                'escolas' => $condutor->escolas,
                'idCondutor' => $condutor->id           
            ]);
        }
    }

    /**
     * Displays a single AlunoRota model.
     * @param string $id
     * @return mixed
     */
    // public function actionView($id)
    // {
    //     return $this->render('view', [
    //         'model' => $this->findModel($id),
    //     ]);
    // }

    // /**
    //  * Creates a new AlunoRota model.
    //  * If creation is successful, the browser will be redirected to the 'view' page.
    //  * @return mixed
    //  */
    // public function actionCreate()
    // {
    //     $model = new AlunoRota();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Updates an existing AlunoRota model.
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
     * Deletes an existing AlunoRota model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

         return $this->redirect(['condutor-rota/view', 'id' => $model->idCondutorRota]);
    }

    /**
     * Finds the AlunoRota model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AlunoRota the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AlunoRota::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
