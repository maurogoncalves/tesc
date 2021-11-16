<?php

namespace frontend\controllers;

use Yii;
use common\models\Log;
use common\models\LogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogController implements the CRUD actions for Log model. 
 */
class LogController extends Controller
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
     * Lists all Log models.
     * @return mixed
     */
    // public function actionIndex()
    // {
    //     $searchModel = new LogSearch();
    //     $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    //     return $this->render('index', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //     ]);
    // }

    public function actionIndex()
    {
        $arrayData = [];
        $titulo = 'Relação de alunos transportados';
       
        // Quando o pdf for solicitado via post, faremos dessa forma
        $post = Yii::$app->request->post();

       

        $get = Yii::$app->request->get();
    
       
        if (!isset($get['periodo']))
        {
            \Yii::$app->getSession()->setFlash('error', 'Selecione um período para a consulta.');
        } 
        else
        {
            $result = Log::find();
            $datas = explode(' - ', $get['periodo']);
            $dtInicial = \DateTime::createFromFormat ( 'd/m/Y', $datas[0]);
            $dtFinal = \DateTime::createFromFormat ( 'd/m/Y', $datas[1]);
            // throw new NotFoundHttpException(print_r($datas, true));

       
            // $result->joinWith('escola');
            // $result->joinWith('historico');

            // if (count($datas) != 2)
            //     \Yii::$app->getSession()->setFlash('error', 'Selecione um período válido para a consulta.');

            if ($dtFinal)
                $result->andFilterWhere(['>=', 'Log.data', $dtInicial->format('Y-m-d').' 00:00:00']);
            
            if ($dtFinal)
                $result->andFilterWhere(['<=', 'Log.data', $dtFinal->format('Y-m-d').' 23:59:59']);

            if (isset($get['acao']))
                $result->andFilterWhere(['=', 'Log.acao', $get['acao']]);

            if (isset($get['idUsuario']))
                $result->andFilterWhere(['=', 'Log.idUsuario', $get['idUsuario']]);
            
            if (isset($get['idAlunoTable']))
                $result->andFilterWhere(['=', 'Log.idAlunoTable', $get['idAlunoTable']]);

            if (isset($get['idModeloTable']))
                $result->andFilterWhere(['=', 'Log.idModeloTable', $get['idModeloTable']]);

            if (isset($get['idMarcaTable']))
                $result->andFilterWhere(['=', 'Log.idMarcaTable', $get['idMarcaTable']]);

            if (isset($get['tabela']))
                $result->andFilterWhere(['=', 'Log.tabela', $get['tabela']]);
            
            if (isset($get['idCondutorTable']))
                $result->andFilterWhere(['=', 'Log.idCondutorTable', $get['idCondutorTable']]);
            
            if (isset($get['idUsuarioTable']))
                $result->andFilterWhere(['=', 'Log.idUsuarioTable', $get['idUsuarioTable']]);

            if (isset($get['idEscolaTable']))
                $result->andFilterWhere(['=', 'Log.idEscolaTable', $get['idEscolaTable']]);

            if (isset($get['idVeiculoTable']))
                $result->andFilterWhere(['=', 'Log.idVeiculoTable', $get['idVeiculoTable']]);

            if (isset($get['idCondutorRotaTable']))
                $result->andFilterWhere(['=', 'Log.idCondutorRotaTable', $get['idCondutorRotaTable']]);

                
                // 'SolicitacaoCredito' => 'Solicitação de crédito',
                // 'SolicitacaoTransporte' => 'Solicitação de transporte',
                

            
            if (isset($get['idModeloTable']))
                $result->andFilterWhere(['=', 'Log.idModeloTable', $get['idModeloTable']]);


            // if (isset($get['escola']))
            //     $result->andFilterWhere(['=', 'Escola.id', $get['escola']]);

   

             $arrayData = $result->orderBy([
            'id' => SORT_DESC
            ])->all();

       
        }
      
        if (!$arrayData)
            \Yii::$app->getSession()->setFlash('error', 'Nenhum resultado encontrado.');
        return $this->render('index', [
            'arrayData' => $arrayData,
            'titulo' => $titulo,
            'get' => $get
        ]);
    }
    /**
     * Displays a single Log model.
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
     * Creates a new Log model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Log();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Log model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
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
     * Deletes an existing Log model.
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
     * Finds the Log model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Log the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Log::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
