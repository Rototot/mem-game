<?php

use yii\db\Migration;

/**
 * Class m171112_041013_create_tbl_meme_section
 */
class m171112_041013_create_tbl_meme_section extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%meme_section}}', [
            'id' => $this->primaryKey(),
            'meme_id' => $this->integer()->notNull(),
            'is_empty' => $this->boolean()->defaultValue(false),
            'width' => $this->integer()->notNull()->defaultValue(16),
            'height' => $this->integer()->notNull()->defaultValue(16),
            'x' => $this->integer()->notNull(),
            'y' => $this->integer()->notNull(),
            'block_x' => $this->integer()->notNull()->comment('позиция блока X'),
            'block_y' => $this->integer()->notNull()->comment('позиция блока Y'),
            'filePath' => $this->string(255)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);


        $this->addForeignKey('FK_meme', 'meme_section', 'meme_id', 'meme', 'id', 'RESTRICT', 'CASCADE');

        //todo index
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%meme_section}}');
    }
}
