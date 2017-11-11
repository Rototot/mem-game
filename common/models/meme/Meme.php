<?php

namespace common\models\meme;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%meme}}".
 *
 * @property int $id
 * @property int $id_on_site
 * @property string $title
 * @property string $url
 * @property string $about
 * @property string $image
 * @property int $origin_year
 * @property string $tags
 * @property string $site_status
 * @property string $created_at
 * @property string $updated_at
 */
class Meme extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meme}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_on_site', 'origin_year'], 'default', 'value' => null],
            [['id_on_site', 'origin_year'], 'integer'],
            [['title', 'url', 'image', 'site_status'], 'required'],
            [['about', 'tags'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'url', 'image'], 'string', 'max' => 255],
            [['site_status'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('meme', 'ID'),
            'id_on_site' => Yii::t('meme', 'Id On Site'),
            'title' => Yii::t('meme', 'Title'),
            'url' => Yii::t('meme', 'Url'),
            'about' => Yii::t('meme', 'About'),
            'image' => Yii::t('meme', 'Image'),
            'origin_year' => Yii::t('meme', 'Origin Year'),
            'tags' => Yii::t('meme', 'Tags'),
            'site_status' => Yii::t('meme', 'Site Status'),
            'created_at' => Yii::t('meme', 'Created At'),
            'updated_at' => Yii::t('meme', 'Updated At'),
        ];
    }

    /**
     * @return array
     */
    public function getTagsAsArray()
    {
        return Json::decode($this->tags);
    }

    /**
     * @param $value
     */
    public function setTagsAsArray($value)
    {
        $this->tags = Json::encode($value);
    }

    /**
     * @inheritdoc
     * @return MemeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MemeQuery(get_called_class());
    }
}
