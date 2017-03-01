<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/*
 * @var yii\web\View $this
 * @var schmunk42\giiant\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
/** @var $generator \schmunk42\giiant\generators\crud\Generator */

## TODO: move to generator (?); cleanup
$model = new $generator->modelClass();
$model->setScenario('crud');
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $model->setScenario('default');
    $safeAttributes = $model->safeAttributes();
}
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}

$modelName = Inflector::camel2words(StringHelper::basename($model::className()));
$className = $model::className();
$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\widgets\LinkPager;

?>
<?php
        $showAllRecords = false;

        if ($relation->via !== null) {
            $pivotName = Inflector::pluralize($generator->getModelByTableName($relation->via->from[0]));
            $pivotRelation = $model->{'get'.$pivotName}();
            $pivotPk = key($pivotRelation->link);

            $addButton = "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-link\"></span> ' . ".$generator->generateString('Attach')." . ' ".
                Inflector::singularize(Inflector::camel2words($name)).
                "', ['".$generator->createRelationRoute($pivotRelation, 'create')."', '".
                Inflector::singularize($pivotName)."'=>['".key(
                    $pivotRelation->link
                )."'=>\$model->{$model->primaryKey()[0]}]],
            ['class'=>'btn btn-info btn-xs']
        ) ?>\n";
        } else {
            $addButton = '';
        }

        // relation list, add, create buttons
        echo "<div style='position: relative'>\n<div style='position:absolute; right: 0px; top: 0px;'>\n";

        echo "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-list\"></span> ' . ".$generator->generateString('List All')." . ' ".
            Inflector::camel2words($name)."',
            ['".$generator->createRelationRoute($relation, 'index')."'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>\n";
        // TODO: support multiple PKs
        echo "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-plus\"></span> ' . ".$generator->generateString('New')." . ' ".
            Inflector::singularize(Inflector::camel2words($name))."',
            ['".$generator->createRelationRoute($relation, 'create')."', '".
            Inflector::id2camel($generator->generateRelationTo($relation), '-', true)."' => ['".key($relation->link)."' => \$model->".$model->primaryKey()[0]."]],
            ['class'=>'btn btn-success btn-xs']
        ); ?>\n";
        echo $addButton;

        echo "</div>\n</div>\n"; #<div class='clearfix'></div>\n";
        // render pivot grid
        if ($relation->via !== null) {
            $pjaxId = "pjax-{$pivotName}";
            $gridRelation = $pivotRelation;
            $gridName = $pivotName;
        } else {
            $pjaxId = "pjax-{$name}";
            $gridRelation = $relation;
            $gridName = $name;
        }

        $output = $generator->relationGrid($gridName, $gridRelation, $showAllRecords);

        // render relation grid
        if (!empty($output)):
            echo "<?php Pjax::begin(['id'=>'pjax-{$name}', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-{$name} ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert(\"yo\")}']]) ?>\n";
            echo "<?=\n ".$output."\n?>\n";
            echo "<?php Pjax::end() ?>\n";
        endif;
