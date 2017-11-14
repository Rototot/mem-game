<?php

namespace common\models\meme;
use common\models\game\GameQuery;
use common\models\User;

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

    /**
     * @param bool $flag
     * @return $this
     */
    public function void(bool $flag = true)
    {
        $this->andWhere(['is_empty' => $flag]);
        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function excludeActiveInGame(User $user)
    {
        $this
            ->joinWith(['game as g' => function(GameQuery $query) use($user){
                $query->byPlayer($user->id);
            }])
            ->joinWith('gameMemeSections');

        return $this;
    }
}
