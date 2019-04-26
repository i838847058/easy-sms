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
 * Class RandomStrategy.
 */
class RandomStrategy implements StrategyInterface
{
    /**
     * @param array $gateways
     *
     * @return array
     */
    public function apply(array $gateways)
    {
        uasort($gateways, function () {
            return mt_rand() - mt_rand();
        });

        return array_keys($gateways);
    }
}
