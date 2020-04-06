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
 * Class JuheGateway.
 *
 * @see https://www.juhe.cn/docs/api/id/54
 */
class JuheGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'http://v.juhe.cn/sms/send';

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
        $params = [
            'mobile' => $to->getNumber(),
            'tpl_id' => $message->getTemplate($this),
            'tpl_value' => $this->formatTemplateVars($message->getData($this)),
            'dtype' => self::ENDPOINT_FORMAT,
            'key' => $config->get('app_key'),
        ];

        $result = $this->get(self::ENDPOINT_URL, $params);

        if ($result['error_code']) {
            throw new GatewayErrorException($result['reason'], $result['error_code'], $result);
        }

        return $result;
    }

    /**
     * @param array $vars
     *
     * @return string
     */
    protected function formatTemplateVars(array $vars)
    {
        $formatted = [];

        foreach ($vars as $key => $value) {
            $formatted[sprintf('#%s#', trim($key, '#'))] = $value;
        }

        return http_build_query($formatted);
    }
}
