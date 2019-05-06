<?php

namespace app\components;

class Convert
{
    public static function make($money)
    {
        $ratio = 10;
        return $money * $ratio;
    }
}