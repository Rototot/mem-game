<?php

use yii\db\Migration;

/**
 * Class m171111_170150_crate_tbl_meme
 */
class m171111_170150_crate_tbl_meme extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->createTable('{{%meme}}', [
            'id' => $this->primaryKey(),
            'id_on_site' => $this->integer(),
            'title' => $this->string(255)->notNull(),
            'url' => $this->string(255)->notNull(),
            'about' => $this->text(),
            'image' => $this->string(255)->notNull(),
            'origin_year' => $this->integer(),
            'tags' => 'JSONB DEFAULT \'{}\' NOT NULL',
            'site_status' => $this->string(20)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->createIndex('i_id_on_site', '{{%meme}}', 'id_on_site');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
       $this->dropTable('{{%meme}}');
    }
}
