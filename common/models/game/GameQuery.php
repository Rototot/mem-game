<?php

namespace common\models\game;

/**
 * This is the ActiveQuery class for [[Game]].
 *
 * @see Game
 */
class GameQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => Game::STATUS_ACTIVE]);
    }

    /**
     * @return $this
     */
    public function finished()
    {
        return $this->andWhere(['status' => Game::STATUS_FINISHED]);
    }

    /**
     * @param string|int|array|null $playerID
     * @return $this
     */
    public function byPlayer($playerID)
    {
        return $this->andWhere(['player_id' => $playerID]);
    }

    /**
     * @inheritdoc
     * @return Game[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Game|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
