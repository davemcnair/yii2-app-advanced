<?php

use yii\db\Migration;

class m161010_101010_dev_user extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%auth_assignment}}','user_id',$this->integer());
        $this->addForeignKey('assignment_user','{{%auth_assignment}}', 'user_id','{{%user}}','id','CASCADE');
        $model=new mdm\admin\models\User;
        $model->setPassword(Yii::$app->params['devPassword']);
        $this->insert('{{%user}}', [
            'id'=>1,
            'username'=>'dev',
            'email'=>Yii::$app->params['devEmail'],
            'password_hash'=> $model->password_hash,
//            'auth_key'=> $model->auth_key,
            'status'=>  \common\models\User::STATUS_ACTIVE,
        ]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('assignment_user','{{%auth_assignment}}');
        $this->alterColumn('{{%auth_assignment}}','user_id',$this->string(64));
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

