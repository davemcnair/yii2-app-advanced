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
use yii\helpers\Url;
use common\components\EkoHelper;
use kartik\grid\GridView;
use kartik\detail\DetailView;
use yii\widgets\Pjax;
use dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/
$copyParams = $model->attributes;

$this->title = Yii::t('<?= $generator->modelMessageCategory ?>', '<?= $modelName ?>');
$this->params['breadcrumbs'][] = ['label' => Yii::t('<?= $generator->modelMessageCategory ?>', '<?= Inflector::pluralize($modelName) ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('View') ?>;
?>
<div class="giiant-crud <?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-view">

    <h1>
        <?= "<?= Yii::t('{$generator->modelMessageCategory}', '{$modelName}') ?>\n" ?>
        <small>
            <?= '<?= $model->'.$generator->getModelNameAttribute($generator->modelClass)." ?>\n" ?>
        </small>
    </h1>


    <div class="clearfix crud-navigation">

        <div class='pull-left'>
            <?= '<?= ' ?>Html::a('<span class="glyphicon glyphicon-pencil"></span> '
                . <?= $generator->generateString('Edit') ?>,
                [ 'update', <?= $urlParams ?>],
                ['class' => 'btn btn-info'])
            ?>
            <?= '<?= ' ?>Html::a('<span class="glyphicon glyphicon-plus"></span> '
                . <?= $generator->generateString('New') ?>,
                ['create'],
                ['class' => 'btn btn-success'])
            ?>
        </div>

        <div class="pull-right">
            
        </div>

    </div>

    <hr />

    <?php
    echo "<?php \$this->beginBlock('{$generator->modelClass}'); ?>\n";
    ?>

    <?= $generator->partialView('detail_prepend', $model); ?>

    <?= '<?= ' ?>DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
    foreach ($safeAttributes as $attribute) {
        $format = $generator->attributeFormat($attribute);
        if (!$format) {
            continue;
        } else {
            echo "\t".$format.",\n";
        }
    }
    ?>
        ],
    ]); ?>

    <?= $generator->partialView('detail_append', $model); ?>

    <hr/>

    <?= '<?= ' ?>Html::a('<span class="glyphicon glyphicon-trash"></span> '
        . <?= $generator->generateString('Delete') ?>,
        ['delete', <?= $urlParams ?>],
        [
            'class' => 'btn btn-danger',
            'data-confirm' => '' . <?= $generator->generateString('Are you sure to delete this item?') ?> . '',
            'data-method' => 'post',
        ]);
    ?>
    <?= "<?php \$this->endBlock(); ?>\n\n"; ?>

    <?php
    // get relation info $ prepare add button
    $model = new $generator->modelClass();

    $items = <<<EOS
                [
                    'label'   => '<b class=""># '.\$model->{$model->primaryKey()[0]}.'</b>',
                    'content' => \$this->blocks['{$generator->modelClass}'],
                    'active'  => true,
                ],\n
EOS;

    echo "<?php\n\t    \$tabBlocks=[\n";
    foreach ($generator->getModelRelations($generator->modelClass, ['has_many']) as $name => $relation) {
        echo "            '".lcfirst($name)."',\n";
    }
    ?>
        ];
        foreach ($tabBlocks as $blockName) {
            $this->beginBlock($blockName);
            echo $this->render('rel/'.lcfirst($blockName),[
                'model'=>$model,
            ]);
            $this->endBlock();
        }
    <?php echo "?>\n";

    foreach ($generator->getModelRelations($generator->modelClass, ['has_many']) as $name => $relation) {
        /**
        echo "\n<?php \$this->beginBlock('$name'); ?>\n";

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

        echo "<?php \$this->endBlock() ?>\n\n";
*/
        // build tab items
        $label = Inflector::camel2words($name);
        $lcname=lcfirst($name);
        $items .= <<<EOS
                [
                    'content' => \$this->blocks['$lcname'],
                    'label'   => '<small>$label
                            <span class="badge badge-default">'
                            .\$model->get{$name}()->count().'
                            </span>
                        </small>',
                    'active'  => false,
                ],\n
EOS;
    }
    ?>

    <?=
    "<?= Tabs::widget([
        'id' => 'relation-tabs',
        'encodeLabels' => false,
        'items' => [\n $items
        ]
    ]);
    ?>";
    ?>

</div>
