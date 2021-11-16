<?php

use yii\helpers\Html;
// use kartik\detail\DetailView;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\helpers\Url;

use common\models\Escola;
use common\models\Usuario;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $model common\models\Escola */

$this->title = '';// $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Roteirização', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3><?= Html::encode($this->title) ?></h3>
            </div>
             <div class="box-body">
              <h4><?= Html::encode($escola->nome) ?></h4>
              <button class="btn btn-primary">Atribuir automaticamente</button><br>
              <br>
              <ul class="nav nav-tabs">
   
                <li class="active"><a data-toggle="tab" href="#manhaIda">Manhã - Ida</a></li>
                <li><a data-toggle="tab" href="#manhaVolta">Manhã - Volta</a></li>
                <li><a data-toggle="tab" href="#tardeIda">Tarde - Ida</a></li>
                <li><a data-toggle="tab" href="#tardeVolta">Tarde - Volta</a></li>
                <li><a data-toggle="tab" href="#noiteIda">Noite - Ida</a></li>
                <li><a data-toggle="tab" href="#noiteVolta">Noite - Volta</a></li>
              </ul>

              <div class="tab-content">
                <div id="manhaIda" class="tab-pane fade in active">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                              <?php foreach ($manhaIda as $rota) { ?>
                                 <div class="col-md-3 <?php print $rota->condutor->veiculo ? 'droppable' : '' ?>" >
                                     <div class="thumbnail "  style="text-align: center;">
                                          <img src="img/profile-default-img.png" alt="..." width="40%" height="20">
                                          <div class="caption">
                                            <span class="hidden"><?php print $rota->id ?></span>
                                            <h3><?php print $rota->condutor->nome ?></h3>
                                            <?php if($rota->condutor->veiculo){ ?>
                                            <p>Placa: <?php print $rota->condutor->veiculo->placa ?></p>
                                            <div class="row">
                                                <div class="col-md-6">

                                                    <h3><span class="totalAlunos" id="rota-<?php print $rota->id; ?>"><?php print count($rota->pontos); ?></span> / <?php print $rota->condutor->veiculo->capacidade ?></h3>
                                                </div>
                                                <div class="col-md-6">
                                                   
                                                    <h3><?= Html::button('<i class="fa fa-search" aria-hidden="true"></i>', ['value' => Url::to(['ponto/index-ajax','idCondutorRota' => $rota->id]), 'title' => 'Alunos', 'class' => 'showModalButton btn btn-primary ']); ?>  <?= Html::a('<i class="fa fa-map" aria-hidden="true"></i>', '',
                                                                      ['onclick' => "window.open ('".Url::toRoute(['ponto/mapa-ajax', 
                                                                                    'idCondutorRota' =>  $rota->id, 'idEscola' => $escola->id ])."'); return false", 
                                                                       'class' => 'btn btn-primary']);?></h3>
                                                  

,
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <br>
                                            <div class="alert alert-danger" role="alert">Nenhum veículo associado</div>
                                            <br>

                                            <?php  } ?>
                                          </div>
                                        </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                         <div id="list-B">
                            <h3>Alunos</h3>
                            <ul id='' class="list-group">
                              <?php 
                              foreach ($escola->alunos as $aluno) {
                                print ' <li  class="draggable list-group-item">'.$aluno->nome.'<span class="hidden">'.$aluno->id.'</span></li>';
                              }
                              ?>
                            </ul>
                        </div>

                             
                           
                        </div>
                    </div>
                </div>
                <div id="manhaVolta" class="tab-pane fade">
                                      <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                              <?php foreach ($manhaVolta as $rota) { ?>
                                 <div class="col-md-3 <?php print $rota->condutor->veiculo ? 'droppable' : '' ?>" >
                                     <div class="thumbnail "  style="text-align: center;">
                                          <img src="img/profile-default-img.png" alt="..." width="40%" height="20">
                                          <div class="caption">
                                            <span class="hidden"><?php print $rota->id ?></span>
                                            <h3><?php print $rota->condutor->nome ?></h3>
                                            <?php if($rota->condutor->veiculo){ ?>
                                            <p>Placa: <?php print $rota->condutor->veiculo->placa ?></p>
                                            <div class="row">
                                                <div class="col-md-6">

                                                    <h3><span class="totalAlunos" id="rota-<?php print $rota->id; ?>"><?php print count($rota->pontos); ?></span> / <?php print $rota->condutor->veiculo->capacidade ?></h3>
                                                </div>
                                                <div class="col-md-6">
                                                   
                                                    <h3><?= Html::button('<i class="fa fa-search" aria-hidden="true"></i>', ['value' => Url::to(['ponto/index-ajax','idCondutorRota' => $rota->id]), 'title' => 'Alunos', 'class' => 'showModalButton btn btn-primary ']); ?>  <?= Html::a('<i class="fa fa-map" aria-hidden="true"></i>', '',
                                                                      ['onclick' => "window.open ('".Url::toRoute(['ponto/mapa-ajax', 
                                                                                    'idCondutorRota' =>  $rota->id, 'idEscola' => $escola->id ])."'); return false", 
                                                                       'class' => 'btn btn-primary']);?></h3>
                                                  

,
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <br>
                                            <div class="alert alert-danger" role="alert">Nenhum veículo associado</div>
                                            <br>

                                            <?php  } ?>
                                          </div>
                                        </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                         <div id="list-B">
                                               <h3>Alunos</h3>

                            <ul id='' class="list-group">
                              <?php 
                              foreach ($escola->alunos as $aluno) {
                                print ' <li  class="draggable list-group-item">'.$aluno->nome.'<span class="hidden">'.$aluno->id.'</span></li>';
                              }
                              ?>
                            </ul>
                        </div>

                             
                           
                        </div>
                    </div>
                </div>
                <div id="tardeIda" class="tab-pane fade">
                                      <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                              <?php foreach ($tardeIda as $rota) { ?>
                                 <div class="col-md-3 <?php print $rota->condutor->veiculo ? 'droppable' : '' ?>" >
                                     <div class="thumbnail "  style="text-align: center;">
                                          <img src="img/profile-default-img.png" alt="..." width="40%" height="20">
                                          <div class="caption">
                                            <span class="hidden"><?php print $rota->id ?></span>
                                            <h3><?php print $rota->condutor->nome ?></h3>
                                            <?php if($rota->condutor->veiculo){ ?>
                                            <p>Placa: <?php print $rota->condutor->veiculo->placa ?></p>
                                            <div class="row">
                                                <div class="col-md-6">

                                                    <h3><span class="totalAlunos" id="rota-<?php print $rota->id; ?>"><?php print count($rota->pontos); ?></span> / <?php print $rota->condutor->veiculo->capacidade ?></h3>
                                                </div>
                                                <div class="col-md-6">
                                                   
                                                    <h3><?= Html::button('<i class="fa fa-search" aria-hidden="true"></i>', ['value' => Url::to(['ponto/index-ajax','idCondutorRota' => $rota->id]), 'title' => 'Alunos', 'class' => 'showModalButton btn btn-primary ']); ?>  <?= Html::a('<i class="fa fa-map" aria-hidden="true"></i>', '',
                                                                      ['onclick' => "window.open ('".Url::toRoute(['ponto/mapa-ajax', 
                                                                                    'idCondutorRota' =>  $rota->id, 'idEscola' => $escola->id ])."'); return false", 
                                                                       'class' => 'btn btn-primary']);?></h3>
                                                  

,
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <br>
                                            <div class="alert alert-danger" role="alert">Nenhum veículo associado</div>
                                            <br>

                                            <?php  } ?>
                                          </div>
                                        </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                         <div id="list-B">
                                               <h3>Alunos</h3>

                            <ul id='' class="list-group">
                              <?php 
                              // foreach ($escola->alunos as $aluno) {
                              //   print ' <li  class="draggable list-group-item">'.$aluno->nome.'<span class="hidden">'.$aluno->id.'</span></li>';
                              // }
                              ?>
                            </ul>
                        </div>

                             
                           
                        </div>
                    </div>
                </div>
                <div id="tardeVolta" class="tab-pane fade">
                                     <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                              <?php foreach ($tardeVolta as $rota) { ?>
                                 <div class="col-md-3 <?php print $rota->condutor->veiculo ? 'droppable' : '' ?>" >
                                     <div class="thumbnail "  style="text-align: center;">
                                          <img src="img/profile-default-img.png" alt="..." width="40%" height="20">
                                          <div class="caption">
                                            <span class="hidden"><?php print $rota->id ?></span>
                                            <h3><?php print $rota->condutor->nome ?></h3>
                                            <?php if($rota->condutor->veiculo){ ?>
                                            <p>Placa: <?php print $rota->condutor->veiculo->placa ?></p>
                                            <div class="row">
                                                <div class="col-md-6">

                                                    <h3><span class="totalAlunos" id="rota-<?php print $rota->id; ?>"><?php print count($rota->pontos); ?></span> / <?php print $rota->condutor->veiculo->capacidade ?></h3>
                                                </div>
                                                <div class="col-md-6">
                                                   
                                                    <h3><?= Html::button('<i class="fa fa-search" aria-hidden="true"></i>', ['value' => Url::to(['ponto/index-ajax','idCondutorRota' => $rota->id]), 'title' => 'Alunos', 'class' => 'showModalButton btn btn-primary ']); ?>  <?= Html::a('<i class="fa fa-map" aria-hidden="true"></i>', '',
                                                                      ['onclick' => "window.open ('".Url::toRoute(['ponto/mapa-ajax', 
                                                                                    'idCondutorRota' =>  $rota->id, 'idEscola' => $escola->id ])."'); return false", 
                                                                       'class' => 'btn btn-primary']);?></h3>
                                                  

,
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <br>
                                            <div class="alert alert-danger" role="alert">Nenhum veículo associado</div>
                                            <br>

                                            <?php  } ?>
                                          </div>
                                        </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                         <div id="list-B">
                                               <h3>Alunos</h3>

                            <ul id='' class="list-group">
                              <?php 
                              // foreach ($escola->alunos as $aluno) {
                              //   print ' <li  class="draggable list-group-item">'.$aluno->nome.'<span class="hidden">'.$aluno->id.'</span></li>';
                              // }
                              ?>
                            </ul>
                        </div>

                             
                           
                        </div>
                    </div>
                </div>
                <div id="noiteIda" class="tab-pane fade">
                                     <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                              <?php foreach ($noiteIda as $rota) { ?>
                                 <div class="col-md-3 <?php print $rota->condutor->veiculo ? 'droppable' : '' ?>" >
                                     <div class="thumbnail "  style="text-align: center;">
                                          <img src="img/profile-default-img.png" alt="..." width="40%" height="20">
                                          <div class="caption">
                                            <span class="hidden"><?php print $rota->id ?></span>
                                            <h3><?php print $rota->condutor->nome ?></h3>
                                            <?php if($rota->condutor->veiculo){ ?>
                                            <p>Placa: <?php print $rota->condutor->veiculo->placa ?></p>
                                            <div class="row">
                                                <div class="col-md-6">

                                                    <h3><span class="totalAlunos" id="rota-<?php print $rota->id; ?>"><?php print count($rota->pontos); ?></span> / <?php print $rota->condutor->veiculo->capacidade ?></h3>
                                                </div>
                                                <div class="col-md-6">
                                                   
                                                    <h3><?= Html::button('<i class="fa fa-search" aria-hidden="true"></i>', ['value' => Url::to(['ponto/index-ajax','idCondutorRota' => $rota->id]), 'title' => 'Alunos', 'class' => 'showModalButton btn btn-primary ']); ?>  <?= Html::a('<i class="fa fa-map" aria-hidden="true"></i>', '',
                                                                      ['onclick' => "window.open ('".Url::toRoute(['ponto/mapa-ajax', 
                                                                                    'idCondutorRota' =>  $rota->id, 'idEscola' => $escola->id ])."'); return false", 
                                                                       'class' => 'btn btn-primary']);?></h3>
                                                  

,
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <br>
                                            <div class="alert alert-danger" role="alert">Nenhum veículo associado</div>
                                            <br>

                                            <?php  } ?>
                                          </div>
                                        </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                         <div id="list-B">
                                               <h3>Alunos</h3>

                            <ul id='' class="list-group">
                              <?php 
                              // foreach ($escola->alunos as $aluno) {
                              //   print ' <li  class="draggable list-group-item">'.$aluno->nome.'<span class="hidden">'.$aluno->id.'</span></li>';
                              // }
                              ?>
                            </ul>
                        </div>

                             
                           
                        </div>
                    </div>
                </div>
                <div id="noiteVolta" class="tab-pane fade">
                                     <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                              <?php foreach ($noiteVolta as $rota) { ?>
                                 <div class="col-md-3 <?php print $rota->condutor->veiculo ? 'droppable' : '' ?>" >
                                     <div class="thumbnail "  style="text-align: center;">
                                          <img src="img/profile-default-img.png" alt="..." width="40%" height="20">
                                          <div class="caption">
                                            <span class="hidden"><?php print $rota->id ?></span>
                                            <h3><?php print $rota->condutor->nome ?></h3>
                                            <?php if($rota->condutor->veiculo){ ?>
                                            <p>Placa: <?php print $rota->condutor->veiculo->placa ?></p>
                                            <div class="row">
                                                <div class="col-md-6">

                                                    <h3><span class="totalAlunos" id="rota-<?php print $rota->id; ?>"><?php print count($rota->pontos); ?></span> / <?php print $rota->condutor->veiculo->capacidade ?></h3>
                                                </div>
                                                <div class="col-md-6">
                                                   
                                                    <h3><?= Html::button('<i class="fa fa-search" aria-hidden="true"></i>', ['value' => Url::to(['ponto/index-ajax','idCondutorRota' => $rota->id]), 'title' => 'Alunos', 'class' => 'showModalButton btn btn-primary ']); ?>  <?= Html::a('<i class="fa fa-map" aria-hidden="true"></i>', '',
                                                                      ['onclick' => "window.open ('".Url::toRoute(['ponto/mapa-ajax', 
                                                                                    'idCondutorRota' =>  $rota->id, 'idEscola' => $escola->id ])."'); return false", 
                                                                       'class' => 'btn btn-primary']);?></h3>
                                                  

,
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <br>
                                            <div class="alert alert-danger" role="alert">Nenhum veículo associado</div>
                                            <br>

                                            <?php  } ?>
                                          </div>
                                        </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                         <div id="list-B">
                                               <h3>Alunos</h3>

                            <ul id='' class="list-group">
                              <?php 
                              // foreach ($escola->alunos as $aluno) {
                              //   print ' <li  class="draggable list-group-item">'.$aluno->nome.'<span class="hidden">'.$aluno->id.'</span></li>';
                              // }
                              ?>
                            </ul>
                        </div>

                             
                           
                        </div>
                    </div>
                </div>
                  
              </div>



             </div>
            </div>
        </div>
    </div>
</div>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script type="text/javascript">

// Case A: Move within the same list
// Case B: Move from one list to another

$(function() {

 
    $( ".draggable" ).draggable({
       revert: "invalid", 
    });
    $( ".droppable" ).droppable({
      drop: function( event, ui ) {
        Swal.fire({
          position: 'top-end',
          type: 'success',
          title: 'Atribuído com sucesso',
          showConfirmButton: false,
          timer: 1500
        })
        let idCondutorRota =   $(this).find('.hidden').text();
        deleteImage( ui.draggable, idCondutorRota);
        $(this).find('.totalAlunos').text( parseInt($(this).find('.totalAlunos').text()) + 1) ;
      }
    });
  } );

   function deleteImage( $item, idCondutorRota ) {
      let idAluno = $item.find('.hidden').text();
      $.post('index.php?r=ponto/create-ajax', {
        idAluno: idAluno,
        idCondutorRota: idCondutorRota,
      }).done((result) => {
                console.log(result);  
          });
        console.log($item.find('.hidden').text());
      $item.fadeOut(function() {
      
      });
    }



 
 
</script>