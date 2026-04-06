<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblBinLocations;

/**
 * TblBinLocationsSearch represents the model behind the search form of `app\models\TblBinLocations`.
 */
class TblBinLocationsSearch extends TblBinLocations
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['area', 'row', 'bay', 'level', 'position', 'ip', 'crt_by', 'mod_by', 'crt_time', 'mod_time'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = TblBinLocations::find()->where('status != 0');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
              ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'row', $this->row])
            ->andFilterWhere(['like', 'bay', $this->bay])
            ->andFilterWhere(['like', 'level', $this->level])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'crt_by', $this->crt_by])
            ->andFilterWhere(['like', 'mod_by', $this->mod_by]);

        return $dataProvider;
    }
}
