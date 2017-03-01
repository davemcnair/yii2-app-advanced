<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use \dmstr\bootstrap\Tabs;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= '<?php ' ?>$form = ActiveForm::begin([
    'id' => '<?= $model->formName() ?>',
    'layout' => '<?= $generator->formLayout ?>',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-error'
    ]
    );
    ?>

    <div class="">
        <?php echo "<?php \$this->beginBlock('main'); ?>\n"; ?>

        <p>
            <?php
            foreach ($safeAttributes as $attribute) {
                echo "\n\n<!-- attribute $attribute -->";
                $prepend = $generator->prependActiveField($attribute, $model);
                $field = $generator->activeField($attribute, $model);
                $append = $generator->appendActiveField($attribute, $model);

                if ($prepend) {
                    echo "\n\t\t\t".$prepend;
                }
                               if ($field) {
                    echo "\n\t\t\t<?= ".$field.' ?>';
                }
                if ($append) {
                    echo "\n\t\t\t".$append;
                }
            }
            ?>

        </p>
        <?php echo '<?php $this->endBlock(); ?>'; ?>

        <?php
        $label = substr(strrchr($model::className(), '\\'), 1);

        $items = <<<EOS
[
    'label'   => Yii::t('$generator->modelMessageCategory', '$label'),
    'content' => \$this->blocks['main'],
    'active'  => true,
],
EOS;
        ?>

        <?=
        "<?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [
                        $items
                    ]
                 ]
    );
    ?>";
        ?>

        <hr/>

        <?= '<?php ' ?>echo $form->errorSummary($model); ?>

        <?= '<?= ' ?>Html::submitButton(
        '<span class="glyphicon glyphicon-check"></span> ' .
        ($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Save') ?>),
        [
        'id' => 'save-' . $model->formName(),
        'class' => 'btn btn-success'
        ]
        );
        ?>

        <?= '<?php ' ?>ActiveForm::end(); ?>

    </div>

</div>

