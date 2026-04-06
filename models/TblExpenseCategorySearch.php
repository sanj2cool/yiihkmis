<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblExpenseCategory;

/**
 * TblExpenseCategorySearch represents the model behind the search form of `app\models\TblExpenseCategory`.
 */
class TblExpenseCategorySearch extends TblExpenseCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'crt_by', 'mod_by'], 'integer'],
            [['title', 'description', 'ip', 'crt_time', 'mod_time'], 'safe'],
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
        $query = TblExpenseCategory::find()->where(['<>','status',0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
              ]
        ]);


        if (!$this->load($params) || !$this->validate()) {
          $this->restoreSearchState();
          // grid filtering conditions
          $query->andFilterWhere([
              'id' => $this->id,
              'status' => $this->status,
              'crt_by' => $this->crt_by,
              'crt_time' => $this->crt_time,
              'mod_by' => $this->mod_by,
              'mod_time' => $this->mod_time,
          ]);

          $query->andFilterWhere(['like', 'title', $this->title])
              ->andFilterWhere(['like', 'description', $this->description])
              ->andFilterWhere(['like', 'ip', $this->ip]);
          return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'crt_by' => $this->crt_by,
            'crt_time' => $this->crt_time,
            'mod_by' => $this->mod_by,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'ip', $this->ip]);
        $this->saveSearchState();
        return $dataProvider;
    }
    public function saveSearchState()
     {
         $session = Yii::$app->session;
         // $session->set($this->formName() . '_search', $this->attributes);
         $attributes = $this->attributes;
         $session->set($this->formName() . '_search', $attributes);
     }

     public function restoreSearchState()
     {
         $session = Yii::$app->session;
         $searchData = $session->get($this->formName() . '_search');

         if ($searchData) {
             $this->attributes = $searchData;
         }
     }
     public function clearSearchState()
    {
        $session = Yii::$app->session;
        $session->remove($this->formName() . '_search');
    }
}
