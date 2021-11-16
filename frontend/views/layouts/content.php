<?php

use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;

?>

<div class="content-wrapper">
    <section class="content-header">
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
            <h1>
                <?php
                    if ($this->title !== null) {
                        // echo \yii\helpers\Html::encode($this->title);
                    } else {
                        echo '';
                    } ?>
            </h1>
        <?php } ?>

        <?=
            Breadcrumbs::widget(
                [
                    'homeLink' => [
                        'label' => 'Página inicial',
                        'url' => Yii::$app->getHomeUrl()
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]
            )

        ?>
    </section>

    <section class="content">
        <?= Alert::widget(); ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Versão</b> 0.9
    </div>
    Desenvolvido por <strong><a href="http://devell.com.br">DEVELL</a></strong>
</footer>