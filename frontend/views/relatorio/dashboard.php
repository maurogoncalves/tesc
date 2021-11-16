<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use miloschuman\highcharts\Highcharts;

$this->title = 'Dashboard'; 
?>
<div class="row">
    <div class="col-md-4">
        <div class="box box-solid">
            <div class="box-header">
                <h4>Alunos sem transporte (Frete)</h4>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
            <h4 style="text-align: center;"><?=  $alunosSemTransporte; ?></h4>
            </div>
        </div>
    </div>
        <div class="col-md-4">
        <div class="box box-solid">
            <div class="box-header">
                <h4>Alunos transportados hoje </h4>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
            <h4 style="text-align: center;"><?=  $alunosTransportadosHoje; ?></h4>
                
            </div>
        </div>
    </div>
        <div class="col-md-4">
        <div class="box box-solid">
            <div class="box-header">
                <h4>Km Rodado hoje</h4>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
            <h4 style="text-align: center;"><?= Yii::$app->formatter->asDecimal($alunosKmRodado,2); ?></h4>

            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-body">  
                    <div class="col-md-6">

                        <h3>Alunos Ativos</h3>
                        <div class="col-md-12">
                            <h4>Alunos ativos por Modalidade</h4>
                            <?= Highcharts::widget([
                                'scripts' => ['modules/drilldown'],
                               'options' => $alunosModalidade
                            ]);
                            ?> 
                        </div>
                        <div class="col-md-12">
                            <h4>Alunos ativos por tipo de Transporte</h4>
                            <?= Highcharts::widget([
                                'scripts' => ['modules/drilldown'],
                               'options' => $alunoTipoTransporte
                            ]);
                            ?> 
                        </div>
                        <div class="col-md-12">
                            <h4>Alunos ativos por Tipo de Rede</h4>
                            <?= Highcharts::widget([
                                'scripts' => ['modules/drilldown'],
                               'options' => $alunosTipoRede
                            ]);
                            ?> 
                        </div>
                        <div class="col-md-12">
                            <h4>Alunos PNE Ativos</h4>
                            <?= Highcharts::widget([
                                'scripts' => ['modules/drilldown'],
                               'options' => $alunosPne
                            ]);
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3>Alunos em espera</h3>
                       <div class="col-md-12">
                        <h4>Alunos em espera por Modalidade</h4>
                        <?= Highcharts::widget([
                            'scripts' => ['modules/drilldown'],
                           'options' => $alunosModalidadeEspera
                        ]);
                        ?> 
                    </div>
                    <div class="col-md-12">
                        <h4>Alunos em espera por tipo de Transporte</h4>
                        <?= Highcharts::widget([
                            'scripts' => ['modules/drilldown'],
                           'options' => $esperaTipoTransporte
                        ]);
                        ?> 
                    </div>
                    <div class="col-md-12">
                        <h4>Alunos em espera por Tipo de Rede</h4>
                        <?= Highcharts::widget([
                            'scripts' => ['modules/drilldown'],
                           'options' => $esperaTipoRede
                        ]);
                        ?> 
                    </div>
                    <div class="col-md-12">
                        <h4>Alunos PNE em Espera</h4>
                        <?= Highcharts::widget([
                            'scripts' => ['modules/drilldown'],
                           'options' => $esperaPne
                        ]);
                        ?> 
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 

