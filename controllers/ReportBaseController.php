<?php
namespace app\controllers;

use yii\web\Controller;
use app\components\ReportAccessControl;
use Yii;

class ReportBaseController extends Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $menuUrl = Yii::$app->controller->id . '/' . $action->id;


        // Check user access
        ReportAccessControl::checkAccess($menuUrl);

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
