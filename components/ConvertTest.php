<?php

namespace app\components;

use PHPUnit\Framework\TestCase;

require_once 'Convert.php';

class ConvertTest extends TestCase
{
    public function testTrue()
    {
        $money = 100;
        $scores = Convert::make($money);

        $this->assertEquals(1000, $scores);
    }
}