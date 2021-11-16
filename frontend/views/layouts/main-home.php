<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */


frontend\assets\AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>TESC</title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-black-light sidebar-mini main-home">
<?php $this->beginBody() ?>
<div class="wrapper">

    <?= $this->render(
        'header.php',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?= $this->render(
        'left.php',
        ['directoryAsset' => $directoryAsset]
    )
    ?>

    <?= $this->render(
        'content-home.php',
        ['content' => $content, 'directoryAsset' => $directoryAsset]
    ) ?>

</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
