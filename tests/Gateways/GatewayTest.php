<?php

/*
 * This file is part of the shuxian/easy-sms.
 *
 * (c) shuxian <i@shuxian.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace shuxian\EasySms\Tests\Gateways;

use shuxian\EasySms\Contracts\MessageInterface;
use shuxian\EasySms\Contracts\PhoneNumberInterface;
use shuxian\EasySms\Gateways\Gateway;
use shuxian\EasySms\Support\Config;
use shuxian\EasySms\Tests\TestCase;

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

        $config = new Config(['name' => 'shuxian']);
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
