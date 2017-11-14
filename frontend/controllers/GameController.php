<?php
/**
 * Created by PhpStorm.
 * User: rototot
 * Date: 13.11.17
 * Time: 21:50
 */

namespace frontend\controllers;


use common\models\game\Game;
use common\models\game\GameHistorySearch;
use common\services\game\GameService;
use frontend\models\game\GameForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GameController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
//                        'actions' => ['start', 'play', 'fi'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'start' => ['POST'],
                    'skip-move' => ['POST'],
                    'surrender' => ['POST'],
                    'check-answer' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Старт игры
     * @return \yii\web\Response
     */
    public function actionStart()
    {
        $serviceGame = new GameService(new Game());

        if(!$game = $serviceGame->getActiveGame()){
            $serviceGame->start();
        }

        return $this->redirect(['play']);
    }

    /**
     * Процесс игры
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPlay()
    {

        //ищем активную игру
        $serviceGame = new GameService(new Game());

        if(!$game = $serviceGame->getActiveGame()){
           throw new NotFoundHttpException('Not found game');
        }

        $gameFormAnswer = new GameForm(['scenario' => GameForm::SCENARIO_CHECK_ANSWER]);

        $prepareActiveGameSections = $serviceGame->prepareGameMemeSections($game->gameMemeSections);

        //history
        $gameHistorySearch = new GameHistorySearch();
        $dataProvider = $gameHistorySearch->search([]);

        return $this->render('play', [
            'game' => $game,
            'gameFormAnswer' => $gameFormAnswer,
            'prepareActiveGameSections' => $prepareActiveGameSections,
            'gameHistorySearch' => $gameHistorySearch,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * Пропускаем ход
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSkipMove()
    {
        $serviceGame = new GameService(new Game());

        if(!$game = $serviceGame->getActiveGame()){
            throw new NotFoundHttpException('Not found game');
        }
        $serviceGame->setGame($game);

        $serviceGame->skipMove();
        return $this->redirect(['play']);
    }


    /**
     * Сдаться
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionSurrender()
    {
        $serviceGame = new GameService(new Game());

        if(!$game = $serviceGame->getActiveGame()){
            throw new NotFoundHttpException('Not found game');
        }
        $serviceGame->setGame($game);

        $serviceGame->surrender();
        return $this->redirect(['result']);
    }

    /**
     * Результат последней завершенной игры
     * @return string
     */
    public function actionResult()
    {
        //последняя игра
        $game = Game::findLastFinished();

        //history
        $gameHistorySearch = new GameHistorySearch();
        $dataProvider = $gameHistorySearch->search([]);

        return $this->render('result', [
            'game' => $game,
            'gameHistorySearch' => $gameHistorySearch,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Проверка отввета
     * @return \yii\web\Response
     */
    public function actionCheckAnswer()
    {
        //проверям имя мема
        return $this->redirect(['play']);
    }
}