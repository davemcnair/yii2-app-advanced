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
use common\components\EkoHelper;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\widgets\LinkPager;

?>
<?php
        $showAllRecords = false;

        if ($relation->via !== null) {
            $via=$relation->via;
            $pivotTable=is_array($via)?$via[0]:$via->from[0];
            $pivotName = Inflector::pluralize($generator->getModelByTableName($pivotTable));
            $pivotRelation = $model->{'get'.$pivotName}();
            $pivotPk = key($pivotRelation->link);

            $addButton = "  <?php
                echo Html::a(
            '<span class=\"glyphicon glyphicon-link\"></span> ' . "
                    .$generator->generateString('Attach')." . ' ".
                Inflector::singularize(Inflector::camel2words($name)).
                "', ['".$generator->createRelationRoute($pivotRelation, 'create')."', '"
                .key($pivotRelation->link)."'=>\$model->{$model->primaryKey()[0]}
                    ],
            ['class'=>'btn btn-info btn-xs']
        )
        ?>\n";
        } else {
            $addButton = '';
        }

        // relation list, add, create buttons
        echo "<div class='text-right'>\n";

/**        echo "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-list\"></span> ' . ".$generator->generateString('List All')." . ' ".
            Inflector::camel2words($name)."',
            ['".$generator->createRelationRoute($relation, 'index')."'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>\n";
 */
        // TODO: support multiple PKs
        echo "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-plus\"></span> ' . "
                .$generator->generateString('Add')." . ' "
            .Inflector::singularize(Inflector::camel2words($name))."',
            ['".$generator->createRelationRoute($relation, 'create')."', '"
            .($relation->link?Inflector::id2camel($generator->generateRelationTo($relation), '-', true)."' => ['".key($relation->link)."' => \$model->".$model->primaryKey()[0]."]],":"")
            ."
            ['class'=>'btn btn-success btn-xs']
        ); ?>\n";
        echo $addButton;

        echo "</div>\n"; #<div class='clearfix'></div>\n";
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
            echo '    <div class="table-responsive">'.PHP_EOL;    
            echo '        <?='.$output."?>\n";
            echo '    </div>'.PHP_EOL;
        endif;
