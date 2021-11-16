<?php

namespace frontend\controllers;

use Yii;
use common\models\Escola;
use common\models\EscolaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\EscolaSecretario;
use common\models\EscolaDiretor;
use common\models\EscolaAtendimento;
use common\models\EscolaAtendimentoHomologacao;
use common\models\EscolaHomologacao;
use common\models\Usuario;
use yii\helpers\Html;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;

/**
 * EscolaController implements the CRUD actions for Escola model.
 */
class EscolaController extends Controller
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

    public function actionAprovacaoInformacoes($id, $status){
        $homologacao = EscolaHomologacao::findOne(['idEscola' => $id]);

        switch($status){
            //Aprovar
            case 1: 
                $escola = $this->findModel($id);
                foreach($homologacao as $key=>$value){
                    if($key != 'idEscola' && $key != 'id')
                        $escola->$key = $value;
                }
                // fazer if de save
                $atendimentos = EscolaAtendimentoHomologacao::find()->where(['idEscola' => $id])->all();
                foreach($atendimentos as $atendimentoHomologacao){
                    if($key != 'id')
                        $atendimento = new EscolaAtendimento();
                        $atendimento->idEscola = $id;
                        $atendimento->idAtendimento = $atendimentoHomologacao->idAtendimento;
                        $atendimento->save(); 
                        $atendimentoHomologacao->delete();
                }
                if($escola->save()){
                    \Yii::$app->getSession()->setFlash('success', 'Aprovado com sucesso.');
                    $homologacao->delete();
                } else {
                    Yii::$app->getSession()->setFlash('error', Html::errorSummary($escola, ['header' => 'Não foi possível aprovar as alterações. Motivo:']));

                }

            break;
            //Reprovar
            case 2:
                \Yii::$app->getSession()->setFlash('success', 'Reprovado com sucesso.');
                $homologacao->delete();
            break;
        }

        return $this->redirect(['escola/index']);
    }
    // public function actionTestarRota(){
    //     $escolas = Escola::find()->where(['>','id', '100'])->all();
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    //         $out = [];
    //     foreach ($escolas as $as) {
    //         array_push($out, ['lat' => $as->lat, 'lng' => $as->lng, 'nome' => $as->nome]);
    //     }
    //     return $out;
    // }

    private function salvarAtendimento($post,$model){
        EscolaAtendimento::deleteAll(['idEscola' => $model->id]);
        if( !empty($post['Escola']['inputEnsino']) ) {
            foreach ($post['Escola']['inputEnsino'] as $key => $value) {
                $modelGrupo = new EscolaAtendimento();
                $modelGrupo->idEscola = $model->id;
                $modelGrupo->idAtendimento = $value;
                if (!$modelGrupo->save())
                {
                    \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar atendimento');
                }
            }
        }
    }
    private function salvarAtendimentoHomologacao($post,$model){
        EscolaAtendimentoHomologacao::deleteAll(['idEscola' => $model->id]);
        if( !empty($post['Escola']['inputEnsino']) ) {
            foreach ($post['Escola']['inputEnsino'] as $key => $value) {
                $modelGrupo = new EscolaAtendimentoHomologacao();
                $modelGrupo->idEscola = $model->id;
                $modelGrupo->idAtendimento = $value;
                if (!$modelGrupo->save())
                {
                    \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar atendimento');
                }
            }
        }
    }

    
    private function salvarDiretores($post,$model){
        EscolaDiretor::deleteAll(['idEscola' => $model->id]);
        if( !empty($post['Escola']['inputDiretores']) ) {
            foreach ($post['Escola']['inputDiretores'] as $key => $value) {
                $modelGrupo = new EscolaDiretor();
                $modelGrupo->idEscola = $model->id;
                $modelGrupo->idUsuario = $value;
                if (!$modelGrupo->save())
                {
                    \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar diretores');
                }
            }
        }
    }

    private function salvarSecretarios($post,$model){
        EscolaSecretario::deleteAll(['idEscola' => $model->id]);
        if( !empty($post['Escola']['inputSecretarios']) ) {
            foreach ($post['Escola']['inputSecretarios'] as $key => $value) {
                $modelGrupo = new EscolaSecretario();
                $modelGrupo->idEscola = $model->id;
                $modelGrupo->idUsuario = $value;
                if (!$modelGrupo->save())
                {
                    \Yii::$app->getSession()->setFlash('error', 'Erro ao salvar secretários');
                }
            }
        }
    }

    public function actionTipo($tipoEscola){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];        
        foreach (Escola::mountSelectTipo($tipoEscola) as $key => $value) {
            array_push($out, ['value' => $key, 'text' => $value]);
        }
        return $out;
    }

    /**
     * Lists all Escola models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EscolaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = ['pageSize' => isset($_GET['pageSize']) ? $_GET['pageSize'] : 20];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Escola model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Escola model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Escola();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->salvarSecretarios(Yii::$app->request->post(), $model);
            $this->salvarDiretores(Yii::$app->request->post(), $model);
            $this->salvarAtendimento(Yii::$app->request->post(), $model);
           return $this->redirect(['view', 'id' => $model->id]);
        } else {
           if($model->getErrors())
                Yii::$app->getSession()->setFlash('error', Html::errorSummary($model, ['header' => 'Corrija os erros abaixo:']));

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Escola model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $escola = Yii::$app->request->post('Escola');
        //($escola['unidade']);
        //SE já existe uma escola com dados em homologação não deixa criar a segunda
        if($model->homologacao) {
            \Yii::$app->getSession()->setFlash('error', 'Já existe uma solicitação de alteração de informações vigente. Aguarde um usuário perfil DRE atender à solicitação.');
            return $this->redirect(['escola/index']);

        }
        if($escola){
            if((Usuario::permissao(Usuario::PERFIL_SECRETARIO) || Usuario::permissao(Usuario::PERFIL_DIRETOR)) && $escola['unidade'] == Escola::UNIDADE_ESTADUAL) {
                $modeHmlgl = new EscolaHomologacao();
                $modeHmlgl->idEscola = $model->id;
                foreach($escola as $key => $value){
                    $modeHmlgl->$key = $value;
                }
                $this->salvarAtendimentoHomologacao(Yii::$app->request->post(), $model);
                
                if ($modeHmlgl->save()) {
                    \Yii::$app->getSession()->setFlash('success', 'As informações foram salvas com sucesso. Um usuário perfil DRE deverá homologar as alterações realizadas.');
                    return $this->redirect(['escola/index']);
                }
            } else {
                //print 'ok2';
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    //SEC e DIR não podem alterar seus usuários
                    if(!Usuario::permissao(Usuario::PERFIL_SECRETARIO) && !Usuario::permissao(Usuario::PERFIL_DIRETOR)){
                        $this->salvarSecretarios(Yii::$app->request->post(), $model);
                        $this->salvarDiretores(Yii::$app->request->post(), $model);
                        $this->salvarAtendimento(Yii::$app->request->post(), $model);
                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                }          
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionReport()
    {
        $searchModel = new EscolaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $content = '';
        // $content =  '<table width="100%">
        //     <tr>
        //         <td><b>CONDUTOR: </b>' . $condutor->nome . '</td>
        //         <td><b>ALVARÁ: </b>' . $condutor->alvara . '</td>
        //         <td><b>TELEFONE: </b>' . $condutor->telefone . '</td>
        //     </tr>
        //     <tr>
        //     <td><b>PERÍODO: </b>' . $periodo . '</td>

        //     </tr>
        //     </table>';

        $content .= '<table border="0" width="100%" class="table">';
        $content .= '
        <tr>
            <th><b>Unidade Escolar</b></th>
            <th><b>Tipo</b></th>
            <th><b>Ensino</b></th>
            <th><b>Nome</b></th>
            <th><b>Endereço</b></th>
            <th><b>Região</b></th>
            <th><b>Telefones</b></th>
            <th><b>E-mail</b></th>
            <th><b>Secretário(s)</b></th>
            <th><b>Diretor(es)</b></th>
            <th><b>Código CIE</b></th>
        </tr>';

        foreach ($dataProvider->getModels() as $model) {
            $content .= '<tr>';
            $content .= $this->td(10, $model->unidade ? Escola::ARRAY_UNIDADE[$model->unidade] : '-');
            $content .= $this->td(5, $model->tipo ? Escola::ARRAY_TIPO[$model->tipo] : '-');
            //Ensino
            $lista = '';
            foreach ($model->atendimento as $i => $atendimento)
            {
                if ($i > 0)
                    $lista .= '<hr>';

                $lista .= Escola::ARRAY_ENSINO[$atendimento->idAtendimento].'<br>';
            }
            $content .= $this->td(15, $lista);
            $content .= $this->td(15, $model->nome);
            $content .= $this->td(15, $model->endereco);
            $content .= $this->td(15, Escola::ARRAY_REGIAO[$model->regiao]);
            $content .= $this->td(10, $model->telefone);
            $content .= $this->td(5, $model->email);
            //Secretários
            $lista = '';
            foreach ($model->secretarios as $i => $secretario)
            {
                if ($i > 0)
                    $lista .= '<hr>';

                $lista .= $secretario->usuario->nome.'<br>';
                $lista .= '<b>CPF: </b>'.\Yii::$app->formatter->asCpf($secretario->usuario->cpf).'<br>';
                $lista .= '<b>RG: </b>'.\Yii::$app->formatter->asCpf($secretario->usuario->rg).'<br>';
                $lista .= '<b>E-mail: </b>'.$secretario->usuario->email;
            }
            $content .= $this->td(10, $lista);
            //Diretores
            $lista = '';
            foreach ($model->diretores as $i => $diretor)
            {
                if ($i > 0)
                    $lista .= '<hr>';

                $lista .= $diretor->usuario->nome.'<br>';
                $lista .= '<b>CPF: </b>'.\Yii::$app->formatter->asCpf($diretor->usuario->cpf).'<br>';
                $lista .= '<b>RG: </b>'.\Yii::$app->formatter->asCpf($diretor->usuario->rg).'<br>';
                $lista .= '<b>E-mail: </b>'.$diretor->usuario->email;
            }
            $content .= $this->td(10, $lista);

            $content .= $this->td(5, $model->codigoCie);
            $content .= '</tr>';
        }
        $content .= '</table>';

        $pdf = new Pdf([
            'mode' => 'c',
            'marginTop' => 50,
            'marginBottom' => 20,
            'marginLeft' => 5,
            'marginRight' => 5,
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .table table { border-collapse: collapse; } .table table, .table th, .table td { border: 1px solid black;} .table th td { padding-left: 3px;}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ['
                <table width="100%">
                <tr>
                  <Td align="center">
                  <img src="img/brasaoFull.png">
                  </Td>
                </tr>
              </table>'],
                'SetFooter' => ['Emitido em ' . date('d/m/Y') . '|| {PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    protected function tdCenter($tamanho, $content, $style = '')
    {
        return '<td width="' . $tamanho . '%" align="center">' . $content . '</td>';
    }
    protected function td($tamanho, $content, $style = '')
    {
        return '<td width="' . $tamanho . '%" >' . $content . '</td>';
    }

    /**
     * Deletes an existing Escola model.
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
     * Finds the Escola model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Escola the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Escola::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
