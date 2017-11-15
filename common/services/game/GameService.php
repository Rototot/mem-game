<?php

namespace common\services\game;


use common\helpers\ApplicationHelper;
use common\models\game\Game;
use common\models\game\GameHistory;
use common\models\game\GameMemeSection;
use common\models\meme\Meme;
use common\models\meme\MemeSection;
use common\models\meme\MemeSectionQuery;
use common\models\User;
use common\services\meme\MemeService;
use frontend\models\game\GameHistoryForm;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\UserException;
use yii\caching\TagDependency;
use yii\db\Expression;
use yii\db\Transaction;

class GameService extends BaseObject
{
    const CONTAINER_KEY_ACTIVE_GAME = 'active-game';

    private $game;

    public function __construct(Game $game, array $config = [])
    {
        $this->game = $game;
        parent::__construct($config);
    }


    /**
     * Старт игры
     * @return Game
     * @throws Exception
     */
    public function start()
    {
        //создаем игру
        $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);
        try {

            $game = $this->getGame();
            $game->player_id = Yii::$app->user->id;

            //рандомный мем
            $meme = Meme::findRandom();

            if (!$meme) {
                throw new UserException('Закончились мемасики, приходите завтра.');
            }

            //создаем игру
            $game = $this->create($meme, [
                'status' => Game::STATUS_ACTIVE,
                'score' => 0,
            ]);

            //создаем сразу блок мема для игры
            $this->addNextRandomBlock($meme, false);

            $this->writeToStory($game, GameHistory::TYPE_START);

            //ставим в сессию активную игру
            $this->setIdActiveGame($game->id);

            $transaction->commit();
            return $game;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Завершаем игру
     * @return Game
     * @throws Exception
     */
    public function finish()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //создаем игру
            $game = $this->update([
                'status' => Game::STATUS_FINISHED,
            ]);

            $this->writeToStory($game, GameHistory::TYPE_FINISH);

            //закрываем сессию
            $this->removeIdActiveGame();

            $transaction->commit();
            return $game;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Сдаемся
     * @return Game
     * @throws Exception
     */
    public function surrender()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //обновялем игру
            $game = $this->update([
                'status' => Game::STATUS_FINISHED,
                'player_is_surrender' => true,
            ]);

            $this->writeToStory($game,GameHistory::TYPE_SURRENDER);
            $this->writeToStory($game, GameHistory::TYPE_FINISH);

            //закрываем сессию
            $this->removeIdActiveGame();

            $transaction->commit();
            return $game;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    /**
     * Пропуск хода. -1 балл. Получаем следующий блок
     * @return MemeSection
     * @throws Exception
     */
    public function skipMove()
    {
        //пропускается ход
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $memeSection = $this->addNextRandomBlock($this->getGame()->meme);

            $this->update([
                'score' => $this->getGame()->score - 1
            ]);

            $this->writeToStory($this->getGame(), GameHistory::TYPE_SKIP_MOVE);

            $transaction->commit();

            return $memeSection;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

    }

    /**
     * Используем подсказку
     * @see GameHistoryInterface
     * @param int $type
     * @return GameHistory
     * @throws UserException
     */
    public function useHint(int $type)
    {

        $form = new GameHistoryForm(['scenario' => GameHistoryForm::SCENARIO_USE_HINT]);
        $form->typeHint = $type;
        $form->gameId = $this->getGame()->id;

        if(!$form->validate()){
            throw new UserException('Incorrect data. ' . implode(PHP_EOL, $form->firstErrors));
        }

        $service = new GameHistoryService(new GameHistory());

        return $service->create($this->getGame(), $type, []);
    }


    /**
     * @return Game|null
     * @throws Exception
     */
    public function getActiveGame()
    {
        /**
         * @var $user User
         */
        $user = \Yii::$app->user->identity;

        if (!$user) {
            throw new Exception('Not found user');
        }

        $idActiveGame = $this->getIdActiveGame();

        if (!$idActiveGame) {

            //последняя активная игра, если сессия умерла
            $lasActiveGame = Game::find()->byPlayer($user->id)->active()->orderBy('created_at DESC')->limit(1)->one();
            if($lasActiveGame){
                $this->setIdActiveGame($lasActiveGame->id);
            }

            return $lasActiveGame;
        }

        $activeGame = Game::getDb()->cache(function () use ($idActiveGame) {
            return Game::find()->where(['id' => $idActiveGame])->active()->one();
        }, ApplicationHelper::CACHE_DURATION_MONTH, new TagDependency(['tags' => [Game::tableName() . '-' . $idActiveGame]]));


        return $activeGame;
    }


    /**
     * @param Meme $meme
     * @param array $attributes
     * @return Game
     * @throws Exception
     */
    public function create(Meme $meme, array $attributes = [])
    {
        $game = $this->getGame();
        $game->attributes = $attributes;
        $game->meme_id = $meme->id;


        if(!$game->isNewRecord){
            throw new Exception('Update not supported');
        }

        if (!$game->validate()) {
            throw new Exception('Incorrect attributes');
        }

        if (!$game->save(false)) {
            throw new Exception('Cannot save mem');
        }

        return $game;
    }

    /**
     * @param Meme|null $meme
     * @param array $attributes
     * @return Game
     * @throws Exception
     */
    public function update(array $attributes = [], ?Meme $meme = null)
    {
        $game = $this->getGame();
        $game->attributes = $attributes;

        if ($meme) {
            $game->meme_id = $meme->id;
        }

        if($game->isNewRecord){
            throw new Exception('Insert not supported');
        }

        if (!$game->validate()) {
            throw new Exception('Incorrect attributes');
        }

        if (!$game->save(false)) {
            throw new Exception('Cannot save mem');
        }

        return $game;
    }

    /**
     * @return string | null
     */
    public function getIdActiveGame(): ?string
    {
        return \Yii::$app->session->get(self::CONTAINER_KEY_ACTIVE_GAME);
    }


    /**
     * @param string $id
     */
    public function setIdActiveGame(string $id)
    {
        \Yii::$app->session->set(self::CONTAINER_KEY_ACTIVE_GAME, $id);
    }

    public function removeIdActiveGame()
    {
        \Yii::$app->session->remove(self::CONTAINER_KEY_ACTIVE_GAME);
    }

    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }


