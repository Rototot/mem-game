<?php

use yii\db\Migration;

/**
 * Class m171112_043120_create_tbl_game
 */
class m171112_043120_create_tbl_game extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%game}}', [
            'id' => $this->primaryKey(),
            'meme_id' => $this->integer()->notNull(),
            'player_id' => $this->integer()->notNull(),
            'player_is_surrender' => $this->boolean()->notNull()->defaultValue(false)->comment('Игрок сдался?'),
            'player_is_win' => $this->boolean()->notNull()->defaultValue(false)->comment('Игрок выйграл?'),
            'score' => $this->integer()->notNull()->defaultValue(0)->comment('очки'),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addForeignKey('FK_game_player', 'game', 'player_id',
            'user', 'id',
            'RESTRICT', 'CASCADE');

        $this->addForeignKey('FK_game_meme', 'game', 'meme_id',
            'meme', 'id',
            'RESTRICT', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%game}}');
    }

}
