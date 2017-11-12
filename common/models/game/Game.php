<?php

namespace common\models\game;

use common\models\meme\Meme;
use common\models\meme\MemeQuery;
use common\models\User;
use Yii;

/**
 * This is the model class for table "{{%game}}".
 *
 * @property int $id
 * @property int $meme_id
 * @property int $player_id
 * @property int $score
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Meme $meme
 * @property User $player
 * @property GameMemeSection[] $gameMemeSections
 */
class Game extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meme_id', 'player_id'], 'required'],
            [['meme_id', 'player_id', 'score', 'status'], 'default', 'value' => null],
            [['meme_id', 'player_id', 'score', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['meme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meme::className(), 'targetAttribute' => ['meme_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('game', 'ID'),
            'meme_id' => Yii::t('game', 'Meme ID'),
            'player_id' => Yii::t('game', 'Player ID'),
            'score' => Yii::t('game', 'Score'),
            'status' => Yii::t('game', 'Status'),
            'created_at' => Yii::t('game', 'Created At'),
            'updated_at' => Yii::t('game', 'Updated At'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(User::className(), ['id' => 'player_id']);
    }

    /**
     * @return GameMemeSectionQuery
     */
    public function getGameMemeSections()
    {
        return $this->hasMany(GameMemeSection::className(), ['game_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return GameQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GameQuery(get_called_class());
    }
}
