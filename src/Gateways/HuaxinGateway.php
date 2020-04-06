<?php

/*
 * This file is part of the shuxian/easy-sms.
 *
 * (c) shuxian <i@shuxian.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shuxian\EasySms\Gateways;

use Shuxian\EasySms\Contracts\MessageInterface;
use Shuxian\EasySms\Contracts\PhoneNumberInterface;
use Shuxian\EasySms\Exceptions\GatewayErrorException;
use Shuxian\EasySms\Support\Config;
use Shuxian\EasySms\Traits\HasHttpRequest;

/**
 * Class HuaxinGateway.
 *
 * @see http://www.ipyy.com/help/
 */
class HuaxinGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'http://%s/smsJson.aspx';

    /**
     * @param \Shuxian\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Shuxian\EasySms\Contracts\MessageInterface     $message
     * @param \Shuxian\EasySms\Support\Config                 $config
     *
     * @return array
     *
     * @throws \Shuxian\EasySms\Exceptions\GatewayErrorException ;
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $endpoint = $this->buildEndpoint($config->get('ip'));

        $result = $this->post($endpoint, [
            'userid' => $config->get('user_id'),
            'account' => $config->get('account'),
            'password' => $config->get('password'),
            'mobile' => $to->getNumber(),
            'content' => $message->getContent($this),
            'sendTime' => '',
            'action' => 'send',
            'extno' => $config->get('ext_no'),
        ]);

        if ('Success' !== $result['returnstatus']) {
            throw new GatewayErrorException($result['message'], 400, $result);
        }

        return $result;
    }

    /**
     * Build endpoint url.
     *
     * @param string $ip
     *
     * @return string
     */
    protected function buildEndpoint($ip)
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $ip);
    }
}
