<?php

namespace frontend\models\game;


use yii\base\Model;

class GameForm extends Model
{
    const SCENARIO_CHECK_ANSWER = 'check-answer';

    public $answer;

    public function rules()
    {
        return [
            [['answer'], 'filter', 'filter' => 'strip_tags'],
            [['answer'], 'filter', 'filter' => 'trim'],
            [['answer'], 'string', 'max' => '255'],
            [['answer'], 'required'],
        ];
    }

}