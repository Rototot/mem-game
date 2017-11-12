<?php

use yii\db\Migration;

/**
 * Class m171112_043233_create_tbl_game_meme_section
 */
class m171112_043233_create_tbl_game_meme_section extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%game_meme_section}}', [
            'id' => $this->primaryKey(),
            'game_id' => $this->integer()->notNull(),
            'meme_section_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('FK_game', 'game_meme_section', 'game_id', 'game', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('FK_mem', 'game_meme_section', 'meme_section_id', 'meme_section', 'id', 'RESTRICT', 'CASCADE');


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%game_meme_section}}');
    }

}
