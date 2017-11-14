<?php

namespace common\services\meme;


use common\models\game\GameMemeSection;
use common\models\meme\Meme;
use common\models\meme\MemeSection;
use Imagine\Image\Box;
use Imagine\Image\Point;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\db\Transaction;
use yii\helpers\FileHelper;
use yii\httpclient\Client;
use yii\imagine\Image;

class MemeService extends BaseObject
{
    const DIVIDE_WIDTH = 16;
    const DIVIDE_HEIGHT = 16;


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


        if(!$meme->isNewRecord){
            throw new Exception('Update not supported');
        }

        if (!$meme->validate()) {
            throw new Exception('Incorrect attributes . ' . implode(PHP_EOL, $meme->firstErrors));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            if (!$meme->save(false)) {
                throw new Exception('Cannot save mem');
            }

            //бьем на фрагменты
            $this->divide();

            $transaction->commit();
            return $meme;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
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

        if($meme->isNewRecord){
            throw new Exception('Insert not supported');
        }

        if (!$meme->validate()) {
            throw new Exception('Incorrect attributes');
        }


        if (!$meme->save(false)) {
            throw new Exception('Cannot save mem');
        }


        return $this->getMeme();
    }

    /**
     * @return Meme
     */
    public function getMeme(): Meme
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


    /**
     * Разделение на блоки
     * @param int $width
     * @param int $height
     */
    public function divide(int $width = self::DIVIDE_WIDTH, int $height = self::DIVIDE_HEIGHT)
    {
        $imageData = file_get_contents($this->getMeme()->image);

        $image = Image::getImagine()->load($imageData);

        $size = $image->getSize();
        $imageWidth = $size->getWidth();
        $imageHeight = $size->getHeight();


        //идем по изображению
        $widthBlock = round($imageWidth / $width, 0, PHP_ROUND_HALF_UP);
        $heightBlock = round($imageHeight / $height, 0, PHP_ROUND_HALF_UP);


        $transaction = Yii::$app->db->beginTransaction();
        try {

            $this->update([
                'width' => $imageWidth,
                'height' => $imageHeight,
            ]);

            $blockX = 0;
            $blockY = 0;
            for ($x = 0; $x < $imageWidth; $x += $widthBlock) {
                //проверяем границы
                $newWidth = ($x + $widthBlock) > $imageWidth ? $imageWidth - $x : $width;
                $memeSectionService = new MemSectionService(new MemeSection());
                $blockY = 0;
                for ($y = 0; $y < $imageHeight; $y += $heightBlock) {
                    $newHeight = ($y + $heightBlock) > $imageHeight ? $imageHeight - $y : $heightBlock;

                    $point = new Point($x, $y);
                    $box = new Box($newWidth, $newHeight);

                    //сохраняем файл
                    $dirSave = implode('/', [
                        'upload',
                        $this->getMeme()->id_on_site,
                        $widthBlock . '_' . $heightBlock,
                    ]);
                    $fullPath = Yii::getAlias('@root/' . $dirSave);
                    FileHelper::createDirectory($fullPath);

                    $fileName = 'img_block_' . $x . '_' . $y . '_' . $width . '_' . $height . '.png';

                    $fullPath .= '/' . $fileName;
                    $dirSave .= '/' . $fileName;
                    $newImage = $image->copy()->crop($point, $box)->save(\Yii::getAlias($fullPath));

                    //сохраняем в базе секцию
                    $memeSectionService->setMemeSection(new MemeSection());
                    $memeSectionService->create($this->getMeme(), [
                        'x' => $x,
                        'y' => $y,
                        'block_x' => $blockX,
                        'block_y' => $blockY,
                        'width' => $newWidth,
                        'height' => $newHeight,
                        'filePath' => $dirSave,
                        //fixme проверка
//                    'is_empty' => $memeSectionService->checkIsVoid($newImage),
                    ]);

                    $blockY++;
                }
                $blockX++;
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

}