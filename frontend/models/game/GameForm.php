<?php

namespace frontend\models\game;


use common\models\game\Game;
use yii\base\Model;

class GameForm extends Model
{
    const SCENARIO_CHECK_ANSWER = 'check-answer';

    public $game_id;
    public $answer;

    public function rules()
    {
        return [
            [['answer'], 'filter', 'filter' => 'strip_tags'],
            [['answer'], 'filter', 'filter' => 'trim'],
            [['answer'], 'string', 'max' => '255'],
            [['answer'], 'required'],

            ['game_id', 'exist', 'targetClass' => Game::className(), 'targetAttribute' => ['game_id' => 'id'], 'filter' => ['status' => Game::STATUS_ACTIVE]],

        ];
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_CHECK_ANSWER => ['answer', 'game_id'],
        ]);
    }


    public function validateAnswer(Game $game)
    {
        $explodeAnswer = explode(' ', $this->answer);
        $explodeTitle = explode(' ', $game->meme->title);


        //todo добавить допустимую погрешность в словам
        return count($explodeAnswer) === count($explodeTitle);
    }

}