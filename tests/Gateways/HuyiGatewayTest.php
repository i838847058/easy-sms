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
use XiaoYun\EasySms\Gateways\HuyiGateway;
use XiaoYun\EasySms\Message;
use XiaoYun\EasySms\PhoneNumber;
use XiaoYun\EasySms\Support\Config;
use XiaoYun\EasySms\Tests\TestCase;

class HuyiGatewayTest extends TestCase
{
    public function testSend()
    {
        $config = [
            'api_id' => 'mock-api-id',
            'api_key' => 'mock-api-key',
        ];
        $gateway = \Mockery::mock(HuyiGateway::class.'[post]', [$config])->shouldAllowMockingProtectedMethods();

        $params = [
            'account' => 'mock-api-id',
            'mobile' => 18188888888,
            'content' => 'This is a test message.',
            'format' => 'json',
        ];
        $gateway->shouldReceive('post')->with('http://106.ihuyi.com/webservice/sms.php?method=Submit', \Mockery::subset($params))
            ->andReturn([
                'code' => HuyiGateway::SUCCESS_CODE,
                'msg' => 'mock-result',
            ], [
                'code' => 1234,
                'msg' => 'mock-err-msg',
            ])->times(2);

        $message = new Message(['content' => 'This is a test message.']);
        $config = new Config($config);

        $this->assertSame([
            'code' => HuyiGateway::SUCCESS_CODE,
            'msg' => 'mock-result',
        ], $gateway->send(new PhoneNumber(18188888888), $message, $config));

        $this->expectException(GatewayErrorException::class);
        $this->expectExceptionCode(1234);
        $this->expectExceptionMessage('mock-err-msg');

        $gateway->send(new PhoneNumber(18188888888), $message, $config);
    }
}
