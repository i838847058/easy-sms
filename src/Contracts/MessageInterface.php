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

/**
 * Interface MessageInterface.
 */
interface MessageInterface
{
    const TEXT_MESSAGE = 'text';

    const VOICE_MESSAGE = 'voice';

    /**
     * Return the message type.
     *
     * @return string
     */
    public function getMessageType();

    /**
     * Return message content.
     *
     * @param \XiaoYun\EasySms\Contracts\GatewayInterface|null $gateway
     *
     * @return string
     */
    public function getContent(GatewayInterface $gateway = null);

    /**
     * Return the template id of message.
     *
     * @param \XiaoYun\EasySms\Contracts\GatewayInterface|null $gateway
     *
     * @return string
     */
    public function getTemplate(GatewayInterface $gateway = null);

    /**
     * Return the template data of message.
     *
     * @param \XiaoYun\EasySms\Contracts\GatewayInterface|null $gateway
     *
     * @return array
     */
    public function getData(GatewayInterface $gateway = null);

    /**
     * Return message supported gateways.
     *
     * @return array
     */
    public function getGateways();
}
