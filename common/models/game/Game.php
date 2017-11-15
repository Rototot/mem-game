<?php

namespace common\models\game;

use common\models\meme\Meme;
use common\models\meme\MemeQuery;
use common\models\User;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "{{%game}}".
 *
 * @property int $id
 * @property int $meme_id
 * @property int $player_id
 * @property boolean $player_is_surrender
 * @property boolean $player_is_win
 * @property int $score
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Meme $meme
 * @property User $player
 * @property GameMemeSection[] $gameMemeSections
 * @property GameHistory[] $gameHistories
 */
class Game extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 10;
    const STATUS_FINISHED = 15;             //игра завершена


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
            ['status', 'default', 'value' => $this->defaultStatus()],
            ['score', 'default', 'value' => 0],

            [['meme_id', 'player_id', 'score', 'status'], 'integer'],
            [['player_is_surrender', 'player_is_win'], 'boolean'],
            ['score', 'default', 'value' => 0],
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
            'player_is_surrender' => Yii::t('game', 'Player is surrender'),
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
     * @return GameHistoryQuery
     */
    public function getGameHistories()
    {
        return $this->hasMany(GameHistory::className(), ['game_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        TagDependency::invalidate(Yii::$app->cache, [static::tableName() . '-' . $this->id]);
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
        TagDependency::invalidate(Yii::$app->cache, [static::tableName() . '-' . $this->id]);
        parent::afterDelete();
    }

    public function getStatusLabel()
    {

    }

    /**
     * @return int
     */
    public function defaultStatus()
    {
        return self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isWin()
    {
        return $this->player_is_win;
    }

    /**
     * @inheritdoc
     * @return GameQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GameQuery(get_called_class());
    }

    /**
     * @param User $user
     * @return Game|null
     */
    public static function findLastFinished(User $user)
    {
        $game = static::find()
            ->finished()
            ->byPlayer($user->id)
            ->orderBy('id DESC')
            ->limit(1)
            ->one()
            ;

        return $game;
    }
}
