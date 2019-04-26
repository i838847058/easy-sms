<?php

/*
 * This file is part of the xiaoyun/easy-sms.
 *
 * (c) xiaoyun <i@xiaoyun.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace XiaoYun\EasySms\Strategies;

use XiaoYun\EasySms\Contracts\StrategyInterface;

/**
 * Class OrderStrategy.
 */
class OrderStrategy implements StrategyInterface
{
    /**
     * Apply the strategy and return result.
     *
     * @param array $gateways
     *
     * @return array
     */
    public function apply(array $gateways)
    {
        return array_keys($gateways);
    }
}
