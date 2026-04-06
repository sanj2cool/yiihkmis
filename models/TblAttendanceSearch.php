<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblAttendance;

/**
 * TblAttendanceSearch represents the model behind the search form of `app\models\TblAttendance`.
 */
class TblAttendanceSearch extends TblAttendance
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_user_id', 'att_status', 'status','fk_location_id'], 'integer'],
            [['in_time', 'out_time', 'date', 'ip', 'crt_by', 'crt_time', 'mod_by', 'mod_time'], 'safe'],
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
        $query = TblAttendance::find()
                ->where('tbl_attendance.status != 0')
                ->andWhere(['tbl_attendance.fk_location_id'=>$user_company]);

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
            'fk_user_id' => $this->fk_user_id,
            'att_status' => $this->att_status,
            'fk_location_id' => $this->fk_location_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'in_time', $this->in_time])
            ->andFilterWhere(['like', 'out_time', $this->out_time])
            ->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'crt_by', $this->crt_by])
            ->andFilterWhere(['like', 'crt_time', $this->crt_time])
            ->andFilterWhere(['like', 'mod_by', $this->mod_by])
            ->andFilterWhere(['like', 'mod_time', $this->mod_time]);

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
