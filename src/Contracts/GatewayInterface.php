<?php

/*
 * This file is part of the shuxian/easy-sms.
 *
 * (c) shuxian <i@shuxian.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace shuxian\EasySms\Contracts;

use shuxian\EasySms\Support\Config;

/**
 * Class GatewayInterface.
 */
interface GatewayInterface
{
    /**
     * Get gateway name.
     *
     * @return string
     */
    public function getName();

    /**
     * Send a short message.
     *
     * @param \shuxian\EasySms\Contracts\PhoneNumberInterface $to
     * @param \shuxian\EasySms\Contracts\MessageInterface     $message
     * @param \shuxian\EasySms\Support\Config                 $config
     *
     * @return array
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config);
}
