<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PacienteSearch;
use common\models\Usuario;

/* @var $this yii\web\View */
/* @var $model frontend\models\PacienteSearch */
/* @var $form yii\widgets\ActiveForm */
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
                Olá <?= Yii::$app->user->identity->nome ?>
            </div>
        </div>
        <?php
    }
    ?>
    
    <?= dmstr\widgets\Menu::widget(
        [
            'options' => ['class' => 'sidebar-menu'],
            'items' => [
             
                [
                    'label' => 'Cadastros',
                    'icon' => 'fa fa-edit',
                    'url' => '#',
                    'items' => [
                      [
                        'label' => 'Usuário',
                        'icon' => 'fa fa-user',
                        'url' => ['usuario/index']
                    ],
                    [
                        'label' => 'Escolas',
                        'icon' => 'fa fa-university',
                        'url' => ['escola/index']
                    ],
                    [
                        'label' => 'Alunos',
                        'icon' => 'fa fa-graduation-cap',
                        'url' => ['aluno/index']
                    ],
                    [
                        'label' => 'Empresas',
                        'icon' => 'fa fa-building',
                        'url' => ['empresa/index']
                    ],
                    [
                        'label' => 'Condutor',
                        'icon' => 'fa fa-car',
                        'url' => ['condutor/index']
                    ],
                    
                    ['label' => 'Solicitação de Crédito', 'icon' => 'fa fa-users', 'url' => ['solicitacao-credito/index'],],
                    ['label' => 'Necessidades especiais', 'icon' => 'fa fa-wheelchair', 'url' => ['necessidades-especiais/index'],],
                    ['label' => 'RPA', 'icon' => 'fa fa-file', 'url' => ['recibo-pagamento-autonomo/index'],],
                    ['label' => 'Configuração', 'icon' => 'fa fa-cogs', 'url' => ['configuracao/update'],],
                            // ['label' => 'Atendimento escolar', 'icon' => 'fa fa-users', 'url' => ['atendimento/index'],],
                    
                            // ['label' => 'Contratos', 'icon' => 'fa fa-file-text-o', 'url' => ['contrato/inxes'],],
                ],
                'visible'=>!Yii::$app->user->isGuest
            ],
                    // [
                    //     'label' => 'Aluno',
                    //     'icon' => 'fa fa-edit',
                    //     'url' => '#',
                    //     'items' => [
                    //         ['label' => 'Alugno', 'icon' => 'fa fa-users', 'url' => ['aluno/index'],],
                    //         // ['label' => 'Contratos', 'icon' => 'fa fa-file-text-o', 'url' => ['contrato/inxes'],],
                    //     ],
                    //     'visible'=>!Yii::$app->user->isGuest
                    // ],

                    // [
                    //     'label' => 'Meu perfil',
                    //     'icon' => 'fa fa-user',
                    //     'url' => ['usuario/update', 'id'=>Yii::$app->user->identity->id],
                    //     'visible'=> (!Yii::$app->user->isGuest)
                    // ],
        ],
    ]
) ?>

</section>

</aside>
