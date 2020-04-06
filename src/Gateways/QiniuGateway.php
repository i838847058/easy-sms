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
 * Class QiniuGateway.
 *
 * @see https://developer.qiniu.com/sms/api/5897/sms-api-send-message
 */
class QiniuGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_TEMPLATE = 'https://%s.qiniuapi.com/%s/%s';

    const ENDPOINT_VERSION = 'v1';

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
        $endpoint = $this->buildEndpoint('sms', 'message/single');

        $data = $message->getData($this);

        $params = [
            'template_id' => $message->getTemplate($this),
            'mobile' => $to->getNumber(),
        ];

        if (!empty($data)) {
            $params['parameters'] = $data;
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $headers['Authorization'] = $this->generateSign($endpoint, 'POST', json_encode($params), $headers['Content-Type'], $config);

        $result = $this->postJson($endpoint, $params, $headers);

        if (isset($result['error'])) {
            throw new GatewayErrorException($result['message'], $result['error'], $result);
        }

        return $result;
    }

    /**
     * Build endpoint url.
     *
     * @param string $type
     * @param string $function
     *
     * @return string
     */
    protected function buildEndpoint($type, $function)
    {
        return sprintf(self::ENDPOINT_TEMPLATE, $type, self::ENDPOINT_VERSION, $function);
    }

    /**
     * Build endpoint url.
     *
     * @param string $url
     * @param string $method
     * @param string $body
     * @param string $contentType
     * @param Config $config
     *
     * @return string
     */
    protected function generateSign($url, $method, $body = null, $contentType = null, Config $config)
    {
        $urlItems = parse_url($url);
        $host = $urlItems['host'];
        if (isset($urlItems['port'])) {
            $port = $urlItems['port'];
        } else {
            $port = '';
        }
        $path = $urlItems['path'];
        if (isset($urlItems['query'])) {
            $query = $urlItems['query'];
        } else {
            $query = '';
        }
        //write request uri
        $toSignStr = $method.' '.$path;
        if (!empty($query)) {
            $toSignStr .= '?'.$query;
        }
        //write host and port
        $toSignStr .= "\nHost: ".$host;
        if (!empty($port)) {
            $toSignStr .= ':'.$port;
        }
        //write content type
        if (!empty($contentType)) {
            $toSignStr .= "\nContent-Type: ".$contentType;
        }
        $toSignStr .= "\n\n";
        //write body
        if (!empty($body)) {
            $toSignStr .= $body;
        }

        $hmac = hash_hmac('sha1', $toSignStr, $config->get('secret_key'), true);

        return 'Qiniu '.$config->get('access_key').':'.$this->base64UrlSafeEncode($hmac);
    }

    /**
     * @param string $data
     *
     * @return string
     */
    protected function base64UrlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');

        return str_replace($find, $replace, base64_encode($data));
    }
}
