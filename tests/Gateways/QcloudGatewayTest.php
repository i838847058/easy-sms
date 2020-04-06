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

use shuxian\EasySms\Contracts\GatewayInterface;
use shuxian\EasySms\Exceptions\GatewayErrorException;
use shuxian\EasySms\Gateways\QcloudGateway;
use shuxian\EasySms\Message;
use shuxian\EasySms\PhoneNumber;
use shuxian\EasySms\Support\Config;
use shuxian\EasySms\Tests\TestCase;

class QcloudGatewayTest extends TestCase
{
    public function testSend()
    {
        $config = [
            'sdk_app_id' => 'mock-sdk-app-id',
            'app_key' => 'mock-api-key',
        ];
        $gateway = \Mockery::mock(QcloudGateway::class.'[request]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('request')
                ->andReturn([
                    'result' => 0,
                    'errmsg' => 'OK',
                    'ext' => '',
                    'sid' => 3310228982,
                    'fee' => 1,
                ], [
                    'result' => 1001,
                    'errmsg' => 'sig校验失败',
                ])->twice();

        $message = new Message(['data' => ['type' => 0], 'content' => 'This is a test message.']);

        $config = new Config($config);

        $this->assertSame([
            'result' => 0,
            'errmsg' => 'OK',
            'ext' => '',
            'sid' => 3310228982,
            'fee' => 1,
        ], $gateway->send(new PhoneNumber(18888888888), $message, $config));

        $this->expectException(GatewayErrorException::class);
        $this->expectExceptionCode(1001);
        $this->expectExceptionMessage('sig校验失败');

        $gateway->send(new PhoneNumber(18888888888), $message, $config);
    }

    public function testSendUsingNationCode()
    {
        $config = [
            'sdk_app_id' => 'mock-sdk-app-id',
            'app_key' => 'mock-api-key',
        ];
        $gateway = \Mockery::mock(QcloudGateway::class.'[request]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('request')
            ->andReturn([
                'result' => 0,
                'errmsg' => 'OK',
                'ext' => '',
                'sid' => 3310228982,
                'fee' => 1,
            ], [
                'result' => 1001,
                'errmsg' => 'sig校验失败',
            ])->twice();

        $message = new Message(['data' => ['type' => 0], 'content' => 'This is a test message.']);

        $config = new Config($config);

        $this->assertSame([
            'result' => 0,
            'errmsg' => 'OK',
            'ext' => '',
            'sid' => 3310228982,
            'fee' => 1,
        ], $gateway->send(new PhoneNumber(18888888888, 251), $message, $config));

        $this->expectException(GatewayErrorException::class);
        $this->expectExceptionCode(1001);
        $this->expectExceptionMessage('sig校验失败');

        $gateway->send(new PhoneNumber(18888888888, 251), $message, $config);
    }

    public function testSendUsingTemplate()
    {
        $config = [
            'sdk_app_id' => 'mock-sdk-app-id',
            'app_key' => 'mock-api-key',
        ];
        $gateway = \Mockery::mock(QcloudGateway::class.'[request]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('request')
                ->with(\Mockery::any(), \Mockery::any(), \Mockery::subset([
                    'json' => [
                        'tpl_id' => 'template-id',
                        'params' => [
                            'param1',
                            'param2',
                        ],
                    ],
                ]))
                ->andReturn([
                    'result' => 0,
                    'errmsg' => 'OK',
                    'ext' => '',
                    'sid' => 3310228982,
                    'fee' => 1,
                ], [
                    'result' => 1001,
                    'errmsg' => 'sig校验失败',
                ])->twice();

        $message = \Mockery::mock(Message::class);
        $message->allows()->getTemplate()->withArgs([GatewayInterface::class])->andReturns('template-id');
        $message->allows()->getData()->withArgs([GatewayInterface::class])->andReturns(['param1', 'param2', 'sign_name' => 'sign']);
        $message->allows()->getContent()->withArgs([GatewayInterface::class])->andReturns(null);

        $config = new Config($config);

        $this->assertSame([
            'result' => 0,
            'errmsg' => 'OK',
            'ext' => '',
            'sid' => 3310228982,
            'fee' => 1,
        ], $gateway->send(new PhoneNumber(18888888888), $message, $config));

        $this->expectException(GatewayErrorException::class);
        $this->expectExceptionCode(1001);
        $this->expectExceptionMessage('sig校验失败');

        $gateway->send(new PhoneNumber(18888888888), $message, $config);
    }
}
