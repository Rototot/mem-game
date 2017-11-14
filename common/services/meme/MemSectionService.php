<?php

namespace common\services\meme;


use common\models\meme\Meme;
use common\models\meme\MemeSection;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\NotSupportedException;

/**
 * Class MemSectionService
 * @package common\services\meme
 */
class MemSectionService extends BaseObject
{
    private $memeSection;

    public function __construct(MemeSection $memeSection, array $config = [])
    {
        $this->memeSection = $memeSection;
        parent::__construct($config);
    }

    /**
     * @param Meme $meme
     * @param array $attributes
     * @return MemeSection
     * @throws Exception
     */
    public function create(Meme $meme, array $attributes = [])
    {
        $memeSection = $this->getMemeSection();
        $memeSection->attributes = $attributes;
        $memeSection->meme_id = $meme->id;

        if(!$meme->isNewRecord){
            throw new Exception('Update not supported');
        }

        if (!$memeSection->validate()) {
            throw new Exception('Incorrect attributes: ' . json_encode($memeSection->firstErrors));
        }

        if (!$memeSection->save(false)) {
            throw new Exception('Cannot save mem');
        }

        return $memeSection;

    }


    public function update(Meme $meme, array $attributes = [])
    {
        throw new NotSupportedException('Cannot release');
    }

    /**
     * @return MemeSection
     */
    public function getMemeSection(): MemeSection
    {
        return $this->memeSection;
    }

    /**
     * @param MemeSection $memeSection
     */
    public function setMemeSection(MemeSection $memeSection)
    {
        $this->memeSection = $memeSection;
    }


    /**
     * Проверка что пустой или сплошной цвет
     * @param ImageInterface $image
     * @return bool
     */
    public function checkIsVoid(ImageInterface $image)
    {

        $size = $image->getSize();
        $alwaysAlpha = true;
        $alwaysOneColor = true;
        $colorValue = null;
        for ($x = 1; $x < $size->getWidth(); $x++) {
            for ($y = 1; $y < $size->getHeight(); $y++) {
                $color = $image->getColorAt(new Point($x, $y));


//                $colorValue = $color->getPalette()->color();
                //проверяем прозрачность
                if($alwaysAlpha){
                    $alwaysAlpha = !$color->isOpaque();
                }

                if($alwaysOneColor){
//                    $color->getPalette()
                }
            }
        }

        return $alwaysAlpha || $alwaysOneColor;
    }


}