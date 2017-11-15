<?php

namespace common\models\game;

/**
 * This is the ActiveQuery class for [[GameHistory]].
 *
 * @see GameHistory
 */
class GameHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/


    /**
     * @param $type
     * @return $this
     */
    public function byType($type)
    {
        $this->andWhere(['type' => $type]);

        return $this;
    }

    /**
     * @param int|array $gameID
     * @return $this
     */
    public function byGame($gameID)
    {
        $this->joinWith('game g');
        $this->andWhere(['g.id' => $gameID]);

        return $this;
    }

    /**
     * @inheritdoc
     * @return GameHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return GameHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
