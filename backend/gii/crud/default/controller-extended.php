<?php
/**
 * Customizable controller class.
 */
echo "<?php\n";
?>

namespace <?= \yii\helpers\StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

class <?= $controllerClassName ?> extends <?= (isset($generator->controllerNs) ? '\\'.$generator->controllerNs.'\\' : '') .'base\\'.$controllerClassName."\n" ?>
{

}
