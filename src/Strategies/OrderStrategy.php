<?php

/*
 * This file is part of the shuxian/easy-sms.
 *
 * (c) shuxian <i@shuxian.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shuxian\EasySms\Strategies;

use Shuxian\EasySms\Contracts\StrategyInterface;

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
