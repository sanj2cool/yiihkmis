<?php
namespace app\components;

use Yii;
use yii\base\Component;
use app\models\TblMenuAccess;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;

class UserAccessControl extends Component
{
    public static function checkAccess($menuUrl, $action)
    {
      // Prevent redirect loop: Allow access to login page
        if (Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'login') {
            return;
        }
        $session = Yii::$app->session;
        $user = $session->get('userId');
        // $user = Yii::$app->user->identity;
        if (!$user) {

            // throw new ForbiddenHttpException('You need to login first.');
            Yii::$app->response->redirect(Url::to(['/site/login']))->send();
            exit;
        }

        // $refurl = strtok($menuUrl, '?');
        // Extract the controller name only (ignore the action)
        $controller = Yii::$app->controller->id;  // e.g., 'customer'
        $refurl = $controller.'/index';
        // die();
        // Fetch menu ID based on the requested URL
        $menu = \app\models\TblMenu::findOne(['url' => $refurl,'status'=>1]);
        if (!$menu) {
            // throw new ForbiddenHttpException('Menu not found.');
        }else{
          //----MENU ITEM FOUND ------
          // Fetch user permissions
          $access = TblMenuAccess::findOne(['fk_user_id' => $user, 'fk_menu_id' => $menu->id,'status'=>1]);

          if (!$access) {
              throw new ForbiddenHttpException('Access Denied.');
          }

          switch ($action) {
              case 'create':
                  if (!$access->create_crud) {
                      throw new ForbiddenHttpException('You do not have permission to create.');
                  }
                  break;
              case 'read':
                  if (!$access->view_crud) {
                      throw new ForbiddenHttpException('You do not have permission to view this page.');
                  }
                  break;
              case 'update':
                  if (!$access->edit_crud) {
                      throw new ForbiddenHttpException('You do not have permission to update.');
                  }
                  break;
              case 'delete':
                  if (!$access->delete_crud) {
                      throw new ForbiddenHttpException('You do not have permission to delete.');
                  }
                  break;
          }
        }


    }
    public static function can($menuUrl, $action)
      {
        $session = Yii::$app->session;
        $user = $session->get('userId');
        // $user = Yii::$app->user->identity;
        if (!$user) {

            // throw new ForbiddenHttpException('You need to login first.');
            Yii::$app->response->redirect(Url::to(['/site/login']))->send();
            exit;
        }
          $driverId = $user;
          $menu = \app\models\TblMenu::find()->where(['url' => $menuUrl,'status'=>1])->one();

          if (!$menu) return false;

          $access = \app\models\TblMenuAccess::findOne([
              'fk_user_id' => $driverId,
              'fk_menu_id' => $menu->id,
              'status'=>1
          ]);

          if (!$access) return false;

          switch ($action) {
              case 'create': return (bool)$access->create_crud;
              case 'edit':   return (bool)$access->edit_crud;
              case 'delete': return (bool)$access->delete_crud;
              case 'view':   return (bool)$access->view_crud;
          }
          return false;
      }
}
?>
