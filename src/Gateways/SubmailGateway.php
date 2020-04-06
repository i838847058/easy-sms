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
 * Class SubmailGateway.
 *
 * @see https://www.mysubmail.com/chs/documents/developer/index
 */
class SubmailGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'https://api.mysubmail.com/%s.%s';

    const ENDPOINT_FORMAT = 'json';

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
        $endpoint = $this->buildEndpoint($this->inChineseMainland($to) ? 'message/xsend' : 'internationalsms/xsend');

        $data = $message->getData($this);

        $result = $this->post($endpoint, [
            'appid' => $config->get('app_id'),
            'signature' => $config->get('app_key'),
            'project' => !empty($data['project']) ? $data['project'] : $config->get('project'),
            'to' => $to->getUniversalNumber(),
            'vars' => json_encode($data, JSON_FORCE_OBJECT),
        ]);

        if ('success' != $result['status']) {
            throw new GatewayErrorException($result['msg'], $result['code'], $result);
        }

        return $result;
    }

    /**
     * Build endpoint url.
     *
     * @param string $function
     *
     * @return string
     */
    protected function buildEndpoint($function)
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $function, self::ENDPOINT_FORMAT);
    }

    /**
     * Check if the phone number belongs to chinese mainland.
     *
     * @param \Shuxian\EasySms\Contracts\PhoneNumberInterface $to
     *
     * @return bool
     */
    protected function inChineseMainland($to)
    {
        $code = $to->getIDDCode();

        return empty($code) || 86 === $code;
    }
}
