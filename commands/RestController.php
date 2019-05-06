<?php

namespace app\commands;


use app\models\Result;
use app\models\User;
use yii\console\Controller;
use yii\httpclient\Client;

class RestController extends Controller
{
    public $qty;

    public function actionRun()
    {
        $bankEndPoint = 'http://www.mocky.io/v2/5cd08bb9320000630000fd67';
        $users = User::find()->all();

        $qty = $this->qty || 1;

        $userSet = [];
        foreach ($users as $user) {
            if (count($userSet) == $qty) {
                $client = new Client();
                $client->createRequest()
                    ->setMethod('post')
                    ->setUrl($bankEndPoint)
                    ->setData(['dataSet'=>$userSet])
                    ->send();
                $userSet = [];
            } else {
                $totalUserSum = 0;
                $userSum = Result::find()->where(['user_id'=>$user->id])->all();
                foreach ($userSum as $record) {
                    $totalUserSum += $record->money ? $record->money : 0;
                }
                $userSet[] = ['id'=>$user->id, 'sum'=>$totalUserSum];
            }
        }
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'qty'
        ]);
    }
}