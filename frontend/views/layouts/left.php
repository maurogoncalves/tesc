<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PacienteSearch;
use common\models\Usuario;

/* @var $this yii\web\View */
/* @var $model frontend\models\PacienteSearch */
/* @var $form yii\widgets\ActiveForm */
 
function firstName(){
    $nomes = explode(' ',Yii::$app->user->identity->nome);

    if(isset($nomes[0]))
        return $nomes[0];
    return Yii::$app->user->identity->nome;
}  
?>

<aside class="main-sidebar">

    <section class="sidebar">

        <?php
        if (!Yii::$app->user->isGuest)
        {
            ?>
            <!-- Sidebar user panel -->
            <div class="user-panel">
               <div class="pull-left image">
                <img src="img/anonimo.png" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                Olá <?= firstName(); ?>
            </div>
        </div>
        <?php
    }
    ?>
    
    <?= dmstr\widgets\Menu::widget(
        [
            'options' => ['class' => 'sidebar-menu'],
            'items' =>[
              [
                        'label' => 'TESC',
                        'icon' => 'fa fa-university',
                        'url' => '#',
                        'items' => [
                            [
                            'label' => 'Escolas',
                            'icon' => 'fa fa-university',
                                'url' => ['escola/index'],
                                'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
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
                                'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
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
                                'label' => 'Solicitação de transporte',
                                'icon' => 'fa fa fa-bus',
                                'url' => ['solicitacao-transporte/index'],
                                'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
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
                                'label' => 'Solicitação de crédito', 'icon' => 'fa fa-circle', 
                                'url' => ['solicitacao-credito/index'],
                                 'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                         Usuario::PERFIL_SUPER_ADMIN,
                                         Usuario::PERFIL_DIRETOR,
                                         Usuario::PERFIL_SECRETARIO,
                                         Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                         Usuario::TESC_CONSULTA
                                     ])
 
                             ],
 

                             [
                                'label' => 'Gestão de Documentos',
                                'icon' => 'fa fa-building',
                                'url' => ['gestao-documentos/index'],
                                'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                    Usuario::PERFIL_SUPER_ADMIN,
                                    Usuario::TESC_CONSULTA
                                ])

                            ],
                            [
                                'label' => 'Gestão de Documentos Pendentes',
                                'icon' => 'fa fa-building',
                                'url' => ['gestao-documentos/index&pendentes=1'],
                                'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                    Usuario::PERFIL_SUPER_ADMIN,
                                    Usuario::TESC_CONSULTA
                                ])

                            ],
                        ],
                    ],
                    [
                        'label' => 'Roteirização',
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
                                    'label' => 'Histórico de viagem',
                                    'icon' => 'fa fa-history',
                                    'url' => ['historico/index']
                                ],
                                [
                                    'label' => 'Ocorrências',
                                    'icon' => 'fa fa-exclamation-circle',
                                    'url' => ['ocorrencia/index']
                                ],
                                [
                                    'label' => 'Comunicados do resp.',
                                    'icon' => 'fa fa-comment',
                                    'url' => ['comunicado/index']
                                ],
 
                        ],
                    'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                        Usuario::PERFIL_SUPER_ADMIN,
                                        Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                        Usuario::TESC_CONSULTA
                                    ])
                    ],
                    [
                        'label' => 'Condutores',
                        'icon' => 'fa fa-users',
                        'url' => '#',
                        'items' => [
                        
                                 [
                                    'label' => 'Empresas',
                                    'icon' => 'fa fa-building',
                                    'url' => ['empresa/index']
                                ],
                              
                                [
                                    'label' => 'Veículos',
                                    'icon' => 'fa fa-car',
                                    'url' => ['#'],
                                    'items' => [
                                        [
                                            'label' => 'Veículos',
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
                                ],
                                [
                                    'label' => 'Condutores',
                                    'icon' => 'fa fa-users',
                                    'url' => ['condutor/index']
                                ],
                                ['label' => 'RPA', 'icon' => 'fa fa-file', 'url' => ['recibo-pagamento-autonomo/index'],],

                        ],
         'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                        Usuario::PERFIL_SUPER_ADMIN,
                                        Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                        Usuario::TESC_CONSULTA
                                    ])
                    ],

                    [
                            'label' => 'Cadastros',
                            'icon' => 'far fa-address-card',
                            'url' => '#',
                            'items' => [
                                [
                                    'label' => 'Usuários',
                                    'icon' => 'fa fa-user',
                                    'url' => ['usuario/index']
                                ],
                                [
                                    'label' => 'Justificativas',
                                    'icon' => 'fa fa-align-justify',
                                    'url' => ['justificativa/index']
                                ],
                                ['label' => 'Necessidades especiais', 'icon' => 'fa fa-wheelchair', 'url' => ['necessidades-especiais/index'],],
                                ['label' => 'Configurações', 'icon' => 'fa fa-cogs', 'url' => ['configuracao/update'],],
                            ],
                    'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                        Usuario::PERFIL_SUPER_ADMIN,
                                        Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        
                                    ])
                    ],
                   
               [
                        'label' => 'Dashboard',
                        'icon' => 'fa fa-chart-bar',
                        'url' => ['relatorio/dashboard'],
                        
                        'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                        Usuario::PERFIL_SUPER_ADMIN,
                                        Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                        Usuario::TESC_CONSULTA
                                    ])
                    ],
                    
                   
               [
                        'label' => 'Rel. Alunos ativos',
                        'icon' => 'fa fa-chart-pie',
                        'url' => '#',
                        'items' => [
                         
                         ['label' => 'Alunos por modalidade', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/alunos-modalidade'],],
                         ['label' => 'Alunos por tipo de transporte', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/alunos-tipo-transporte'],],
                         ['label' => 'Alunos por tipo de rede', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/alunos-tipo-rede'],],
                         ['label' => 'Alunos PNE', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/alunos-pne'],],
                        ],
                        'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                        Usuario::PERFIL_SUPER_ADMIN,
                                        Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                        Usuario::TESC_CONSULTA
                                    ])
                    ],
                    
                    [
                        'label' => 'Rel. Alunos espera',
                        'icon' => 'fa fa-chart-pie',
                        'url' => '#',
                        'items' => [
                         
                         ['label' => 'Alunos por modalidade', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/espera-modalidade'],],
                         ['label' => 'Alunos por tipo de transporte', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/espera-tipo-transporte'],],
                         ['label' => 'Alunos por tipo de rede', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/espera-tipo-rede'],],
                         ['label' => 'Alunos PNE', 'icon' => 'fas fa-chart-pie', 'url' => ['relatorio/espera-pne'],],
                        ],
                       'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                        Usuario::PERFIL_SUPER_ADMIN,
                                        Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                        Usuario::TESC_CONSULTA
                                    ])
                    ],
 					[
                        'label' => 'Rel. de serviço',
                        'icon' => 'fa fa-list-alt',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Qtd. Passes por escola', 'icon' => 'fas fa-th', 'url' => ['relatorio/passe-escola'],],
                            ['label' => 'Alunos transportados', 'icon' => 'fas fa-th', 'url' => ['relatorio/alunos-transportados'],],

                        ],
                    'visible'=> !Yii::$app->user->isGuest && Usuario::permissoes([
                                        Usuario::PERFIL_SUPER_ADMIN,
                                        Usuario::PERFIL_TESC_DISTRIBUICAO,
                                        Usuario::PERFIL_TESC_PASSE_ESCOLAR,
                                        Usuario::TESC_CONSULTA
                                    ])
                    ],

                    [
                        'label' => 'Sair',
                        'icon' => 'fa fa-arrow-right',
                        'url' => ['site/logout'],
                        'visible' => !Yii::$app->user->isGuest,
                        'template' => '<a href="{url}" data-method="post">{icon} {label}</a>',
                    ],
                            // ['label' => 'Atendimento escolar', 'icon' => 'fa fa-users', 'url' => ['atendimento/index'],],
                    
                            // ['label' => 'Contratos', 'icon' => 'fa fa-file-text-o', 'url' => ['contrato/inxes'],],
                ],
    ]
) ?>

</section>

</aside>
