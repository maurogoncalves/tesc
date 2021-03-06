<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\models\SolicitacaoCredito;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;
use kartik\dialog\Dialog;
use common\models\Usuario;

AppAsset::register($this);
// isset($_GET['r']) ? preg_match('/\/(.*)/', $_GET['r'], $uri) : $uri[0] = '/index';
// if (!isset($uri[0])) $uri[0] = "/index";
function lastLogin()
{
    return date("d/m/Y H:i");
    // $lastLogin = Yii::$app->user->identity->ultimoLogin;
    // if($lastLogin && $lastLogin != '0000-00-00 00:00:00'){
    //     $d = new DateTime($lastLogin);
    //     return $d->format('d/m/Y H:i'); 
    // }
    // return date('d/m/Y H:i');
}


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
<meta charset="ISO-8859-1">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="css/custom-processo.css">
    <style>
        .select2-container--krajee .select2-selection--single {
            height: 34px !important;
            line-height: 1.8528571429 !important;
            /* padding: 10px 47px 6px 12px !important; */
            padding: 8px 25px 6px 12px;
        }

        .select2-container--krajee .select2-selection--single .select2-selection__clear {
            right: 1.3em;
            margin-top: -5px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            margin-right: 13px;
        }
    </style>
</head>

<body>
    <?php $this->beginBody() ?>
    <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js" ></script> -->

    <!-- <link rel="stylesheet/less" type="text/css" href="less/style.less"> -->

    <div class="wrap">
        <!-- <nav class="navbar navbar-default bg-cinza border-bottom-1 border-color-cinza-2 navbar-fixed"> -->
        <nav style="min-height:60px;" class="navbar navbar-default bg-cinza border-bottom-1 border-color-cinza-2">

            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= Url::toRoute(['site/index']); ?>">
                    <img src="img/TRANSPORTE_ESCOLAR_SJC_2.png" alt="" style="margin-top: -8px !important; max-height:30px;">

                </a>
            </div>
        <div id="navbar" class="navbar-collapse collapse navbar-title">
                <?= yii\bootstrap\Nav::widget(
                    [
                        'options' => ['class' => 'nav navbar-nav'],
                        'items' => [
                            [
                                'label' => 'Avisos',
                                'icon' => 'fa fa-university',
                                'url' => ['aviso/meus-avisos'],
                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                    Usuario::PERFIL_SECRETARIO,
                                    Usuario::PERFIL_DIRETOR,
                                    Usuario::PERFIL_DRE,
                                ])

                            ],
                            [
                                'label' => 'TESC',
                                'icon' => 'fa fa-university',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Escolas',
                                        'icon' => 'fa fa-university',
                                        'url' => ['escola/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_SECRETARIO,
                                            Usuario::PERFIL_DIRETOR,
                                            Usuario::PERFIL_DRE,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA
                                        ])

                                    ],
                                    [
                                        'label' => 'Alunos',
                                        'icon' => 'fa fa-graduation-cap',
                                        'url' => ['aluno/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_SECRETARIO,
                                            Usuario::PERFIL_DIRETOR,
                                            Usuario::PERFIL_DRE,

                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA
                                        ])
                                    ],
									  [
                                        'label' => 'Renova????o',
                                        'icon' => 'fa-handshake-o',
                                        'url' => ['renovacao/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_DIRETOR,
                                            Usuario::PERFIL_SECRETARIO,
                                        ])
                                    ],
                                    [
                                        'label' => 'Solicita????o de Transporte',
                                        'icon' => 'fa fa fa-bus',
                                        'url' => ['solicitacao-transporte/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_DIRETOR,
                                            Usuario::PERFIL_SECRETARIO,
                                            Usuario::PERFIL_DRE,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA
                                        ])
                                    ],
                                    [
                                        'label' => 'Solicita????es Pendentes',
                                        'icon' => 'fa fa fa-bus',
                                        'url' => ['solicitacao-transporte/solicitacoes-pendentes'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        ])
                                    ],
                                    [
                                        'label' => 'Solicita????es Aguardando Atendimento',
                                        'icon' => 'fa fa fa-bus',
                                        'url' => ['solicitacao-transporte/solicitacoes-aguardando-atendimento'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::TESC_CONSULTA
                                        ])
                                    ],
                                    // [
                                    //     'label' => 'Renova????es',
                                    //     'icon' => 'fa fa fa-renew',
                                    //     'url' => ['solicitacao-transporte/renovacoes'],
                                    //     'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                    //         Usuario::PERFIL_SUPER_ADMIN,
                                    //         Usuario::PERFIL_TESC_DISTRIBUICAO,
                                    //         Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                    //         // Usuario::TESC_CONSULTA
                                    //     ])
                                    // ],
                                    [
                                        'label' => 'Solicita????o de Cr??dito', 'icon' => 'fa fa-circle',
                                        'url' => ['solicitacao-credito/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_DIRETOR,
                                            Usuario::PERFIL_SECRETARIO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::TESC_CONSULTA,
                                            Usuario::PERFIL_DRE
                                        ])
                                    ],
                                    [
                                        'label' => 'Nova Solicita????o de Cr??dito', 'icon' => 'fa fa-circle',
                                        'url' => '#',
                                        'items' => [
                                            [
                                                'label' => 'Vale Transporte',
                                                'icon' => 'fa fa-university',
                                                //CriarValeTransporte actionCriarValeTransporte
                                                'url' => ['solicitacao-credito/criar-credito', 'tipo' => SolicitacaoCredito::TIPO_VALE_TRANSPORTE],
                                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                                    Usuario::PERFIL_SUPER_ADMIN,
                                                    Usuario::PERFIL_DIRETOR,
                                                    Usuario::PERFIL_SECRETARIO,
                                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                                    // Usuario::TESC_CONSULTA,
                                                    Usuario::PERFIL_DRE
                                                ])
                                            ],
                                            [
                                                'label' => 'Passe Escolar',
                                                'icon' => 'fa fa-university',
                                                //CriarValeTransporte actionCriarValeTransporte
                                                'url' => ['solicitacao-credito/criar-credito', 'tipo' => SolicitacaoCredito::TIPO_PASSE_ESCOLAR],
                                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                                    Usuario::PERFIL_SUPER_ADMIN,
                                                    Usuario::PERFIL_DIRETOR,
                                                    Usuario::PERFIL_SECRETARIO,
                                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                                    // Usuario::TESC_CONSULTA,
                                                    Usuario::PERFIL_DRE
                                                ])
                                            ],
                                            [
                                                'label' => 'Cr??dito Administrativo',
                                                'icon' => 'fa fa-university',
                                                //CriarValeTransporte actionCriarValeTransporte
                                                'url' => ['solicitacao-credito/criar-credito', 'tipo' => SolicitacaoCredito::TIPO_CREDITO_ADMINISTRATIVO],
                                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                                    Usuario::PERFIL_SUPER_ADMIN,
                                                    Usuario::PERFIL_DIRETOR,
                                                    Usuario::PERFIL_SECRETARIO,
                                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                                    // Usuario::TESC_CONSULTA,
                                                    Usuario::PERFIL_DRE
                                                ])
                                            ],
                                        ],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_DIRETOR,
                                            Usuario::PERFIL_SECRETARIO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            // Usuario::TESC_CONSULTA,
                                            Usuario::PERFIL_DRE
                                        ])
                                    ],

                            
                                ],
                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                    Usuario::PERFIL_SUPER_ADMIN,
                                    Usuario::PERFIL_SECRETARIO,
                                    Usuario::PERFIL_DIRETOR,
                                    Usuario::PERFIL_DRE,
                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                    Usuario::TESC_CONSULTA
                                ])

                            ],
                            [
                                'label' => 'Roteiriza????o',
                                'icon' => 'fa fa-location-arrow',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Ao vivo',
                                        'icon' => 'fa fa-child',
                                        'url' => ['condutor/ao-vivo']
                                    ],
                                    [
                                        'label' => 'Rotas',
                                        'icon' => 'fa fa-street-view',
                                        'url' => ['condutor-rota/index']
                                    ],
                                    [
                                        'label' => 'Hist??rico de viagem',
                                        'icon' => 'fa fa-history',
                                        'url' => ['historico/index']
                                    ],
                                    [
                                        'label' => 'Ocorr??ncias',
                                        'icon' => 'fa fa-exclamation-circle',
                                        'url' => ['ocorrencia/index']
                                    ],
                                    [
                                        'label' => 'Comunicados de aus??ncia',
                                        'icon' => 'fa fa-comment',
                                        'url' => ['comunicado/index']
                                    ],
                                    [
                                        'label' => 'Notifica????es enviadas',
                                        'icon' => 'fa fa-bell',
                                        'url' => ['notificacao-enviada/index']
                                    ],

                                ],
                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                    Usuario::PERFIL_SUPER_ADMIN,
                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                    // Usuario::TESC_CONSULTA
                                ])
                            ],
                            [
                                'label' => Usuario::permissao(Usuario::PERFIL_CONDUTOR) ? 'In??cio' : 'Condutores',
                                'icon' => 'fa fa-users',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Empresas',
                                        'icon' => 'fa fa-building',
                                        'url' => ['empresa/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA,
                                        ])
                                    ],
                                    [
                                        'label' => 'Ve??culos',
                                        'icon' => 'fa fa-car',
                                        //'url' => ['#'],
                                        'items' => [
                                            [
                                                'label' => 'Ve??culos',
                                                'icon' => 'fa fa-car',
                                                'url' => ['veiculo/index']
                                            ],
                                            [
                                                'label' => 'Marcas',
                                                'icon' => 'fa fa-building',
                                                'url' => ['marca/index']
                                            ],
                                            [
                                                'label' => 'Modelos',
                                                'icon' => 'fa fa-building',
                                                'url' => ['modelo/index']
                                            ],
                                        ],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA,
                                            // Usuario::PERFIL_CONDUTOR,
                                        ])
                                    ],
                                    [
                                        'label' => 'Condutores',
                                        'icon' => 'fa fa-users',
                                        'url' => ['condutor/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA,
                                        ])
                                    ],
                                    [
                                        'label' => 'Gest??o de Documentos',
                                        'icon' => 'fa fa-building',
                                        'url' => ['gestao-documentos/index'],
                                        'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::TESC_CONSULTA
                                        ])
        
                                    ],
                                    [
                                        'label' => 'Gest??o de Documentos Pendentes',
                                        'icon' => 'fa fa-building',
                                        'url' => ['gestao-documentos-pendentes/index'],
                                        'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::TESC_CONSULTA
                                        ])
        
                                    ],
                                    [
                                        'label' => 'Controle Financeiro',
                                        'icon' => 'fa fa-building',
                                        'url' => ['condutor/controle-financeiro'],
                                        'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA,
                                        ])
        
                                    ],
                                    [
                                        'label' => 'Meu perfil',
                                        'icon' => 'fa fa-users',
                                        'url' => ['condutor/view', 'id' => Yii::$app->user->identity->condutor->id],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_CONDUTOR,
                                        ])
                                    ],
                                    [
                                        'label' => 'Folha de ponto',
                                        'icon' => 'fa fa-users',
                                        'url' => ['pdf/folha-ponto', 'pdf' => 1, 'id' => Yii::$app->user->identity->condutor->id],
                                        'linkOptions' => ['target' => '_blank'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_CONDUTOR,
                                        ])
                                    ],
									 [
                                        'label' => 'Folha de ponto - Online',
                                        'icon' => 'fa fa-calendar',
                                        'url' => ['condutor/folha-ponto', 'id' => Yii::$app->user->identity->condutor->id],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_CONDUTOR,
                                        ])
                                    ],
                                    // ['label' => 'RPA', 'icon' => 'fa fa-file', 'url' => ['recibo-pagamento-autonomo/index'],],

                                ],
                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                    Usuario::PERFIL_SUPER_ADMIN,
                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                    Usuario::TESC_CONSULTA,
                                    Usuario::PERFIL_CONDUTOR,
                                ])
                            ],
                            [
                                'label' => 'Cadastros',
                                'icon' => 'far fa-address-card',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Usu??rios',
                                        'icon' => 'fa fa-user',
                                        'url' => ['usuario/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN
                                        ])
                                    ],
                                    [
                                        'label' => 'Renova????es',
                                        'icon' => 'fa fa fa-renew',
                                        'url' => ['solicitacao-transporte/renovacoes'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            // Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            // Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            // Usuario::TESC_CONSULTA
                                        ])
                                    ],
                                    [
                                        'label' => 'Justificativas',
                                        'icon' => 'fa fa-align-justify',
                                        'url' => ['justificativa/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN
                                        ])
                                    ],
                                    [   'label' => 'Necessidades especiais', 
                                        'icon' => 'fa fa-wheelchair', 
                                        'url' => ['necessidades-especiais/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN
                                        ])
                                    ],
                                    [   'label' => 'Configura????es', 
                                        'icon' => 'fa fa-cogs', 
                                        'url' => ['configuracao/update'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN
                                        ])
                                    ],
                                    [   'label' => 'Avisos', 
                                        'icon' => 'fa fa-cogs', 
                                        'url' => ['aviso/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::TESC_CONSULTA,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        ])
                                    ],

                                    // [
                                    //     'label' => 'Calend??rio',
                                    //     'icon' => '',
                                    //     'url' => ['calendario/index']
                                    // ],

                                    [
                                        'label' => 'Agrupamento de bairros',
                                        'icon' => '',
                                        'url' => ['agrupamento-bairro/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        ])
                                    ],
                                ],
                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                    Usuario::PERFIL_SUPER_ADMIN,
                                    Usuario::TESC_CONSULTA,
                                    Usuario::PERFIL_TESC_DISTRIBUICAO
                                ])
                            ],
                            [
                                'label' => 'Dashboard',
                                'icon' => 'fa fa-chart-bar',
                                // 'url' => ['relatorio/dashboard'],
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => 'Hist??rico de altera????es',
                                        'icon' => 'fa fa-columns',
                                        'url' => ['log/index'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA
                                        ])
                                    ],
                                    [
                                        'label' => 'Gr??ficos',
                                        'icon' => 'fa fa-columns',
                                        'url' => ['relatorio/dashboard'],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA
                                        ])
                                    ],
                                    [
                                        'label' => 'Painel de indicadores',
                                        'icon' => 'fa fa-car',
                                        //'url' => ['#'],
                                        'items' => [
                                            [
                                                'label' => 'Passe Escolar',
                                                'icon' => 'fa fa-car',
                                                'url' => ['painel-indicadores/index']
                                            ],
                                            [
                                                'label' => 'Valor pago aos Condutores',
                                                'icon' => 'fa fa-building',
                                                'url' => ['painel-indicadores/valor-condutores']
                                            ],
                                        ],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA
                                        ])
                                    ],
                                    [
                                        'label' => 'Painel de atendimento',
                                        'icon' => 'fa fa-car',
                                        //'url' => ['#'],
                                        'items' => [
                                            [
                                                'label' => 'Hist??rico de Atendimento',
                                                'icon' => 'fa fa-car',
                                                'url' => ['painel-atendimento/historico-atendimento'],
                                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                                    Usuario::PERFIL_SUPER_ADMIN,
                                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                                    Usuario::TESC_CONSULTA
                                                ])
                                            ],
                                            [
                                                'label' => 'Pesquisa de Atendimento',
                                                'icon' => 'fa fa-car',
                                                'url' => ['painel-atendimento/pesquisa-atendimento'],
                                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                                    Usuario::PERFIL_SUPER_ADMIN,
                                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                                    Usuario::TESC_CONSULTA
                                                ])
                                            ],
                                            [
                                                'label' => 'Alunos Atendidos - Frete',
                                                'icon' => 'fa fa-car',
                                                'url' => ['painel-atendimento/index'],
                                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                                    Usuario::PERFIL_SUPER_ADMIN,
                                                    Usuario::PERFIL_SECRETARIO,
                                                    Usuario::PERFIL_DIRETOR,
                                                    Usuario::PERFIL_DRE,
                                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                                    Usuario::TESC_CONSULTA
                                                ])
                                            ],
                                            [
                                                'label' => 'Alunos Atendidos - Passe Escolar',
                                                'icon' => 'fa fa-car',
                                                'url' => ['painel-atendimento/alunos-atendidos-passe-escolar'],
                                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                                    Usuario::PERFIL_SUPER_ADMIN,
                                                    Usuario::PERFIL_SECRETARIO,
                                                    Usuario::PERFIL_DIRETOR,
                                                    Usuario::PERFIL_DRE,
                                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                                    Usuario::TESC_CONSULTA
                                                ])
                                            ],
                                            [
                                                'label' => 'Alunos por condutor',
                                                'icon' => 'fa fa-building',
                                                'url' => ['painel-atendimento/alunos-condutor'],
                                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                                    Usuario::PERFIL_SUPER_ADMIN,
                                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                                    Usuario::TESC_CONSULTA
                                                ])
                                            ],
                                        ],
                                        'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                            Usuario::PERFIL_SUPER_ADMIN,
                                            Usuario::PERFIL_SECRETARIO,
                                            Usuario::PERFIL_DIRETOR,
                                            Usuario::PERFIL_DRE,
                                            Usuario::PERFIL_TESC_DISTRIBUICAO,
                                            Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                            Usuario::TESC_CONSULTA
                                        ])
                                    ],
                                ],

                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                        Usuario::PERFIL_SUPER_ADMIN,
                                        Usuario::PERFIL_SECRETARIO,
                                        Usuario::PERFIL_DIRETOR,
                                        Usuario::PERFIL_DRE,
                                        Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                        Usuario::TESC_CONSULTA
                                ])
                            ],

                            [
                                'label' => 'Alunos',
                                'icon' => 'fa fa-list-alt',
                                'url' => ['condutor/alunos'],

                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([

                                    Usuario::PERFIL_CONDUTOR
                                ])
                            ],
                            // [
                            //          'label' => 'Rel. Alunos ativos',
                            //          'icon' => 'fa fa-chart-pie',
                            //          'url' => '#',
                            //          'items' => [

                            //           ['label' => 'Alunos por modalidade', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/alunos-modalidade'],],
                            //           ['label' => 'Alunos por tipo de transporte', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/alunos-tipo-transporte'],],
                            //           ['label' => 'Alunos por tipo de rede', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/alunos-tipo-rede'],],
                            //           ['label' => 'Alunos PNE', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/alunos-pne'],],
                            //          ],
                            //          'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                            //                          Usuario::PERFIL_SUPER_ADMIN,
                            //                          Usuario::PERFIL_TESC_DISTRIBUICAO,
                            //                          Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                            //                          Usuario::TESC_CONSULTA
                            //                      ])
                            //      ],

                            // [
                            //     'label' => 'Rel. Alunos espera',
                            //     'icon' => 'fa fa-chart-pie',
                            //     'url' => '#',
                            //     'items' => [

                            //      ['label' => 'Alunos por modalidade', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/espera-modalidade'],],
                            //      ['label' => 'Alunos por tipo de transporte', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/espera-tipo-transporte'],],
                            //      ['label' => 'Alunos por tipo de rede', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/espera-tipo-rede'],],
                            //      ['label' => 'Alunos PNE', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/espera-pne'],],
                            //     ],
                            //    'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                            //                     Usuario::PERFIL_SUPER_ADMIN,
                            //                     Usuario::PERFIL_TESC_DISTRIBUICAO,
                            //                     Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                            //                     Usuario::TESC_CONSULTA
                            //                 ])
                            // ],
                            /*
                            [
                                'label' => 'Rel. de servi??o',
                                'icon' => 'fa fa-list-alt',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Qtd. Passes por escola', 'icon' => 'fas fa-th', 'url' => ['relatorio/passe-escola'],],
                                    ['label' => 'Alunos transportados', 'icon' => 'fas fa-th', 'url' => ['relatorio/alunos-transportados'],],

                                ],
                                'visible' => !Yii::$app->user->isGuest && Usuario::permissoes([
                                    Usuario::PERFIL_SUPER_ADMIN,
                                    Usuario::PERFIL_TESC_DISTRIBUICAO,
                                    Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                    Usuario::TESC_CONSULTA
                                ])
                            ],
                            */

                            // ['label' => 'Atendimento escolar', 'icon' => 'fa fa-users', 'url' => ['atendimento/index'],],

                            // ['label' => 'Contratos', 'icon' => 'fa fa-file-text-o', 'url' => ['contrato/inxes'],],
                        ],
                    ]
                ) ?>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"><img src="img/Prefeitura-de-SJC.png" style="height:25px;" alt=""></a></li>
                    <li class="divider"></li>
                    <li>
                        <?php if (Yii::$app->user->identity->idPerfil == Usuario::PERFIL_CONDUTOR) { ?>
                            <a href="<?= Url::toRoute(['condutor/view', 'id' => Yii::$app->user->identity->condutor->id]) ?>" style="padding: 5px 15px;"><?= Yii::$app->user->identity->nome; ?><br><span style="font-weight: normal!important;font-size: 11px;"><?= lastLogin();  ?></span></a>
                        <?php } else { ?>
                            <a href="<?= Url::toRoute(['usuario/view', 'id' => Yii::$app->user->identity->id]) ?>" style="padding: 5px 15px;"><?= Yii::$app->user->identity->nome; ?><br><span style="font-weight: normal!important;font-size: 11px;"><?= lastLogin();  ?></span></a>
                        <?php } ?>
                        <button value="<?= Url::toRoute(['usuario/nova-senha', 'id' => Yii::$app->user->identity->id]) ?>" class="btn btn-clear btn-sm showModalButton" title="Trocar senha">Trocar senha</button>
                    </li>
                    <li class="divider"></li>
                    <li class=""><a href="<?= Url::toRoute(['site/logout']); ?>"><i class="fas fa-sign-out-alt"></i></a></li>
                    <br>
                </ul>
            </div>

