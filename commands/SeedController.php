<?php
namespace app\commands;

use yii\console\Controller;
use app\models\User;
use app\models\Award;

class SeedController extends Controller
{
    public function actionIndex()
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'admin';
        $user->token = 'token';
        $user->password = hash('sha256', 'admin');

        $user->save();

        $awards = [
            1=>'award#1',
            2=>'award#2',
            3=>'award#3'
        ];

        foreach ($awards as $key => $awardName) {
            $award = new Award();
            $award->id = $key;
            $award->title = $awardName;
            $award->save();
        }
    }
}
