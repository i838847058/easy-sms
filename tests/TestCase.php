<?php

/*
 * This file is part of the xiaoyun/easy-sms.
 *
 * (c) xiaoyun <i@xiaoyun.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace XiaoYun\EasySms\Tests;

use Mockery;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        Mockery::globalHelpers();
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
