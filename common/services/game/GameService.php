<?php

namespace common\services\game;


use common\helpers\ApplicationHelper;
use common\models\game\Game;
use common\models\meme\Meme;
use common\models\meme\MemeSection;
use common\models\User;
use common\services\meme\MemeService;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\UserException;
use yii\caching\TagDependency;
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

            if(!$meme){
                throw new UserException('Закончились мемасики, приходите завтра.');
            }

            //создаем игру
            $game = $this->create($meme, [
                'status' => Game::STATUS_ACTIVE,
                'score' => 0,
            ]);

            //создаем сразу блок мема для игры

            //todo запись в историю


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
     */
    public function finish()
    {
        //создаем игру
        $game = $this->update([
            'status' => Game::STATUS_FINISHED,
        ]);

        //todo запись в историю

        //закрываем сессию
        $this->removeIdActiveGame();

        return $game;

    }

    /**
     * Сдаемся
     * @return Game
     */
    public function surrender()
    {
        //обновялем игру
        //todo добавить, что игрок сдался
        $game = $this->update([
            'status' => Game::STATUS_FINISHED,
            'player_is_surrender' => true,
        ]);

        //todo запись в историю


        //закрываем сессию
        $this->removeIdActiveGame();

        return $game;
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
        try{
            $memeSection = $this->nexRandomBlock();


            $this->update([
                'score' => $this->getGame()->score - 1
            ]);

            //todo запись в историю


            $transaction->commit();
            return $memeSection;
        }
        catch (Exception $e){
            $transaction->rollBack();
            throw $e;
        }

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
            return null;
        }

        //todo последняя активная игра, если сессия сдохла

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
        //todo check new record

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

        if($meme){
            $game->meme_id = $meme->id;
        }

        //todo check new record

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
     *
     */
    protected function nexRandomBlock()
    {




    }

}