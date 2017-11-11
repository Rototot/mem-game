<?php

namespace common\models\meme;

/**
 * This is the ActiveQuery class for [[Meme]].
 *
 * @see Meme
 */
class MemeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Meme[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Meme|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
