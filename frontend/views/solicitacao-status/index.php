<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\SolicitacaoStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Solicitacao Statuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">

            <h3><?= Html::encode($this->title) ?></h3>
                            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                        </div>
    <p>
        <?= Html::a('Create Solicitacao Status', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'idSolicitacaoTransporte',
            'idUsuario',
            'justificativa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?>        </div>
    </div>
</div>