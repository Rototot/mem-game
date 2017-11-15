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


        //todo очереди

        $service = new KnowYouMemeParser();

        $url = 'http://knowyourmeme.com/memes';

        $this->stdout("\n" . 'Старт подготовки мемов');
        $listLinks = $service->parsePagination($url)['links'] ?? [];
        $countList = 0;
        $countitems = 0;
        $countAllList = count($listLinks);
        $this->stdout("\n" . 'Найдено списков: ' . $countAllList);

        foreach ($listLinks as $listLink){
            $itemsLinks = $service->parseItemsList($listLink);
            $countAllItems = count($itemsLinks);
            $localCountList = 0;
            foreach ($itemsLinks as $itemsLink){
                $url = $itemsLink['url'] ?? null;
                if(!$url){
                    continue;
                }
                $service->parseItemPage($url);
                $countitems++;
                $localCountList++;
                $this->stdout("\n" . "Обработано элементов списка $localCountList из $countAllItems");

            }

            $countList++;
            $this->stdout("\n" ."Обработано списоков $countList из $countAllList");
        }

        $this->stdout("\n" ."Найдено $countList списков. Обработано $countitems страниц мемов");
    }

}