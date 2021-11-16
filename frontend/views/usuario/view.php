<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Usuario;
use kartik\grid\GridView;
use kartik\dialog\Dialog;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Usuario */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Usuários', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-solid">
            <div class="box-header with-border">

                <h3><?= Html::encode($this->title) ?></h3>
            </div>
            <!-- <div class="box-header with-border">
                    <?php
                    echo Html::a('Apagar', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger pull-right align-button',
                        'id' => 'btn-dialog',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]);

                    echo Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right ']);
                ?>
            </div> -->
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                         [
                            'label' => 'Diretor(a) nas escolas',
                            'value' => function($model) {
                               if($model->diretores){
                                    foreach ($model->diretores as $items)
                                        $meus[] = $items->escola->nome;
                                    return implode (', ', $meus);
                                } else {
                                    return '-';
                                }
                            }
                        ],
                         [
                            'label' => 'Secretário(a) nas escolas',
                            'value' => function($model) {
                               if($model->secretarios){
                                    foreach ($model->secretarios as $items)
                                        $meus[] = $items->escola->nome;
                                    return implode (', ', $meus);
                                } else {
                                    return '-';
                                }
                            }
                        ],
                        'id',
                        [
                             'label' => 'Perfil',
                             'attribute'=>  function($model){
                                    return $model->idPerfil ? Usuario::ARRAY_PERFIS[$model->idPerfil] : '-';
                            },
                        ],
                        'nome',
                        'username',
                        'email:email',

                        // 'authKey',
                        // 'passwordHash',
                        // 'passwordResetToken',
                        // 'idFirebase',
                        // 'status',
                        // 'imagem',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php 
    echo Dialog::widget([
    'libName' => 'krajeeDialogCust', // optional if not set will default to `krajeeDialog`
    'options' => ['draggable' => true, 'closable' => true], // custom options
    ]);
?>