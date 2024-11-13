<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\MyClass;

class MyClassTest extends TestCase
{
    public function testAdd()
    {
        $obj = new MyClass();
        $this->assertEquals(4, $obj->add(2, 2));
    }
}
