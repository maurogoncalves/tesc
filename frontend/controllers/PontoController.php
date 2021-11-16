<?php

namespace frontend\controllers;

use Yii;
use common\models\Ponto;
use common\models\PontoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Aluno;
use common\models\Escola;
use common\models\CondutorRota;

use common\models\SolicitacaoTransporte;

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
use dosamigos\google\maps\services\DirectionsClient;
use yii\filters\AccessControl;

/**
 * PontoController implements the CRUD actions for Ponto model.
 */
class PontoController extends Controller
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
     * Lists all Ponto models.
     * @return mixed
     */
    public function actionIndexAjax($idCondutorRota)
    {
        $searchModel = new PontoSearch();
        $queryParams= Yii::$app->request->getQueryParams();

        $queryParams['Ponto']['idCondutorRota'] = $idCondutorRota;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'idCondutorRota' => $idCondutorRota,
        ]);
    }

    /**
     * Displays a single Ponto model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

  
    public function actionTeste(){
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $aluno = Aluno::findOne(99);
    $trajeto = Yii::$app->distanceMatrix->singleRoute(
      [
        ['lat' => $aluno->lat, 'lng' => $aluno->lng],

      ],
      [
        ['lat' => $aluno->escola->lat, 'lng' => $aluno->escola->lng],
      ]);
        

        // print '<pre>';
        // print_r($trajeto);
        // print '</pre>';
        // exit(1);
        //print_r($escolas[0]);
        if($trajeto){
            $distanciaKm = $trajeto['distanceValue'] / 1000;
            $escolas = Escola::escolasProximas($aluno->lat, $aluno->lng, $distanciaKm, $aluno->escola->id);
        }


        return [
            'trajeto' => $trajeto,
            'aluno'   => Yii::$app->arrayPicker->pick([$aluno], ['lat','lng']),
            'escola'  => Yii::$app->arrayPicker->pick([$aluno], ['lat','lng']),
            'escolas' => Yii::$app->arrayPicker->pick($escolas, ['id','nome','endereco','lat','lng']),
        ];


    }

  


    /**
     * Displays a single Ponto model.
     * @param string $id
     * @return mixed
     */
    public function actionMapaAjax($idCondutorRota, $idEscola)
    {
      $rota = CondutorRota::findOne($idCondutorRota);
      $escola = Escola::findOne($idEscola);

      $coord = new LatLng(['lat' => $escola->lat, 'lng' => $escola->lng]);
        $map = new Map([
            'center' => $coord,
            'zoom' => 14,
        ]);

        $pontos = Ponto::find()->where(['=','idCondutorRota', $idCondutorRota])->orderBy(['distancia' => SORT_ASC])->all();
        if(!$pontos){
            print '<h1>Nenhum aluno(a) atribuído</h1>';
            exit();
        }
        // lets use the directions renderer
        $home = new LatLng(['lat' => $pontos[0]->lat, 'lng' => $pontos[0]->lng]);
        $school = new LatLng(['lat' => $escola->lat, 'lng' => $escola->lng]);

        // setup just one waypoint (Google allows a max of 8)
       $waypoints = [];
        for ($i=1; $i < count($pontos); $i++) { 
            $ponto = $pontos[$i];
            array_push($waypoints,  new DirectionsWayPoint(['location' => new LatLng(['lat' => $ponto->lat, 'lng' => $ponto->lng]) ]));
        }


        $directionsRequest = new DirectionsRequest([
            'destination' => $home,
            'origin' => $school,
            'waypoints' => $waypoints,
            'travelMode' => TravelMode::DRIVING
        ]);

        // Lets configure the polyline that renders the direction
        $polylineOptions = new PolylineOptions([
            'strokeColor' => '#FFAA00',
            'draggable' => true
        ]);

        // Now the renderer
        $directionsRenderer = new DirectionsRenderer([
            'map' => $map->getName(),
            'polylineOptions' => $polylineOptions
        ]);

        // Finally the directions service
        $directionsService = new DirectionsService([
            'directionsRenderer' => $directionsRenderer,
            'directionsRequest' => $directionsRequest
        ]);

        // Thats it, append the resulting script to the map
        $map->appendScript($directionsService->getJs());
        return $this->renderAjax('mapa', [
            'mapa' => $map,
            'rota' => $rota,
            'escola' => $escola 
        ]);
    }

    public function actionCreateAjax()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $model = new Ponto();
        $model->idCondutorRota = $post['idCondutorRota'];
        $model->idAluno = $post['idAluno'];
                
        if ($model->save()) {
            return ['status' => true];
            //return $this->redirect(['view', 'id' => $model->id]);
        } else {
           return ['status' => false, 'errors' => $model->getErrors()];
        }
    }


    /**
     * Creates a new Ponto model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // public function actionCreateAjax()
    // {
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //     $post = Yii::$app->request->post();
    //     $model = new Ponto();
    //     $model->idCondutorRota = $post['idCondutorRota'];
    //     $model->idAluno = $post['idAluno'];

    //     $aluno = Aluno::findOne($post['idAluno']);
    //     $model->lat = $aluno->lat;
    //     $model->lng = $aluno->lng;
    //     $form =  SolicitacaoTransporte::find()->where(['=', 'idAluno', $post['idAluno']])->one();;
    //     if($form){
    //         $model->distancia = $form->distanciaEscola;    
    //     }
        
    //     if ($model->save()) {
    //         return ['status' => true];
    //         //return $this->redirect(['view', 'id' => $model->id]);
    //     } else {
    //        return ['status' => false, 'errors' => $model->getErrors()];
    //     }
    // }

    /**
     * Updates an existing Ponto model.
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
     * Deletes an existing Ponto model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => true];
       // return $this->redirect(['index']);
    }

    /**
     * Finds the Ponto model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Ponto the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ponto::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
