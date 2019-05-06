<?php

namespace app\models;

use yii\db\ActiveRecord;

class Result extends ActiveRecord
{

    public function handleAward($userId)
    {
        $awards = $this->find()
            ->where(['user_id'=>$userId])
            ->andWhere(['not', ['award_id' => null]])
            ->all();

        if (count($awards) > 3) {
            return false;
        }

        $award = Award::find()->where(['id' => rand(1, 3)])->one();
        $this->user_id = $userId;
        $this->award_id = $award->id;

        $this->save();

        return $award->title;
    }

    public function handleScores($userId)
    {
        $this->user_id = $userId;
        $result = rand(1, 1000);
        $this->score = $result;

        $this->save();

        return $result;
    }

    public function handleMoney($userId)
    {
        $result = rand(1, 100);
        $this->user_id = $userId;
        $this->money = $result;

        $allUserMoney = $this->find()
            ->where(['user_id'=>$userId])
            ->andWhere(['not', ['money' => null]])
            ->all();

        $totalAmount = 0;

        foreach ($allUserMoney as $record) {
            $totalAmount += $record->money;
        }

        if ($totalAmount + $result > 999999) {
            return false;
        }

        $this->save();
        return $result;

    }

    public static function tableName()
    {
        return 'result';
    }
}