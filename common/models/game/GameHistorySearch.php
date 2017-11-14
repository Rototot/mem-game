<?php

namespace common\models\game;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\game\GameHistory;

/**
 * GameHistorySearch represents the model behind the search form of `common\models\game\GameHistory`.
 */
class GameHistorySearch extends GameHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'game_id', 'score_cost'], 'integer'],
            [['title_label', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = GameHistory::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'game_id' => $this->game_id,
            'score_cost' => $this->score_cost,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'title_label', $this->title_label]);

        return $dataProvider;
    }
}