<!--             
            <div class="alert alert-warning" role="alert">
            <?= \Yii::$app->User->identity->editarDadosProtegidos ?>
            </div> -->
            <!--/.nav-collapse -->
        </nav>

        <div class="container-header d-flex">
            <!-- <section class="content-header pb-3" style="margin-top: 50px;"> -->
      
            <section class="content-header pb-3">
      
            <h1>
                    <?= isset($this->title) ? $this->title : '' ?>
                </h1>
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'homeLink' => ['label' => 'P??gina inicial', 'url' => ['site/index']]
                ]) ?>
            </section>
        </div>
        <section class="content p-0">
            <?= Alert::widget() ?>
            <?= $content ?>
        </section>
    </div>
    <link rel="stylesheet" type="text/css" href="css/bootstrap4-bootstrap-utilities.css">

    <!-- <footer class="footer navbar-fixed-bottom d-flex"> -->
    <footer class="footer d-flex">
        <div class="text-left">
            <img src="img/Prefeitura-de-SJC.png" class="h-100 p-2" alt="">
        </div>
        <div class="ml-auto text-right">
            Desenvolvido por
            <img src="img/IPPLAN_SECUND??RIO.png" class="h-100 p-2" alt="">
        </div>
    </footer>


    <div class="modal overflow-y-scroll " id="modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="modalHeader">
                </div>
                <div class="modal-body p-2">
                    <div class="box-body" id="modalContent">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>

<script>
        var pageSize = '<?= isset($get['pageSize']) ?  $get['pageSize'] : '' ?>';
        $("#paginacao").change(() => {
            pageSize = $("#paginacao").val();
            window.location.href += '&pageSize='+pageSize
        });
</script>
<script>
    //Intecepta todas as requests para desabilitar o bot??o de submit
    // $('form').on('beforeSubmit', function()
    // {
    //     var $form = $(this);
    //     var $submit = $form.find(':submit');
    //     $submit.html('<span class="fa fa-spin fa-spinner"></span> Processando...');
    //     $submit.prop('disabled', true);
    // });
        window.request = false;
        $(document).ready(function() {
            $('form').on('beforeSubmit', function() {
                if (window.request == true) return false;
                window.request = true;
                var $form = $(this);
                var $submit = $form.find(':submit');
                $submit.html('<span class="fa fa-spin fa-spinner"></span> Processando...');
                $submit.prop('disabled', true);
            });
        })
    //habilita tooltip utilizado em condutor-rota
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

// window.onload = function() {
//     console.warn('Carregado');
//         document.getElementById('w2').innerHTML= document.getElementById('w66').innerHTML
// }
</script>