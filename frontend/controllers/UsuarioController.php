<?php

namespace frontend\controllers;

use Yii;
use common\models\Usuario;
use common\models\UsuarioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\AccessRule;
use yii\filters\AccessControl;
use yii\helpers\BaseHtml;
use common\models\UsuarioGrupo;
use common\models\Configuracao;
use common\models\TipoDocumento;
/**
 * UsuarioController implements the CRUD actions for Usuario model.
 */
class UsuarioController extends Controller
{
    /**
     * {@inheritdoc}
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
            'access' => [
                'class' => AccessControl::className(),
                // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['update', 'delete', 'index', 'view'],
                'rules' => [
                    [
                        'actions' => ['update', 'delete', 'index'],
                        'allow' => true,
                        // Allow moderators and admins to update
                        'roles' => [
                          '@'
                          //  Usuario::PERFIL_SUPER_ADMIN,
                        ],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        // Allow moderators and admins to update
                        'roles' => [
                            '@'
                            //Usuario::PERFIL_SUPER_ADMIN,
                        ],
                    ],
                ],
            ]
        ];
    }
    public function actionBatchPassword(){
        $usuarios = Usuario::find()->all();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        foreach ($usuarios as $u) {
            if(!$u->passwordHash || $u->passwordHash == '123'){
                $config = Configuracao::setup();
                $u->setPassword($config->senhaPadrao);
                $u->generateAuthKey();
                $u->generatePasswordResetToken();
                $u->save();
                if(!$u->save()){
                    return $u->getErrors();
                }
            }
        }
    }
    /**
     * Lists all Usuario models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuarioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Lists all Usuario models.
     * @return mixed
     */
    public function actionNovaSenha()
    {
        $this->layout = 'main-login';
        $model = $this->findModel(\Yii::$app->User->identity->id);
        if ($model->load(Yii::$app->request->post()) ) {

            // $senhaAntiga = Yii::$app->security->generatePasswordHash($model->senhaAntiga);

            if ($model->validatePassword($model->senhaAntiga))
            {                
                if ($model->password)
                {   
                    $model->setPassword($model->password);
                    $model->generateAuthKey();
                    $model->generatePasswordResetToken();
                }
        
                if ($model->save()) {
    
                    \Yii::$app->getSession()->setFlash('success', 'Senha cadastrada com sucesso.');
                    return $this->redirect(['site/index']);
                }
                \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar o usuário.');
            }
            else
            {
                \Yii::$app->getSession()->setFlash('error', 'A senha antiga está incorreta.');
                return $this->redirect(['site/index']);
            }

        }
        
        return $this->render('nova-senha', [
            'model' => $model,
        ]);
      }
            
    public function actionValidaSenhaAntiga($idUsuario, $senhaAntiga)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = Usuario::findOne($idUsuario);

