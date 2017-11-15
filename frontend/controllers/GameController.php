<?php
/**
 * Created by PhpStorm.
 * User: rototot
 * Date: 13.11.17
 * Time: 21:50
 */

namespace frontend\controllers;


use common\models\game\Game;
use common\models\game\GameHistory;
use common\models\game\GameHistorySearch;
use common\services\game\GameService;
use frontend\models\game\GameForm;
use frontend\models\game\GameHistoryForm;
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
        $gameHistorySearch->game_id = $game->id;
        $dataProvider = $gameHistorySearch->search([]);
        $dataProvider->pagination->setPageSize(10);


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
        $game = Game::findLastFinished(\Yii::$app->user->identity);

        //history
        $gameHistorySearch = new GameHistorySearch();
        $gameHistorySearch->game_id = $game->id ?? null;
        $dataProvider = $gameHistorySearch->search([]);
        $dataProvider->pagination->setPageSize(10);



        return $this->render('result', [
            'game' => $game,
            'gameHistorySearch' => $gameHistorySearch,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Проверка отввета
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCheckAnswer()
    {

        $serviceGame = new GameService(new Game());
        if(!$game = $serviceGame->getActiveGame()){
            throw new NotFoundHttpException('Not found game');
        }
        $serviceGame->setGame($game);

        $gameForm = new GameForm();
        $gameForm->load(\Yii::$app->request->post());

        $result = $serviceGame->checkAnswer($gameForm->answer, $errors);

        if($result === 1){
            \Yii::$app->session->setFlash('Вуху! Вы победили! поздравляем');
            $serviceGame->win();
            return $this->redirect('result');
        }elseif($result === -1){
            \Yii::$app->session->setFlash('info', 'Вам почти удалось, вы близки к правильному ответу.');

        }

        if(!$result && $errors){
            \Yii::$app->session->setFlash('error', implode(PHP_EOL, $errors));
        }

        //проверям имя мема
        return $this->redirect(['play']);
    }

    /**
     * используем подсказку
     * @param $type
     * @return \yii\web\Response
     */
    public function actionHint($type)
    {
        $gameService = new GameService(new Game());
        $gameService->setGame($gameService->getActiveGame());

        $type = intval($type);
        $gameService->useHint($type);

        \Yii::$app->session->setFlash('success', 'Подсказка активирована');

        return $this->redirect(['play']);

    }
}