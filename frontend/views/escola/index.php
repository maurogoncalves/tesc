<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;

use common\models\Escola;
use common\models\EscolaHomologacao;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper; 
/* @var $this yii\web\View */   
/* @var $searchModel common\models\EscolaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
use yii\helpers\Url; 
use common\models\Usuario;
$this->title = 'Escolas';
$this->params['breadcrumbs'][] = $this->title;
function escolasAlteradas($id, $nome){
    return '<a target="_new" href="'.Url::toRoute(["escola/view",'id'=>$id]).'">'.$nome.'</a>';
}
?>

<div class="row">
       
    <div class="col-md-12">
        <div class="box box-solid">

 
          <div class="box-header with-border">
              <?php 
              $escolas = EscolaHomologacao::find()->all();
              if(Usuario::permissoes([USUARIO::PERFIL_DRE, USUARIO::PERFIL_SUPER_ADMIN]) && $escolas):?>
          <div class="alert alert-light" role="alert">
        Escolas com solicitação de alteração:
        <?php 
            $str = '';
            foreach($escolas as $escola)
            $str .= ' '.escolasAlteradas($escola->idEscola,$escola->nome).', ';
            echo substr($str, 0, -2).'.';
        ?>
        
        </div>
        <?php endif; ?>
            <?= Escola::permissaoCriar() ? Html::a('Nova Escola', ['create'], ['class' => 'btn btn-success pull-right']) : ''; ?>
        </div>

        <div class="box-body">

               <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'panel' => [
                    'heading'=>false,
                    'type'=>false,
                    'showFooter'=>false
                ],
                'summary' => "Exibindo <b>{begin}</b>-<b>{end}</b> de <b>{totalCount}</b> itens.",
                'toolbar' => \Yii::$app->showEntriesToolbar->create(),
                'columns' => [
                    // ['class' => 'yii\grid\SerialColumn'],
                    // 'id',
                    [
                        'attribute' => 'unidade',
                        'value' => function($model){
                            return $model->unidade ? Escola::ARRAY_UNIDADE[$model->unidade] : '-';

                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' =>  Escola::ARRAY_UNIDADE, 
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => [
                            'placeholder' => '-',
                            
                        ]
                    ],
                    [
                        'attribute' => 'tipo',
                        'value' => function($model){
                            return $model->tipo ? Escola::ARRAY_TIPO[$model->tipo] : '-';

                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' =>  Escola::ARRAY_TIPO, 
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => [
                            'placeholder' => '-',
                            
                        ]
                    ],
                    [
                        'attribute' => 'ensino',
                        'filter' => Escola::ARRAY_ENSINO,
                        'format' => 'raw',
                        'value' => function ($model) {
                            $lista = '<ul>';
                            foreach ($model->atendimento as $i => $atendimento)
                            {                
                                $lista .= '<li>'.Escola::ARRAY_ENSINO[$atendimento->idAtendimento].'</li>';
                            }
                            $lista .= '</ul>';

                            return $lista;
                        }
                    ],
                    'nome',
                    'endereco',
                    [
                        'attribute' => 'regiao',
                        'filter' => Escola::ARRAY_REGIAO,
                        'value' => function ($model) {
                            return Escola::ARRAY_REGIAO[$model->regiao];
                        }
                    ],
                    'telefone',
                    'email:email',
                    [
                        'attribute' => 'secretarios',
                        'label' => 'Secretário(s)',
                        // 'filter' => Escola::ARRAY_ENSINO,
                        'format' => 'raw',
                        'value' => function ($model) {
                            //Secretários
                            $lista = '';
                            foreach ($model->secretarios as $i => $secretario)
                            {
                                if ($i > 0)
                                    $lista .= '<hr>';

                                $lista .= $secretario->usuario->nome.'<br>';
                                $lista .= '<b>CPF: </b>'.\Yii::$app->formatter->asCpf($secretario->usuario->cpf).'<br>';
                                $lista .= '<b>RG: </b>'.\Yii::$app->formatter->asCpf($secretario->usuario->rg).'<br>';
                                $lista .= '<b>E-mail: </b>'.$secretario->usuario->email;
                            }
                            return $lista;
                        }
                    ],
                    [
                        'attribute' => 'diretores',
                        'label' => 'Diretor(es)',
                        // 'filter' => Escola::ARRAY_ENSINO,
                        'format' => 'raw',
                        'value' => function ($model) {
                            //Secretários
                            $lista = '';
                            foreach ($model->diretores as $i => $diretor)
                            {
                                if ($i > 0)
                                    $lista .= '<hr>';

                                $lista .= $diretor->usuario->nome.'<br>';
                                $lista .= '<b>CPF: </b>'.\Yii::$app->formatter->asCpf($diretor->usuario->cpf).'<br>';
                                $lista .= '<b>RG: </b>'.\Yii::$app->formatter->asCpf($diretor->usuario->rg).'<br>';
                                $lista .= '<b>E-mail: </b>'.$diretor->usuario->email;
                            }
                            return $lista;
                        }
                    ],
                    'codigoCie',
                    [
                        'contentOptions' => ['style' => 'min-width:80px;'],  //Largura coluna
                        'class' => 'yii\grid\ActionColumn',
                        'template' => Escola::permissaoActions(), 
                        'buttons' => [
                            'delete' => function($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url,
                                [                                    
                                'data' => [
                                'confirm' => 'Tem certeza que deseja excluir este item?',
                                'method' => 'post',
                                'pjax' => 0,                                            
                                'ok' => Yii::t('yii', 'Confirm'),
                                'cancel' => Yii::t('yii', 'Cancel'),
                                ],
                                ]);
                            }
                        ]
                    ]
                    ],
                    'exportConfig' => [
                        GridView::HTML => true,
                        GridView::CSV => true,
                        GridView::TEXT => true,
                        GridView::EXCEL => true
                    ]
                ]); ?>
              
        </div>
    </div>
</div>
</div>

<script type="text/javascript">

    function gerenciadorPdf(){
        let get = window.location.search;

        get = get.replace('escola%2Findex', 'escola/report');
        
        window.open(get)
    }
    
    setInterval(() => {
        itens = $("#w2 li");
        item = $("#w2 li")[itens.length-1];
        if($(item).prop('title') != 'Portable Document Format') {
            $("#w2").append('<li id="meuPdf" title="Portable Document Format"><a onclick="gerenciadorPdf()" tabindex="-1"><i class="text-danger glyphicon glyphicon-floppy-disk"></i> PDF</a></li>')
        }
    }, 500);
</script>