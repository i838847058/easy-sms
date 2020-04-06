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

use GuzzleHttp\Exception\ClientException;
use shuxian\EasySms\Contracts\MessageInterface;
use shuxian\EasySms\Contracts\PhoneNumberInterface;
use shuxian\EasySms\Exceptions\GatewayErrorException;
use shuxian\EasySms\Support\Config;
use shuxian\EasySms\Traits\HasHttpRequest;

/**
 * Class TwilioGateway.
 *
 *  @see https://www.twilio.com/docs/api/messaging/send-messages
 */
class TwilioGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json';

    protected $errorStatuses = [
        'failed',
        'undelivered',
    ];

    public function getName()
    {
        return 'twilio';
    }

    /**
     * @param \shuxian\EasySms\Contracts\PhoneNumberInterface $to
     * @param \shuxian\EasySms\Contracts\MessageInterface     $message
     * @param \shuxian\EasySms\Support\Config                 $config
     *
     * @return array
     *
     * @throws \shuxian\EasySms\Exceptions\GatewayErrorException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $accountSid = $config->get('account_sid');
        $endpoint = $this->buildEndPoint($accountSid);

        $params = [
            'To' => $to->getUniversalNumber(),
            'From' => $config->get('from'),
            'Body' => $message->getContent($this),
        ];

        try {
            $result = $this->request('post', $endpoint, [
                'auth' => [
                    $accountSid,
                    $config->get('token'),
                ],
                'form_params' => $params,
            ]);
            if (in_array($result['status'], $this->errorStatuses) || !is_null($result['error_code'])) {
                throw new GatewayErrorException($result['message'], $result['error_code'], $result);
            }
        } catch (ClientException $e) {
            throw new GatewayErrorException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    /**
     * build endpoint url.
     *
     * @param string $accountSid
     *
     * @return string
     */
    protected function buildEndPoint($accountSid)
    {
        return sprintf(self::ENDPOINT_URL, $accountSid);
    }
}
