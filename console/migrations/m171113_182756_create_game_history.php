<?php

use yii\db\Migration;

/**
 * Class m171113_182756_create_game_history
 */
class m171113_182756_create_game_history extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->createTable('game_history', [
            'id' => $this->primaryKey(),
            'title_label' => $this->string()->notNull()->comment('Метка перевода'),
            'type' => $this->integer()->notNull()->comment('Тип элемента истории'),
            'game_id' => $this->integer()->notNull()->comment('игра'),
            'score_cost' => $this->integer()->notNull()->defaultValue(0)->comment('цена элемента истории'),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey('FK_game_history_game', 'game_history', 'game_id',
            'game', 'id', 'RESTRICT', "CASCADE");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('game_history');
    }
}
