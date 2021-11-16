<?php

namespace frontend\controllers;

use Yii;
use common\models\ReciboPagamentoAutonomo;
use common\models\ReciboPagamentoAutonomoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\DocumentoReciboPagamentoAutonomo;

use yii\web\UploadedFile;
use common\models\TipoDocumento;
use yii\filters\AccessControl;

use common\components\AccessRule;
use common\models\Usuario;

/**
 * ReciboPagamentoAutonomoController implements the CRUD actions for ReciboPagamentoAutonomo model.
 */
class ReciboPagamentoAutonomoController extends Controller
{
//     /**
//      * @inheritdoc
//      */

//     public function behaviors()
//     {
//         return [
//             'verbs' => [
//                 'class' => VerbFilter::className(),
//                 'actions' => [
//                     'delete' => ['POST'],
//                 ],
//             ],
//             'access' => [
//                 'class' => AccessControl::className(),
//                 // We will override the default rule config with the new AccessRule class
//                 'ruleConfig' => [
//                     'class' => AccessRule::className(),
//                 ],
//                 'only' => ['update', 'index','view','create','delete'],
//                 'rules' => [
//                     [
//                         'actions' => ['update','index','view','create','delete'],
//                         'allow' => true,
//                         // Allow moderators and admins to update
//                         'roles' => [
                            
//                             // Usuario::PERFIL_SUPER_ADMIN,
//                             // Usuario::PERFIL_TESC_DISTRIBUICAO,
//                             // Usuario::PERFIL_TESC_PASSE_ESCOLAR,
//                             // Usuario::TESC_CONSULTA
//                         ],
//                     ],
//                 ],
//             ]
//         ];
//     }

//     /**
//      * Lists all ReciboPagamentoAutonomo models.
//      * @return mixed
//      */
//     public function actionIndex()
//     {
//         $searchModel = new ReciboPagamentoAutonomoSearch();
//         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

//         return $this->render('index', [
//             'searchModel' => $searchModel,
//             'dataProvider' => $dataProvider,
//         ]);
//     }

//     /**
//      * Displays a single ReciboPagamentoAutonomo model.
//      * @param integer $id
//      * @return mixed
//      */
//     public function actionView($id)
//     {
//         return $this->render('view', [
//             'model' => $this->findModel($id),
//         ]);
//     }

//     /**
//      * Creates a new ReciboPagamentoAutonomo model.
//      * If creation is successful, the browser will be redirected to the 'view' page.
//      * @return mixed
//      */
//     public function actionCreate()
//     {
//         $model = new ReciboPagamentoAutonomo();
            

//         if ($model->load(Yii::$app->request->post()) && $model->save()) {
//             $model = $this->getDates($model);
//             $ultimoId = ReciboPagamentoAutonomo::find()->where(['idCondutor' => $model->idCondutor])->max('numRecibo');
//             $model->numRecibo = $ultimoId + 1;
//             $model->save();

//             DocumentoReciboPagamentoAutonomo::deleteAll(['idRecibo' => $model->id]);
//             $this->actionUploadFile($model, 'documentoRecibo', TipoDocumento::TIPO_RECIBO_PAGAMENTO_AUTONOMO);

//            // return $this->redirect(['view', 'id' => $model->id]);
//            return $this->redirect(['pdf/rpa', 'idRecibo' => $model->id, 'pdf' => 1]);

//         } else {
//             return $this->render('create', [
//                 'model' => $model,
//             ]);
//         }
//     }

//     /**
//      * Updates an existing ReciboPagamentoAutonomo model.
//      * If update is successful, the browser will be redirected to the 'view' page.
//      * @param integer $id
//      * @return mixed
//      */
//     public function actionUpdate($id)
//     {
//         $model = $this->findModel($id);

//         if ($model->load(Yii::$app->request->post()) && $model->save()) {
//             $model = $this->getDates($model);
//             $model->save();

//          //DocumentoReciboPagamentoAutonomo::deleteAll(['idRecibo' => $model->id]);
//          $this->actionUploadFile($model, 'documentoRecibo', TipoDocumento::TIPO_RECIBO_PAGAMENTO_AUTONOMO);
//             return $this->redirect(['view', 'id' => $model->id]);
//         } else {
//              $model = $this->getDatesBr($model);
//             return $this->render('update', [
//                 'model' => $model,
//             ]);
//         }
//     }
//     private function actionUploadFile($model,$file, $idTipoDocumento){

//     $arquivos = UploadedFile::getInstances($model, $file);

//     if ($arquivos)
//     {

//         $dirBase = Yii::getAlias('@webroot').'/';
//         $dir = 'arquivos/'.$idTipoDocumento.'/';

//         if (!file_exists($dirBase.$dir))
//           mkdir($dir, 0777, true);

//       $i = 1;
//       foreach ($arquivos as $arquivo)
//       {
//         $nomeArquivo = $idTipoDocumento.'_'.time().'_'.$i.'.'.$arquivo->extension;
//         $arquivo->saveAs($dirBase.$dir.$nomeArquivo);

//         $modelDocumento = new DocumentoReciboPagamentoAutonomo();
//         $modelDocumento->nome = $nomeArquivo;
//         $modelDocumento->idRecibo = $model->id;
//         $modelDocumento->arquivo = $dir.$nomeArquivo;
//         $modelDocumento->idTipo = $idTipoDocumento;
//         $modelDocumento->dataCadastro = date('Y-m-d');
//         $modelDocumento->save();

//         $i++;
//     }
// } 
// }
//     /**
//      * Deletes an existing ReciboPagamentoAutonomo model.
//      * If deletion is successful, the browser will be redirected to the 'index' page.
//      * @param integer $id
//      * @return mixed
//      */
//     public function actionDelete($id)
//     {
//         $this->findModel($id)->delete();

//         return $this->redirect(['index']);
//     }

//     /**
//      * Finds the ReciboPagamentoAutonomo model based on its primary key value.
//      * If the model is not found, a 404 HTTP exception will be thrown.
//      * @param integer $id
//      * @return ReciboPagamentoAutonomo the loaded model
//      * @throws NotFoundHttpException if the model cannot be found
//      */
//     protected function findModel($id)
//     {
//         if (($model = ReciboPagamentoAutonomo::findOne($id)) !== null) {
//             return $model;
//         } else {
//             throw new NotFoundHttpException('The requested page does not exist.');
//         }
//     }

//         private function getDates($model){




//      $data = \DateTime::createFromFormat ( 'd/m/Y', $model->data);
//      if ($data)
//         $model->data = $data->format('Y-m-d');

//     return $model;
// }

// private function getDatesBr($model){

//     $data = \DateTime::createFromFormat ( 'Y-m-d', $model->data);
//     if ($data)
//         $model->data = $data->format('d/m/Y');


//     return $model;
// }
}
