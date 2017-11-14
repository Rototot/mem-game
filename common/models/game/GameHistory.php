<?php

namespace common\models\game;

use Yii;

/**
 * This is the model class for table "{{%game_history}}".
 *
 * @property int $id
 * @property string $title_label Метка перевода
 * @property int $type Тип элемента истории
 * @property int $game_id игра
 * @property int $score_cost цена элемента истории
 * @property string $created_at
 * @property string $updated_at
 *
 * @property string $title      - переведенный заголовок
 * @property Game $game
 */
class GameHistory extends \yii\db\ActiveRecord implements GameHistoryInterface
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%game_history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_label', 'type', 'game_id'], 'required'],
            [['type', 'game_id'], 'default', 'value' => null],
            ['score_cost', 'default', 'value' => 0],
            [['type', 'game_id', 'score_cost'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title_label'], 'string', 'max' => 255],
            [['game_id'], 'exist', 'skipOnError' => true, 'targetClass' => Game::className(), 'targetAttribute' => ['game_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('game-mem-section', 'ID'),
            'title_label' => Yii::t('game-mem-section', 'Title Label'),
            'type' => Yii::t('game-mem-section', 'Type'),
            'game_id' => Yii::t('game-mem-section', 'Game ID'),
            'score_cost' => Yii::t('game-mem-section', 'Score Cost'),
            'created_at' => Yii::t('game-mem-section', 'Created At'),
            'updated_at' => Yii::t('game-mem-section', 'Updated At'),
        ];
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Yii::t('game-mem-section', $this->title_label);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGame()
    {
        return $this->hasOne(Game::className(), ['id' => 'game_id']);
    }


    /**
     * @inheritdoc
     * @return GameHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GameHistoryQuery(get_called_class());
    }

    /**
     * @return array
     */
    public function historyPrepareAttributesValues() : array
    {
        return [
            GameHistory::TYPE_START => [
                  'title_label' => 'type.start',
                  'type' => GameHistory::TYPE_START,
                  'score_cost' => 0,
            ],
            GameHistory::TYPE_SKIP_MOVE => [
                'title_label' => 'type.skip_move',
                'type' => GameHistory::TYPE_SKIP_MOVE,
                'score_cost' => -1,
            ],
            GameHistory::TYPE_SURRENDER => [
                'title_label' => 'type.surrender',
                'type' => GameHistory::TYPE_SURRENDER,
                'score_cost' => 0,
            ],
            GameHistory::TYPE_HINT_YEAR_ORIGIN => [
                'title_label' => 'type.hint.year_origin',
                'type' => GameHistory::TYPE_HINT_YEAR_ORIGIN,
                'score_cost' => -10,
            ],
            GameHistory::TYPE_HINT_RANDOM_TAG => [
                'title_label' => 'type.hint.random_tag',
                'type' => GameHistory::TYPE_HINT_RANDOM_TAG,
                'score_cost' => -10,
            ],
            GameHistory::TYPE_HINT_RANDOM_ABOUT => [
                'title_label' => 'type.hint.random_about',
                'type' => GameHistory::TYPE_HINT_RANDOM_ABOUT,
                'score_cost' => -5,
            ],
            GameHistory::TYPE_CORRECT_ANSWER => [
                'title_label' => 'type.correct_answer',
                'type' => GameHistory::TYPE_CORRECT_ANSWER,
                'score_cost' => 10,
            ],
        ];
    }
}
