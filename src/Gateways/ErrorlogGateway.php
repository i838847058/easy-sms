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
use Shuxian\EasySms\Support\Config;

/**
 * Class ErrorlogGateway.
 */
class ErrorlogGateway extends Gateway
{
    /**
     * @param \Shuxian\EasySms\Contracts\PhoneNumberInterface $to
     * @param \Shuxian\EasySms\Contracts\MessageInterface     $message
     * @param \Shuxian\EasySms\Support\Config                 $config
     *
     * @return array
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        if (is_array($to)) {
            $to = implode(',', $to);
        }

        $message = sprintf(
            "[%s] to: %s | message: \"%s\"  | template: \"%s\" | data: %s\n",
            date('Y-m-d H:i:s'),
            $to,
            $message->getContent($this),
            $message->getTemplate($this),
            json_encode($message->getData($this))
        );

        $file = $this->config->get('file', ini_get('error_log'));
        $status = error_log($message, 3, $file);

        return compact('status', 'file');
    }
}