        return $model->validatePassword($senhaAntiga);
    }


    /**
     * Displays a single Usuario model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    private function salvarGrupos($post,$model){
        UsuarioGrupo::deleteAll(['idUsuario' => $model->id]);
        if( !empty($post['Usuario']['inputGrupo']) ) {
            foreach ($post['Usuario']['inputGrupo'] as $key => $value) {
                $modelGrupo = new UsuarioGrupo();
                $modelGrupo->idUsuario = $model->id;
                $modelGrupo->idGrupo = $value;
                if (!$modelGrupo->save())
                {
                    \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar grupos');
                }
            }
        }
    }


    /**
     * Creates a new Usuario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Usuario();
        $desabilitarPerfil = false;
        if ($model->load(Yii::$app->request->post()) ) {
            $model->cpf = Usuario::limparCPF($model->cpf);
            if($model->idPerfil == Usuario::PERFIL_CONDUTOR || $model->idPerfil == Usuario::PERFIL_RESPONSAVEL){
                $desabilitarPerfil = true;
            }

            if ($model->password)
            {   

                $model->setPassword($model->password);
                $model->generateAuthKey();
                $model->generatePasswordResetToken();
            }
    
            if (!$desabilitarPerfil && $model->validate() && $model->save()) {
                $this->salvarGrupos(Yii::$app->request->post(), $model);

               //s \Yii::$app->getSession()->setFlash('success', 'Usuário criado com sucesso');
                return $this->redirect(['usuario/view', 'id' => $model->id]);
            } else {
               // \Yii::$app->getSession()->setFlash('error', BaseHtml::errorSummary($model, ['header'=>'Erro ao salvar o usuário.']));
            }
        }
          return $this->render('create', [
            'model' => $model,
            'desabilitarPerfil' => $desabilitarPerfil
        ]);
    }
     

    /**
     * Updates an existing Usuario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save())
            {
                $this->salvarGrupos(Yii::$app->request->post(), $model);
    
                if ($model->password)
                {
                    $model->setPassword($model->password);
                    $model->save();
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
            else
                \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar o usuário. '.print_r($model->getErrors(), true));
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    private function actionUploadFile($model, $file, $idTipoDocumento)
    {

        $arquivos = UploadedFile::getInstances($model, $file);

        if ($arquivos) {
            //DocumentoCondutor::deleteAll(['idCondutor' => $model->id, 'idTipo' => $idTipoDocumento]);   
            $documentos = DocumentoCondutor::find()->andWhere(['idCondutor' => $model->condutor->id])->andWhere(['idTipo' => $idTipoDocumento])->all();
            foreach ($documentos as $documento) {
                $documento->delete();
            }
            $dirBase = Yii::getAlias('@webroot') . '/';
            $dir = 'arquivos/' . $idTipoDocumento . '/';

            if (!file_exists($dirBase . $dir))
                mkdir($dir, 0777, true);

            $i = 1;
            foreach ($arquivos as $arquivo) {
                $nomeArquivo = $idTipoDocumento . '_' . time() . '_' . $i . '.' . $arquivo->extension;
                $arquivo->saveAs($dirBase . $dir . $nomeArquivo);

                $modelDocumento = new DocumentoCondutor();
                $modelDocumento->nome = $nomeArquivo;
                $modelDocumento->idCondutor = $model->condutor->id;
                $modelDocumento->arquivo = $dir . $nomeArquivo;
                $modelDocumento->idTipo = $idTipoDocumento;
                $modelDocumento->dataCadastro = date('Y-m-d H:i:s');
                $modelDocumento->save();

                $i++;
            }
        }
    }

    private function uploadMultiple($model)
    {
        $this->actionUploadFile($model, 'documentoCRLV', TipoDocumento::TIPO_CRLV);
        $this->actionUploadFile($model, 'documentoApoliceSeguro', TipoDocumento::TIPO_APOLICE_SEGURO);
        $this->actionUploadFile($model, 'documentoAutorizacaoEscolar', TipoDocumento::TIPO_AUTOTIZACAO_ESCOLAR);
        $this->actionUploadFile($model, 'documentoProntuarioCNH', TipoDocumento::TIPO_PRONTUARIO_CNH);

        $this->actionUploadFile($model, 'documentoComprovanteEndereco', TipoDocumento::TIPO_COMPROVANTE_ENDERECO);
        $this->actionUploadFile($model, 'documentoCNHCondutor', TipoDocumento::TIPO_CNH);
        $this->actionUploadFile($model, 'documentoContrato', TipoDocumento::TIPO_CONTRATO);
        $this->actionUploadFile($model, 'documentoMonitorRG', TipoDocumento::TIPO_RG_MONITOR);
        $this->actionUploadFile($model, 'documentoMonitorCPF', TipoDocumento::TIPO_CPF_MONITOR);
        $this->actionUploadFile($model, 'documentoMonitorContratoTrabalho', TipoDocumento::TIPO_CONTRATO_TRABALHO);
        $this->actionUploadFile($model, 'documentoMonitorCertidaoAntecedentesCriminais', TipoDocumento::TIPO_CERTIDAO_ANTECEDENTES_CRIMINAIS);
        $this->actionUploadFile($model, 'documentoCertidaoInscricaoMunicipal', TipoDocumento::TIPO_CERTIDAO_INSCRICAO_MUNICIPAL);
        $this->actionUploadFile($model, 'documentoDebitosMunicipais', TipoDocumento::TIPO_CERTIDAO_NEGATIVA_DEBITOS_MUNICIPAIS);
        $this->actionUploadFile($model, 'documentoCertidaoNegativaAcoesCiveis', TipoDocumento::TIPO_CERTIDAO_NEGATIVA_ACOES_CIVEIS);
    }

    // public function actionUsuarioExistente(){
      
    // }
    /**
     * Deletes an existing Usuario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Usuario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Usuario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuario::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
