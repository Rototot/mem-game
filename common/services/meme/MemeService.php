<?php

namespace common\services\meme;


use common\models\meme\Meme;
use yii\base\BaseObject;
use yii\base\Exception;

class MemeService extends BaseObject
{
    private $meme;


    public function __construct(Meme $meme, array $config = [])
    {
        $this->meme = $meme;
        parent::__construct($config);
    }

    /**
     * @param array $attributes
     * @return Meme|
     * @throws Exception
     */
    public function create(array $attributes)
    {
        $meme = $this->getMeme();
        $meme->attributes = $attributes;

        //todo check new record

        if(!$meme->validate()){
            throw new Exception('Incorrect attributes');
        }

        //todo бьем на фрагменты ещё


        if(!$meme->save(false)){
            throw new Exception('Cannot save mem');
        }


        return $this->getMeme();
    }

    /**
     * @param array $attributes
     * @return Meme|
     * @throws Exception
     */
    public function update(array $attributes)
    {
        $meme = $this->getMeme();
        $meme->attributes = $attributes;

        //todo check new record

        if(!$meme->validate()){
            throw new Exception('Incorrect attributes');
        }


        if(!$meme->save(false)){
            throw new Exception('Cannot save mem');
        }


        return $this->getMeme();
    }

    /**
     * @return Meme
     */
    public function getMeme() : Meme
    {
        return $this->meme;
    }

    /**
     * @param Meme $meme
     */
    public function setMeme(Meme $meme)
    {
        $this->meme = $meme;
    }


}