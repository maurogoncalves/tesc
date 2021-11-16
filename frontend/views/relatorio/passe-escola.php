<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use common\models\Escola;
use common\models\Configuracao;
use kartik\daterange\DateRangePicker;
$this->title = 'Quantidade de passes por escola';

?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h4>Filtros</h4>
            </div>
            <div class="box-body">
                <?= Html::beginForm(['relatorio/passe-escola'], 'GET', ['id' => 'formFilter']); ?>
                <div class="row form-group">
                    <div class="col-md-3">
                        <?php
                            echo Html::label('Período', 'periodo');
                            echo DateRangePicker::widget([
                                'name' => 'periodo',
                                'value' => isset($_GET['periodo'])?$_GET['periodo']:'',
                                'attribute'=>'datetime_range',
                                'convertFormat'=>true,
                                'pluginOptions'=>[
                                    'timePicker'=>false,
                                    'timePickerIncrement'=>30,
                                    'locale'=>[
                                        'format'=>'d/m/Y'
                                    ]
                                ],
                                'options' => [
                                    'autocomplete' => 'off',
                                    'class' => 'form-control'
                                ]
                            ]);
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?= Html::submitButton('Filtrar', ['class' => 'btn btn-primary pull-right']) ?>
                    </div>
                </div>

                <?php echo Html::endForm(); ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
         
            <div class="box-body">
                <?= GridView::widget([
                    	'panel' => [
                            'heading'=>false,
                            'type'=>false,
                            'showFooter'=>false
                        ],
                        'toolbar' =>  [
                            '{export}{toggleData}',
                        ],
                                'dataProvider' => new ArrayDataProvider([
                                    'allModels' => $arrayData,
                                    'key' => 'id',
                                    'pagination' => [
                                        'pageSize' => 20,
                                    ],
                                    
                                ]),
                                
                                'id' => 'relatorio',
                                'pjax' => true,
                                'pjaxSettings' =>[
                                    'neverTimeout'=>true,
                                    'options'=>[
                                            'id'=>'grid',
                                        ]
                                    ],
                                'options' => [
                                    'class' => 'table-header-ajax',
                                 ],
                                'striped' => true,
                                'bootstrap' => true,
                                'emptyText' => '<h4 class="vazio">Nenhum registro encontrado</h4>',
                                'columns' => [
                                    [
                                        'attribute' => 'idEscola',
                                        'label' => 'Escola',
                                        'value' => 'nomeEscola',
                                        'filter'=>  function($model){
                                                return ArrayHelper::map(Escola::find()->all(), 'id', 'nome');
                                        },
                                    ],
                                    [
                                        'attribute' => 'valor',
                                        'label' => 'Total de crédito',
                                    ],
                                    [
                                        
                                        'label' => 'Total ',
                                        'value' => function($model){
                                            return 'R$ '.Yii::$app->formatter->asDecimal($model->valor, 2) ;
                                        }
                                    ],
                                ],
                                'exportConfig' => [
                                    GridView::HTML => true,
                                    GridView::CSV => true,
                                    GridView::TEXT => true,
                                    GridView::EXCEL => true,
                                    GridView::PDF=> [
                                        'config' => [
                                            'mode' => 'c',
                                            'format' => 'A4-L',
                                            'destination' => 'D',
                                            'marginTop' => 40,
                                            'marginBottom' => 20,
                                            'marginLeft' => 5,
                                            'marginRight' => 5,
                                            'cssInline' => 
                                                '.img {float:right !important;}'.
                                                '.table{font-size:10px}' .
                                                '.kv-wrap{padding:20px;}' .
                                                '.kv-align-center{text-align:center;}' .
                                                '.kv-align-left{text-align:left;}' .
                                                '.kv-align-right{text-align:right;}' .
                                                '.kv-align-top{vertical-align:top!important;}' .
                                                '.kv-align-bottom{vertical-align:bottom!important;}' .
                                                '.kv-align-middle{vertical-align:middle!important;}' .
                                                '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                                                '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                                                '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}'.
                                                '.texto{text-align:left!important;}',
                                            'methods' => [
                                                'SetHeader' => '
                                                <table width="100%">
                                                    <tr>
                                                
                                                        <Td align="center">
                                                        <table width="40%">
                                                        <tr>
                                                            <td align="right"><img width="100" src="img/brasaoCompleto.png"></td>
                                                            <td align="left">											<b>Secretaria de Educação e Cidadania</b><br>Setor de Transporte Escolar<br>E-mail: transporte.escolar@sjc.sp.gov.br<br>Telefone: 3901-2165</td>
                                                        </tr>
                                                        </table>
                                                        </Td>
                                                    </tr>
                                                </table>
                                                ',
                                                'SetFooter' => [
                                                    ['odd' => $pdfFooter, 'even' => $pdfFooter]
                                                ],
                                            ],
                                            'options' => [
                                                'title' => $title,
                                                'subject' => 'xx1',
                                                'keywords' => 'xx3',
                                            ],
                                            'contentBefore'=>'',
                                            'contentAfter'=>''
                                        ]
                                    ],
                                ]
                            ]); ?>
                                <?php 
                $qtdAluno = 0;
                $volFinanceiro = 0;
                foreach ($arrayData as $data)
                {
                    if ($data->quantidade) {
                        $volFinanceiro += $data->quantidade * Configuracao::setup()->passeEscolar;
                        $qtdAluno ++;
                    }
                }
            ?>
            <div class="row">
                <div class="col-xs-4">
                    <p class="lead"></p>
                    <div class="table-responsive">
                        <table class="table sumario">
                                <tr>
                                    <td class="titulo" colspan="2" style="text-align: center;">Passe escolar</td>
                                </tr>
                                <tr>
                                    <td class="titulo">Quantidade de Escolas</td>
                                    <td><?= $qtdAluno?></td>
                                </tr>
                                <tr>
                                    <td class="titulo">Volume financeiro</td>
                                    <td><?= 'R$ '.Yii::$app->formatter->asDecimal($volFinanceiro, 2)?></td>
                                </tr>
                        </table>
                    </div>
                </div>
                <div class="col-xs-6">

                </div>
            </div>
            </div>
        </div>

    </div>

</div>
        
<script type="text/javascript">
    $(document).ready(function() {
        $('table').DataTable({
            "paging":   false,
            "ordering": true,
            "order": [[ 1, "desc" ]],
            "info":     false,
            "bFilter": false
        });
    });
</script>