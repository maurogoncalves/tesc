<?php

namespace frontend\controllers;

use Yii;
use common\models\Aviso;
use common\models\AvisoSearch;
use common\models\DocumentoAviso;
use common\models\TipoDocumento;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
 
/**
 * AvisoController implements the CRUD actions for Aviso model.
 */
class AvisoController extends Controller
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

    private function uploadMultiple($model)
    {
        $this->actionUploadFile($model, 'documentoLegislacao', TipoDocumento::TIPO_AVISO_LEGISLACAO);
        $this->actionUploadFile($model, 'documentoFrete', TipoDocumento::TIPO_AVISO_FRETE);
        $this->actionUploadFile($model, 'documentoPasse', TipoDocumento::TIPO_AVISO_PASSE);
        $this->actionUploadFile($model, 'documentoOrientacoesSetor', TipoDocumento::TIPO_AVISO_ORIENTACOES_SETOR);
        $this->actionUploadFile($model, 'documentoAtualizacaoSistema', TipoDocumento::TIPO_AVISO_ATUALIZACOES_SISTEMA);
  
    }

    private function actionUploadFile($model, $file, $idTipoDocumento)
    {
       
        $arquivos = UploadedFile::getInstances($model, $file);
    
        if ($arquivos) {
            //print 'DELETED '.$idTipoDocumento;
            //DocumentoAluno::deleteAll(['idAluno' => $model->id, 'idTipo' => $idTipoDocumento]);		            DocumentoAluno::deleteAll(['idAluno' => $model->id, 'idTipo' => $idTipoDocumento]);
            $documentos = DocumentoAviso::find()->andWhere(['idAviso' => $model->id])->andWhere(['idTipo' => $idTipoDocumento])->all();
            // print_r($documentos);		
            // foreach ($documentos as $documento) {
            //     $documento->delete();
            // }
            $dirBase = Yii::getAlias('@webroot') . '/';
            $dir = 'arquivos/' . $idTipoDocumento . '/';

            if (!file_exists($dirBase . $dir))
                mkdir($dir, 0777, true);

            $i = 1;
            foreach ($arquivos as $arquivo) {
                $nomeArquivo = $idTipoDocumento . '_' . time() . '_' . $i . '.' . $arquivo->extension;
                $arquivo->saveAs($dirBase . $dir . $nomeArquivo);
                $modelDocumento = new DocumentoAviso();
                $modelDocumento->nome = $arquivo->name;
                $modelDocumento->idAviso = $model->id;
                $modelDocumento->arquivo = $dir . $nomeArquivo;
                $modelDocumento->idTipo = $idTipoDocumento;
                $modelDocumento->dataCadastro = date('Y-m-d H:i:s');
                if(!$modelDocumento->save())
                    print_r($modelDocumento->getErrors());
                $i++;
            }
        }
    }
    private function getDates($model)
    {

        $data = \DateTime::createFromFormat('d/m/Y', $model->data);
        if ($data)
            $model->data = $data->format('Y-m-d');

        return $model;
    }

    private function getDatesBr($model)
    {

        $data = \DateTime::createFromFormat('Y-m-d', $model->data);
        if ($data)
            $model->data = $data->format('d/m/Y');
        return $model;
    }
    /**
     * Lists all Aviso models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AvisoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Aviso models.
     * @return mixed
     */
    public function actionMeusAvisos()
    {
       
        
        $avisos = Aviso::find()
                    ->where(['<=', 'data', date('Y-m-d')])
                    ->limit(100)
                    ->orderBy(['fixado' => SORT_DESC,'data' => SORT_DESC])->all();

        return $this->render('meus-avisos', [
            'avisos' => $avisos,
        ]);
    }

    /**
     * Displays a single Aviso model.
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
     * Creates a new Aviso model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Aviso();

        if ($model->load(Yii::$app->request->post())) {
            $model = $this->getDates($model);
            $model->idUsuario = \Yii::$app->User->identity->id;
            $model->save();
         
            $this->uploadMultiple($model);

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Aviso model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model = $this->getDatesBr($model);
        if ($model->load(Yii::$app->request->post())) {
            $model = $this->getDates($model);
            $model->save();
            $this->uploadMultiple($model);

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Aviso model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteDoc($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = DocumentoAviso::findOne($id);
        $arquivo = $model->arquivo;
        if ($model->delete())
        {
            return [
                'status' => true,
                'message' => 'Documento excluído da base. '. ((!unlink(Yii::$app->basePath . "/web/" . $arquivo))?'Arquivo não excluído.':''),
            ];
        }
        else
        {
            return [
                'status' => false,
                'message' => 'Erro ao excluir documento',
            ];
        }
    }

    /**
     * Finds the Aviso model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Aviso the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Aviso::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
