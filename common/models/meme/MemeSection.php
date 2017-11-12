<?php

namespace common\models\meme;

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
 * @property string $filePath
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Meme $meme
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
            [['meme_id', 'width', 'height', 'x', 'y'], 'integer'],
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
     * @inheritdoc
     * @return MemeSectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MemeSectionQuery(get_called_class());
    }
}
