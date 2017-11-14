<?php

namespace common\services\game;


use common\helpers\ApplicationHelper;
use common\models\game\Game;
use common\models\game\GameMemeSection;
use common\models\meme\Meme;
use common\models\meme\MemeSection;
use common\models\meme\MemeSectionQuery;
use common\models\User;
use common\services\meme\MemeService;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\UserException;
use yii\caching\TagDependency;
use yii\db\Transaction;

class GameMemeSectionService extends BaseObject
{

    private $gameMemSection;

    public function __construct(GameMemeSection $gameMemeSection, array $config = [])
    {
        $this->gameMemSection = $gameMemeSection;
        parent::__construct($config);
    }


    /**
     * @param Game $game
     * @param MemeSection $memeSection
     * @param array $attributes
     * @return GameMemeSection $gameMemeSection
     * @throws Exception
     */
    public function create(Game $game, MemeSection $memeSection, array $attributes = [])
    {
        $gameMemeSection = $this->getGameMemeSection();
        $gameMemeSection->attributes = $attributes;
        $gameMemeSection->meme_section_id = $memeSection->id;
        $gameMemeSection->game_id = $game->id;

        if(!$gameMemeSection->isNewRecord){
            throw new Exception('Update not supported');
        }

        if (!$gameMemeSection->validate()) {
            throw new Exception('Incorrect attributes');
        }

        if (!$gameMemeSection->save(false)) {
            throw new Exception('Cannot save mem');
        }

        return $gameMemeSection;
    }

    /**
     * @param array $attributes
     * @param Game $game
     * @param MemeSection $memeSection
     * @return GameMemeSection
     * @throws Exception
     */
    public function update(array $attributes = [], Game $game, MemeSection $memeSection)
    {
        $gameMemeSection = $this->getGameMemeSection();
        $gameMemeSection->attributes = $attributes;
        $gameMemeSection->meme_section_id = $memeSection->id;
        $gameMemeSection->game_id = $game->id;

        if(!$gameMemeSection->isNewRecord){
            throw new Exception('Update not supported');
        }

        if (!$gameMemeSection->validate()) {
            throw new Exception('Incorrect attributes');
        }

        if (!$gameMemeSection->save(false)) {
            throw new Exception('Cannot save mem');
        }

        return $gameMemeSection;
    }

    /**
     * @return GameMemeSection
     */
    public function getGameMemeSection(): GameMemeSection
    {
        return $this->gameMemSection;
    }

    /**
     * @param GameMemeSection $game
     */
    public function setGameMemeSection(GameMemeSection $game)
    {
        $this->gameMemSection = $game;
    }


    /**
     *
     */
    protected function nexRandomBlock()
    {




    }



    /**
     * @param GameMemeSection[] $gameMemeSections
     * @return array
     */
    public function prepareGameMemeSections(array $gameMemeSections)
    {
        $result = [];
        foreach ($gameMemeSections as $gameMemeSection)
        {
            $result[$gameMemeSection->memeSection->x][$gameMemeSection->memeSection->y] = $gameMemeSection;
        }

        return $result;
    }
}