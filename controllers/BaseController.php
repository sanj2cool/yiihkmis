<?php
namespace app\controllers;

use yii\web\Controller;
use app\components\UserAccessControl;
use Yii;

class BaseController extends Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $menuUrl = Yii::$app->controller->id . '/' . $action->id;
        $actionType = $this->getActionType($action->id);

        // Check user access
        UserAccessControl::checkAccess($menuUrl, $actionType);

        return true;
    }

    private function getActionType($actionId)
    {
        $actionMap = [
            'index' => 'read',
            'view' => 'read',
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete'
        ];

        return $actionMap[$actionId] ?? 'read'; // Default to 'read' if action not mapped
    }
}
?>
