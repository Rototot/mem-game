<?php

namespace frontend\models\game;


use common\models\game\Game;
use common\models\game\GameHistory;
use yii\base\Model;

/**
 * Class GameHistoryForm
 * @package frontend\models\game
 */
class GameHistoryForm extends Model
{
    const SCENARIO_USE_HINT = 'use-hint';


    public $typeHint;
    public $gameId;

    public function rules()
    {
        return [
            [['typeHint', 'gameId'], 'integer'],
            [['typeHint', 'gameId'], 'required'],
            [['typeHint'], 'in', 'range' => $this->hintTypeList(), 'on' => [self::SCENARIO_USE_HINT]],

            ['gameId', 'exist', 'targetClass' => Game::className(), 'targetAttribute' => ['gameId' => 'id']],

            ['typeHint', 'validateHint']
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_USE_HINT => ['typeHint', 'gameId'],
        ]);
    }

    /**
     * @param $attribute
     * @param array $params
     */
    public function validateHint($attribute, $params = [])
    {
        //для подсказки года делаем проверку
        if($this->typeHint == GameHistory::TYPE_HINT_YEAR_ORIGIN){
            $exists = GameHistory::find()
                ->byType($this->typeHint)
                ->byGame($this->gameId)
                ->limit(1)
                ->exists();

            if($exists){
                $this->addError($attribute, 'Подсказка уже использова');
            }
        }


    }

    /**
     * @return array
     */
    protected function hintTypeList()
    {
        return [
            GameHistory::TYPE_HINT_YEAR_ORIGIN,
            GameHistory::TYPE_HINT_RANDOM_ABOUT,
            GameHistory::TYPE_HINT_RANDOM_TAG,
        ];
    }

}