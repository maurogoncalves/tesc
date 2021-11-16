<?php

use common\models\Aviso;
use yii\helpers\Url;

$this->title = 'Avisos';
// $this->params['breadcrumbs'][] = ['label' => 'Alunos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    h2 {
        font-weight: 300;
    }
    .click {
        cursor:pointer;
    }
    a {
        font-weight: normal;
    }
</style>

<?php foreach ($avisos as $aviso) { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <div class="col-md-8">
                        <div class="col-md-9">
                        <h3>
                        <?php if($aviso->fixado == Aviso::AVISO_FIXADO) { ?>
                                <span class="label label-primary">FIXADO</span>
                            <?php } ?>
                        </h3>
                        <h2 style="overflow-wrap:break-word;">
                            <?= $aviso->titulo ?>
                        </h2>
                        </div>
                        <div class="col-md-3">
                            <h4 class="pull-right"><?= ($aviso->data && $aviso->data != '0000-00-00') ? date("d/m/Y", strtotime($aviso->data)) : '-'; ?></h4>
                        </div>
                        <div class="col-md-12 box-body">
                            <p> <?= $aviso->mensagem ?></p>

                            <?php if($aviso->link != ''): ?>
                                <iframe src="<?= strtolower($aviso->link); ?>" width="100%" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>                            
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4" >

                        <div class="panel-group">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <span>Arquivos</span>
                                    </h4>
                                </div>
                                <ul class="list-group">
                                    <div class="panel-group">
                                        <div class="panel panel-white">
                                            <div class="panel-heading click" data-toggle="collapse" href="#docLeg-<?= $aviso->id ?>">
                                                <h4 class="panel-title">
                                                    <a><i class="far fa-folder "></i> Legislação <i class="fas fa-chevron-right pull-right"></i></a>
                                                </h4>
                                            </div>
                                            <div id="docLeg-<?= $aviso->id ?>" class="panel-collapse collapse">
                                                <ul class="list-group">
                                                <?php  
                                                    if ($aviso->docLegislacao) {
                                                        foreach ($aviso->docLegislacao as $documento) {
                                                            $tipo = substr($documento->arquivo, -3); 
                                                            echo '<li class="list-group-item"><a target="_new" href="'.$documento->arquivo.'">'.$documento->nome.' <i class="fas fa-cloud-download-alt pull-right"></i></a></li>';
                                                        }
                                                    } else { 
                                                        echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Nenhum anexo.</div></div>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-group">
                                        <div class="panel panel-white">
                                            <div class="panel-heading click" data-toggle="collapse" href="#docFre-<?= $aviso->id ?>">
                                                <h4 class="panel-title">
                                                    <a><i class="far fa-folder "></i> Frete <i class="fas fa-chevron-right pull-right"></i></a>
                                                </h4>
                                            </div>
                                            <div id="docFre-<?= $aviso->id ?>" class="panel-collapse collapse">
                                                <ul class="list-group">
                                                <?php  
                                                    if ($aviso->docFrete) {
                                                        foreach ($aviso->docFrete as $documento) {
                                                            $tipo = substr($documento->arquivo, -3); 
                                                            echo '<li class="list-group-item"><a target="_new" href="'.$documento->arquivo.'">'.$documento->nome.' <i class="fas fa-cloud-download-alt pull-right"></i></a></li>';
                                                        }
                                                    } else { 
                                                        echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Nenhum anexo.</div></div>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-group">
                                        <div class="panel panel-white">
                                            <div class="panel-heading click" data-toggle="collapse" href="#docPass-<?= $aviso->id ?>">
                                                <h4 class="panel-title">
                                                    <a><i class="far fa-folder "></i> Passe <i class="fas fa-chevron-right pull-right"></i></a>
                                                </h4>
                                            </div>
                                            <div id="docPass-<?= $aviso->id ?>" class="panel-collapse collapse">
                                                <ul class="list-group">
                                                <?php  
                                                    if ($aviso->docPasse) {
                                                        foreach ($aviso->docPasse as $documento) {
                                                            $tipo = substr($documento->arquivo, -3); 
                                                            echo '<li class="list-group-item"><a target="_new" href="'.$documento->arquivo.'">'.$documento->nome.' <i class="fas fa-cloud-download-alt pull-right"></i></a></li>';
                                                        }
                                                    } else { 
                                                        echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Nenhum anexo.</div></div>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-group">
                                        <div class="panel panel-white">
                                            <div class="panel-heading click" data-toggle="collapse" href="#docOrientacoes-<?= $aviso->id ?>">
                                                <h4 class="panel-title">
                                                    <a><i class="far fa-folder "></i> Orientações Setor <i class="fas fa-chevron-right pull-right"></i></a>
                                                </h4>
                                            </div>
                                            <div id="docOrientacoes-<?= $aviso->id ?>" class="panel-collapse collapse">
                                                <ul class="list-group">
                                                <?php  
                                                    if ($aviso->docOrientacoesSetor) {
                                                        foreach ($aviso->docOrientacoesSetor as $documento) {
                                                            $tipo = substr($documento->arquivo, -3); 
                                                            echo '<li class="list-group-item"><a target="_new" href="'.$documento->arquivo.'">'.$documento->nome.' <i class="fas fa-cloud-download-alt pull-right"></i></a></li>';
                                                        }
                                                    } else { 
                                                        echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Nenhum anexo.</div></div>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-group">
                                        <div class="panel panel-white">
                                            <div class="panel-heading click" data-toggle="collapse" href="#docAtualizacoes-<?= $aviso->id ?>">
                                                <h4 class="panel-title">
                                                    <a><i class="far fa-folder "></i> Atualizações Sistema <i class="fas fa-chevron-right pull-right"></i></a>
                                                </h4>
                                            </div>
                                            <div id="docAtualizacoes-<?= $aviso->id ?>" class="panel-collapse collapse">
                                                <ul class="list-group">
                                                <?php  
                                                    if ($aviso->docAtualizacaoSistema) {
                                                        foreach ($aviso->docAtualizacaoSistema as $documento) {
                                                            $tipo = substr($documento->arquivo, -3); 
                                                            echo '<li class="list-group-item"><a target="_new" href="'.$documento->arquivo.'">'.$documento->nome.' <i class="fas fa-cloud-download-alt pull-right"></i></a></li>';
                                                        }
                                                    } else { 
                                                        echo '<div class="box-header with-border"><div class="alert alert-danger" role="alert">Nenhum anexo.</div></div>';
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </ul> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
    if(window.location.href.indexOf("&1%5Balerta%5D=1") > -1){
      Swal.fire({
        title: '😊 Atenção usuário(a)!',
        text: "O Sistema possui uma Base de Conhecimento e Orientações disponíveis aos usuários. Favor verificar o Aviso Fixo inserido em 02/09/2020 e seus respectivos anexos.",
        icon: 'warning',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ok, vou verificar',
    }).then((result) => {
        console.log('OK VOU VERIFICAR')
    });
}
</script>
