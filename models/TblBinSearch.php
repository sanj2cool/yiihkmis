<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblBin;

/**
 * TblBinSearch represents the model behind the search form of `app\models\TblBin`.
 */
class TblBinSearch extends TblBin
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_area_id', 'fk_row_id', 'fk_bay_id', 'fk_level_id', 'fk_position_id', 'status'], 'integer'],
            [['ip', 'crt_by', 'mod_by', 'crt_time', 'mod_time'], 'safe'],
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
        $query = TblBin::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'fk_area_id' => $this->fk_area_id,
            'fk_row_id' => $this->fk_row_id,
            'fk_bay_id' => $this->fk_bay_id,
            'fk_level_id' => $this->fk_level_id,
            'fk_position_id' => $this->fk_position_id,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'crt_by', $this->crt_by])
            ->andFilterWhere(['like', 'mod_by', $this->mod_by]);

        return $dataProvider;
    }
}
