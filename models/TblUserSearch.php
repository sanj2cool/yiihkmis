<?php

namespace app\models;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TblUser;

/**
 * TblUserSearch represents the model behind the search form of `app\models\TblUser`.
 */
class TblUserSearch extends TblUser
{
  public $locationNames;
  public $locations;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fk_role_id', 'fk_employee_id', 'status'], 'integer'],
            [['username', 'password', 'alias', 'user_key', 'auth_key', 'ip', 'crt_by', 'mod_by', 'crt_time', 'mod_time','locationNames','locations'], 'safe'],
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
      //get locations
      $fk_loc_arr = [];
      $getloc = TblUserLocation::find()->where(['fk_user_id'=>$fk_user_id])->andWhere(['status'=>1])->all();
      if(isset($getloc) && count($getloc) > 0){
        foreach($getloc as $gl){
            $fk_loc_arr[] = $gl->fk_location_id;
        }

      }
      $getlocs = TblUserLocation::find()->where(['in','fk_location_id',$fk_loc_arr])->andWhere(['status'=>1])->all();
      $loc_arr = array();
      if(isset($getlocs) && count($getlocs) > 0){
        foreach($getlocs as $l){
            array_push($loc_arr,$l->fk_user_id);
        }
      }

        $query = TblUser::find()
                ->where('tbl_user.status != 0')
                ->andWhere(['in','tbl_user.id',$loc_arr]);
        $query->joinWith('locations');
        $query->distinct();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Set default sorting
                ],
              ]
        ]);

        $dataProvider->sort->attributes['locationNames'] = [
            'asc' => ['tbl_ownership_company.company_name' => SORT_ASC],
            'desc' => ['tbl_ownership_company.company_name' => SORT_DESC],
        ];
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
            'fk_employee_id' => $this->fk_employee_id,
            'tbl_user.status' => $this->status,
            'crt_time' => $this->crt_time,
            'mod_time' => $this->mod_time,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'user_key', $this->user_key])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'crt_by', $this->crt_by])
            ->andFilterWhere(['like', 'mod_by', $this->mod_by]);
        $query->andFilterWhere(['like', 'tbl_ownership_company.company_name', $this->locationNames]);
        return $dataProvider;
    }
    public function saveSearchState()
    {
        $session = Yii::$app->session;
        $attributes = $this->attributes;
        $attributes['locationNames'] = $this->locationNames; // Include the virtual column
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
              $this->locationNames = $searchData['locationNames'] ?? null; // Restore the virtual column
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
