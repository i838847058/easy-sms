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
 * Class YunpianGateway.
 *
 * @see https://www.yunpian.com/doc/zh_CN/intl/single_send.html
 */
class YunpianGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'https://%s.yunpian.com/%s/%s/%s.%s';

    const ENDPOINT_VERSION = 'v2';

    const ENDPOINT_FORMAT = 'json';

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
        $endpoint = $this->buildEndpoint('sms', 'sms', 'single_send');

        $signature = $config->get('signature', '');

        $content = $message->getContent($this);

        $result = $this->request('post', $endpoint, [
            'form_params' => [
                'apikey' => $config->get('api_key'),
                'mobile' => $to->getUniversalNumber(),
                'text' => 0 === \stripos($content, '【') ? $content : $signature.$content,
            ],
            'exceptions' => false,
        ]);

        if ($result['code']) {
            throw new GatewayErrorException($result['msg'], $result['code'], $result);
        }

        return $result;
    }

    /**
     * Build endpoint url.
     *
     * @param string $type
     * @param string $resource
     * @param string $function
     *
     * @return string
     */
    protected function buildEndpoint($type, $resource, $function)
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $type, self::ENDPOINT_VERSION, $resource, $function, self::ENDPOINT_FORMAT);
    }
}
