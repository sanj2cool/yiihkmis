<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\TblEmployee;
use app\models\TblMenu;
use app\models\TblMenuAccess;
use app\models\TblReport;
use app\models\TblReportAccess;
use app\models\TblUserLocation;
use app\models\TblOwnershipCompany;

/** @var yii\web\View $this */
/** @var app\models\TblUser $model */
/** @var yii\widgets\ActiveForm $form */
//-----get employees------------
$allemps = TblEmployee::find()->where(['status'=>1])->all();
$emparr = ArrayHelper::map($allemps,'id','name');


$locations = TblOwnershipCompany::find()->where(['status'=>1])->all();
$locarr = ArrayHelper::map($locations,'id','location_name');

$rolearr = \app\models\TblRoleMaster::find()->select(['title', 'id'])->where('status != 0')->indexBy('id')->column();
?>

<div class="tbl-user-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card shadow rounded mt-2">
      <div class="card-body p-3">
        <div class="row">
          <?php
              if(!$model->isNewRecord){
                  //--------update time
                  ?>
                  <div class="col-lg-6">
                      <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                  </div>
                  <div class="col-lg-6">
                      <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
                  </div>
                  <?php
              }else{
                  //--------new record ---------
                  ?>
                  <div class="col-lg-4">
                      <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                  </div>
                  <div class="col-lg-4">
                      <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
                  </div>
                  <div class="col-lg-4">
                      <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
                  </div>
                  <?php
              }
          ?>
        </div>
        <div class="row mt-2">
          <div class="col-lg-4">
            <label>Location</label>
            <select name="location[]" id="location" class="form-control" required data-choices="data-choices" multiple="multiple" data-options='{"removeItemButton":true,"placeholder":true,"allowHTML":true}'>
              <?php
              if(isset($locations) && count($locations) > 0){
                foreach($locations as $l){
                  if(!$model->isNewRecord){
                    //check if location exists for the user
                    $checkloc = TblUserLocation::find()->where(['fk_location_id'=>$l->id])->andWhere(['fk_user_id'=>$model->id])->andWhere(['status'=>1])->one();
                    if(isset($checkloc) && $checkloc->id != ""){
                      echo '<option value="'.$l->id.'" selected="selected">'.$l->location_name.'</option>';
                    }else{
                      echo '<option value="'.$l->id.'">'.$l->location_name.'</option>';
                    }
                  }else{
                    echo '<option value="'.$l->id.'">'.$l->location_name.'</option>';
                  }

                }
              }
              ?>
            </select>
          </div>
          <div class="col-lg-4">
            <?= $form->field($model, 'fk_role_id')->dropDownList($rolearr,['prompt'=>'Select','class'=>'form-select']) ?>
          </div>
          <div class="col-lg-4">
            <?= $form->field($model, 'status')->dropDownList(['1'=>'Active',2=>'Inactive'],['prompt'=>'Select','class'=>'form-select']) ?>
          </div>
        </div>
      </div>
    </div>


    <!-- Menu start -->
    <div class="card shadow rounded mt-4">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <div class="row">
                        <div class="col-lg-6">
                            <h4>User Access</h4>
                        </div>
                        <div class="col-lg-6 text-end">
                            <input type="checkbox" id="select_all_menu">&nbsp;&nbsp;<strong>Select All</strong>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Module Name</th>
                                <th>View</th>
                                <th>Create</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $getcountmodules = TblMenu::find()->where(['!=','id',1])->andWhere(['status'=>1])->orderBy(['priority' => SORT_ASC])->all();
                                $getallmodules = TblMenu::find()->where(['!=','id',1])->andWhere(['parent_id'=>1])->andWhere(['status'=>1])->orderBy(['priority' => SORT_ASC])->all();
                                $i = 0;
                                echo '<input type="hidden" name="menuitems" value="'.count($getcountmodules).'">';
                                foreach ($getallmodules as $gm) {
                                    //check if menu item has children
                                    $getchild = TblMenu::find()->where(['parent_id'=>$gm->id])->andWhere(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
                                    if(count($getchild) > 0){
                                    // echo "has children";
                                        if(!$model->isNewRecord){
                                            echo '<tr class="table-success">
                                                <td><input type="hidden" name="menuitem_'.$i.'" value="'.$gm->id.'">'.$gm->title.'</td>
                                                <td>';
                                            $getmaccess = TblMenuAccess::find()->where(['fk_user_id'=>$model->id])->andWhere(['fk_menu_id'=>$gm->id,'status'=>1])->one();
                                            if(isset($getmaccess) && $getmaccess->view_crud == 1){
                                                echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="1" class="view_hidden">
                                                <input type="checkbox" class="viewparent" id="'.$i.'" checked="checked">
                                                </td>';
                                            }else{
                                                echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="0" class="view_hidden">
                                                <input type="checkbox" class="viewparent" id="'.$i.'">
                                                </td>';
                                            }
                                            if(isset($getmaccess) && $getmaccess->create_crud == 1){
                                                echo '<td><input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="1" class="create_hidden">
                                                                <input type="checkbox" class="createparent" id="'.$i.'" checked="checked">
                                                                </td>';
                                            }else{
                                                echo '<td><input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="0" class="create_hidden">
                                                <input type="checkbox" class="createparent" id="'.$i.'">
                                                </td>';
                                            }
                                            if(isset($getmaccess) && $getmaccess->edit_crud == 1){
                                                echo '<td><input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="1" class="edit_hidden">
                                                                <input type="checkbox" class="editparent" id="'.$i.'" checked="checked">
                                                                </td>';
                                            }else{
                                                echo '<td><input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="0" class="edit_hidden">
                                                <input type="checkbox" class="editparent" id="'.$i.'">
                                                </td>';
                                            }
                                            if(isset($getmaccess) && $getmaccess->delete_crud == 1){
                                                echo '<td><input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="1" class="delete_hidden">
                                                                <input type="checkbox" class="deleteparent" id="'.$i.'" checked="checked">
                                                                </td>';
                                            }else{
                                                echo '<td><input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="0" class="delete_hidden">
                                                <input type="checkbox" class="deleteparent" id="'.$i.'">
                                                </td>';
                                            }
                                            echo '</tr>';
                                            $parentvar = $i;
                                            //get all the children in for loop and print them all
                                            foreach($getchild as $gc){
                                                if(!$model->isNewRecord){
                                                    $i++;
                                                    $getmenuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$model->id])->andWhere(['fk_menu_id'=>$gc->id,'status'=>1])->one();
                                                    $check_child = TblMenu::find()->where(['parent_id'=>$gc->id])->andWhere(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
                                                    if(count($check_child)>0){
                                                        //has third level children
                                                        echo '<tr class="table-warning">
                                                        <td><input type="hidden" name="menuitem_'.$i.'" value="'.$gc->id.'">'.$gc->title.'</td>
                                                        <td>';
                                                        if(isset($getmenuaccess) && $getmenuaccess->view_crud == 1){
                                                            echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="'.$getmenuaccess->view_crud.'" class="view_hidden view_hide_'.$parentvar.'">
                                                            <input type="checkbox" checked="checked" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$parentvar.'"></td>';
                                                        }else{
                                                            echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="0" class="view_hidden view_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$parentvar.'"></td>';
                                                        }
                                                        echo '<td>';
                                                        if(isset($getmenuaccess) && $getmenuaccess->create_crud == 1){
                                                            echo '<input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="'.$getmenuaccess->create_crud.'"  class="create_hidden create_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$parentvar.'" checked="checked"></td>';
                                                        }else{
                                                            echo '<input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="0"  class="create_hidden create_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$parentvar.'"></td>';
                                                        }
                                                        echo '<td>';
                                                        if(isset($getmenuaccess) && $getmenuaccess->edit_crud == 1){
                                                            echo '<input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="'.$getmenuaccess->edit_crud.'"  class="edit_hidden edit_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$parentvar.'" checked="checked"></td>';
                                                        }else{
                                                            echo '<input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="0"  class="edit_hidden edit_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$parentvar.'"></td>';
                                                        }
                                                        echo '<td>';
                                                        if(isset($getmenuaccess) && $getmenuaccess->delete_crud == 1){
                                                            echo '<input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="'.$getmenuaccess->delete_crud.'"  class="delete_hidden delete_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$parentvar.'" checked="checked"></td>';
                                                        }else{
                                                            echo '<input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="0"  class="delete_hidden delete_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$parentvar.'"></td>';
                                                        }
                                                        echo '</tr>';
                                                        foreach($check_child as $cc){
                                                            $i++;
                                                            $getmenuaccesschild = TblMenuAccess::find()->where(['fk_user_id'=>$model->id])->andWhere(['fk_menu_id'=>$cc->id,'status'=>1])->one();
                                                            echo '<tr>
                                                                <td style="padding-left:20px"><input type="hidden" name="menuitem_'.$i.'" value="'.$cc->id.'">'.$cc->title.'</td>
                                                                <td>';
                                                            if(isset($getmenuaccesschild) && $getmenuaccesschild->view_crud == 1){
                                                                echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="'.$getmenuaccesschild->view_crud.'"  class="view_hidden view_hide_'.$parentvar.'">
                                                                    <input type="checkbox" checked="checked" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$parentvar.'"></td>';
                                                            }else{
                                                                echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="0"  class="view_hidden view_hide_'.$parentvar.'">
                                                                <input type="checkbox" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$parentvar.'"></td>';
                                                            }
                                                            echo '<td>';
                                                            if(isset($getmenuaccesschild) && $getmenuaccesschild->create_crud == 1){
                                                                echo '<input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="'.$getmenuaccesschild->create_crud.'"  class="create_hidden create_hide_'.$parentvar.'">
                                                                <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$parentvar.'" checked="checked"></td>';
                                                            }else{
                                                                echo '<input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="0"  class="create_hidden create_hide_'.$parentvar.'">
                                                                <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$parentvar.'"></td>';
                                                            }
                                                            echo '<td>';
                                                            if(isset($getmenuaccesschild) && $getmenuaccesschild->edit_crud == 1){
                                                                echo '<input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="'.$getmenuaccesschild->edit_crud.'" class="edit_hidden edit_hide_'.$parentvar.'">
                                                                <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$parentvar.'" checked="checked"></td>';
                                                            }else{
                                                                echo '<input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="0" class="edit_hidden edit_hide_'.$parentvar.'">
                                                                <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$parentvar.'"></td>';
                                                            }
                                                            echo '<td>';
                                                            if(isset($getmenuaccesschild) && $getmenuaccesschild->delete_crud == 1){
                                                                echo '<input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="'.$getmenuaccesschild->delete_crud.'" class="delete_hidden delete_hide_'.$parentvar.'">
                                                                <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$parentvar.'" checked="checked"></td>';
                                                            }else{
                                                                echo '<input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="0" class="delete_hidden delete_hide_'.$parentvar.'">
                                                                <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$parentvar.'"></td>';
                                                            }
                                                            echo '</tr>';
                                                        }
                                                    }else{
                                                        echo '<tr>
                                                        <td><input type="hidden" name="menuitem_'.$i.'" value="'.$gc->id.'">'.$gc->title.'</td>
                                                        <td>';
                                                        if(isset($getmenuaccess) && $getmenuaccess->view_crud == 1){
                                                            echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="'.$getmenuaccess->view_crud.'" class="view_hidden view_hide_'.$parentvar.'">
                                                            <input type="checkbox" checked="checked" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$parentvar.'"></td>';
                                                        }else{
                                                            echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="0" class="view_hidden view_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$parentvar.'"></td>';
                                                        }
                                                        echo '<td>';
                                                        if(isset($getmenuaccess) && $getmenuaccess->create_crud == 1){
                                                            echo '<input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="'.$getmenuaccess->create_crud.'"  class="create_hidden create_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$parentvar.'" checked="checked"></td>';
                                                        }else{
                                                            echo '<input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="0"  class="create_hidden create_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$parentvar.'"></td>';
                                                        }
                                                        echo '<td>';
                                                        if(isset($getmenuaccess) && $getmenuaccess->edit_crud == 1){
                                                            echo '<input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="'.$getmenuaccess->edit_crud.'"  class="edit_hidden edit_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$parentvar.'" checked="checked"></td>';
                                                        }else{
                                                            echo '<input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="0"  class="edit_hidden edit_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$parentvar.'"></td>';
                                                        }
                                                        echo '<td>';
                                                        if(isset($getmenuaccess) && $getmenuaccess->delete_crud == 1){
                                                            echo '<input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="'.$getmenuaccess->delete_crud.'"  class="delete_hidden delete_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$parentvar.'" checked="checked"></td>';
                                                        }else{
                                                            echo '<input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="0"  class="delete_hidden delete_hide_'.$parentvar.'">
                                                            <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$parentvar.'"></td>';
                                                        }
                                                        echo '</tr>';
                                                    }
                                                }
                                            }
                                        }else{
                                            //new record
                                            echo '<tr class="table-info">
                                            <td><input type="hidden" name="menuitem_'.$i.'" value="'.$gm->id.'">'.$gm->title.'</td>
                                            <td><input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="1">
                                            <input type="checkbox" class="viewparent" id="'.$i.'" checked="checked">
                                            </td>
                                            <td><input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="1">
                                            <input type="checkbox" class="createparent" id="'.$i.'">
                                            </td>
                                            <td><input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="1">
                                            <input type="checkbox" class="editparent" id="'.$i.'">
                                            </td>
                                            <td><input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="1">
                                            <input type="checkbox" class="deleteparent" id="'.$i.'">
                                            </td>
                                            </tr>';
                                            $classvar = $i;
                                            foreach($getchild as $gc){
                                                $i++;
                                                $check_child = TblMenu::find()->where(['parent_id'=>$gc->id])->andWhere(['status'=>1])->orderBy(['priority'=>SORT_ASC])->all();
                                                if(count($check_child)>0){
                                                    //has third level children
                                                    echo '<tr class="table-warning">
                                                    <td><input type="hidden" name="menuitem_'.$i.'" value="'.$gc->id.'">'.$gc->title.'</td>
                                                    <td><input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="1"  class="view_hidden view_hide_'.$classvar.'">
                                                    <input type="checkbox" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$classvar.'"></td>
                                                    <td><input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="1"  class="create_hidden create_hide_'.$classvar.'">
                                                    <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$classvar.'"></td>
                                                    <td><input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="1" class="edit_hidden edit_hide_'.$classvar.'">
                                                    <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$classvar.'"></td>
                                                    <td><input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="1" class="delete_hidden delete_hide_'.$classvar.'">
                                                    <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$classvar.'"></td>
                                                    </tr>';
                                                    foreach($check_child as $cc){
                                                        $i++;
                                                        echo '<tr>
                                                        <td style="padding-left:20px"><input type="hidden" name="menuitem_'.$i.'" value="'.$cc->id.'">'.$cc->title.'</td>
                                                        <td><input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="1"  class="view_hidden view_hide_'.$classvar.'">
                                                        <input type="checkbox" checked="checked" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$classvar.'"></td>
                                                        <td><input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="0"  class="create_hidden create_hide_'.$classvar.'">
                                                        <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$classvar.'"></td>
                                                        <td><input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="0" class="edit_hidden edit_hide_'.$classvar.'">
                                                        <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$classvar.'"></td>
                                                        <td><input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="0" class="delete_hidden delete_hide_'.$classvar.'">
                                                        <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$classvar.'"></td>
                                                        </tr>';
                                                    }
                                                }else{
                                                    //no third level children
                                                    echo '<tr>
                                                    <td><input type="hidden" name="menuitem_'.$i.'" value="'.$gc->id.'">'.$gc->title.'</td>
                                                    <td><input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="1"  class="view_hidden view_hide_'.$classvar.'">
                                                    <input type="checkbox" checked="checked" name="view_'.$i.'" id="view_'.$i.'" class="view viewmenu_'.$classvar.'"></td>
                                                    <td><input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="0"  class="create_hidden create_hide_'.$classvar.'">
                                                    <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create createmenu_'.$classvar.'"></td>
                                                    <td><input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="0" class="edit_hidden edit_hide_'.$classvar.'">
                                                    <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit editmenu_'.$classvar.'"></td>
                                                    <td><input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="0" class="delete_hidden delete_hide_'.$classvar.'">
                                                    <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete deletemenu_'.$classvar.'"></td>
                                                    </tr>';
                                                }
                                            }
                                        }
                                    }else{
                                        if(!$model->isNewRecord){
                                            $getmenuaccess = TblMenuAccess::find()->where(['fk_user_id'=>$model->id])->andWhere(['fk_menu_id'=>$gm->id,'status'=>1])->one();
                                            echo '<tr class="table-warning">
                                                <td><input type="hidden" name="menuitem_'.$i.'" value="'.$gm->id.'">'.$gm->title.'</td>
                                                <td>';
                                            if(isset($getmenuaccess) && $getmenuaccess->view_crud == 1){
                                                echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="'.$getmenuaccess->view_crud.'" class="view_hidden">
                                                <input type="checkbox" checked="checked" name="view_'.$i.'" id="view_'.$i.'" class="view"></td>';
                                            }else{
                                                echo '<input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="0" class="view_hidden">
                                                <input type="checkbox" name="view_'.$i.'" id="view_'.$i.'" class="view"></td>';
                                            }
                                            echo '<td>';
                                            if(isset($getmenuaccess) && $getmenuaccess->create_crud == 1){
                                                echo '<input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="'.$getmenuaccess->create_crud.'"  class="create_hidden">
                                                <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create" checked="checked"></td>';
                                            }else{
                                                echo '<input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="0"  class="create_hidden">
                                                <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create"></td>';
                                            }
                                            echo '<td>';
                                            if(isset($getmenuaccess) && $getmenuaccess->edit_crud == 1){
                                                echo '<input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="'.$getmenuaccess->edit_crud.'"  class="edit_hidden">
                                                <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit" checked="checked"></td>';
                                            }else{
                                                echo '<input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="0"  class="edit_hidden">
                                                <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit"></td>';
                                            }
                                            echo '<td>';
                                            if(isset($getmenuaccess) && $getmenuaccess->delete_crud == 1){
                                                echo '<input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="'.$getmenuaccess->delete_crud.'"  class="delete_hidden">
                                                <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete" checked="checked"></td>';
                                            }else{
                                                echo '<input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="0"  class="delete_hidden">
                                                <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete"></td>';
                                            }
                                            echo '</tr>';
                                        }else{
                                            echo '<tr class="table-warning">
                                            <td><input type="hidden" name="menuitem_'.$i.'" value="'.$gm->id.'">'.$gm->title.'</td>
                                            <td><input type="hidden" name="menuview_'.$i.'" id="menuview_'.$i.'" value="1"   class="view_hidden">
                                            <input type="checkbox" checked="checked" name="view_'.$i.'" id="view_'.$i.'" class="view"></td>
                                            <td><input type="hidden" name="menucreate_'.$i.'" id="menucreate_'.$i.'" value="1" class="create_hidden">
                                            <input type="checkbox" name="create_'.$i.'" id="create_'.$i.'" class="create"></td>
                                            <td><input type="hidden" name="menuedit_'.$i.'" id="menuedit_'.$i.'" value="1" class="edit_hidden">
                                            <input type="checkbox" name="edit_'.$i.'" id="edit_'.$i.'" class="edit"></td>
                                            <td><input type="hidden" name="menudelete_'.$i.'" id="menudelete_'.$i.'" value="1" class="delete_hidden">
                                            <input type="checkbox" name="delete_'.$i.'" id="delete_'.$i.'" class="delete"></td>
                                            </tr>';
                                    }	}
                                    $i++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Menu end -->

    <div class="card shadow rounded p-3 mt-4">
      <div class="row text-center">
        <?php
        if($model->isNewRecord){
          ?>
          <div class="d-grid gap-2 col-4 mx-auto pe-1">
            <input type="submit" name="new_update" value="Create & Edit" class="btn btn-info btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
            <input type="submit" name="new_new" value="Create & New" class="btn btn-primary btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto ps-1">
            <input type="submit" name="new_exit" value="Create & Exit" class="btn btn-secondary btn-sm"/>
          </div>
          <?php
        }else{
          ?>
          <div class="d-grid gap-2 col-4 mx-auto pe-1">
            <input type="submit" name="update" value="Update & Edit" class="btn btn-info btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto pe-1 ps-1">
            <input type="submit" name="new" value="Update & New" class="btn btn-primary btn-sm"/>
          </div>
          <div class="d-grid gap-2 col-4 mx-auto ps-1">
            <input type="submit" name="exit" value="Update & Exit" class="btn btn-secondary btn-sm"/>
          </div>
          <?php
        }
        ?>

      </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php
$this->registerJs('
	$(document).ready(function(){
		$(".view").on("click",function(){
			var id = $(this).attr("id");
			console.log(id);
			var pos = id.lastIndexOf("_");
			var indx = parseInt(pos)+1;
			var getpos =id.substring(indx);
			console.log(getpos);
 			if($(this).is(":checked")){
 				$("#menuview_"+getpos).val(1);
            }
            else if($(this).is(":not(:checked)")){
            	$("#menuview_"+getpos).val(0);
            }
        });
		$(".create").on("click",function(){
			var id = $(this).attr("id");
			console.log(id);
			var pos = id.lastIndexOf("_");
			var indx = parseInt(pos)+1;
			var getpos =id.substring(indx);
			console.log(getpos);
 			if($(this).is(":checked")){
 				$("#menucreate_"+getpos).val(1);
            }
            else if($(this).is(":not(:checked)")){
            	$("#menucreate_"+getpos).val(0);
            }
        });
		$(".edit").on("click",function(){
			var id = $(this).attr("id");
			console.log(id);
			var pos = id.lastIndexOf("_");
			var indx = parseInt(pos)+1;
			var getpos =id.substring(indx);
			console.log(getpos);
 			if($(this).is(":checked")){
 				$("#menuedit_"+getpos).val(1);
            }
            else if($(this).is(":not(:checked)")){
            	$("#menuedit_"+getpos).val(0);
            }
        });
        $(".delete").on("click",function(){
        var id = $(this).attr("id");
        console.log(id);
        var pos = id.lastIndexOf("_");
        var indx = parseInt(pos)+1;
        var getpos =id.substring(indx);
        console.log(getpos);
        if($(this).is(":checked")){
            $("#menudelete_"+getpos).val(1);
        }else if($(this).is(":not(:checked)")){
            $("#menudelete_"+getpos).val(0);
        }
        });
		$("#select_all_menu").click(function(){
			if($(this).is(":checked")){
 				$(".view").prop("checked", true);
				$(".create").prop("checked", true);
				$(".edit").prop("checked", true);
                $(".delete").prop("checked", true);
                $(".viewparent").prop("checked",true);
                $(".createparent").prop("checked",true);
                $(".editparent").prop("checked",true);
                $(".deleteparent").prop("checked",true);
				$(".view_hidden").val(1);
				$(".create_hidden").val(1);
				$(".edit_hidden").val(1);
                $(".delete_hidden").val(1);
            }else if($(this).is(":not(:checked)")){
                $(".view").prop("checked", false);
				$(".create").prop("checked", false);
				$(".edit").prop("checked", false);
                $(".delete").prop("checked", false);
                $(".viewparent").prop("checked",false);
                $(".createparent").prop("checked",false);
                $(".editparent").prop("checked",false);
                $(".deleteparent").prop("checked",false);
				$(".view_hidden").val(0);
				$(".create_hidden").val(0);
				$(".edit_hidden").val(0);
                $(".delete_hidden").val(0);
            }
		});
		$(".viewparent").click(function(){
			var id = $(this).attr("id");
			if($(this).is(":checked")){
 				$(".viewmenu_"+id).prop("checked", true);
                $("#menuview_"+id).val(1);
				$(".view_hide_"+id).val(1);
            }else if(
                $(this).is(":not(:checked)")){
				$(".viewmenu_"+id).prop("checked", false);
				$(".view_hide_"+id).val(0);
                $("#menuview_"+id).val(0);
            }
		});
		$(".createparent").click(function(){
			var id = $(this).attr("id");
			if($(this).is(":checked")){
 				$(".createmenu_"+id).prop("checked", true);
				$(".create_hide_"+id).val(1);
                $("#menucreate_"+id).val(1);
            }else if(
                $(this).is(":not(:checked)")){
				$(".createmenu_"+id).prop("checked", false);
				$(".create_hide_"+id).val(0);
                $("#menucreate_"+id).val(0);
            }
		});
		$(".editparent").click(function(){
			var id = $(this).attr("id");
			if($(this).is(":checked")){
 				$(".editmenu_"+id).prop("checked", true);
				$(".edit_hide_"+id).val(1);
                $("#menuedit_"+id).val(1);
            }else if(
                $(this).is(":not(:checked)")){
				$(".editmenu_"+id).prop("checked", false);
				$(".edit_hide_"+id).val(0);
                $("#menuedit_"+id).val(0);
            }
		});
        $(".deleteparent").click(function(){
			var id = $(this).attr("id");
			if($(this).is(":checked")){
 				$(".deletemenu_"+id).prop("checked", true);
				$(".delete_hide_"+id).val(1);
                $("#menudelete_"+id).val(1);
            }else if($(this).is(":not(:checked)")){
				$(".deletemenu_"+id).prop("checked", false);
				$(".delete_hide_"+id).val(0);
                $("#menudelete_"+id).val(0);
            }
		});
        $(".rview").on("click",function(){
            var id = $(this).attr("id");
            console.log(id);
            var pos = id.lastIndexOf("_");
            var indx = parseInt(pos)+1;
            var getpos =id.substring(indx);
            console.log(getpos);
            if($(this).is(":checked")){
                $("#reportview_"+getpos).val(1);
            }else if($(this).is(":not(:checked)")){
                $("#reportview_"+getpos).val(0);
            }
        });
        $("#select_all_report").click(function(){
        if($(this).is(":checked")){
            $(".rview").prop("checked", true);
            $(".reportviewparent").prop("checked",true);
            $(".report_view_hidden").val(1);
        }else if($(this).is(":not(:checked)")){
            $(".rview").prop("checked", false);
            $(".reportviewparent").prop("checked",false);
            $(".report_view_hidden").val(0);
        }
        });
        $(".reportviewparent").click(function(){
        var id = $(this).attr("id");
        if($(this).is(":checked")){
            $(".viewreport_"+id).prop("checked", true);
            $("#reportview_"+id).val(1);
            $(".report_view_hide_"+id).val(1);
        }else if(
            $(this).is(":not(:checked)")){
            $(".viewreport_"+id).prop("checked", false);
            $(".report_view_hide_"+id).val(0);
            $("#reportview_"+id).val(0);
        }
        });
	});
');
?>
