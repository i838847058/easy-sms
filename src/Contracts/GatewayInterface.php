<?php

/*
 * This file is part of the xiaoyun/easy-sms.
 *
 * (c) xiaoyun <i@xiaoyun.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace XiaoYun\EasySms\Contracts;

use XiaoYun\EasySms\Support\Config;

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
     * @param \XiaoYun\EasySms\Contracts\PhoneNumberInterface $to
     * @param \XiaoYun\EasySms\Contracts\MessageInterface     $message
     * @param \XiaoYun\EasySms\Support\Config                 $config
     *
     * @return array
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config);
}
