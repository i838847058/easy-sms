<?php

/*
 * This file is part of the shuxian/easy-sms.
 *
 * (c) shuxian <i@shuxian.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace shuxian\EasySms\Tests;

use shuxian\EasySms\PhoneNumber;

/**
 * Class PhoneNumberTest.
 *
 * @author shuxian <i@shuxian.me>
 */
class PhoneNumberTest extends TestCase
{
    public function testOnlyNumber()
    {
        $n = new PhoneNumber(18888888888);
        $this->assertSame(18888888888, $n->getNumber());
        $this->assertNull($n->getIDDCode());
        $this->assertSame('18888888888', $n->getUniversalNumber());
        $this->assertSame('18888888888', $n->getZeroPrefixedNumber());
        $this->assertSame('18888888888', \strval($n));
    }

    public function testDiffCode()
    {
        $n = new PhoneNumber(18888888888, 68);
        $this->assertSame(68, $n->getIDDCode());

        $n = new PhoneNumber(18888888888, '+68');
        $this->assertSame(68, $n->getIDDCode());

        $n = new PhoneNumber(18888888888, '0068');
        $this->assertSame(68, $n->getIDDCode());
    }

    public function testJsonEncode()
    {
        $n = new PhoneNumber(18888888888, 68);
        $this->assertSame(json_encode(['number' => $n->getUniversalNumber()]), \json_encode(['number' => $n]));
    }
}
