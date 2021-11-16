<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use common\models\Aluno;
use kartik\select2\Select2;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PontoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Pontos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">

   
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'idCondutorRota',
             [
                'attribute' => 'idAluno',
                'value' => function($model){
                    return $model->aluno->nome;//Yii::t('app', $model->escola->nome);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Aluno::find()->all(), 'id', 'nome'), 
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => [
                    'placeholder' => '-',
                ]
            ],
            // 'lat',
            // 'lng',

            // ['class' => 'yii\grid\ActionColumn'],
             [
                   'class' => 'yii\grid\ActionColumn',
                   'template' => '{view}',
                   'buttons' => [
                         'view' => function ($url, $model) {
                            return Html::button('<i class="fa fa-fw fa-trash"></i>', [ 'class' => 'btn btn-primary ', 'onclick' => 'remover('.$model->id.')' ]);

                        
                        },
                 
                    ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>        </div>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>


<script>
    var idCondutorRota = <?php print $idCondutorRota; ?>;
    function remover(id){
         $.ajax({
                type: 'post',
                url:'http://localhost/tesc/frontend/web/index.php?r=ponto%2Fdelete&id='+id,
                
                contentType: false
            }).done(function(data) {
                if (data.status) {
                    console.log(data)
                    console.log("RECARREGAR O INPUT");
                     $('#rota-'+idCondutorRota).text(parseFloat($('#rota-'+idCondutorRota).text()) - 1);
                    // $("#lancamento-idplanoconta").html("");
                    // $("#lancamento-idplanoconta").append("<option value=''><option>");
                    // $.get('index.php?r=planoconta/index-ajax').done((result) => {
                    //     result.forEach((item, i) => {
                    //         $("#lancamento-idplanoconta").append('<OPTION value="' + item.id + '">' + item.nome + '</OPTION>');
                    //     })
                    // });
                    $("#modal").modal('hide');
                     Swal.fire({
                      position: 'top-end',
                      type: 'success',
                      title: 'Removido com sucesso',
                      showConfirmButton: false,
                      timer: 1500
                    })
                    return false;
                }
        });
    }
    // $(document).on("submit", "#formAjax", function(e) {
    //     e.preventDefault();
    //     e.stopImmediatePropagation();

    //     var $yiiform = $(this);
    //     var formData = new FormData($(this)[0]);
    //     $.ajax({
    //             type: $yiiform.attr('method'),
    //             url: $yiiform.attr('action'),
    //             data: formData,
    //             processData: false,
    //             contentType: false
    //         }).done(function(data) {
    //             if (data.status) {
    //                 console.log(data)
    //                 console.log("RECARREGAR O INPUT");
    //                 $.pjax.reload({container:"#gridSolicitacoes"});
    //                 // $("#lancamento-idplanoconta").html("");
    //                 // $("#lancamento-idplanoconta").append("<option value=''><option>");
    //                 // $.get('index.php?r=planoconta/index-ajax').done((result) => {
    //                 //     result.forEach((item, i) => {
    //                 //         $("#lancamento-idplanoconta").append('<OPTION value="' + item.id + '">' + item.nome + '</OPTION>');
    //                 //     })
    //                 // });
    //                 $("#modal").modal('hide');
    //                 return false;
    //             } else if (data.validation) {
    //                 console.log("2")
    //                 console.log(data)
    //             } else {
    //                 console.log("3")
    //                 console.log(data)
    //                 $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
    //                 return false;
    //             }
    //         })
    //         .fail(function() {
    //         })

    //     return false;
    // });
</script>