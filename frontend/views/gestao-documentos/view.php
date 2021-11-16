<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Condutor */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Condutors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">

                <h3><?= Html::encode($this->title) ?></h3>
            </div>
                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Tem certeza que deseja excluir este item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
            'status',
            'nome',
            'idUsuario',
            'idEmpresa',
            'idVeiculo',
            'fotoMotorista',
            'regiao',
            'lugares',
            'dataNascimento',
            'alvara',
            'inscricaoMunicipal',
            'cpf',
            'rg',
            'orgaoEmissor',
            'lat',
            'lng',
            'nit',
            'endereco',
            'bairro',
            'telefone',
            'celularMonitor',
            'telefoneWhatsapp',
            'telefone2',
            'celular',
            'celular2',
            'telefoneMonitor',
            'telefoneMonitorWhatsapp',
            'telefoneWhatsapp2',
            'celularWhatsapp',
            'celularWhatsapp2',
            'celularMonitorWhatsapp',
            'email:email',
            'cnhRegistro',
            'numeroApolice',
            'cnhValidade',
            'dataInicioContrato',
            'dataFimContrato',
            'tipoContrato',
            'valorPagoKmViagem',
            'idCNHCondutor',
            'idComprovanteEndereco',
            'idCRLV',
            'idVistoriaEstadual',
            'idVstoriaMunicipal',
            'idApoliceSeguro',
            'idContrato',
            'nomeMonitor',
            'rgMonitor',
            'cpfMonitor',
            'minKmDia',
            'maxKmDia',
            'maxViagensDia',
            'numeroResidencia',
            'cep',
            'complementoResidencia',
            'tipoLogradouro',
                    ],
                ]) ?>
        </div>
    </div>
</div>
