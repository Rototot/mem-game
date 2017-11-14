<?php
/**
 * Created by PhpStorm.
 * User: rototot
 * Date: 14.11.17
 * Time: 1:50
 */

namespace common\services\game;


use common\models\game\Game;
use common\models\game\GameHistory;
use yii\base\BaseObject;

class GameHistoryService extends BaseObject
{
    private $gameHistory;

    public function __construct(GameHistory $gameHistory, array $config = [])
    {
        $this->gameHistory = $gameHistory;
        parent::__construct($config);
    }

    public function create(Game $game, array $attributes = [])
    {

    }


}