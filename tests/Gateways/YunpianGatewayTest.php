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

use shuxian\EasySms\Exceptions\GatewayErrorException;
use shuxian\EasySms\Gateways\YunpianGateway;
use shuxian\EasySms\Message;
use shuxian\EasySms\PhoneNumber;
use shuxian\EasySms\Support\Config;
use shuxian\EasySms\Tests\TestCase;

class YunpianGatewayTest extends TestCase
{
    public function testSend()
    {
        $config = [
            'api_key' => 'mock-api-key',
        ];
        $gateway = \Mockery::mock(YunpianGateway::class.'[request]', [$config])->shouldAllowMockingProtectedMethods();

        $gateway->shouldReceive('request')->with('post', 'https://sms.yunpian.com/v2/sms/single_send.json', [
            'form_params' => [
                'apikey' => 'mock-api-key',
                'mobile' => '18188888888',
                'text' => '【shuxian】This is a test message.',
            ],
            'exceptions' => false,
        ])->andReturn([
            'code' => 0,
            'msg' => '发送成功',
            'count' => 1, //成功发送的短信计费条数
            'fee' => 0.05,    //扣费条数，70个字一条，超出70个字时按每67字一条计
            'unit' => 'RMB',  // 计费单位
            'mobile' => '18188888888', // 发送手机号
            'sid' => 3310228982,   // 短信ID
        ], [
            'code' => 100,
            'msg' => '发送失败',
        ])->times(2);

        $message = new Message(['content' => '【shuxian】This is a test message.']);
        $config = new Config($config);
        $this->assertSame([
            'code' => 0,
            'msg' => '发送成功',
            'count' => 1, //成功发送的短信计费条数
            'fee' => 0.05,    //扣费条数，70个字一条，超出70个字时按每67字一条计
            'unit' => 'RMB',  // 计费单位
            'mobile' => '18188888888', // 发送手机号
            'sid' => 3310228982,   // 短信ID
        ], $gateway->send(new PhoneNumber(18188888888), $message, $config));

        $this->expectException(GatewayErrorException::class);
        $this->expectExceptionCode(100);
        $this->expectExceptionMessage('发送失败');

        $gateway->send(new PhoneNumber(18188888888), $message, $config);
    }

    public function testDefaultSignature()
    {
        $config = [
            'api_key' => 'mock-api-key',
            'signature' => '【测试】',
        ];
        $response = [
            'code' => 0,
            'msg' => '发送成功',
            'count' => 1, //成功发送的短信计费条数
            'fee' => 0.05,    //扣费条数，70个字一条，超出70个字时按每67字一条计
            'unit' => 'RMB',  // 计费单位
            'mobile' => '18188888888', // 发送手机号
            'sid' => 3310228982,   // 短信ID
        ];

        $gateway = \Mockery::mock(YunpianGateway::class.'[request]', [$config])->shouldAllowMockingProtectedMethods();
        $config = new Config($config);
        $gateway->shouldReceive('request')->with('post', 'https://sms.yunpian.com/v2/sms/single_send.json', [
            'form_params' => [
                'apikey' => 'mock-api-key',
                'mobile' => '18188888888',
                'text' => '【测试】This is a 【test】 message.',
            ],
            'exceptions' => false,
        ])->andReturn($response);

        $this->assertSame($response, $gateway->send(new PhoneNumber(18188888888), new Message(['content' => 'This is a 【test】 message.']), $config));

        // with signature
        $gateway->shouldReceive('request')->with('post', 'https://sms.yunpian.com/v2/sms/single_send.json', [
            'form_params' => [
                'apikey' => 'mock-api-key',
                'mobile' => '18188888888',
                'text' => '【已经存在】This is a 【test】 message.',
            ],
            'exceptions' => false,
        ])->andReturn($response);

        $this->assertSame($response, $gateway->send(new PhoneNumber(18188888888), new Message(['content' => '【已经存在】This is a 【test】 message.']), $config));
    }
}
