<?php

namespace frontend\controllers;

use Yii;
use common\models\Calendario;
use common\models\CalendarioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\CalendarioEscola;
use common\models\CalendarioDia;
use common\models\Event;
use common\models\SolicitacaoCredito;
/**
 * CalendarioController implements the CRUD actions for Calendario model.
 */
class CalendarioController extends Controller
{
    // /**
    //  * @inheritdoc
    //  */
    // public function behaviors()
    // {
    //     return [
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 'delete' => ['POST'],
    //             ],
    //         ],
    //     ];
    // }

    // /**
    //  * Lists all Calendario models.
    //  * @return mixed
    //  */
    // public function actionIndex()
    // {
    //     $searchModel = new CalendarioSearch();
    //     $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    //     return $this->render('index', [
    //         'searchModel' => $searchModel,
    //         'dataProvider' => $dataProvider,
    //     ]);
    // } 

    // public function actionEventos($idCalendario){
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    //     $result = CalendarioDia::find()
    //     // ->with(['local', 'servico'])
    //      ->andWhere(['=', 'idCalendario', $idCalendario])
    //     ->all(); 
    //     $datasAgendadas = [];
    //     $fim = (date('Y')+10).'-12-31';
    //     $dias = SolicitacaoCredito::diasUteis(date('Y-01-01'),$fim,'dias');
    //     $finalDeSemana = SolicitacaoCredito::finalDeSemana(date('Y-01-01'),$fim,'dias');
    //     foreach ($result as $item){
    //             $Event = new Event();
    //             $Event->id = $item->id;
    //             $Event->start = $item->data;
    //             $Event->title = $item->descricao;
    //             $Event->color = $item->tipo == CalendarioDia::TIPO_COM_AULA ? '#00a65a' : '#dd4b39';
    //             $agendamentos[] = $Event;
    //             $datasAgendadas[] = $item->data;

    //     }
    //     foreach($dias as $dia){
    //         if(!in_array($dia, $datasAgendadas)){
    //             $Event = new Event();
    //             $Event->id = 0;
    //             $Event->start = $dia;
    //             $Event->title = 'Dia letivo padrão do sistema';
    //             $Event->color = '#00a65a';

    //             $agendamentos[] = $Event;
    //             $datasAgendadas[] = $item->data;
                                
    //         }
    //     }
    //     foreach($finalDeSemana as $dia){
    //         if(!in_array($dia, $datasAgendadas)){
    //             $Event = new Event();
    //             $Event->id = 0;
    //             $Event->start = $dia;
    //             $Event->title = 'Dia não letivo padrão do sistema';
    //             $Event->color = '#dd4b39';

    //             $agendamentos[] = $Event;
    //             $datasAgendadas[] = $item->data;
                                
    //         }
    //     }
        
    //     return $agendamentos;

    // }
    // /**
    //  * Displays a single Calendario model.
    //  * @param integer $id
    //  * @return mixed
    //  */
    // public function actionView($id)
    // {
    //     $model = $this->findModel($id);
        
       
    //     return $this->render('view', [
    //         'model' => $model, 
           
    //     ]);
    // }

    // private function salvarEscolas($post,$model){
    //     CalendarioEscola::deleteAll(['idCalendario' => $model->id]);
    //     if( !empty($post['inputEscola']) ) {
    //         foreach ($post['inputEscola'] as $key => $value) {
    //             $modelGrupo = new CalendarioEscola();
    //             $modelGrupo->idCalendario = $model->id;
    //             $modelGrupo->tipoEscola = $value;
    //             if (!$modelGrupo->save())
    //             {
    //                 \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar escolar');
    //             }
    //         }
    //     }
    // }
    // /**
    //  * Creates a new Calendario model.
    //  * If creation is successful, the browser will be redirected to the 'view' page.
    //  * @return mixed
    //  */
    // public function actionCreate()
    // {
    //     $model = new Calendario();
     
      
    //     //print_r(Yii::$app->request->post());
    //     if (Yii::$app->request->post()) {
    //         $model->ano = date("Y");
    //         $model->save();
    //         //           salvarEscolassalvarEscolas  $this->salvarAtendimento(Yii::$app->request->post(), $model);
    //         $this->salvarEscolas(Yii::$app->request->post(), $model);
            
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
           
    //         return $this->render('create', [
    //             'model' => $model,
    //           //  'events' => $events,
    //         ]);
    //     }
    // }

    // public function actionCreateAgendamento(){
    //     $model = new CalendarioDia();
    //     if(Yii::$app->request->get('id')){
    //         $model->idCalendario = Yii::$app->request->get('id');
    //     }

    //     if($model->load(Yii::$app->request->post())){
    //          \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //          $model = $this->getDates($model);
    //          $agendamentoExistente = CalendarioDia::find()->where(['=','idCalendario', $model->idCalendario])->andWhere(['=','data', $model->data])->one();
    //          if($agendamentoExistente)
    //               return ['status' => false, 'validation' => ['data' => '1']];
            
    //         if($model->save()){
    //             return ['status' => true];
    //         }  else {
    //             return ['status' => false, 'validation' => $model->getErrors()];
    //         }
            
    //     }
 
    //     return $this->renderAjax('_formAjax', [
    //         'model' => $model,
    //         'action' => 'calendario/create-agendamento',
    //     ]);
       
    // } 

    // private function getDates($model){
    //     $data = \DateTime::createFromFormat ( 'd/m/Y', $model->data);
    //     if ($data)
    //         $model->data = $data->format('Y-m-d');
    //     return $model;
    // }

    // private function getDatesBr($model){
    //     $data = \DateTime::createFromFormat ( 'Y-m-d', $model->data);
    //     if ($data)
    //         $model->data = $data->format('d/m/Y');
    //     return $model;
    // }
    // /**
    //  * Updates an existing Calendario model.
    //  * If update is successful, the browser will be redirected to the 'view' page.
    //  * @param integer $id
    //  * @return mixed
    //  */
    // public function actionUpdate($id)
    // {
    //     $model = $this->findModel($id);

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('update', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    // /**
    //  * Deletes an existing Calendario model.
    //  * If deletion is successful, the browser will be redirected to the 'index' page.
    //  * @param integer $id
    //  * @return mixed
    //  */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();

    //     return $this->redirect(['index']);
    // }

    // public function actionCancelarEvento(){
    //     if(Yii::$app->request->post()){ 
         
    //         CalendarioDia::deleteAll(['id' => Yii::$app->request->post('evento')]);

    //         \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //         return ['status' => true];
    //     }
    //     return ['status' => false];
       
    // }

    // /**
    //  * Finds the Calendario model based on its primary key value.
    //  * If the model is not found, a 404 HTTP exception will be thrown.
    //  * @param integer $id
    //  * @return Calendario the loaded model
    //  * @throws NotFoundHttpException if the model cannot be found
    //  */
    // protected function findModel($id)
    // {
    //     if (($model = Calendario::findOne($id)) !== null) {
    //         return $model;
    //     } else {
    //         throw new NotFoundHttpException('The requested page does not exist.');
    //     }
    // }
}
