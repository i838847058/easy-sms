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
use XiaoYun\EasySms\Gateways\SubmailGateway;
use XiaoYun\EasySms\Message;
use XiaoYun\EasySms\PhoneNumber;
use XiaoYun\EasySms\Support\Config;
use XiaoYun\EasySms\Tests\TestCase;

class SubmailGatewayTest extends TestCase
{
    public function testSend()
    {
        $config = [
            'app_id' => 'mock-app-id',
            'app_key' => 'mock-app-key',
            'project' => 'mock-project',
        ];
        $gateway = \Mockery::mock(SubmailGateway::class.'[post]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('post')->with('https://api.mysubmail.com/message/xsend.json', [
            'appid' => 'mock-app-id',
            'signature' => 'mock-app-key',
            'project' => 'mock-project',
            'to' => new PhoneNumber(18188888888),
            'vars' => json_encode(['code' => '123456', 'time' => '15']),
        ])->andReturn([
            'status' => 'success',
            'send_id' => '093c0a7df143c087d6cba9cdf0cf3738',
            'fee' => 1,
            'sms_credits' => 14197,
        ], [
            'status' => 'error',
            'code' => 100,
            'msg' => 'mock-err-msg',
        ])->times(2);

        $message = new Message(['data' => ['code' => '123456', 'time' => '15']]);
        $config = new Config($config);

        $this->assertSame([
            'status' => 'success',
            'send_id' => '093c0a7df143c087d6cba9cdf0cf3738',
            'fee' => 1,
            'sms_credits' => 14197,
        ], $gateway->send(new PhoneNumber(18188888888), $message, $config));

        $this->expectException(GatewayErrorException::class);
        $this->expectExceptionCode(100);
        $this->expectExceptionMessage('mock-err-msg');

        $gateway->send(new PhoneNumber(18188888888), $message, $config);
    }

    public function testProject()
    {
        $config = [
            'app_id' => 'mock-app-id',
            'app_key' => 'mock-app-key',
            // no project id
        ];
        $gateway = \Mockery::mock(SubmailGateway::class.'[post]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('post')->with('https://api.mysubmail.com/message/xsend.json', [
            'appid' => 'mock-app-id',
            'signature' => 'mock-app-key',
            'project' => 'mock-project',
            'to' => new PhoneNumber(18188888888),
            'vars' => json_encode(['code' => '123456', 'time' => '15', 'project' => 'mock-project']),
        ])->andReturn([
            'status' => 'success',
            'send_id' => '093c0a7df143c087d6cba9cdf0cf3738',
            'fee' => 1,
            'sms_credits' => 14197,
        ]);

        $message = new Message(['data' => ['code' => '123456', 'time' => '15', 'project' => 'mock-project']]);
        $config = new Config($config);

        $this->assertSame([
            'status' => 'success',
            'send_id' => '093c0a7df143c087d6cba9cdf0cf3738',
            'fee' => 1,
            'sms_credits' => 14197,
        ], $gateway->send(new PhoneNumber(18188888888), $message, $config));
    }

    public function testEndpointChina()
    {
        $config = [
            'app_id' => 'mock-app-id',
            'app_key' => 'mock-app-key',
            'project' => 'mock-project',
        ];
        $gateway = \Mockery::mock(SubmailGateway::class.'[post]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('post')->with('https://api.mysubmail.com/message/xsend.json', [
            'appid' => 'mock-app-id',
            'signature' => 'mock-app-key',
            'project' => 'mock-project',
            'to' => new PhoneNumber(18188888888, 86),
            'vars' => json_encode(['code' => '123456', 'time' => '15']),
        ])->andReturn([
            'status' => 'success',
            'send_id' => '093c0a7df143c087d6cba9cdf0cf3738',
            'fee' => 1,
            'sms_credits' => 14197,
        ]);

        $message = new Message(['data' => ['code' => '123456', 'time' => '15']]);
        $config = new Config($config);

        $this->assertSame([
            'status' => 'success',
            'send_id' => '093c0a7df143c087d6cba9cdf0cf3738',
            'fee' => 1,
            'sms_credits' => 14197,
        ], $gateway->send(new PhoneNumber(18188888888, 86), $message, $config));
    }

    public function testEndpointInternational()
    {
        $config = [
            'app_id' => 'mock-app-id',
            'app_key' => 'mock-app-key',
            'project' => 'mock-project',
        ];
        $gateway = \Mockery::mock(SubmailGateway::class.'[post]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('post')->with('https://api.mysubmail.com/internationalsms/xsend.json', [
            'appid' => 'mock-app-id',
            'signature' => 'mock-app-key',
            'project' => 'mock-project',
            'to' => new PhoneNumber(18188888888, 1),
            'vars' => json_encode(['code' => '123456', 'time' => '15']),
        ])->andReturn([
            'status' => 'success',
            'send_id' => '093c0a7df143c087d6cba9cdf0cf3738',
            'fee' => 1,
            'sms_credits' => 14197,
        ]);

        $message = new Message(['data' => ['code' => '123456', 'time' => '15']]);
        $config = new Config($config);

        $this->assertSame([
            'status' => 'success',
            'send_id' => '093c0a7df143c087d6cba9cdf0cf3738',
            'fee' => 1,
            'sms_credits' => 14197,
        ], $gateway->send(new PhoneNumber(18188888888, 1), $message, $config));
    }
}
