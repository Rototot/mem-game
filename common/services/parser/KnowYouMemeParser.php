<?php

namespace common\services\parser;


use common\models\meme\Meme;
use common\services\meme\MemeService;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\httpclient\Client;

/**
 */
class KnowYouMemeParser extends BaseObject
{

    public $baseUrl = 'http://knowyourmeme.com';

    protected $_httpClient;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }


    /**
     * @param string $url
     * @param int $min
     * @param int $max
     * @return array
     */
    public function parsePagination(string $url, int $min = 1, int $max = -1)
    {
        //находим пагинацию
        $page = $this->getPage($url);

        $pagination = pq($page)->find('#infinite-scroll-wrapper .pagination_bottom .pagination:first');
        $links = [];
        if ($pagination) {
            $nextBtn = pq($pagination)->find('.next_page:first');
            $prev = pq($nextBtn)->prev();
            $max = $prev ? intval($this->filterBase($prev->text())) : $max;
        } else {
            $max = 1;
        }

        //формируем полный урл, т.к. можно передать и относительный путь
        $fullUrl = $this->getHttpClient()->get($url)->fullUrl;
        //создаем список ссылок на странциы с мемами
        for ($localMin = $min; $localMin <= $max; $localMin++) {
            $links[] = rtrim($fullUrl, '/') . '/' . 'page/' . $localMin;
        }


        //todo в очередь

        return [
            'min' => $min,
            'max' => $max,
            'links' => $links,
        ];
    }

    /**
     * @param string $url
     * @return array
     */
    public function parseItemsList(string $url)
    {
        try {
            //парсим список мемов
            $document = $this->getPage($url);


            $entryListTrs = $document->find('#entries table.entry_list > tbody > tr');

            $items = [];
            foreach ($entryListTrs as $entryListTr) {
                //парсим элемент

                /**
                 * @var $link
                 */
                $td = pq($entryListTr)->children('td:first');
                $link = null;
                $id = -1;
                if ($td) {
                    $link = pq($td)->children('a.photo:first');

                    $id = pq($td)->attr('class');
                    $id = str_replace('entry_', '', $this->filterBase($id));
                    $id = intval($id);
                }

                $urlMem = $link ? pq($link)->attr('href') : null;
                $urlMem = $urlMem ? rtrim($this->baseUrl, '/') . '/' . $urlMem : null;


                //todo провеоки

                $itemData = [
                    'id' => $id,
                    'url' => $urlMem,
                ];

                $items[] = $itemData;
            }


            //fixme, очередь
            //кладем в очередь ссылку на элемент для парсинга

            return $items;
        } catch (\Throwable $exception) {
            throw new Exception('Ошибка при парсинге ' . $url . ' Error: ' . $exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @param string $url
     * @param int|null $idOnSite
     * @return array
     * @throws Exception
     */
    public function parseItemPage(string $url, int $idOnSite = null)
    {

        try {

            $page = $this->getPage($url);
            $article = pq($page)->find('#content article.entry:first')->get(0);


            //парсим инфу о меме
            $meme = new Meme();

            //about text
            $about = pq($page)->find('#about')->next('p:first')->get(0);
            $meme->about = $about->textContent ?? null;

            //aside
            $asideYear = pq($page)->find('#entry_body > aside.left dt:contains(Year) + dd:first')->get(0);
            $meme->origin_year = $this->filterBase($asideYear->textContent ?? null);

            //status
            $htmlStatus = pq($page)->find('#entry_body > aside.left dt:contains(Status) + dd:first')->get(0);
            $meme->site_status = $this->filterBase($htmlStatus->textContent ?? null);


            //tags
            $entryTags = pq($page)->find('#entry_tags dt:contains(Tags) + dd > a');
            $tags = [];
            foreach ($entryTags as $entryTag) {
                $tags[] = $this->filterBase($entryTag->textContent ?? null);
            }
            $meme->setTagsAsArray($tags);

            //id_mem
            if (!$idOnSite) {
                preg_match('/_([0-9]+)$/i', $article->getAttribute('id'), $matches);
                $idOnSite = intval($this->filterBase($matches[1] ?? null));
            }
            $meme->id_on_site = $idOnSite;

            //photo and title
            $photo = $article ? pq($article)->find('a.photo > img:first')->get(0) : null;
            if ($photo) {
                $meme->title = $photo->getAttribute('title');
                $meme->image = $photo->getAttribute('data-src');
            }

            $meme->url = $url;
            $serviceMem = new MemeService($meme);

            //сохраняем в базу

            $serviceMem->create([]);

            return $meme->attributes;
        } catch (\Throwable $exception) {
            throw new Exception('Ошибка при парсинге ' . $url . ' Error: ' . $exception->getMessage(), 0, $exception);
        }
    }

    /**
     * Проверка ответа в поисковой выдаче
     * @param string $answer
     * @param Meme $meme
     * @return bool
     */
    public function checkAnswer(string $answer, Meme $meme)
    {
        $url = 'search?q=' . urlencode($answer);
        $page = $this->getPage($url);

        $trEntries = pq($page)->find('#entries table.entry_list tr:first');

        if (!isset($trEntries[0])) {
            return false;
        }

        //первая позиция выдачи
        $td = pq($trEntries[0])->find('td:first');

        if (!isset($td[0])) {
            return false;
        }

        $h2 = pq($td[0])->find('h2');
        $title = $this->filterBase(pq($h2)->text());
        if (!strcasecmp($title, $meme->title)) {
            return true;
        }

        return false;
    }


    /**
     * @param string $url
     * @return \phpQueryObject|\QueryTemplatesParse|\QueryTemplatesSource|\QueryTemplatesSourceQuery
     * @throws Exception
     */
    public function getPage(string $url)
    {
        $request = $this->getHttpClient()->get($url, null, [], [
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
        ]);
        $response = $request->send();

        if (!$response->isOk) {
            throw new Exception('Incorrect response after parse. Reason: status code ' . $response->getStatusCode());
        }

        $document = \phpQuery::newDocumentHTML($response->getContent(), 'utf-8');

        return $document;
    }


    /**
     * @return Client
     */
    public function getHttpClient()
    {
        if (is_null($this->_httpClient)) {
            $this->_httpClient = new Client([
                'baseUrl' => $this->baseUrl,
                'transport' => 'yii\httpclient\CurlTransport'
            ]);
        }

        return $this->_httpClient;
    }

    protected function filterBase($string)
    {
        return trim($string);
    }
}