<?php
use common\models\ReciboPagamentoAutonomo;
use yii\helpers\Html;
use common\models\SolicitacaoCredito;
use yii\helpers\Url;
use common\models\Usuario;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;


?>
<style type="text/css">
input[type=checkbox] {
    /* Double-sized Checkboxes */
    -ms-transform: scale(2);
    /* IE */
    -moz-transform: scale(2);
    /* FF */
    -webkit-transform: scale(2);
    /* Safari and Chrome */
    -o-transform: scale(2);
    /* Opera */
    padding: 10px;
    margin-top: 0px;
}

a {
    cursor: pointer;
}

label {
    white-space: break-spaces !important;
}

.mt-10 {
    margin-top: 10px;
}

.mt-15 {
    margin-top: 15px;
}

.p-10 {
    padding: 10px;
}

input[type=checkbox] {
    /* Double-sized Checkboxes */
    -ms-transform: scale(2);
    /* IE */
    -moz-transform: scale(2);
    /* FF */
    -webkit-transform: scale(2);
    /* Safari and Chrome */
    -o-transform: scale(2);
    /* Opera */
    padding: 10px;
    margin-top: 0px;
}

.tgl {
    font-size: 20px;
    white-space: inherit !important;
    width: auto !important;
    margin-top: 0px !important;
}

.center-td {
    text-align: center !important;
    vertical-align: middle !important;
}
t
.center-text {
    vertical-align: middle !important;
}

