<?php

namespace common\models\game;

/**
 * This is the ActiveQuery class for [[GameMemeSection]].
 *
 * @see GameMemeSection
 */
class GameMemeSectionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return GameMemeSection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GameMemeSection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
