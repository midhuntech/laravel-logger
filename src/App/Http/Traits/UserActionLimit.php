<?php

namespace midhuntech\LaravelLogger\App\Http\Traits;

use App\Helpers\GlobalUserHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use midhuntech\LaravelLogger\App\Http\Traits\ActivityLogger;

/**
 * Observable trait
 *
 * @package App\Traits
 */
trait UserActionLimit
{
    public static function checkActionLimit(string $activity = null)
    {
        if($activity == null) {
            $activity = self::getActivity();
            if(!$activity) return true;
        }
        $user = GlobalUserHelper::user();
        $userId = $user['id'];
        $activity = DB::connection('logger')->table('activity')->where(['activity' => $activity])->first();
        if ($activity) {
            $userActivity = DB::connection('logger')->table('user_activity_limit')->where(['activity_id' => $activity->id, 'user_uid' => $userId])->first();
            if ($userActivity) {
                $currentLimit = $userActivity->current_limit + 1;
                $limit = $userActivity->limit;
                if ($currentLimit <= $limit) {
                    $whereData = ['activity_id' => $activity->id, 'user_uid' => $userId];
                    $updateData = ['current_limit' => $currentLimit];
                    DB::connection('logger')->table('user_activity_limit')->where($whereData)->update($updateData);
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }
    public static function getActivity() {
        $routePath = Request::path();
        $activity = DB::connection('logger')->table('activity')->where(['path' => $routePath])->first();
        if($activity) {
            return $activity->activity;
        } else {
            return false;
        }
    }

}
