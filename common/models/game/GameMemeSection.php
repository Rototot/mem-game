<?php

namespace common\models\game;

use common\models\meme\MemeSection;
use Yii;

/**
 * This is the model class for table "{{%game_meme_section}}".
 *
 * @property int $id
 * @property int $game_id
 * @property int $meme_section_id
 *
 * @property Game $game
 * @property MemeSection $memeSection
 */
class GameMemeSection extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game_meme_section}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['game_id', 'meme_section_id'], 'required'],
            [['game_id', 'meme_section_id'], 'default', 'value' => null],
            [['game_id', 'meme_section_id'], 'integer'],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => Game::className(), 'targetAttribute' => ['game_id' => 'id']],
            [['meme_section_id'], 'exist', 'skipOnError' => true, 'targetClass' => MemeSection::className(), 'targetAttribute' => ['meme_section_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('game-mem-section', 'ID'),
            'game_id' => Yii::t('game-mem-section', 'Game ID'),
            'meme_section_id' => Yii::t('game-mem-section', 'Meme Section ID'),
        ];
    }

    /**
     * @return GameQuery
     */
    public function getGame()
    {
        return $this->hasOne(Game::className(), ['id' => 'game_id']);
    }

    /**
     * @return MemeSection
     */
    public function getMemeSection()
    {
        return $this->hasOne(MemeSection::className(), ['id' => 'meme_section_id']);
    }

    /**
     * @inheritdoc
     * @return GameMemeSectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GameMemeSectionQuery(get_called_class());
    }
}