.habilitadoNecessidadeCredito {
    display: none;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <?php if(isset($mostrarExportacao)): ?>
                <div class="btn-group pull-right">
                    <button id="w1" class="btn btn-default dropdown-toggle " title="Exportar" data-toggle="dropdown" aria-expanded="false" style="color:#3980D8">Exportar dados  <i class="glyphicon glyphicon-cloud-download"></i>  </button>
                    <ul id="w2" class="dropdown-menu dropdown-menu-right">
                        <li title="Texto Delimitado por Tabulação"><a class="export-txt" href="<?= Url::toRoute(['solicitacao-credito/exportar', 'id' =>  $model->id, 'tipo' => 'TXT']) ?>" data-mime="text/plain" data-hash="b7d45805ba6739212bd208b8d5896e0ccaad77368c9df96b5dd483320ddecb67gridviewexportar-listagemtext/plainutf-81{&quot;colDelimiter&quot;:&quot;\t&quot;,&quot;rowDelimiter&quot;:&quot;\r\n&quot;}" data-css-styles="[]" tabindex="-1"><i class="text-muted glyphicon glyphicon-floppy-save"></i> Texto</a></li>
                        <li title="Microsoft Excel 95+"><a class="export-xls" href="<?= Url::toRoute(['solicitacao-credito/exportar', 'id' =>  $model->id, 'tipo' => 'EXCEL']) ?>" data-mime="application/vnd.ms-excel" data-hash="c78def80d35ad515b4ececb6260d2a82230d11149b73a853a3f74d8ea62c7dfcgridviewexportar-listagemapplication/vnd.ms-excelutf-81{&quot;worksheet&quot;:&quot;ExportarPlanilha&quot;,&quot;cssFile&quot;:&quot;&quot;}" data-css-styles="{&quot;.kv-group-even&quot;:{&quot;background-color&quot;:&quot;#f0f1ff&quot;},&quot;.kv-group-odd&quot;:{&quot;background-color&quot;:&quot;#f9fcff&quot;},&quot;.kv-grouped-row&quot;:{&quot;background-color&quot;:&quot;#fff0f5&quot;,&quot;font-size&quot;:&quot;1.3em&quot;,&quot;padding&quot;:&quot;10px&quot;},&quot;.kv-table-caption&quot;:{&quot;border&quot;:&quot;1px solid #ddd&quot;,&quot;border-bottom&quot;:&quot;none&quot;,&quot;font-size&quot;:&quot;1.5em&quot;,&quot;padding&quot;:&quot;8px&quot;},&quot;.kv-table-footer&quot;:{&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-page-summary td&quot;:{&quot;background-color&quot;:&quot;#ffeeba&quot;,&quot;border-top&quot;:&quot;4px double #ddd&quot;,&quot;font-weight&quot;:&quot;bold&quot;},&quot;.kv-align-center&quot;:{&quot;text-align&quot;:&quot;center&quot;},&quot;.kv-align-left&quot;:{&quot;text-align&quot;:&quot;left&quot;},&quot;.kv-align-right&quot;:{&quot;text-align&quot;:&quot;right&quot;},&quot;.kv-align-top&quot;:{&quot;vertical-align&quot;:&quot;top&quot;},&quot;.kv-align-bottom&quot;:{&quot;vertical-align&quot;:&quot;bottom&quot;},&quot;.kv-align-middle&quot;:{&quot;vertical-align&quot;:&quot;middle&quot;},&quot;.kv-editable-link&quot;:{&quot;color&quot;:&quot;#428bca&quot;,&quot;text-decoration&quot;:&quot;none&quot;,&quot;background&quot;:&quot;none&quot;,&quot;border&quot;:&quot;none&quot;,&quot;border-bottom&quot;:&quot;1px dashed&quot;,&quot;margin&quot;:&quot;0&quot;,&quot;padding&quot;:&quot;2px 1px&quot;}}" tabindex="-1"><i class="text-success glyphicon glyphicon-floppy-remove"></i> Excel</a></li>
                        <li title="Portable Document Format"><a class="export-pdf"  tabindex="-1" href="<?= Url::toRoute(['solicitacao-credito/exportar', 'id' =>  $model->id, 'tipo' => 'PDF']) ?>"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>
                    </ul>
                </div>
                <?php endif; ?>
                <h3><?= Html::encode($this->title) ?></h3>
                <h4>Escola: <?= Html::encode($model->escola->nome) ?></h4>
                <h4>Período: <?= ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesInicio]; ?> -
                    <?= ReciboPagamentoAutonomo::ARRAY_MESES[$model->mesFim]; ?></h4>
                <h4>Status atual: <?= SolicitacaoCredito::ARRAY_STATUS[$model->status]; ?></h4>
                <?php 
                    foreach($model->historico as $h) {
                        if($h->status != $model->status)
                            print '<h4>'.SolicitacaoCredito::ARRAY_STATUS[$h->status].': '.date("d/m/Y", strtotime($h->dataCadastro)).'</h4>';
                    } 
                ?>
                <h4>Solicitação criada em: <?= date("d/m/Y H:i", strtotime($model->criado)) ?></h4>
                <h4>Valor unitário da passagem: R$ <?= $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? \Yii::$app->formatter::DoubletoReal($configuracao->passeEscolar) : \Yii::$app->formatter::DoubletoReal($configuracao->valeTransporte) ?></h4>
                <?php if($model->dataTransferencia): ?>
                    <h4>Data da transferência: <?= date("d/m/Y", strtotime($model->dataTransferencia)) ?></h4>
                <?php endif; ?>
                <?php if($model->valorTransferido): ?>
                    <h4>Valor transferido: <?= \Yii::$app->formatter::DoubletoReal($model->valorTransferido) ?></h4>
                <?php endif; ?>

                <?php
                     if(isset($mostrarAprovacao)):
                        switch ($model->status) {
                            case SolicitacaoCredito::STATUS_EFETIVADA:
                                if (Usuario::permissao(Usuario::PERFIL_DIRETOR)) {
                                    echo '<div class="box-header with-border">';
                                    echo Html::button('DEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_DEFERIDO_DIRETOR]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
                                    echo Html::button('INDEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_INDEFERIDO]), 'title' => 'INDEFERIR', 'class' => 'showModalButton btn btn-danger pull-right']);
                                    echo '</div>';
                                }
                                break;

                            case SolicitacaoCredito::STATUS_DEFERIDO_DIRETOR:
                                if (Usuario::permissoes([Usuario::PERFIL_TESC_PASSE_ESCOLAR, Usuario::PERFIL_SUPER_ADMIN])) {
                                    //actionAlteracaoStatusAjax
                                    echo '<div class="box-header with-border">';
                                    echo Html::button('DEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_DEFERIDO]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
                                    echo Html::button('INDEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_INDEFERIDO]), 'title' => 'INDEFERIR', 'class' => 'showModalButton btn btn-danger pull-right']);
                                    echo '</div>';
                                }
                                if (Usuario::permissoes([Usuario::PERFIL_DRE])) {
                                    //actionAlteracaoStatusAjax
                                    echo '<div class="box-header with-border">';
                                    echo Html::button('DEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_DEFERIDO]), 'title' => 'Deferimento', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
                                    echo Html::button('INDEFERIR', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_INDEFERIDO]), 'title' => 'INDEFERIR', 'class' => 'showModalButton btn btn-danger pull-right']);
                                    echo '</div>';
                                }
                                break;


                            case SolicitacaoCredito::STATUS_DEFERIDO_DRE:   
                                if (Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_PASSE_ESCOLAR])) {
                                    //actionAlteracaoStatusAjax
                                    echo '<div class="box-header with-border">';
  
                                    echo Html::button('RECEBER', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_DEFERIDO]), 'title' => 'RECEBER', 'class' => 'showModalButton  align-button btn btn-success pull-right']);
                                    echo Html::button('DEVOLVER', ['value' => Url::to(['solicitacao-credito/alteracao-status-ajax', 'id' => $model->id, 'status' => SolicitacaoCredito::STATUS_INDEFERIDO]), 'title' => 'DEVOLVER', 'class' => 'showModalButton btn btn-danger pull-right']);
                                    echo '</div>';
                                }
                                break;
                            
                            case SolicitacaoCredito::STATUS_DEFERIDO: 
                                if (Usuario::permissoes([Usuario::PERFIL_SUPER_ADMIN, Usuario::PERFIL_TESC_PASSE_ESCOLAR])) { ?>
                                  <?php $form = ActiveForm::begin([
                                        'id' => 'formAluno',
                                        'options' => ['enctype' => 'multipart/form-data'],
                                        'encodeErrorSummary' => false,
                                        'errorSummaryCssClass' => 'help-block',

                                    ]); ?>
                                   <div class="row">
                                       <?php if(!$model->dataTransferencia): ?>
                                       <div class="col-md-2">
                                            <?php 
                                                echo $form->field($model, 'dataTransferencia')->widget(DatePicker::classname(), [
                                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                                    'value' =>  $model->dataTransferencia,
                                                    'options' => ['placeholder' => 'Data',   'id' => 'dataTransferencia',],
                                                    'pluginOptions' => [
                                                      
                                                        'orientation' => 'bottom left',
                                                        'autoclose'=>true,
                                                        'format' => 'dd/mm/yyyy',
                                                        // 'startDate' => 'today',
                                                    ]
                                                ]);
                                            ?>
                                       </div>
                                        <?php  endif; ?>
                                        <?php if(!$model->valorTransferido): ?>
                                        <div class="col-md-2">
                                            <?= $form->field($model, 'valorTransferido')->textInput(['id' => 'valorTransferido','maxlength' => true, 'class' => 'form-control money']); ?>

                                        </div> 
                                        <?php  endif; ?>
                                        <?php if(!$model->valorTransferido || !$model->dataTransferencia): ?>
                                        <div class="col-md-1">
                                            <Br><Br>
                                            <?= Html::submitButton('Salvar', ['class' =>  'btn btn-success', 'disabled' => true, 'id' => 'salvarTransferencia']) ?>
                                        </div> 
                                        <?php  endif; ?>
                                   </div> 
                                   <?php ActiveForm::end(); ?>  
                                <?php
                                }
                            break;
                        }
                        endif; 
                    ?>
					

            </div>
            
        </div>
    </div>	
</div>


<script>
$("#dataTransferencia").change(function(){
    habilitarTransferencia()
})
$("#valorTransferido").keyup(function(){
   // setTimeout(() => habilitarTransferencia(), 500)
   habilitarTransferencia()
})
function habilitarTransferencia(){
    
    $("#salvarTransferencia").prop('disabled', true);
    if(dataTransferencia.value){
        try {
            var dateString = dataTransferencia.value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            var d = new Date( dateString[3], dateString[2]-1, dateString[1] );
            // console.log(d)
        } catch(e){
            console.error(e)
            dataTransferencia.value = ""
            return Swal.fire(
                '',
                'Selecione uma data válida',
                'error'
            )
        }
    }
    console.warn(valorTransferido.value,dataTransferencia.value)
    if(valorTransferido.value && dataTransferencia.value){
        $("#salvarTransferencia").prop('disabled', false);
    }
}
</script>