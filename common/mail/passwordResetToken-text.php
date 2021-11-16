<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->passwordResetToken]);
?>
Olá <?= $user->nome ?>,

Siga o link abaixo para redefinir a sua senha:

<?= $resetLink ?>
