<?php

namespace Controllers;

use App\Controllers\BlogController;
use PHPUnit\Framework\TestCase;

class BlogControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testTest(): void
    {
        $expectResult = 4;

        $result = 2 + 2;

        $this->assertEquals($expectResult, $result);
    }

}
