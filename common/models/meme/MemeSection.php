<?php

namespace common\models\meme;

use common\models\game\Game;
use common\models\game\GameMemeSection;
use common\models\game\GameMemeSectionQuery;
use common\models\game\GameQuery;
use Yii;

/**
 * This is the model class for table "{{%meme_section}}".
 *
 * @property int $id
 * @property int $meme_id
 * @property bool $is_empty
 * @property int $width
 * @property int $height
 * @property int $x
 * @property int $y
 * @property int $block_x
 * @property int $block_y
 * @property string $filePath
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Meme $meme
 * @property GameMemeSection[] $gameMemeSections
 */
class MemeSection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meme_section}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meme_id', 'x', 'y', 'filePath'], 'required'],
            [['meme_id', 'width', 'height', 'x', 'y'], 'default', 'value' => null],
            [['meme_id', 'width', 'height', 'x', 'y', 'block_x', 'block_y'], 'integer'],
            [['filePath'], 'string', 'max' => 255],
            [['is_empty'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['meme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meme::className(), 'targetAttribute' => ['meme_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('meme-section', 'ID'),
            'meme_id' => Yii::t('meme-section', 'Meme ID'),
            'is_empty' => Yii::t('meme-section', 'Is Empty'),
            'width' => Yii::t('meme-section', 'Size X'),
            'height' => Yii::t('meme-section', 'Size Y'),
            'x' => Yii::t('meme-section', 'X'),
            'y' => Yii::t('meme-section', 'Y'),
            'created_at' => Yii::t('meme-section', 'Created At'),
            'updated_at' => Yii::t('meme-section', 'Updated At'),
        ];
    }

    /**
     * @return MemeQuery
     */
    public function getMeme()
    {
        return $this->hasOne(Meme::className(), ['id' => 'meme_id']);
    }

    /**
     * @return GameMemeSectionQuery
     */
    public function getGameMemeSections()
    {
        return $this->hasMany(GameMemeSection::className(), ['meme_section_id' => 'id']);
    }

    /**
     * @return GameQuery
     */
    public function getGame()
    {
        return $this->hasMany(Game::className(), ['id' => 'game_id'])
            ->via('gameMemeSections');
    }

    /**
     * Web путь к изображению
     * @return string
     */
    public function getImageWebPath()
    {
        return '/' . $this->filePath;
    }

    /**
     * @inheritdoc
     * @return MemeSectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MemeSectionQuery(get_called_class());
    }
}
