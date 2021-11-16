<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\Escola;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Usuario;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\data\ArrayDataProvider;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UsuarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = isset($pendente) ? 'Gestão de Documentos Pendentes' : 'Gestão de Documentos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4>
                    <?= '<span class="label label-primary">Total: '.$dataProvider->getTotalCount().'</span>'; ?>
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        
    <div class="box-header with-border">
                <div class="btn-group pull-right" style="display: none;">
                    <button id="w55" class="btn btn-default dropdown-toggle " title="Exportar" data-toggle="dropdown" aria-expanded="false" style="color:#3980D8">Exportar dados  <i class="glyphicon glyphicon-cloud-download"></i>  </button>
                    <ul id="w66" class="dropdown-menu dropdown-menu-right">
                        <!-- <li title="Texto Delimitado por Tabulação"><a class="export-txt" onclick='gerenciarExportacao(event,"TXT")' data-mime="text/plain" data-hash="b7d45805ba6739212bd208b8d5896e0ccaad77368c9df96b5dd483320ddecb67gridviewexportar-listagemtext/plainutf-81{&quot;colDelimiter&quot;:&quot;\t&quot;,&quot;rowDelimiter&quot;:&quot;\r\n&quot;}" data-css-styles="[]" tabindex="-1"><i class="text-muted glyphicon glyphicon-floppy-save"></i> Texto</a></li> -->
                        <li title="Microsoft Excel 95+"><a class="export-xls" onclick='gerenciarExportacao(event,"EXCEL")' data-mime="application/vnd.ms-excel" data-hash="c78def80d35ad515b4ececb6260d2a82230d11149b73a853a3f74d8ea62c7dfcgridviewexportar-listagemapplication/vnd.ms-excelutf-81{&quot;worksheet&quot;:&quot;ExportarPlanilha&quot;,&quot;cssFile&quot;:&quot;&quot;}" data-css-styles="{&quot;.kv-group-even&quot;:{&quot;background-color&quot;:&quot;#f0f1ff&quot;},&quot;.kv-group-odd&quot;:{&quot;background-color&quot;:&quot;#f9fcff&quot;},&quot;.kv-grouped-row&quot;:{&quot;background-color&quot;:&quot;#fff0f5&quot;,&quot;font-size&quot;:&quot;1.3em&quot;,&quot;padding&quot;:&quot;10px&quot;},&quot;.kv-table-caption&quot;:{&quot;border&quot;:&quot;1px solid #ddd&quot;,&quot;border-bottom&quot;:&quot;none&quot;,&quot;font-size&quot;:&quot;1.5em&quot;,&quot;padding&quot;:&quot;8px&quot;},&quot;.kv-table-footer&quot;:{&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-page-summary td&quot;:{&quot;background-color&quot;:&quot;#ffeeba&quot;,&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-align-center&quot;:{&quot;text-align&quot;:&quot;center&quot;},&quot;.kv-align-left&quot;:{&quot;text-align&quot;:&quot;left&quot;},&quot;.kv-align-right&quot;:{&quot;text-align&quot;:&quot;right&quot;},&quot;.kv-align-top&quot;:{&quot;vertical-align&quot;:&quot;top&quot;},&quot;.kv-align-bottom&quot;:{&quot;vertical-align&quot;:&quot;bottom&quot;},&quot;.kv-align-middle&quot;:{&quot;vertical-align&quot;:&quot;middle&quot;},&quot;.kv-editable-link&quot;:{&quot;color&quot;:&quot;#428bca&quot;,&quot;text-decoration&quot;:&quot;none&quot;,&quot;background&quot;:&quot;none&quot;,&quot;border&quot;:&quot;none&quot;,&quot;border-bottom&quot;:&quot;1px dashed&quot;,&quot;margin&quot;:&quot;0&quot;,&quot;padding&quot;:&quot;2px 1px&quot;}}" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>
                        <li title="Portable Document Format"><a class="export-pdf"  tabindex="-1"  onclick='gerenciarExportacao(event,"PDF")'><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>
                    </ul>
                </div>
        </div>
        <div class="box box-solid">
    


        <div class="box-body">
             <?= GridView::widget([
        'filterModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'panel' => [
            'heading'=>false,
            'type'=>false,
            'showFooter'=>false
        ],
        'summary' => "Exibindo <b>{begin}</b>-<b>{end}</b> de <b>{totalCount}</b> itens.",
        'toolbar' => \Yii::$app->showEntriesToolbar->create(),
        // 'dataProvider' => new ArrayDataProvider([
        //     'allModels' => $dataProvider,
        //     'sort' => [
        //         'attributes' => [
        //           'nome',
        //           // ''
        //           // 'status',
        //           // 'inicio',
        //           // 'fim',
        //           // 'criado',
        //           // 'creditoAdministrativo',
        //           // 'total'
        //         ],
        //     ],
        // ]),
        // 'dataProvider' => new ArrayDataProvider([
        //     'sort' => [
        //         'attributes' => ['nome', 'cnhValidade', 'veiculo.CRLV'],
        //     ],
        //     'pagination' => [
        //         'pageSize' => 10,
        //     ],
        // ]),
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
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
            'nome',
            [
                'attribute'=>'cnhValidade',
                'label'=>'CNH',
                'format'=>'text',
                'filter'=> '<div class="drp-container input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>'.
                 DateRangePicker::widget([
                    'name'  => 'GestaoDocumentosSearch[cnhValidade]',
                    'value' => $searchModel->cnhValidade,
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'd/m/Y',
                        ],
                    ],
                ]) . '</div>',
                'content'=>function($data){
                    return $data->cnhAlerta();
                }
            ],    
            [
                'attribute'=>'veiculo.CRLV',
                'label'=>'CRLV',
                'format'=>'text',
                'filter'=> '<div class="drp-container input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>'.
                 DateRangePicker::widget([
                    'name'  => 'GestaoDocumentosSearch[crlv]',
                    'value' => $searchModel->crlv,
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'd/m/Y',
                        ],
                    ],
                ]) . '</div>',
                'content'=>function($data){
                    return $data->veiculo ? $data->veiculo->crlvAlerta() : '-';
                }
            ], 
            [
                'attribute'=>'veiculo.dataVistoriaEstadual',
                'label'=>'Vistoria Semestral',
                'format'=>'text',
                'filter'=> '<div class="drp-container input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>'.
                 DateRangePicker::widget([
                    'name'  => 'GestaoDocumentosSearch[dataVistoriaEstadual]',
                    'value' => $searchModel->dataVistoriaEstadual,

                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'd/m/Y',
                        ],
                    ],
                ]) . '</div>',
                'content'=>function($data){
                    return $data->veiculo ? $data->veiculo->vistoriaEstadualAlerta() : '-';
                }
            ],  
            [
                'attribute'=>'veiculo.dataVencimentoSeguro',
                'label'=>'Seguro',
                'format'=>'text',
                'filter'=> '<div class="drp-container input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>'.
                 DateRangePicker::widget([
                    'name'  => 'GestaoDocumentosSearch[dataVencimentoSeguro]',
                    'value' => $searchModel->dataVencimentoSeguro,

                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'd/m/Y',
                        ],
                    ],
                ]) . '</div>',
                'content'=>function($data){
                    return $data->veiculo ? $data->veiculo->seguroAlerta() : '-';
                }
            ],     
            [
                'attribute' => 'anoFabricacao',
                'label' => 'Idade do veículo',
                'content' => function($data) {
                  return $data->veiculo ? $data->veiculo->anoAlerta() : '-';
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' =>  [
                    // 0 => '0 ou -',
                    1 => '1 Ano',
                    2 => '2 Anos',
                    3 => '3 Anos',
                    4 => '4 Anos',
                    5 => '5 Anos',
                    6 => '6 Anos',
                    7 => '7 Anos',
                    8 => '8 Anos',
                    9 => '8 Anos',
                    10 => '10 Anos',
                    11 => '11 Anos',
                    12 => '12 Anos',
                    13 => '13 Anos',
                    14 => '14 Anos',
                    15 => '15 Anos',
                    16 => '16 Anos',
                    17 => '17 Anos',
                    18 => '18 Anos',
                    19 => '19 Anos',
                    20 => '20 Anos',

                ], 
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => [
                    'placeholder' => '-',
                ]
            ],        
            // 'status',
            // 'nome',
            // 'idUsuario',
            // 'idEmpresa',
            // 'idVeiculo',
            // 'fotoMotorista',
            // 'regiao',
            // 'lugares',
            // 'dataNascimento',
            // 'alvara',
            // 'inscricaoMunicipal',
            // 'cpf',
            // 'rg',
            // 'orgaoEmissor',
            // 'lat',
            // 'lng',
            // 'nit',
            // 'endereco',
            // 'bairro',
            // 'telefone',
            // 'celularMonitor',
            // 'telefoneWhatsapp',
            // 'telefone2',
            // 'celular',
            // 'celular2',
            // 'telefoneMonitor',
            // 'telefoneMonitorWhatsapp',
            // 'telefoneWhatsapp2',
            // 'celularWhatsapp',
            // 'celularWhatsapp2',
            // 'celularMonitorWhatsapp',
            // 'email:email',
            // 'cnhRegistro',
            // 'numeroApolice',
            // 'cnhValidade',
            // 'dataInicioContrato',
            // 'dataFimContrato',
            // 'tipoContrato',
            // 'valorPagoKmViagem',
            // 'idCNHCondutor',
            // 'idComprovanteEndereco',
            // 'idCRLV',
            // 'idVistoriaEstadual',
            // 'idVstoriaMunicipal',
            // 'idApoliceSeguro',
            // 'idContrato',
            // 'nomeMonitor',
            // 'rgMonitor',
            // 'cpfMonitor',
            // 'minKmDia',
            // 'maxKmDia',
            // 'maxViagensDia',
            // 'numeroResidencia',
            // 'cep',
            // 'complementoResidencia',
            // 'tipoLogradouro',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
        </div>
    </div>
</div>
</div>

<script> 
$(document).ready(function() {
setTimeout(() => $("#w6").html($("#w66").html()), 200)

    
});
setTimeout(() => {
    try {
    document.getElementById('w6').innerHTML= document.getElementById('w66').innerHTML
} catch(e) {
    console.log(e);
}
}, 200)

function gerenciarExportacao(event, tipo){
        event.preventDefault();
        console.log(1);
        let indexes = [];
        let keys = $('#w4').yiiGridView('getSelectedRows');
        let checkboxes = keys;       
        
        
        let x='todos os registros de condutores?';
        if(checkboxes.length > 0){
            x = checkboxes.length+' registros?';
        } 
        console.warn(x)
        Swal.fire({
            title: 'Exportar registros',
            text: "Confirma a exportação de "+x,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'SIM',
            cancelButtonText: 'NÃO'
          }).then((result) => {
            if (result.value) {
                console.warn(checkboxes)
              window.open('index.php?r=gestao-documentos/exportar&selecionados='+checkboxes+'&tipo='+tipo)
            }
          })


    }
</script>