<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'common\models\User';
    public $dataFile='@app/tests/_data/user.php';

    const AUTO_INC=1;

    protected function resetTable()
    {
        $table = $this->getTableSchema();
        $this->db->createCommand()->delete($table->fullName,
                'id>'.self::AUTO_INC)->execute();
        if ($table->sequenceName !== null) {
            $this->db->createCommand()->resetSequence($table->fullName, self::AUTO_INC+1)->execute();
        }
    }

}
