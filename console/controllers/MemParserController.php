<?php

namespace console\controllers;

use common\services\parser\KnowYouMemeParser;
use yii\console\Controller;

/**
 * Парсинг мемов
 * Class MemParserController
 * @package console\controllers
 */
class MemParserController extends Controller
{



    public function actionParsePage()
    {

        $service = new KnowYouMemeParser();

//        $service->parseItemsList();

        $url = 'http://knowyourmeme.com/memes/subcultures/true-capitalist-radio';
        $service->parseItemPage($url);
    }

}