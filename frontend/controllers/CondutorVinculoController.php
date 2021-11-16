<?php

namespace frontend\controllers;

use Yii;
use common\models\CondutorVinculo;
use common\models\CondutorVinculoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CondutorVinculoController implements the CRUD actions for CondutorVinculo model.
 */
class CondutorVinculoController extends Controller
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
     * Lists all CondutorVinculo models.
     * @return mixed
     */
    public function actionIndexAjax()
    {
        $searchModel = new CondutorVinculoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CondutorVinculo model.
     * @param string $id
     * @return mixed
     */
    public function actionViewAjax($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CondutorVinculo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateAjax()
    {
        $model = new CondutorVinculo();
        if(Yii::$app->request->get('idCondutor')){
            $model->idCondutor = Yii::$app->request->get('idCondutor');
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
       

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['status' => true];
        } else {
          
            return $this->renderAjax('_formAjax', [
                'model' => $model,
                'action' => 'condutor-vinculo/create-ajax',
            ]);
        }

        // $model = new CondutorVinculo();

        // if ($model->load(Yii::$app->request->post()) && $model->save()) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // } else {
        //     return $this->render('create', [
        //         'model' => $model,
        //     ]);
        // }
    }

    /**
     * Updates an existing CondutorVinculo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdateAjax($id)
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
     * Deletes an existing CondutorVinculo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDeleteAjax($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CondutorVinculo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CondutorVinculo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CondutorVinculo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
