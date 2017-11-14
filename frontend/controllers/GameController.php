<?php
/**
 * Created by PhpStorm.
 * User: rototot
 * Date: 13.11.17
 * Time: 21:50
 */

namespace frontend\controllers;


use common\models\game\Game;
use common\services\game\GameService;
use frontend\models\game\GameForm;
use yii\filters\AccessControl;
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

        return $this->render('play', [
            'game' => $game,
            'gameFormAnswer' => $gameFormAnswer,
        ]);

    }

    /**
     * Пропускаем ход
     * @return \yii\web\Response
     */
    public function actionSkipMove()
    {
        $serviceGame = new GameService(new Game());

        $serviceGame->skipMove();
        return $this->redirect(['play']);
    }


    /**
     * Сдаться
     * @return \yii\web\Response
     */
    public function actionSurrender()
    {
        $serviceGame = new GameService(new Game());

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
        return $this->render('result', ['game' => $game]);
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