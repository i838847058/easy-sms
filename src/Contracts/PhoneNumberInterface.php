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

/**
 * Interface PhoneNumberInterface.
 *
 * @author shuxian <i@shuxian.me>
 */
interface PhoneNumberInterface extends \JsonSerializable
{
    /**
     * 86.
     *
     * @return int
     */
    public function getIDDCode();

    /**
     * 18888888888.
     *
     * @return int
     */
    public function getNumber();

    /**
     * +8618888888888.
     *
     * @return string
     */
    public function getUniversalNumber();

    /**
     * 008618888888888.
     *
     * @return string
     */
    public function getZeroPrefixedNumber();

    /**
     * @return string
     */
    public function __toString();
}
