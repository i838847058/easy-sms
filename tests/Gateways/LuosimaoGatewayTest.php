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

use XiaoYun\EasySms\Exceptions\GatewayErrorException;
use XiaoYun\EasySms\Gateways\LuosimaoGateway;
use XiaoYun\EasySms\Message;
use XiaoYun\EasySms\PhoneNumber;
use XiaoYun\EasySms\Support\Config;
use XiaoYun\EasySms\Tests\TestCase;

class LuosimaoGatewayTest extends TestCase
{
    public function testSend()
    {
        $config = [
            'api_key' => 'mock-api-key',
        ];
        $gateway = \Mockery::mock(LuosimaoGateway::class.'[post]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('post')->with('https://sms-api.luosimao.com/v1/send.json', [
            'mobile' => 18188888888,
            'message' => '【xiaoyun】This is a test message.',
        ], [
            'Authorization' => 'Basic '.base64_encode('api:key-mock-api-key'),
        ])->andReturn([
            'error' => 0,
            'msg' => 'success',
        ], [
            'error' => 10000,
            'msg' => 'mock-err-msg',
        ])->times(2);

        $message = new Message(['content' => '【xiaoyun】This is a test message.']);
        $config = new Config($config);

        $this->assertSame([
            'error' => 0,
            'msg' => 'success',
        ], $gateway->send(new PhoneNumber(18188888888), $message, $config));

        $this->expectException(GatewayErrorException::class);
        $this->expectExceptionCode(10000);
        $this->expectExceptionMessage('mock-err-msg');

        $gateway->send(new PhoneNumber(18188888888), $message, $config);
    }
}
