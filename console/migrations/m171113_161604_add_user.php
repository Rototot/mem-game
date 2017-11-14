<?php

use yii\db\Migration;

/**
 * Class m171113_161604_add_user
 */
class m171113_161604_add_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('user', [
            'username' => 'user',
            'email' => 'user@mail.local',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash('user'),
            'status' => \common\models\User::STATUS_ACTIVE,
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('user', ['email' => 'user@mail.local']);
    }

}
