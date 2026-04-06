<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblEmployee;

/**
 * TblEmployeeSearch represents the model behind the search form of `app\models\TblEmployee`.
 */
class TblEmployeeSearch extends TblEmployee
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_role_id', 'status','fk_location_id'], 'integer'],
            [['name', 'email', 'phone_no', 'user_key', 'ip', 'crt_by', 'mod_by', 'crt_time', 'mod_time','driver_license','health_card'], 'safe'],
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
      $session = Yii::$app->session;
      $fk_user_id = $session['userId'];
      $user_company = $session['userCompany'];
        $query = TblEmployee::find()
        ->where('status != 0')
        ->andWhere(['tbl_employee.fk_location_id'=>$user_company]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
              ]
        ]);

        // Restore any saved state first
        $this->restoreSearchState();

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // Save the current state after loading and validating
        $this->saveSearchState();
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'fk_role_id' => $this->fk_role_id,
            'fk_location_id' => $this->fk_location_id,
            'status' => $this->status,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone_no', $this->phone_no])
            ->andFilterWhere(['like', 'user_key', $this->user_key])
            ->andFilterWhere(['like', 'driver_license', $this->driver_license])
            ->andFilterWhere(['like', 'health_card', $this->health_card])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'crt_by', $this->crt_by])
            ->andFilterWhere(['like', 'mod_by', $this->mod_by]);

        return $dataProvider;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $session->set($this->formName() . '_search', $attributes);

        // Always save the current 'sort' param from request
        $sortParam = Yii::$app->request->get('sort');
        if ($sortParam) {
            $session->set($this->formName() . '_sort', $sortParam);
        }
    }


     public function restoreSearchState()
      {
          $session = Yii::$app->session;
          $searchData = $session->get($this->formName() . '_search');

          if ($searchData) {
              $this->attributes = $searchData;
          }

          // Only restore sort if it's NOT present in the current request
          if (!Yii::$app->request->get('sort')) {
              $sortParam = $session->get($this->formName() . '_sort');
              if ($sortParam) {
                  Yii::$app->request->setQueryParams(
                      array_merge(Yii::$app->request->queryParams, ['sort' => $sortParam])
                  );
              }
          }
      }

     public function clearSearchState()
    {
        $session = Yii::$app->session;
        $session->remove($this->formName() . '_search');
        $session->remove($this->formName() . '_sort');
    }
}
