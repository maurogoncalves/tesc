<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use common\models\Escola;
use kartik\date\DatePicker;
use common\models\Veiculo;
use common\models\Condutor;

use kartik\widgets\FileInput;
use common\models\NecessidadesEspeciais;
/* @var $this yii\web\View */
/* @var $model common\models\Veiculo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="box-body">

    <?php $form = ActiveForm::begin([
        'id' => 'formVeiculo',
        'options' => ['enctype'=>'multipart/form-data'],
        'encodeErrorSummary' => false,
        'errorSummaryCssClass' => 'help-block',
    ]); ?>

      <?php echo Yii::$app->controller->renderPartial('_inputs', ['form' => $form, 'model' => $model ]);  ?>
      
    <div class="form-group" style="margin-top: 15px;">
        <?= Html::submitButton($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
