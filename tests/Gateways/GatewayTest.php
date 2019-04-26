<?php

/*
 * This file is part of the xiaoyun/easy-sms.
 *
 * (c) xiaoyun <i@xiaoyun.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace XiaoYun\EasySms\Tests\Gateways;

use XiaoYun\EasySms\Contracts\MessageInterface;
use XiaoYun\EasySms\Contracts\PhoneNumberInterface;
use XiaoYun\EasySms\Gateways\Gateway;
use XiaoYun\EasySms\Support\Config;
use XiaoYun\EasySms\Tests\TestCase;

class GatewayTest extends TestCase
{
    public function testTimeout()
    {
        $gateway = new DummyGatewayForGatewayTest(['foo' => 'bar']);

        $this->assertInstanceOf(Config::class, $gateway->getConfig());
        $this->assertSame(5.0, $gateway->getTimeout());
        $gateway->setTimeout(4.0);
        $this->assertSame(4.0, $gateway->getTimeout());

        $gateway = new DummyGatewayForGatewayTest(['foo' => 'bar', 'timeout' => 12.0]);
        $this->assertSame(12.0, $gateway->getTimeout());
    }

    public function testConfigSetterAndGetter()
    {
        $gateway = new DummyGatewayForGatewayTest(['foo' => 'bar']);

        $this->assertInstanceOf(Config::class, $gateway->getConfig());

        $config = new Config(['name' => 'xiaoyun']);
        $this->assertSame($config, $gateway->setConfig($config)->getConfig());
    }
}

class DummyGatewayForGatewayTest extends Gateway
{
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        return 'mock-result';
    }
}
