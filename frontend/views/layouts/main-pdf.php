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
<html lang="<?= Yii::$app->language ?>">
	<head>
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <?= Html::csrfMetaTags() ?>
	    <?php $this->head() ?>
	</head>
	<body class="hold-transition skin-black-light">
		<?php $this->beginBody() ?>
		<div class="pdf-page">
		    <?= $this->render(
		        'content-pdf.php',
		        ['content' => $content, 'directoryAsset' => $directoryAsset]
		    ) ?>

		</div>
		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>

