<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/*
 * @var yii\web\View $this
 * @var schmunk42\giiant\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass();
$model->setScenario('crud');

$modelName = Inflector::camel2words(Inflector::pluralize(StringHelper::basename($model::className())));

$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    /** @var \yii\db\ActiveRecord $model */
    $model = new $generator->modelClass();
    $safeAttributes = $model->safeAttributes();
    if (empty($safeAttributes)) {
        $safeAttributes = $model->getTableSchema()->columnNames;
    }
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\EkoHelper;
use <?= $generator->indexWidgetType === 'grid' ? $generator->indexGridClass : 'yii\\widgets\\ListView' ?>;

/**
* @var yii\web\View $this
* @var yii\data\ActiveDataProvider $dataProvider
*/

$this->title = Yii::t(<?= "'{$generator->modelMessageCategory}', '{$modelName}'" ?>);
$this->params['breadcrumbs'][] = $this->title;

<?php
echo '?>';
?>

<div class="giiant-crud <?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-index">
    
    <?php if ($generator->indexWidgetType === 'grid'): ?>

    <h1>
        <?= "<?= Yii::t('{$generator->modelMessageCategory}', '{$modelName}') ?>\n" ?>
        <small>
            List
        </small>
    </h1>
    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?= '<?= ' ?>Html::a('<span class="glyphicon glyphicon-plus"></span> '
                . <?= $generator->generateString('New') ?>,
                ['create'],
                ['class' => 'btn btn-success']) ?>
        </div>

        <div class="pull-right">
            <?php
            $items = '';
            $model = new $generator->modelClass();
            ?>
            <?php foreach ($generator->getModelRelations($model) as $relation): ?>
                <?php
                // relation dropdown links
                $iconType = ($relation->multiple) ? 'arrow-right' : 'arrow-left';
                if ($generator->isPivotRelation($relation)) {
                    $iconType = 'random text-muted';
                }
                $controller = $generator->pathPrefix.Inflector::camel2id(
                        StringHelper::basename($relation->modelClass),
                        '-',
                        true
                    );
                $route = $generator->createRelationRoute($relation, 'index');
                $label = Inflector::titleize(StringHelper::basename($relation->modelClass), '-', true);
                $items .= <<<PHP
            [
                'url' => ['{$route}'],
                'label' => '<i class="glyphicon glyphicon-{$iconType}"></i> '
                    . Yii::t('$generator->modelMessageCategory', '$label'),
             ],

 PHP;

            ],

PHP;
                ?>
            <?php endforeach; ?>
        </div>
    </div>

    <hr />

    <div class="table-responsive">
        <?= '<?= ' ?>GridView::widget([
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => yii\widgets\LinkPager::className(),
                'firstPageLabel' => <?= $generator->generateString('First') ?>,
                'lastPageLabel' => <?= $generator->generateString('Last').",\n" ?>
            ],
        <?php if ($generator->searchModelClass !== ''): ?>
            'filterModel' => $searchModel,
        <?php endif; ?>
    'tableOptions' => ['class' => 'table table-striped table-bordered table-hover'],
            'headerRowOptions' => ['class'=>'x'],
            'columns' => [
<?php
            $count = 0;
            foreach ($safeAttributes as $attribute) {
                $format = trim($generator->columnFormat($attribute, $model));
                if ($format == false) {
                    continue;
                }
                if (++$count < $generator->gridMaxColumns) {
                    echo "\t\t\t\t" . str_replace("\n", "\n\t\t\t", $format) . ",\n";
                } else {
                    echo "\t\t\t\t/*" . str_replace("\n", "\n\t\t\t", $format) . ",*/\n";
                }
            }

            $actionButtonColumn = <<<PHP
                [
                    'class' => '{$generator->actionButtonClass}',
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function (\$url, \$model, \$key) {
                            \$options = [
                                'title' => Yii::t('yii', 'View'),
                                'aria-label' => Yii::t('yii', 'View'),
                            ];
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                                \$url, \$options);
                        },
                        'delete' => function (\$url, \$model) {                            
    						return Html::a('<span class="glyphicon glyphicon-trash"></span>', \$url, [
								'class' => 'text-danger',
								'title'         => 'Delete',
								'data-confirm'  => 'Are you sure you want to delete '.\$model.'?',
								'data-method' => 'post',
								'data-pjax' => '0',
							]);
                        },
                    ],
                    'contentOptions' => ['nowrap'=>'nowrap']
                ],
PHP;

        // action buttons first
        echo $actionButtonColumn;
        echo "\n";
        ?>
            ],
        ]); ?>
    </div>
</div>


<?php else: ?>

    <?= '<?= ' ?> ListView::widget([
    'dataProvider' => $dataProvider,
    'itemOptions' => ['class' => 'item'],
    'itemView' => function ($model, $key, $index, $widget) {
    return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
    },
    ]); ?>

<?php endif; ?>

