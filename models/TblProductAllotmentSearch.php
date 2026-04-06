<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblProductAllotment;

/**
 * TblProductAllotmentSearch represents the model behind the search form of `app\models\TblProductAllotment`.
 */
class TblProductAllotmentSearch extends TblProductAllotment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_product_id', 'fk_vendor_invoice_id', 'no_of_items', 'allotted_by', 'fk_allotment_status_id', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['allotment_date', 'remarks', 'ip', 'crt_time', 'mod_time'], 'safe'],
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
        $query = TblProductAllotment::find()->where('status != 0');

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
            'fk_product_id' => $this->fk_product_id,
            'fk_vendor_invoice_id' => $this->fk_vendor_invoice_id,
            'no_of_items' => $this->no_of_items,
            'allotment_date' => $this->allotment_date,
            'allotted_by' => $this->allotted_by,
            'fk_allotment_status_id' => $this->fk_allotment_status_id,
            'status' => $this->status,
            'crt_by' => $this->crt_by,
            'mod_by' => $this->mod_by,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'ip', $this->ip]);

        return $dataProvider;
    }
}
