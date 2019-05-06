<?php

namespace app\models;

use yii\db\ActiveRecord;

class Award extends ActiveRecord
{
    public static function tableName()
    {
        return 'award';
    }
}