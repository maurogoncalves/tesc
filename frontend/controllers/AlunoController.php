<?php

namespace frontend\controllers;

use Yii;
use common\models\Aluno;
use common\models\DocumentoAluno;
use common\models\AlunoCurso;
use common\models\AlunoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\data\ArrayDataProvider;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\web\UploadedFile;
use common\models\TipoDocumento;
use common\models\AlunoNecessidadesEspeciais;
use common\models\Usuario;
use common\models\PontoAluno;

use yii\helpers\ArrayHelper;

use common\models\Configuracao;
use common\models\EscolaDiretor;
use common\models\Escola;
use yii\helpers\Url;
use common\models\EscolaSecretario;

/**
 * AlunoController implements the CRUD actions for Aluno model.
 */
class AlunoController extends Controller
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
                    ]
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
     * Lists all Aluno models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(\Yii::$app->User->identity->idPerfil == 0)
            die('Você não está autorizado a acessar esta página');

        //Go horse para forçar o usuário a trocar a senha no primeiro acesso
        $config = Configuracao::setup();

        // if(\Yii::$app->User->identity){
        //     $usuario = Usuario::findOne(\Yii::$app->User->identity->id);
        //     if($usuario->validatePassword($config->senhaPadrao))
        //       $this->redirect(['usuario/nova-senha']);
        // } 
        if (Usuario::permissao(Usuario::PERFIL_CONDUTOR)) {
            $this->redirect(['condutor/alunos']);
        }
        $searchModel = new AlunoSearch();
        // if(Usuario::permissao(Usuario::PERFIL_DIRETOR) )
        //   $searchModel->idEscola = 1739;

        $models = $searchModel->search(Yii::$app->request->queryParams);




        $dataProvider = new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $models,
            'sort' => [
                'attributes' => ['condutor', 'nome', 'RA', 'idEscola', 'redeEnsino', 'ensino', 'modalidadeBeneficio', 'tipoFrete', 'status', 'serie', 'turma'],
            ],
            'pagination' => [
                'pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20,
            ],
        ]);

        switch (\Yii::$app->User->identity->idPerfil) {
            case Usuario::PERFIL_SECRETARIO:
                $escolas = [];
                foreach (\Yii::$app->User->identity->secretarios as $registro)
                    array_push($escolas, $registro->escola);
                break;
            case Usuario::PERFIL_DIRETOR:
                $escolas = [];
                foreach (\Yii::$app->User->identity->diretores as $registro)
                    array_push($escolas, $registro->escola);

                break;
            default:
                $escolas = Escola::find()->rightJoin('Aluno', 'Aluno.idEscola=Escola.id')->all();
                break;
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'escolas' => $escolas,
        ]);
    }

    /**
     * Displays a single Aluno model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        // print_r($ponto);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Aluno model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Aluno();
		
		

        $cadastroOK = true;
        if ($model->load(Yii::$app->request->post())) {
            $RAcompleto = $model->RA . $model->RAdigito;
            $arrayRA = Aluno::find()->asArray()->all();
            $strs = [];

            foreach ($arrayRA as $reg) {
                array_push($strs, $reg['RA'] . $reg['RAdigito']);
            }

            if (in_array($RAcompleto, $strs)) {
                $cadastroOK = false;
                \Yii::$app->getSession()->setFlash('error', 'O RA do aluno deve ser único.');
            }
            $novaSenha = $model->dataNascimentoResponsavel;
            $model = $this->getDates($model);
            if ($cadastroOK && $model->save()) {
                //$model->save();
            
                $this->criarUsuario($model, $novaSenha);
                $this->uploadMultiple($model);
                $this->salvarCursoLivre(Yii::$app->request->post(), $model);
                $this->salvarNecessidades($model->necessidadesEspeciais, $model);
                return $this->redirect(['index']);
            } else {
                \Yii::$app->getSession()->setFlash('error', 'Ocorreram erros no formulário. Verifique por favor.');
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    private function salvarCursoLivre($post, $model)
    {
        AlunoCurso::deleteAll(['idAluno' => $model->id]);
        if (!empty($post['Aluno']['inputCursoLivre'])) {
            foreach ($post['Aluno']['inputCursoLivre'] as $key => $value) {
                $modelGrupo = new AlunoCurso();
                $modelGrupo->idAluno = $model->id;
                $modelGrupo->dia = $value;
                if (!$modelGrupo->save()) {
                    if ($modelGrupo->getErrors()) {
                        \Yii::$app->getSession()->setFlash('error', Html::errorSummary($modelGrupo, ['header' => 'Erro ao salvar dias da semana.']));
                    }
                }
            }
        }
    }

    private function getDates($model)
    {
        $data = \DateTime::createFromFormat('d/m/Y', $model->dataNascimento);
        if ($data)
            $model->dataNascimento = $data->format('Y-m-d');
        $data = \DateTime::createFromFormat('d/m/Y', $model->dataNascimentoResponsavel);
        if ($data)
            $model->dataNascimentoResponsavel = $data->format('Y-m-d');

        return $model;
    }

    private function getDatesBr($model)
    {
        $data = \DateTime::createFromFormat('Y-m-d', $model->dataNascimento);

        if ($data && $model->dataNascimento != '0000-00-00')
            $model->dataNascimento = $data->format('d/m/Y');
        else
            $model->dataNascimento = '';

        $data = \DateTime::createFromFormat('Y-m-d', $model->dataNascimentoResponsavel);
        if ($data && $model->dataNascimentoResponsavel != '0000-00-00')
            $model->dataNascimentoResponsavel = $data->format('d/m/Y');
        else
            $model->dataNascimentoResponsavel = '';
        return $model;
    }

    private function uploadMultiple($model)
    {
        $this->actionUploadFile($model, 'documentoRgAluno', TipoDocumento::TIPO_RG_ALUNO);
        $this->actionUploadFile($model, 'documentoComprovanteEndereco', TipoDocumento::TIPO_COMPROVANTE_ENDERECO);
        $this->actionUploadFile($model, 'documentoInexistenciaVaga', TipoDocumento::TIPO_DECLARACAO_INEXISTENCIA_VAGA);
        $this->actionUploadFile($model, 'documentoDeclaracaoVizinho', TipoDocumento::TIPO_DECLARACAO_VIZINHOS);
        $this->actionUploadFile($model, 'documentoTransporteEspecial', TipoDocumento::TIPO_DECLARACAO_TRANSPORTE_ESPECIAL);
        $this->actionUploadFile($model, 'documentoRgResponsavel', TipoDocumento::TIPO_RG_RESPONSAVEL);
        $this->actionUploadFile($model, 'documentoLaudoMedico', TipoDocumento::TIPO_LAUDO_MEDICO);
    }
    private function actionUploadFile($model, $file, $idTipoDocumento)
    {

        $arquivos = UploadedFile::getInstances($model, $file);

        if ($arquivos) {
            //print 'DELETED '.$idTipoDocumento;
            //DocumentoAluno::deleteAll(['idAluno' => $model->id, 'idTipo' => $idTipoDocumento]);                   DocumentoAluno::deleteAll(['idAluno' => $model->id, 'idTipo' => $idTipoDocumento]);
            $documentos = DocumentoAluno::find()->andWhere(['idAluno' => $model->id])->andWhere(['idTipo' => $idTipoDocumento])->all();
            // print_r($documentos);        
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

                $modelDocumento = new DocumentoAluno();
                $modelDocumento->nome = $nomeArquivo;
                $modelDocumento->idAluno = $model->id;
                $modelDocumento->arquivo = $dir . $nomeArquivo;
                $modelDocumento->idTipo = $idTipoDocumento;
                $modelDocumento->dataCadastro = date('Y-m-d H:i:s');
                $modelDocumento->save();

                $i++;
            }
        }
    }
    private function salvarNecessidades($input, $model)
    {
        AlunoNecessidadesEspeciais::deleteAll(['idAluno' => $model->id]);
        if (isset($input) && !empty($input)) {
            foreach ($input as $key => $value) {
                $modelGrupo = new AlunoNecessidadesEspeciais();
                $modelGrupo->idAluno = $model->id;
                $modelGrupo->idNecessidadesEspeciais = $value;
                if (!$modelGrupo->save()) {
                    \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar necessidades especiais');
                }
            }
        }
    }
    private function criarUsuario($model, $novaSenha)
    {
        $usuario = Usuario::findOne(['cpf' => $model->cpfResponsavel]);
        if (!$usuario) {
            $usuario = new Usuario();
        }
        $usuario->nome = 'RESPONSÁVEL ' . $model->cpfResponsavel;
        $usuario->username = $model->cpfResponsavel;
        $usuario->cpf = $model->cpfResponsavel;
        $usuario->setPassword(str_replace('/', '', $novaSenha));
        $usuario->idPerfil = Usuario::PERFIL_RESPONSAVEL;
        $usuario->generateAuthKey();
        $usuario->generatePasswordResetToken();
        $usuario->save();

        // $model->idUsuario = $usuario->id;
        $model->save();
    }
    // public function actionUpdatexx($id)
    // // {
    // //     // ini_set('post_max_size', '64M');
    // //     // ini_set('upload_max_filesize', '64M');
    // //     $model = $this->findModel($id);

    // //     if ($model->load(Yii::$app->request->post())) {
    // //         $arquivo = UploadedFile::getInstance($model, 'anexo');
    // //         if (isset($arquivo))
    // //         {
    // //             $nomeArquivo = 'audio-'.time().'.'.$arquivo->extension;
    // //             $dirBase = \Yii::getAlias('@webroot').'/';
    // //             $dir = 'audios/';   
    // //             if (!file_exists($dirBase.$dir))
    // //               mkdir($dir, 0777, true);
    // //             $arquivo->saveAs($dirBase.$dir.$nomeArquivo);
    // //             $model->anexo = $dir.$nomeArquivo;

    // //         }
    // //         if( $model->save() ){

    // //             $this->uploadDocumentos($model);
    // //         }

    // //          return $this->redirect(['view', 'id' => $model->id]);
    // //     }

    // //     return $this->render('update', [
    // //         'model' => $model,
    // //     ]);
    // // }
    /**
     * Updates an existing Aluno model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id,$redirect=false)
    {
        $model = $this->findModel($id);
        $oldNumero = $model->numeroResidencia;
        $oldTurma = $model->turma;
        $oldSerie = $model->serie;
		$oldCep = $model->cep;
		$oldEndereco = $model->endereco;
		$oldBairro = $model->bairro;
		$oldCidade = $model->cidade;
		$oldTipoLogradouro = $model->tipoLogradouro;
		$oldHorarioEntrada = $model->horarioEntrada;
		$oldHorarioSaida = $model->horarioSaida;
		$oldTurno = $model->turno;
		$model->atualiza_endereco_renovacao = 0;
        if ($model->load(Yii::$app->request->post())) {
            // throw new NotFoundHttpException(print_r($model, true));
            $RAcompleto = $model->RA . $model->RAdigito;
            $arrayRA = Aluno::find()->where(['<>', 'id', $model->id])->asArray()->all();
            $strs = [];
            $cadastroOK = true;
            foreach ($arrayRA as $reg) {
                array_push($strs, $reg['RA'] . $reg['RAdigito']);
            }

            if (in_array($RAcompleto, $strs)) {
                $cadastroOK = false;
            }
            $novaSenha = str_replace('/', '', $model->dataNascimento);
            $model = $this->getDates($model);
			
			if($redirect == '1'){
				$model->atualiza_endereco_renovacao = '1';
			}else{
				if ($model->tipoLogradouro != $oldTipoLogradouro)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->cidade != $oldCidade)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->bairro != $oldBairro)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->turno != $oldTurno)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->horarioEntrada != $oldHorarioEntrada)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->horarioSaida != $oldHorarioSaida)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->endereco != $oldEndereco)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->cep != $oldCep)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->numeroResidencia != $oldNumero)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->numeroResidencia != $oldNumero)
					$model->encerrarSolicitacoesViaCadastro();
				if ($model->turma != $oldTurma)
					//$model->encerrarSolicitacoesViaCadastro();
					$model->atualiza_endereco_renovacao = '1';
				if ($model->serie != $oldSerie)
					//$model->encerrarSolicitacoesViaCadastro();
					$model->atualiza_endereco_renovacao = '1';
			}
				
            

            if ($cadastroOK && $model->save()) {
                $this->uploadMultiple($model);
                $this->salvarCursoLivre(Yii::$app->request->post(), $model);
                $this->salvarNecessidades($model->necessidadesEspeciais, $model);

                $usuario = Usuario::findOne(['cpf' => Usuario::limparCPF($model->cpfResponsavel)]);
                if ($usuario)
                {
                    $usuario->setPassword($novaSenha);
                    $usuario->save();
                }
				if($redirect == '1'){
					//return $this->redirect(['renovacao/index&ra='.$model->RA]);
					return $this->redirect(['renovacao/index', 'ra' => $model->RA,'idAluno' => $model->id]);
				}else{
					return $this->redirect(['view', 'id' => $model->id]);
				}
                
            } else {
                \Yii::$app->getSession()->setFlash('error', 'O RA do aluno deve ser único.');
            }
        }

        $model = $this->getDatesBr($model);
        return $this->render('update', [
            'model' => $model,
			'redirect' => $redirect,
        ]);
    }

    /** 
     * Deletes an existing Aluno model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Aluno model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Aluno the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Aluno::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAlunoEscolaAjax($idEscola)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            return Aluno::find()->where(['idEscola' => $idEscola])->all();
        } catch (NotFoundHttpException $e) {
            return new Aluno;
        }
    }

    public function actionAlunoAjax($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            return $this->findModel($id);
        } catch (NotFoundHttpException $e) {
            return new Aluno;
        }
    }

    public function actionAlunoRa($ra, $digito)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $aluno = Aluno::find()->andWhere(['RA' => $ra])->andWhere(['RAdigito' => $digito])->one();
        if (!$aluno)
            return ['status' => false];
        return [
            'status' => true,
            'aluno' => $aluno,
            'redirect' => Url::toRoute(['aluno/update', 'id' =>  $aluno->id]),
        ];
    }
    public function actionArquivos($id, $tipo)
    {
        DocumentoAluno::deleteAll(['idAluno' => $id, 'idTipo' => $tipo]);
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDeleteDoc($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = DocumentoAluno::findOne($id);
        $arquivo = $model->arquivo;
        if ($model->delete()) {
            return [
                'status' => true,
                'message' => 'Documento excluído da base. ' . ((!unlink(Yii::$app->basePath . "/web/" . $arquivo)) ? 'Arquivo não excluído.' : ''),
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Erro ao excluir documento',
            ];
        }
    }
}