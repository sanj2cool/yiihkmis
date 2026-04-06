<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblProductCatalog;

/**
 * TblProductCatalogSearch represents the model behind the search form of `app\models\TblProductCatalog`.
 */
class TblProductCatalogSearch extends TblProductCatalog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_active', 'fk_location_id', 'status'], 'integer'],
            [['title', 'file_name', 'file_path', 'catalog_year', 'description', 'ip', 'crt_by', 'mod_by', 'crt_time', 'mod_time'], 'safe'],
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
        $query = TblProductCatalog::find()->where('status != 0');

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
            'is_active' => $this->is_active,
            'fk_location_id' => $this->fk_location_id,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'file_name', $this->file_name])
            ->andFilterWhere(['like', 'file_path', $this->file_path])
            ->andFilterWhere(['like', 'catalog_year', $this->catalog_year])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'crt_by', $this->crt_by])
            ->andFilterWhere(['like', 'mod_by', $this->mod_by]);

        return $dataProvider;
    }
}
