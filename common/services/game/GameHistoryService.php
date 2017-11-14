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
use function PHPSTORM_META\type;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidParamException;

class GameHistoryService extends BaseObject
{
    private $gameHistory;

    public function __construct(GameHistory $gameHistory, array $config = [])
    {
        $this->gameHistory = $gameHistory;
        parent::__construct($config);
    }

    /**
     * @param Game $game
     * @param int $type
     * @param array $attributes
     * @return GameHistory
     * @throws Exception
     */
    public function create(Game $game, int $type, array $attributes = [])
    {

        $gameHistory = $this->getGameHistory();
        $gameHistory->attributes = $attributes;
        $gameHistory->game_id = $game->id;

        //готовим модель
        $this->prepareBeforeCreate($game, $type);

        if (!$gameHistory->validate()) {
            throw new Exception('Incorrect attributes');
        }

        if (!$gameHistory->save(false)) {
            throw new Exception('Cannot save game history');
        }

        return $gameHistory;

    }

    /**
     * готовим перед созданием
     * @param Game $game
     * @param int $typeHistory
     * @return bool
     */
    protected function prepareBeforeCreate(Game $game, int $typeHistory)
    {

        $gameHistory = $this->getGameHistory();
        $prepareAttributes = $gameHistory->historyPrepareAttributesValues() ?? [];

        if(!isset($prepareAttributes[$typeHistory])){
            throw new InvalidParamException('Incorrect history type');
        }

        $gameHistory->type = $typeHistory;
        $gameHistory->attributes = $prepareAttributes[$typeHistory];
        return true;
    }

    /**
     * @return GameHistory
     */
    public function getGameHistory(): GameHistory
    {
        return $this->gameHistory;
    }

    /**
     * @param GameHistory $gameHistory
     */
    public function setGameHistory(GameHistory $gameHistory)
    {
        $this->gameHistory = $gameHistory;
    }


}