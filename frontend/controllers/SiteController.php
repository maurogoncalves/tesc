<?php
namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\AccessRule;
use common\models\Usuario;
use common\models\UsuarioSearch;
use common\models\Configuracao;


use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\LoginForm;
use yii\web\NotFoundHttpException;
use yii\web\JsExpression; 

/**
 * Site controller                                           
 */
class SiteController extends Controller
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
                    'logout' => ['POST','GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                // We will override the default rule config with the new AccessRule class
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['logout', 'signup', 'admin'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['admin'],
                        'allow' => true,
                        // Allow users, moderators and admins to create
                        'roles' => [
                            Usuario::PERFIL_SUPER_ADMIN,
                        ],
                    ],
                ],
            ],
        ];
    }
 
    /*public function beforeAction($action) {
        if($action->id == 'portal' || $action->id == 'login')
            $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }*/


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'main-home';
        //$this->redirect('http://www.miniportal.com.br/projetos/edutopia/frontend/web/index.php?r=site/dashboard');
        return $this->redirect(['aluno/index']); 
        
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = '@app/views/layouts/main-login';


        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if(Usuario::permissao(Usuario::PERFIL_DRE) || Usuario::permissao(Usuario::PERFIL_DIRETOR) || Usuario::permissao(Usuario::PERFIL_SECRETARIO)){
                return $this->redirect(['aviso/meus-avisos', ['alerta'=>1]]);
            } elseif(Usuario::permissao(Usuario::PERFIL_SUPER_ADMIN) || Usuario::permissao(Usuario::PERFIL_TESC_DISTRIBUICAO)) {
                return $this->redirect(['solicitacao-transporte/solicitacoes-aguardando-atendimento']);
            } else {
                return $this->goBack();
            }
            if (!Yii::$app->user->isGuest) {
                return $this->goHome();
            }
        } else { 
            if (!Yii::$app->user->isGuest) { 
                if(Usuario::permissao(Usuario::PERFIL_DRE) || Usuario::permissao(Usuario::PERFIL_DIRETOR) || Usuario::permissao(Usuario::PERFIL_SECRETARIO)){
                return $this->redirect(['aviso/meus-avisos', ['alerta'=>1]]);
            } elseif(Usuario::permissao(Usuario::PERFIL_SUPER_ADMIN) || Usuario::permissao(Usuario::PERFIL_TESC_DISTRIBUICAO)) {
                return $this->redirect(['solicitacao-transporte/solicitacoes-aguardando-atendimento']);
            } else {
                return $this->goBack();
            }
            
                return $this->goHome();
            }
        // $c = new Usuario();
        // $c->setPassword('ikxr38');
        // print $c->passwordHash;
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    // public function actionSearch($street){
    //     \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
    //     date_default_timezone_set('America/Sao_Paulo');
    //     $street = urlencode($street); 
        
    //     $url = 'https://nominatim.openstreetmap.org/search/?street='.$street.'&country=Brasil&state=São+Paulo&format=json';
    //     $ch = curl_init();
        
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     return $response;
    // }

    public function actionPortal(){
        

        $token = Yii::$app->request->get('token');
        // if(!isset($token)){
        //        \Yii::$app->getSession()->setFlash('error', 'O Token não foi enviado');
        //         return $this->redirect(['site/login', 'model' => new LoginForm()]);
        // }
        if($token){
                    Yii::$app->user->logout();
        $idSistema = Configuracao::setup()->idAutenticacaoIPPLAN;

            try {   
                $client = Yii::$app->portalwcf;         

                $usuarioPortal = $client->ValidarToken(['token' => $token]);

                if($usuarioPortal){
                    $usuarioPortal = $usuarioPortal->ValidarTokenResult;
                } else {
                        \Yii::$app->getSession()->setFlash('error', 'Solicite ao suporte. Seu token de autenticação não é válido');
                    return $this->redirect(['site/login', 'model' => new LoginForm()]);  
                }
                // print_r($usuarioPortal);
                // exit(1);
                $usuarioDB = Usuario::findByUsername($usuarioPortal->Login);

                if($usuarioDB){ 
                    $usuarioDB->idPortal = $usuarioPortal->Id;
                    $usuarioDB->setPassword('123');

                    $usuarioDB->save();
                    if($usuarioDB->idPerfil == 0){
                        \Yii::$app->getSession()->setFlash('error', 'Solicite ao suporte para autorizar o seu usuário nesse sistema.');
                        return $this->redirect(['site/login', 'model' => new LoginForm()]);  
                    }
                } else {

                    if($usuarioPortal){
                        $usuarioDB = new Usuario();
                        $usuarioDB->idPerfil = 0;
                        $usuarioDB->email = $usuarioPortal->Email;
                         
                        $usuarioDB->username = $usuarioPortal->Login;
                        $usuarioDB->idPortal = $usuarioPortal->Id;
                        $usuarioDB->nome = $usuarioPortal->Nome;
                        // $usuarioDB->status = 0;
                        $usuarioDB->setPassword('123');
                        $usuarioDB->generateAuthKey();
                        $usuarioDB->generatePasswordResetToken();
                        //$usuarioDB->cpf = time();
                        $usuarioDB->save(false);
                    } else {
                        \Yii::$app->getSession()->setFlash('error', 'Realize o login através do portal.');

                        // return $this->redirect(['site/login', 'model' => new LoginForm()]);   
                    }
               
                    // $usuarioDB->username = ; 
                    // $usuarioDB->cpf = ;
                    \Yii::$app->getSession()->setFlash('error', 'Solicite ao suporte para autorizar o seu usuário nesse sistema.');
                    // return $this->redirect(['site/login', 'model' => new LoginForm()]);          

                }

            

                Yii::$app->user->login($usuarioDB, 0);
                return $this->redirect(['site/login', 'model' => new LoginForm()]); 

            } catch (Exception $e) {
                \Yii::$app->getSession()->setFlash('error', 'Erro ao realizar operação. Detalhes Técnicos: '.$e->getMessage());
                return $this->redirect(['site/login', 'model' => new LoginForm()]);          
            }
        }

    }
    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->identity->ultimoLogin = date("Y-m-d H:i:s");
        Yii::$app->user->identity->save();
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    // public function actionContact()
    // {
    //     $model = new ContactForm();
    //     if ($model->load(Yii::$app->request->post()) && $model->validate()) {
    //         if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
    //             Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
    //         } else {
    //             Yii::$app->session->setFlash('error', 'There was an error sending your message.');
    //         }

    //         return $this->refresh();
    //     } else {
    //         return $this->render('contact', [
    //             'model' => $model,
    //         ]);
    //     }
    // }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    // public function actionSignup($idRole=null)
    // {
    //     $model = new SignupForm();

    //     if ($model->load(Yii::$app->request->post())) {
            
    //         $model->username = $model->email;
    //         // if ($model->role == Usuario::PERFIL_EXTERNO || $model->role == Usuario::PERFIL_TERCEIRO)
    //         // {
    //         //     if ($configuracao)
    //         //         $model->status = $configuracao->status_padrao_usuario;
    //         //     else
    //         //         $model->status = 2;
    //         // }

    //         if ($user = $model->signup()) {
    //             if (Yii::$app->getUser()->login($user)) {
    //                 return $this->goHome();
    //             }
    //         }
    //     }

    //     return $this->render('signup', [
    //         'model' => $model,
    //     ]);
    // }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'main-login';

        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Em instantes você receberá um e-mail com as instruções para recuperar sua senha.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Desculpe, mas não encontramos este e-mail no nosso cadastro.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'main-login';

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post())) {

            if($model->validate()) {
                $model->resetPassword();
                Yii::$app->session->setFlash('success', 'Nova senha salva.');
            }
            else
            {
                Yii::$app->session->setFlash('error', 'Erro ao salvar nova senha.');
            }

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }


    // public function actionAdmin()
    // {
    //     $usuariosSearch = new UserSearch();
    //     $usuarios = $usuariosSearch->search(Yii::$app->request->queryParams);
    //     // $perfis = Perfil::find()->all();

    //     return $this->render('admin', [
    //             'usuarios' => $usuarios,
    //             'usuariosSearch' => $usuariosSearch,
    //             // 'perfis' => $perfis,
    //         ]);
    // }
}
