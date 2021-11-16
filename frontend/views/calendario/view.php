<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use common\models\Escola;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Calendário';
$this->params['breadcrumbs'][] = ['label' => 'Calendário', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
/* @var $this yii\web\View */
/* @var $model common\models\Calendario */
/* @var $form yii\widgets\ActiveForm */
?>
<?php 
    // CASO ele NÃO seja perfil de visualização
$JSEventClick = <<<EOF
function(calEvent, jsEvent, view) { 
    var eventoSelecionado = calEvent.id;
    idAgendamento = calEvent.id;
    console.log("idAgendamento")
    console.log(idAgendamento)
    var compareceu = '';
    var buttons = [
        {
          text: "OK",
          class: "btn btn-primary btn-clear",
          click: function() {
            $( this ).dialog( "close" );
          }
        }
    ];

    if(calEvent.id  != 0) {
        showAlert(calEvent.id); 
    } else {
        showNotice();
    }
 
  
}
EOF;


$JSEventMouseover = <<<EOF
function(calEvent, jsEvent, view) {
    $(this).addClass('fc-event-hover');
}
EOF;

$JSEventMouseout = <<<EOF
function(calEvent, jsEvent, view) {
    $(this).removeClass('fc-event-hover');
}
EOF;
?>
<style>
.btn-swal {
    margin-left: 9px !important;
}
</style>

<script>    
const swalWithBootstrapButtons = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-primary',
    cancelButton: 'btn btn-danger btn-swal'
  },
  buttonsStyling: false
})
function showNotice(){
    swalWithBootstrapButtons.fire(
        'Dia padrão do sistema',
        'O sistema não pode ficar sem um registro nesse dia. Crie um novo registro para sobrepor o registro atual',
        'warning'
        )
       
}
function showAlert(id){    
    swalWithBootstrapButtons.fire({
    title: 'Excluir registro?',
    text: "A remoção dese registro não afeta solicitações de crédito antigas",
    type: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Confirmar exclusão',
    cancelButtonText: 'Cancelar',
    reverseButtons: true
    }).then((result) => {
      
    if (result.value) {
        $.post( "index.php?r=calendario/cancelar-evento", { evento: id })
      .done(function( data ) {
            swalWithBootstrapButtons.fire(
            'Removido!',
            'Operação realizada com sucesso',
            'success'
            )
            $("#w1").fullCalendar('refetchEvents');
      });
       
    }
    })
}

</script>
<div class="box-body">

    <?php $form = ActiveForm::begin(); ?>
     
    <div class="row">
        <div class="col-md-8">
            <h3> 
            Tipo(s) de escola:
            <?php
            $str = '';
            foreach($model->calendarioEscolas as $escola)
                $str .= Escola::ARRAY_TIPO[$escola->tipoEscola].', ';
                 echo substr($str, 0, -2).'';
             ?>.
             </h3>
        </div>
        <div class="col-md-4">
                  <?=   Html::button('Novo Registro', ['value' => Url::to(['calendario/create-agendamento','id' => $model->id ]), 'title' => 'Novo registro', 'class' => 'showModalButton btn btn-success pull-right']); ?>
            </div>
        <div class="col-md-12">
        <small class="label label-danger"><i class="fa fa-clock-o"></i>Dia não letivo</small>
        <small class="label label-success"><i class="fa fa-clock-o"></i>Dia letivo</small>

        <?= yii2fullcalendar\yii2fullcalendar::widget(array(
                                'ajaxEvents' => Url::to(['/calendario/eventos/','idCalendario' => $model->id]),
                                    'header' => [
                                    'center'=>'title',
                                    //                'left'=>'title',
                                    'left'=>'prev,next',
                                    'right'=>'',
                                ],
                                'options' => [
                                    'themeSystem' => 'bootstrap4',
                                    'language' => 'pt-BR',
                                ],
                                'clientOptions' => [

                                    'lang'=>'pt-BR',
                                    'eventClick' => new JsExpression($JSEventClick),
                                    'eventMouseover' => new JsExpression($JSEventMouseover),
                                    'eventMouseout' => new JsExpression($JSEventMouseout),
                                    'selectable' => true,
                                    'selectHelper' => true,
                                    'droppable' => true,
                                    'editable' => true,
                                    'timeFormat'=> 'HH:mm',
                                
                                ],
                            ));
                        ?>
        </div>
    </div>
  
    <?php ActiveForm::end(); ?>
</div>
