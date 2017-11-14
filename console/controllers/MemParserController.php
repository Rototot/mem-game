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

        $url = 'http://knowyourmeme.com/memes';

//        $service->parseItemsList($url);

        $url = 'http://knowyourmeme.com/memes/subcultures/true-capitalist-radio';
        $url = 'http://knowyourmeme.com/memes/wtf-is-this-shit';
        $service->parseItemPage($url);
    }

}