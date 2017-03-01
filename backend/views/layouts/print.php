<?php

use backend\assets\PrintAsset;
use yii\helpers\Html;

PrintAsset::register($this);
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

    <body>
<?= $content ?>
<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
