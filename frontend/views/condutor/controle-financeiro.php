<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use common\models\Condutor;
use common\models\Veiculo;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $searchModel common\models\CondutorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Controle financeiro';
$this->params['breadcrumbs'][] = $this->title;
// print_r($historico[350]['diasTrabalhados']);
// print_r(Yii::$app->request->get("ControleFinanceiroSearch")['diasTrabalhados']);
?>
<style>

</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-body">
                <?= Html::beginForm(['condutor/controle-financeiro'], 'GET', ['id' => 'formFilter']); ?>
                <div class="row form-group">
                    <div class="col-md-3">
                    <?php
                        echo Html::label('Ano', 'ano');
                        echo Select2::widget([
                            'name' => 'ano',
                            'attribute' => 'ano',
                            'data' => Yii::$app->params['arrayAnos'],
                            'value' => $_GET['ano'] ? $_GET['ano'] : '',
                            'options' => ['placeholder' => 'Selecione o ano'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                    ?>
                </div>
                <div class="col-md-3">
                    <?php
                        echo Html::label('Mês', 'mes');
                        echo Select2::widget([
                            'name' => 'mes',
                            'attribute' => 'mes',
                            'data' => Yii::$app->params['arrayMeses'],
                            'value' => $_GET['mes'] ? $_GET['mes'] : '',
                            'options' => ['placeholder' => 'Selecione o mês'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                    ?>
                </div>
                <div class="col-md-12">
                    <?= Html::submitButton('Confirmar', ['class' => 'btn btn-primary pull-right']) ?>
                </div>
            </div>

            <?php echo Html::endForm(); ?>
        </div>

        
        <?php

        if (Yii::$app->request->get('ano') && Yii::$app->request->get('mes'))
        {
            echo  Html::beginForm(['condutor/save-historico-financeiro'], 'POST', ['id' => 'formData']);
        ?>
            <input type="hidden" name="ano" value="<?= Yii::$app->request->get('ano') ?>" />
            <input type="hidden" name="mes" value="<?= Yii::$app->request->get('mes') ?>" />
            <div class="box-header with-border">
                <h4><?= '<span class="label label-primary">Total: '.$dataProvider->getTotalCount().'</span>'; ?></h4>
            </div>

            <div class="box-body"> 
                <?php Pjax::begin(); ?>  
                <?= GridView::widget([
                    // 'grid-historico' => 'grid-historico',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'containerOptions' => ['class' => 'table-responsive'],
                    'tableOptions' => ['class' => 'table table-bordered'],
                    'pjax' => false,
                    'striped' => false,
                    'emptyText' => 'Nenhum registro encontrado',
                    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
                    'filterRowOptions' => ['class' => 'fixed-first'],
                    'panel' => [
                        'heading'=>false,
                        'type'=>false,
                        'showFooter'=>false
                    ],
                    'summary' => "Exibindo <b>{begin}</b>-<b>{end}</b> de <b>{totalCount}</b> itens.",
                    'toolbar' => \Yii::$app->showEntriesToolbar->create(),
                    'exportConfig' => [
                        GridView::EXCEL => true
                    ],
                    'columns' => [
                        [
                            'class' => 'kartik\grid\CheckboxColumn',
                            'headerOptions' => ['class' => 'kartik-sheet-style'],
                            'checkboxOptions' =>
                                function($model) {
                                    return ['value' => $model->id, 'class' => 'checkbox-row', 'id' => 'checkbox'];
                                },
                            'hAlign'=>'center',
                            'vAlign'=>'middle',
                            'hiddenFromExport'=>true,
                            'mergeHeader'=>true,
                        ],
                        [
                            'attribute' => 'duplicar',
                            'label' => '',
                            'format' => 'raw',
                            'contentOptions' => ['style' => 'min-width:50px; text-align:center; white-space: normal; display:inline-block;'],
                            'value' => function ($model) {
                                return Html::button('<i class="glyphicon glyphicon-plus"></i>', ['value' => '', 'title' => 'Solicitação', 'class' => 'btn btn-clear bth-xs cloneBtn', 'id' => $model->id]);
                            }
                        ],
                        [
                            'attribute' => 'nome',
                            'contentOptions' => ['style' => 'min-width:350px;'],
                            'headerOptions' => ['style' => 'min-width:350px;'],
                            'value' => function ($model) {
                                return $model->condutor->nome;
                            }
                        ],
                        [ 
                            'label' => 'NIT',
                            'attribute' => 'nit',
                            'format' => ['Nit'],
                            'contentOptions' => array('style' => 'min-width:150px;'),
                            'headerOptions' => array('style' => 'min-width:150px;'),
                            'value' => function ($model) {
                                return $model->condutor->nit;
                            }
                        ],
                        [
                            'attribute' => 'diasTrabalhados',
                            'header' => 'Dias<br>Trabalhados',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[diasTrabalhados]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['diasTrabalhados'].'">',
                            'contentOptions' => ['style' => 'min-width:100px;'],
                            'headerOptions' => ['style' => 'min-width:100px;text-align:center;'],
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control diasTrabalhados" name="diasTrabalhados[]" id="diasTrabalhados_'.$model->id.'" value="'.$historico[$model->id]['diasTrabalhados'].'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'sabadoLetivo',
                            'header' => 'Sábado<br>Letivo',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[sabadoLetivo]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['sabadoLetivo'].'">',
                            'contentOptions' => ['style' => 'min-width:100px;'],
                            'headerOptions' => ['style' => 'min-width:100px;text-align:center;'],
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control sabadoLetivo" name="sabadoLetivo[]" id="sabadoLetivo_'.$model->id.'" value="'.$historico[$model->id]['sabadoLetivo'].'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'diasExcepcionais1',
                            'header' => 'Dia(s)<br>Excepcional(is) (1)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[diasExcepcionais1]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['diasExcepcionais1'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control diasExcepcionais1" name="diasExcepcionais1[]" id="diasExcepcionais1_'.$model->id.'" value="'.$historico[$model->id]['diasExcepcionais1'].'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'viagemKm1',
                            'header' => 'Viagem/Km<br>(1)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[viagemKm1]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['viagemKm1'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control viagemKm1" name="viagemKm1[]" id="viagemKm1_'.$model->id.'" value="'.$historico[$model->id]['viagemKm1'].'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'diasExcepcionais2',
                            'header' => 'Dia(s)<br>Excepcional(is) (2)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[diasExcepcionais2]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['diasExcepcionais2'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control diasExcepcionais2" name="diasExcepcionais2[]" id="diasExcepcionais2_'.$model->id.'" value="'.$historico[$model->id]['diasExcepcionais2'].'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'viagemKm2',
                            'header' => 'Viagem/Km<br>(2)',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[viagemKm2]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['viagemKm2'].'">',
                            'contentOptions' => array('style' => 'min-width:100px;'),
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control viagemKm2" name="viagemKm2[]" id="viagemKm2_'.$model->id.'" value="'.$historico[$model->id]['viagemKm2'].'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'valorNota',
                            'header' => 'Valor<br>Nota',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[valorNota]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['valorNota'].'">',
                            'contentOptions' => array('style' => 'min-width:200px;text-align:center;'),
                            'headerOptions' => array('style' => 'min-width:200px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input size="50" class="form-control valorNota" name="valorNota[]" id="valorNota_'.$model->id.'" readonly value="'.sprintf('%0.2f', $historico[$model->id]['valorNota']).'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'protocoloTESC',
                            'header' => 'Protocolo<br>TESC',
                            'format' => 'raw',
                            'filter' => '<input type="date" class="form-control" name="ControleFinanceiroSearch[protocoloTESC]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['protocoloTESC'].'">',
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="date" class="form-control protocoloTESC" name="protocoloTESC[]" id="protocoloTESC_'.$model->id.'" value="'.$historico[$model->id]['protocoloTESC'].'" max="1979-12-31" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'protocoloGC',
                            'header' => 'Protocolo<br>GC',
                            'format' => 'raw',
                            'filter' => '<input type="date" class="form-control" name="ControleFinanceiroSearch[protocoloGC]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['protocoloGC'].'">',
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="date" class="form-control protocoloGC" name="protocoloGC[]" id="protocoloGC_'.$model->id.'" value="'.$historico[$model->id]['protocoloGC'].'" max="1979-12-31" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'lote',
                            'header' => 'Lote',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[lote]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['lote'].'">',
                            'headerOptions' => array('style' => 'min-width:100px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campo = '<input type="number" size="50" class="form-control lote" name="lote[]" id="lote_'.$model->id.'" value="'.$historico[$model->id]['lote'].'" />';
                                return $campo;
                            }
                        ],
                        [
                            'attribute' => 'saldoAF',
                            'label' => 'Saldo AF',
                            'format' => 'raw',
                            'filter' => '<input type="number" class="form-control" name="ControleFinanceiroSearch[saldoAF]" value="'.Yii::$app->request->get('ControleFinanceiroSearch')['saldoAF'].'">',
                            'headerOptions' => array('style' => 'min-width:200px;text-align:center;'),
                            'value' => function($model) use ($historico) {
                                $campos = '<input size="50" class="form-control saldoAF" name="saldoAF[]" id="saldoAF_'.$model->id.'" readonly value="'.sprintf('%0.2f', $historico[$model->id]['saldoAF']).'" />';
                                $campos .= '<input type="hidden" class="form-control valorPago" name="valorPago[]" id="valorPago_'.$model->id.'" value="'.$model->condutor->valorPagoKmViagem.'" />';
                                $campos .= '<input type="hidden" class="form-control kmViagemAtual" name="kmViagemAtual[]" id="kmViagemAtual_'.$model->id.'" value="'.$model->condutor->kmViagemAtual.'" />';
                                $campos .= '<input type="hidden" class="form-control kmViagemSabadoLetivo" name="kmViagemSabadoLetivo[]" id="kmViagemSabadoLetivo_'.$model->id.'" value="'.$model->condutor->kmViagemSabadoLetivo.'" />';
                                $campos .= '<input type="hidden" class="form-control saldoAFAnterior" name="saldoAFAnterior[]" id="saldoAFAnterior_'.$model->id.'" value="'.$model->condutor->saldoAFAnterior.'" />';
                                $campos .= '<input type="hidden" class="form-control condutores" name="condutores[]" value="'.$model->idCondutor.'" value="'.$historico[$model->id]['diasTrabalhados'].'" />';
                                return $campos;
                            }
                        ],
                        [
                            'attribute' => 'acao',
                            'label' => '',
                            'format' => 'raw',
                            'contentOptions' => ['style' => 'min-width:100px; white-space: normal; display:inline-block;'],
                            'value' => function ($model) {
                                return  Html::button('<i class="glyphicon glyphicon-search"></i>', ['value' => Url::to(['condutor/view', 'id' => $model->id, 'ajax' => true]), 'title' => 'Detalhes do condutor', 'class' => 'showModalButton btn btn-primary', 'aria-label' => 'Detalhes']);
                            }
                        ],
                    ]
                ]); ?>
                <?php Pjax::end(); ?> 
            </div>

            <div class="box-footer">
                <?= Html::submitButton('Salvar Dados', ['class' => 'btn btn-success pull-right']) ?>
                <!-- <button class="btn btn-success pull-right" id="saveBtn">Salvar</button> -->
            </div>
        <?php 
            echo Html::endForm();
        }
        else { ?>
        <div class="box-body">
            <div class="callout callout-warning">
                <h4>Atenção!</h4>
                <p>Selecione um ano e mês para prosseguir.</p>
            </div>
        </div>
        <?php } ?>
    </div>
</div>


<script type="text/javascript">

    function gerenciadorPdf(){
        let get = window.location.search;

        get = get.replace('condutor%2Fcontrole-financeiro', 'condutor/report-financeiro-pdf');
        var keys = $('#w2').yiiGridView('getSelectedRows');
        console.log(keys)
        window.open(get + '&keys=' + JSON.stringify(keys))
    }

    function gerenciadorXls(){
        let get = window.location.search;

        get = get.replace('condutor%2Fcontrole-financeiro', 'condutor/report-financeiro-xls');
        var keys = $('#w2').yiiGridView('getSelectedRows');
        console.log(keys)
        window.open(get + '&keys=' + JSON.stringify(keys))
    }
    
    setInterval(() => {
        itens = $("#w4 li").remove();
        item = $("#w4 li")[itens.length-1];
        if($(item).prop('title') != 'Portable Document Format') {
            $("#w4").append('<li id="meuXls" title="Excel"><a onclick="gerenciadorXls()" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>')
            $("#w4").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')
        }
    }, 500);

    $('.diasTrabalhados').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.sabadoLetivo').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.diasExcepcionais1').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.viagemKm1').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.diasExcepcionais2').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.viagemKm2').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.protocoloTESC').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.protocoloGC').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    $('.lote').change(function(val) {
        var valores = $(this).attr('id').split('_');
        var id = valores[1];
        calculaValorNota(id);
        console.log(id);
    })

    // $('.saldoAF').change(function(val) {
    //     calculaValorNota();
    //     console.log($(this).attr('id'));
    // })

    function calculaValorNota(id)
    {
        var diasTrabalhados = $('#diasTrabalhados_' + id).val();
        var sabadoLetivo = $('#sabadoLetivo_' + id).val();
        var diasExcepcionais1 = $('#diasExcepcionais1_' + id).val();
        var viagemKm1 = $('#viagemKm1_' + id).val();
        var diasExcepcionais2 = $('#diasExcepcionais2_' + id).val();
        var viagemKm2 = $('#viagemKm2_' + id).val();
        var kmViagemAtual = $('#kmViagemAtual_' + id).val();
        var kmViagemSabadoLetivo = $('#kmViagemSabadoLetivo_' + id).val();
        var valorPago = $('#valorPago_' + id).val();
        
        var valorNota = (diasTrabalhados * valorPago * kmViagemAtual) + 
            (sabadoLetivo * valorPago * kmViagemSabadoLetivo) +
            (diasExcepcionais1 * viagemKm1 * valorPago) +
            (diasExcepcionais2 * viagemKm2 * valorPago);

        $('#valorNota_' + id).val(valorNota.toFixed(2));

        calculaSaldoAF(id);
    }

    function calculaSaldoAF(id)
    {
        var valorNota = $('#valorNota_' + id).val();
        
        var saldoAF = $('#saldoAFAnterior_' + id).val() - valorNota;

        $('#saldoAF_' + id).val(saldoAF.toFixed(2));
    }

    $('#saveBtn').click(function () {
        var data = [];

        $('.diasTrabalhados').each (function(index) {
            console.log($(this))
        })
        
        // $.post("index.php?r=condutor%2Fsave-historico-financeiro", data, function(result){
        //         console.log(result)
        //         console.log($(this).parent())
        //     });
        // }
    })

    $('.cloneBtn').click(function () {
        var tr = $(this).parent().parent();
        tr.after(tr.clone());
        console.log($(this).parent().parent());
    })

    $( "input[name='ControleFinanceiroSearch[diasTrabalhados]']" ).attr('type', 'number')
    function forceNumeric(){
        var $input = $(this);
        $input.val($input.val().replace(/[^\d]+/g,''));
    }
    $('body').on('input', 'input[type="number"]', forceNumeric);


</script>