    /**
     * @param GameMemeSection[] $gameMemeSections
     * @return array
     */
    public function prepareGameMemeSections(array $gameMemeSections)
    {
        $result = [];
        foreach ($gameMemeSections as $gameMemeSection) {
            $result[$gameMemeSection->memeSection->block_x][$gameMemeSection->memeSection->block_y] = $gameMemeSection;
        }

        return $result;
    }

    /**
     * Добавляем следующий блок в игру
     * @param Meme $meme
     * @param bool $withVoid - добавлять дополнительный пустой блок с непустым
     * @return GameMemeSection[]
     * @throws Exception
     */
    protected function addNextRandomBlock(Meme $meme, $withVoid = true)
    {
        $subQuery = $meme->getMemeSections()
            ->alias('ms')
            ->select('ms.id')
            ->excludeActiveInGame(Yii::$app->user->identity);

        $memeSectionQuery = $meme->getMemeSections()
            ->andWhere(['not in', 'id', $subQuery])
            //todo оптимизировать
            ->orderBy(new Expression('RANDOM()'))
            ->limit(1);

        //получаем блок сразу одним запросом
        if ($withVoid) {
            $memeSectionQuery->union((clone $memeSectionQuery)->void(true));
        }
        //основной запрос возвращает непустые
        $memeSectionQuery->void(false);

        $memeSections = $memeSectionQuery->all();

        if (!$memeSections) {
            return [];
        }

        $result = [];
        $serviceGameMemeSection = new GameMemeSectionService(new GameMemeSection());
        foreach ($memeSections as $memeSection) {
            $serviceGameMemeSection->setGameMemeSection(new GameMemeSection());
            $result[] = $serviceGameMemeSection->create($this->getGame(), $memeSection, []);
        }


        return $result;
    }



    /**
     * запись в историю
     * @param Game $game
     * @param int $type
     * @return GameHistory
     */
    protected function writeToStory(Game $game, int $type)
    {

        $service = new GameHistoryService(new GameHistory());
        return $service->create($game, $type, []);
    }
}