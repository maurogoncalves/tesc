<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;
use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;

use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AlunoRotaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Aluno Rotas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
    <?php Pjax::begin(); ?>  
    <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $alunos,
            'key' => 'id',
            'pagination' => [
                'pageSize' => 20,
            ],
        ]),
        'pjax' => true,
        'pjaxSettings' =>[
            'options'=>[
                    'id'=>'gridAlunoRota',
                    'enablePushState'=>false,

                ]
            ],
        'options' => [
            'class' => 'table-header-ajax',
         ],
        'striped' => false,
        'bootstrap' => true,
        'emptyText' => '<h3 class="vazio">Nenhuma rota</h3>',
        'columns' => [
           [
                'attribute' => 'idAluno',
                'label' => 'Aluno',
                'value'=>  function($model){
                        return $model->aluno->nome;
                },
            ],
            [
                'attribute' => 'idEscola',
                'label' => 'Escola',
                'value'=>  function($model){
                        return $model->escola->nome;
                },
            ],
           [
                   'class' => 'yii\grid\ActionColumn',
                   'template' => '{delete}  ',
                   'buttons' => [
           
                          'delete' => function($url, $model){
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['aluno-rota/delete', 'id' => $model->id],
                                    [                                    
                                    'data' => [
                                    'confirm' => 'Tem certeza que deseja excluir este item?',
                                    'method' => 'post',
                                    'pjax' => 1,                                            
                                    'ok' => Yii::t('yii', 'Confirm'),
                                    'cancel' => Yii::t('yii', 'Cancel'),
                                    ],
                                    ]);
                            },
                       
                 
                    ]
            ],
        ],
    ]); ?>
        <?php Pjax::end(); ?> 
    </div>
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(
            [
                'action' => Url::to([$action, 'id' => $model->id, 'idCondutorRota' => $idCondutorRota]),
                'options' => [
                    'id' => 'formAjaxFromHell',
                    'enctype' => 'multipart/form-data',
                    'data-pjax' => true
                ],
                // 'validateOnBlur' => false,
                // 'enableClientValidation' => true,
                // 'encodeErrorSummary' => false,
                // 'errorSummaryCssClass' => 'help-block',
            ]
        ); ?>

        <?= $form->field($model, 'idCondutorRota')->hiddenInput()->label(false) ?>

        <div class="row">
            <div class="col-md-6">
                       <div class="form-group">
            <label class="control-label" for="idServico">Escola</label>
            <?php 
            echo Select2::widget([
                        'model' => $model,
                        'attribute' => 'idEscola',
                         'data' => ArrayHelper::map($escolas, 'escola.id', 'escola.nome'),
                        'options' => [
                            'id' => 'id-unidade',
                            'placeholder' => 'Selecione a escola',
                            'multiple' => false,
                        ],
                        'pluginEvents' => [
                            "change" => 'function() { 
                                 
                                  $("#id-tipo").select2("val", "");
                                  $("#id-tipo").html("");  

                                  $.get( "index.php?r=aluno/aluno-escola-ajax", { idEscola: $(this).val() } )
                                  .done(function( data ) {
                                    console.log(data);
                                    data.forEach((item, i) => {
                                        console.log(item);
                                          $("#id-tipo").append($("<option/>", {
                                            value: item.id,
                                            text: item.nome
                                        }));
                                    });
                                  });
                               
                            }',
                        ],
                    ]);

                ?>
                </div>
            </div>
        
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label" for="idServico">Aluno</label>
                    <?php 
                    echo Select2::widget([
                                'model' => $model,
                                'attribute' => 'idAluno',
                                'data' => [],
                                'options' => [
                                    'id' => 'id-tipo',
                                    'placeholder' => 'Selecione o aluno',
                                    'multiple' => false,
                                ],
                                'pluginEvents' => [
                                    "change" => 'function() { 

                                    }',
                                ],
                            ]); 
                    ?>
                </div>
            </div>
        </div>



        <div class="form-group">
         <?= Html::Button($model->isNewRecord ? 'Salvar' : 'Atualizar', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right','id' => 'salvarAlunoRota']) ?>
        </div>

        <?php ActiveForm::end(); ?>        
    </div>
</div>




<script>
    $(document).on("pjax:timeout", function() { return false; });
    $(document).on("click", "#salvarAlunoRota", function(e) {
        console.log('CLICADO');
        let form = $("#formAjaxFromHell");
        e.preventDefault();
        e.stopImmediatePropagation();

        var $yiiform = form;
        var formData = new FormData(form[0]);
        $.ajax({
                type: $yiiform.attr('method'),
                url: $yiiform.attr('action'),
                data: formData,
                processData: false,
                contentType: false
            }).done(function(data) {
                e.preventDefault();
                e.stopImmediatePropagation();
                if (data.status) {
                    console.log(data)
                    console.log("RECARREGAR O INPUT");
                    //$.pjax.reload({container:"#gridAlunoRota"});
                    // $("#lancamento-idplanoconta").html("");
                    // $("#lancamento-idplanoconta").append("<option value=''><option>");
                    // $.get('index.php?r=planoconta/index-ajax').done((result) => {
                    //     result.forEach((item, i) => {
                    //         $("#lancamento-idplanoconta").append('<OPTION value="' + item.id + '">' + item.nome + '</OPTION>');
                    //     })
                    // });
                    $("#modal").modal('hide');
                    return false;
                } else if (data.validation) {
                    console.log("2")
                    console.log(data)
                } else {
                    console.log("3")
                    console.log(data)
                    $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                    return false;
                }
            })
            .fail(function() {
            })

        return false;
    });


</script>
