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
use XiaoYun\EasySms\Gateways\AvatardataGateway;
use XiaoYun\EasySms\Message;
use XiaoYun\EasySms\PhoneNumber;
use XiaoYun\EasySms\Support\Config;
use XiaoYun\EasySms\Tests\TestCase;

class AvatardataGatewayTest extends TestCase
{
    public function testSend()
    {
        $config = [
            'app_key' => 'mock-key',
        ];
        $gateway = \Mockery::mock(AvatardataGateway::class.'[get]', [$config])->shouldAllowMockingProtectedMethods();

        $params = [
            'mobile' => 18888888888,
            'templateId' => 'mock-tpl-id',
            'param' => implode(',', ['1234']),
            'dtype' => AvatardataGateway::ENDPOINT_FORMAT,
            'key' => 'mock-key',
        ];
        $gateway->shouldReceive('get')->with(AvatardataGateway::ENDPOINT_URL, $params)
            ->andReturn([
                'reason' => 'Success',
                'error_code' => 0,
            ], [
                'reason' => '错误的请求KEY',
                'error_code' => 10001,
            ])->times(2);

        $message = new Message([
            'content' => 'This is a test message.',
            'template' => 'mock-tpl-id',
            'data' => [
                '1234',
            ],
        ]);

        $config = new Config($config);

        $this->assertSame([
            'reason' => 'Success',
            'error_code' => 0,
        ], $gateway->send(new PhoneNumber(18888888888), $message, $config));

        $this->expectException(GatewayErrorException::class);
        $this->expectExceptionCode(10001);
        $this->expectExceptionMessage('错误的请求KEY');

        $gateway->send(new PhoneNumber(18888888888), $message, $config);
    }
}
