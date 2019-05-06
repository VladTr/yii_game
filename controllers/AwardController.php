<?php

namespace app\controllers;

use Yii;
use app\models\Result;
use yii\web\Controller;
use yii\httpclient\Client;
use app\components\Convert;
use app\models\Award;
use app\models\User;

class AwardController extends Controller
{
    /*
     * send user money to the bank
     * @param $userId string
     * @return Response
     * */
    public function actionTransaction($userId=1)
    {
        //ToDo move to constant file
        $bankEndPoint = 'http://www.mocky.io/v2/5cd08bb9320000630000fd67';

        $moneyAwards = Result::find()
            ->where(['user_id'=>$userId])
            ->andWhere(['not', ['money' => null]])
            ->all();

        $totalAmount = 0;
        $deletedIDs = [];

        foreach ($moneyAwards as $award) {
            $deletedIDs[] = $award->id;
            $totalAmount += $award->money;
        }
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl($bankEndPoint)
            ->setData(['id' => $userId, 'amount' => $totalAmount])
            ->send();

        Result::deleteAll(['id' => $deletedIDs]);

        return $response->isOk ? $this->asJson(['transfered'=>$totalAmount]) : null;
    }

    /*
     * send letter to admin about user award
     * @param $userId string
     * @param $awardId string
     * @return Response
     * */
    public function actionSend($userId=1, $awardId=1)
    {
        $user = User::find()->where(['id'=>$userId])->one();
        $award = Award::find()->where(['id'=>$awardId])->one();

        Yii::$app->mailer->compose()
            ->setFrom('admin@domain.com')
            ->setTo('suzirie@gmail.com')
            ->setSubject('send award')
            ->setTextBody('you should send award'.$award->title.' to user: '.$user->username)
            ->send();

        //Result::deleteAll(['user_id'=>$userId, 'award_id'=>$awardId]);
        return $this->asJson(['sent'=>'OK']);
    }

    /*
     * user refuse its award
     * @param $userId string
     * @param $awardId string
     * @return Response
     * */
    public function actionRefuse($userId=1, $awardId=1)
    {
        $award = Result::findOne(['user_id'=>$userId, 'award_id'=>$awardId]);
        if ($award) {
            $award->delete();
            return $this->asJson(['refuse'=>true]);
        } else {
            return $this->asJson(['refuse'=>false]);
        }
    }

    /*
     * convert money to score
     * @param $resultId string
     * @return Response
     * */
    public function actionConvert($resultId=1)
    {

        $result = Result::find()
            ->where(['id'=>$resultId])
            ->andWhere(['not', ['money' => null]])
            ->one();

        if (!$result) {
            return $this->asJson(['convert'=>false]);
        }

        $resultScore = Convert::make($result->money);
        $result->score = $resultScore;
        $result->money = null;
        $result->save();
        return $this->asJson(['score'=>$resultScore]);
    }

}
