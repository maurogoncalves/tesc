<?php

namespace frontend\controllers;

use Yii;
use common\models\Escola;
use common\models\EscolaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\CondutorRota;

use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\services\DirectionsWayPoint;
use dosamigos\google\maps\services\TravelMode;
use dosamigos\google\maps\overlays\PolylineOptions;
use dosamigos\google\maps\services\DirectionsRenderer;
use dosamigos\google\maps\services\DirectionsService;
use dosamigos\google\maps\overlays\InfoWindow;
use dosamigos\google\maps\overlays\Marker;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\services\DirectionsRequest;
use dosamigos\google\maps\overlays\Polygon;
use dosamigos\google\maps\layers\BicyclingLayer;
use yii\filters\AccessControl;

/**
 * AtendimentoController implements the CRUD actions for Atendimento model.
 */
class RoteirizacaoController extends Controller
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
     * Lists all Atendimento models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new EscolaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }   

    public function actionRoteirizacao($id){
           $this->layout = 'main2';
        $escola = Escola::findOne($id);
 
        $manhaIda = CondutorRota::find()->where(['=','idEscola', $id])->andWhere(['=','turno',CondutorRota::TURNO_MANHA])->andWhere(['=','sentido', CondutorRota::SENTIDO_IDA])->all();
        $manhaVolta = CondutorRota::find()->where(['=','idEscola', $id])->andWhere(['=','turno',CondutorRota::TURNO_MANHA])->andWhere(['=','sentido', CondutorRota::SENTIDO_VOLTA])->all();
        $tardeIda = CondutorRota::find()->where(['=','idEscola', $id])->andWhere(['=','turno',CondutorRota::TURNO_TARDE])->andWhere(['=','sentido', CondutorRota::SENTIDO_IDA])->all();
        $tardeVolta = CondutorRota::find()->where(['=','idEscola', $id])->andWhere(['=','turno',CondutorRota::TURNO_TARDE])->andWhere(['=','sentido', CondutorRota::SENTIDO_VOLTA])->all();
        $noiteIda = CondutorRota::find()->where(['=','idEscola', $id])->andWhere(['=','turno',CondutorRota::TURNO_NOITE])->andWhere(['=','sentido', CondutorRota::SENTIDO_IDA])->all();
        $noiteVolta = CondutorRota::find()->where(['=','idEscola', $id])->andWhere(['=','turno',CondutorRota::TURNO_NOITE])->andWhere(['=','sentido', CondutorRota::SENTIDO_VOLTA])->all();



        return $this->render('roteirizacao', [
            'escola' => $escola,
            'manhaIda' => $manhaIda,
            'manhaVolta' => $manhaVolta,
            'tardeIda' => $tardeIda,
            'tardeVolta' => $tardeVolta,
            'noiteIda' => $noiteIda,
            'noiteVolta' => $noiteVolta,
        
        ]);

    }

    // /**
    //  * Displays a single Atendimento model.
    //  * @param string $id
    //  * @return mixed
    //  */
    // public function actionView($id)
    // {
    //     return $this->render('view', [
    //         'model' => $this->findModel($id),
    //     ]);
    // }

    // /**
    //  * Creates a new Atendimento model.
    //  * If creation is successful, the browser will be redirected to the 'view' page.
    //  * @return mixed
    //  */
    // public function actionCreate()
    // {
    //     $model = new Atendimento();

    //     if ($model->load(Yii::$app->request->post()) && $model->save()) {
    //         return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //         return $this->render('create', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    // /**
    //  * Updates an existing Atendimento model.
    //  * If update is successful, the browser will be redirected to the 'view' page.
    //  * @param string $id
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
    //  * Deletes an existing Atendimento model.
    //  * If deletion is successful, the browser will be redirected to the 'index' page.
    //  * @param string $id
    //  * @return mixed
    //  */
    // public function actionDelete($id)
    // {
    //     $this->findModel($id)->delete();

    //     return $this->redirect(['index']);
    // }

    // /**
    //  * Finds the Atendimento model based on its primary key value.
    //  * If the model is not found, a 404 HTTP exception will be thrown.
    //  * @param string $id
    //  * @return Atendimento the loaded model
    //  * @throws NotFoundHttpException if the model cannot be found
    //  */
    // protected function findModel($id)
    // {
    //     if (($model = Atendimento::findOne($id)) !== null) {
    //         return $model;
    //     } else {
    //         throw new NotFoundHttpException('The requested page does not exist.');
    //     }
    // }
}
