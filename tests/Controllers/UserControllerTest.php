<?php

namespace Controllers;

use App\Controllers\UserController;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{

    public function testTest(): void
    {
        $expectResult = 4;

        $result = 2 + 2;

        $this->assertEquals($expectResult, $result);
    }

}
