<?php

namespace common\models\meme;

/**
 * This is the ActiveQuery class for [[MemeSection]].
 *
 * @see MemeSection
 */
class MemeSectionQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MemeSection[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MemeSection|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
