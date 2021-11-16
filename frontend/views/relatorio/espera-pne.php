<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use miloschuman\highcharts\Highcharts;

$this->title = 'Lista de espera por PNE';
?>

<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header">
                <h4><?= $this->title ?></h4>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <?= Highcharts::widget([
                   'options' => $arrayData
                ]);
                ?>
            </div>
        </div>
    </div>
</div>