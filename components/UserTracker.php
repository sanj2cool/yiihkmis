<?php
namespace app\components;

use Yii;
use yii\base\Component;

class UserTracker extends Component
{
    public static function logAction($action)
    {
        $session = Yii::$app -> session;
        $userId = $session -> get('userId') ?? 0; // Handle guest users
        $locationId = $session->get('userCompany') ?? 0;
        // $userId = Yii::$app->user->id ?? 0; // Handle guest users
        $pageUrl = Yii::$app->request->url;
        $timestamp = date('Y-m-d H:i:s');
        $ip = Yii::$app -> getRequest() -> getUserIp();
        // $this -> crt_by = $session -> get('userId');
        $crt_time = date('Y-m-d H:i:s');
        // Save to the database
        Yii::$app->db->createCommand()->insert('tbl_user_behavior', [
            'fk_user_id' => $userId,
            'fk_location_id' => $locationId,
            'page_url' => $pageUrl,
            'action' => $action,
            'timestamp' => $timestamp,
            'ip' => $ip,
            'crt_by' => $userId,
            'crt_time' => $crt_time
        ])->execute();
    }
}
