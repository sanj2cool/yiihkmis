<?php
  $this->title = "Reports";
  use yii\helpers\Url;
  use app\models\TblReport;
  use app\models\TblReportFavorite;
  use yii\helpers\Html;
  use app\models\TblReportAccess;
  $session = Yii::$app->session;
$id = $session['userId'];
 ?>
 <div class="card mt-3 mb-3">

<div class="tbl-report-index">
  <div class="card-header">
    <div class="row align-items-center">
      <div class="col">
        <h5 class="mb-0"><?= Html::encode($this->title) ?></h5>
      </div>

    </div>
  </div>

<div class="card-body border-top">
  <div class="row">
    <div class="col-sm-12">
      <div class="card mb-3">
        <div class="card-header bg-light">
          <h5 class="mb-0">Favourites</h5>
        </div>
        <div class="card-body border-top p-0" id="favs_div">
          <?php
            $favs = TblReport::find()->where(['status'=>1])->andWhere('id in (select fk_report_id from tbl_report_favorite where status = 1 and fk_user_id = '.$session->get('userId').')')->all();
            if(isset($favs) && count($favs) > 0){
                foreach($favs as $f){
                $getaccess = TblReportAccess::find()->where(['fk_user_id'=>$id])->andWhere(['fk_report_id'=>$f->id])->andWhere(['status'=>1])->one();
                if(isset($getaccess) && $getaccess->view_crud == 1){
                    echo '<div class="row g-0 align-items-center border-bottom py-2 px-3" id="fav_'.$f->id.'">
                    <div class="col-md mt-1 mt-md-0">
                        <a class="unformat" href="'.Url::to([$f->url]).'">'.$f->title.'</a> |
                        <a class="unformat text-sm" href="'.Url::to([$f->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                        <p>'.$f->description.'</p>
                    </div>
                    <div class="col-md-auto">
                        <p class="mb-0">';
                        echo '<i class="fas fa-star" id="fav_star_'.$f->id.'">&nbsp;&nbsp;&nbsp;</i>';
                        echo '</p>
                    </div>
                    </div>';
                }
              }//-----for loop ended
            }else{
                echo '<p class="p-2">No favorite reports yet.</p>';
            }
           ?>

        </div>
      </div>
    </div>
  </div>

  <?php
    //get all parents
    $getparents = TblReport::find()->where(['parent_id'=>1])->andWhere(['!=','id',1])->andWhere(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();

    if(isset($getparents) && count($getparents) > 0){
      foreach($getparents as $gp){


                  //check if user has access to the report
                  $getaccess = TblReportAccess::find()->where(['fk_user_id'=>$id])->andWhere(['fk_report_id'=>$gp->id])->andWhere(['status'=>1])->one();

                  // if(isset($getaccess) && $getaccess->view_crud == 1){
                    echo '<div class="row">
                      <div class="col-sm-12">
                        <div class="card mb-3">
                          <div class="card-header bg-light">
                            <h5 class="mb-0">'.$gp->title.'</h5>
                          </div>
                          <div class="card-body border-top p-0">';
                    //get all children'
                    $getchildren = TblReport::find()->where(['parent_id'=>$gp->id])->andWhere(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
                    if(isset($getchildren) && count($getchildren) > 0){
                      foreach($getchildren as $m){

                        //check access here
                        $getchildaccess = TblReportAccess::find()->where(['fk_user_id'=>$id])->andWhere(['fk_report_id'=>$m->id])->andWhere(['status'=>1])->one();
                        // if(isset($getchildaccess) && $getchildaccess->view_crud == 1){
                          echo '<div class="row g-0 align-items-center border-bottom py-2 px-3">
                            <div class="col-md mt-1 mt-md-0">
                              <a class="unformat" href="'.Url::to([$m->url]).'">'.$m->title.'</a> |
                              <a class="unformat text-sm" href="'.Url::to([$m->url]).'" target="_blank"><i class="fas fa-external-link-alt" data-bs-toggle="tooltip" data-bs-placement="right" title="Open in New Tab"></i></a>
                              <p>'.$m->description.'</p>
                            </div>
                            <div class="col-md-auto">
                              <p class="mb-0">';
                              //check
                              $check = TblReportFavorite::find()->where(['fk_report_id'=>$m->id])->andWhere(['fk_user_id'=>$session->get('userId')])->andWhere(['status'=>1])->one();
                              if(isset($check) && $check->id != ""){
                                echo '<i class="fas fa-star click_start" id="star_'.$m->id.'">&nbsp;&nbsp;&nbsp;</i>';
                              }else{
                                echo '<i class="far fa-star click_start" id="star_'.$m->id.'">&nbsp;&nbsp;&nbsp;</i>';
                              }

                              echo '</p>
                            </div>
                          </div>';
                        // }else{
                        //   //no access
                        // }

                      }
                    }
                    echo '</div>
                      </div>
                    </div>
                  </div>';
                  // }else{
                  //   //no access for the report menu
                  // }



      }
    }
   ?>
</div>
</div>
</div>
<?php
  $this->registerJs('
    $(document).on("click",".click_start",function(){
        var data_prefix = $(this).attr("data-prefix");
        var fav = 0;
        if(data_prefix == "far"){
          //its not fav
          $(this).removeClass("far");
          $(this).addClass("fas");
          fav = 1;
        }else{
          $(this).removeClass("fas");
          $(this).addClass("far");
          fav = 0;
        }
        var id = $(this).attr("id");
        var lastin = id.indexOf("_");
        var newlast = parseInt(lastin)+parseInt(1);
        var sub = id.substr(newlast);
        console.log("id:::"+sub+":::fav::"+fav);
        $.post("index.php?r=report/setfav",{id:sub,fav:fav},function(r){
          // console.log(r);
          $("#favs_div").html(r);
        });
    });
  ');
 ?>
