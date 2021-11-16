<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Veiculo;
use common\models\Condutor;
use common\models\CondutorRota;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use yii\helpers\Url;

use kartik\widgets\FileInput;
use yii\data\ArrayDataProvider;
use common\models\Escola;
use common\models\Usuario;
use common\models\TipoDocumento;
// use limion\bootstraplightbox\BootstrapMediaLightboxAsset;
// BootstrapMediaLightboxAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Condutor */

$this->title = 'Condutor: '.$model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Condutores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12"> 
        <div class="box box-solid">            
            <div class="box-body">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [ 
                        [
                            'attribute' => 'tipoContrato',
                            'value' => function ($data) {
                                return $data->tipoContratoText;
                            }
                        ],
                        [
                            'attribute' => 'valorPagoKmViagem',
                            'value' => function ($data) {
                                return 'R$ '.Yii::$app->formatter->asDecimal($data->valorPagoKmViagem, 2);
                            }
                        ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>
</div>