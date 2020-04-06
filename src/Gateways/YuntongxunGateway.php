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
 * Class YuntongxunGateway.
 *
 * @see http://www.yuntongxun.com/doc/rest/sms/3_2_2_2.html
 */
class YuntongxunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'https://%s:%s/%s/%s/%s/%s/%s?sig=%s';

    const SERVER_IP = 'app.cloopen.com';

    const DEBUG_SERVER_IP = 'sandboxapp.cloopen.com';

    const DEBUG_TEMPLATE_ID = 1;

    const SERVER_PORT = '8883';

    const SDK_VERSION = '2013-12-26';

    const SUCCESS_CODE = '000000';

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
        $datetime = date('YmdHis');

        $endpoint = $this->buildEndpoint('SMS', 'TemplateSMS', $datetime, $config);

        $result = $this->request('post', $endpoint, [
            'json' => [
                'to' => $to,
                'templateId' => (int) ($this->config->get('debug') ? self::DEBUG_TEMPLATE_ID : $message->getTemplate($this)),
                'appId' => $config->get('app_id'),
                'datas' => $message->getData($this),
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json;charset=utf-8',
                'Authorization' => base64_encode($config->get('account_sid').':'.$datetime),
            ],
        ]);

        if (self::SUCCESS_CODE != $result['statusCode']) {
            throw new GatewayErrorException($result['statusCode'], $result['statusCode'], $result);
        }

        return $result;
    }

    /**
     * Build endpoint url.
     *
     * @param string                           $type
     * @param string                           $resource
     * @param string                           $datetime
     * @param \Shuxian\EasySms\Support\Config $config
     *
     * @return string
     */
    protected function buildEndpoint($type, $resource, $datetime, Config $config)
    {
        $serverIp = $this->config->get('debug') ? self::DEBUG_SERVER_IP : self::SERVER_IP;

        $accountType = $this->config->get('is_sub_account') ? 'SubAccounts' : 'Accounts';

        $sig = strtoupper(md5($config->get('account_sid').$config->get('account_token').$datetime));

        return sprintf(self::ENDPOINT_TEMPLATE, $serverIp, self::SERVER_PORT, self::SDK_VERSION, $accountType, $config->get('account_sid'), $type, $resource, $sig);
    }
}
