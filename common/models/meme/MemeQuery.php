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
     * @param $id
     * @return $this
     */
    public function byIdOnSite($id)
    {
        return $this->andWhere(['id_on_site' => $id]);
    }

    /**
     * @param $url
     * @return $this
     */
    public function byUrl($url)
    {
        return $this->andWhere(['url' => $url]);
    }

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
