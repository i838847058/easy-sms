<?php

/*
 * This file is part of the xiaoyun/easy-sms.
 *
 * (c) xiaoyun <i@xiaoyun.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace XiaoYun\EasySms\Gateways;

use XiaoYun\EasySms\Contracts\MessageInterface;
use XiaoYun\EasySms\Contracts\PhoneNumberInterface;
use XiaoYun\EasySms\Exceptions\GatewayErrorException;
use XiaoYun\EasySms\Support\Config;
use XiaoYun\EasySms\Traits\HasHttpRequest;

/**
 * Class HuyiGateway.
 *
 * @see http://www.ihuyi.com/api/sms.html
 */
class HuyiGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'http://106.ihuyi.com/webservice/sms.php?method=Submit';

    const ENDPOINT_FORMAT = 'json';

    const SUCCESS_CODE = 2;

    /**
     * @param \XiaoYun\EasySms\Contracts\PhoneNumberInterface $to
     * @param \XiaoYun\EasySms\Contracts\MessageInterface     $message
     * @param \XiaoYun\EasySms\Support\Config                 $config
     *
     * @return array
     *
     * @throws \XiaoYun\EasySms\Exceptions\GatewayErrorException ;
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $params = [
            'account' => $config->get('api_id'),
            'mobile' => $to->getIDDCode() ? \sprintf('%s %s', $to->getIDDCode(), $to->getNumber()) : $to->getNumber(),
            'content' => $message->getContent($this),
            'time' => time(),
            'format' => self::ENDPOINT_FORMAT,
            'sign' => $config->get('signature'),
        ];

        $params['password'] = $this->generateSign($params);

        $result = $this->post(self::ENDPOINT_URL, $params);

        if (self::SUCCESS_CODE != $result['code']) {
            throw new GatewayErrorException($result['msg'], $result['code'], $result);
        }

        return $result;
    }

    /**
     * Generate Sign.
     *
     * @param array $params
     *
     * @return string
     */
    protected function generateSign($params)
    {
        return md5($params['account'].$this->config->get('api_key').$params['mobile'].$params['content'].$params['time']);
    }
}
