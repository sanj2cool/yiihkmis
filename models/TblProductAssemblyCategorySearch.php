<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblProductAssemblyCategory;

/**
 * TblProductAssemblyCategorySearch represents the model behind the search form of `app\models\TblProductAssemblyCategory`.
 */
class TblProductAssemblyCategorySearch extends TblProductAssemblyCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'show_on_website', 'img_width', 'translate_x', 'translate_y', 'status', 'crt_by', 'mod_by','parent_id'], 'integer'],
            [['title', 'description', 'image_url', 'ip', 'crt_time', 'mod_time','exploded_image_url'], 'safe'],
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
        $query = TblProductAssemblyCategory::find()->where('status != 0');

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
            'show_on_website' => $this->show_on_website,
            'img_width' => $this->img_width,
            'translate_x' => $this->translate_x,
            'translate_y' => $this->translate_y,
            'parent_id' => $this->parent_id,
            'status' => $this->status,
            'crt_by' => $this->crt_by,
            'mod_by' => $this->mod_by,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'image_url', $this->image_url])
            ->andFilterWhere(['like', 'exploded_image_url', $this->exploded_image_url])
            ->andFilterWhere(['like', 'ip', $this->ip]);

        return $dataProvider;
    }
}
