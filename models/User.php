<?php

namespace app\models;
use yii\db\ActiveRecord;


class User extends ActiveRecord implements \yii\web\IdentityInterface
{

    public static function findUser($username, $password)
    {
        $user = self::find()
            ->where([
                "username" => $username,
                "password" => hash('sha256', $password)
            ])->one();

        return $user;
    }

    public function getId()
    {
        return $this->id;
    }

    public static function tableName()
    {
        return 'user';
    }

    public static function primaryKey()
    {
        return ['id'];
    }

    public function validateAuthKey($attributeNames = null, $clearErrors = true)
    {
    }

    public function getAuthKey()
    {
    }

    public static function findIdentityByAccessToken($token, $type=null)
    {
    }

    public static function findIdentity($id)
    {
    }

}
