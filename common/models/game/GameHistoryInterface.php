<?php
/**
 * Created by PhpStorm.
 * User: rototot
 * Date: 14.11.17
 * Time: 1:46
 */

namespace common\models\game;


interface GameHistoryInterface
{
    const TYPE_START = 5;                       //старт игры
    const TYPE_SKIP_MOVE = 7;                  //сдаться
    const TYPE_SURRENDER = 10;                  //сдаться
    const TYPE_FINISH = 15;                     //конец игры

    //подсказки
    const TYPE_HINT_YEAR_ORIGIN = 20;           //год возникновения
    const TYPE_HINT_RANDOM_TAG = 21;            //случайное ключевое слово
    const TYPE_HINT_RANDOM_ABOUT = 22;          //слово из about


    const TYPE_CORRECT_ANSWER = 25;          //правильны ответ

    /**
     * Настройки аттрибутов для каждого типа истории
     * @return array
     */
    public function historyPrepareAttributesValues() : array ;


}