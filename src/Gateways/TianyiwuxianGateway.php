<?php

/*
 * This file is part of the shuxian/easy-sms.
 *
 * (c) shuxian <i@shuxian.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace shuxian\EasySms\Gateways;

use shuxian\EasySms\Contracts\MessageInterface;
use shuxian\EasySms\Contracts\PhoneNumberInterface;
use shuxian\EasySms\Exceptions\GatewayErrorException;
use shuxian\EasySms\Support\Config;
use shuxian\EasySms\Traits\HasHttpRequest;

/**
 * Class TianyiwuxianGateway.
 *
 * @author Darren Gao <realgaodacheng@gmail.com>
 */
class TianyiwuxianGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'http://jk.106api.cn/sms%s.aspx';

    const ENDPOINT_ENCODE = 'UTF8';

    const ENDPOINT_TYPE = 'send';

    const ENDPOINT_FORMAT = 'json';

    const SUCCESS_STATUS = 'success';

    const SUCCESS_CODE = '0';

    /**
     * @param \shuxian\EasySms\Contracts\PhoneNumberInterface $to
     * @param \shuxian\EasySms\Contracts\MessageInterface     $message
     * @param \shuxian\EasySms\Support\Config                 $config
     *
     * @return array
     *
     * @throws \shuxian\EasySms\Exceptions\GatewayErrorException ;
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $endpoint = $this->buildEndpoint();

        $params = [
            'gwid' => $config->get('gwid'),
            'type' => self::ENDPOINT_TYPE,
            'rece' => self::ENDPOINT_FORMAT,
            'mobile' => $to->getNumber(),
            'message' => $message->getContent($this),
            'username' => $config->get('username'),
            'password' => strtoupper(md5($config->get('password'))),
        ];

        $result = $this->post($endpoint, $params);

        $result = json_decode($result, true);

        if (self::SUCCESS_STATUS !== $result['returnstatus'] || self::SUCCESS_CODE !== $result['code']) {
            throw new GatewayErrorException($result['remark'], $result['code']);
        }

        return $result;
    }

    /**
     * Build endpoint url.
     *
     * @return string
     */
    protected function buildEndpoint()
    {
        return sprintf(self::ENDPOINT_TEMPLATE, self::ENDPOINT_ENCODE);
    }
}
