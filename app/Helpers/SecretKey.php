<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SecretKey
{
    /**
     * @var
     */
    public $prefix;

    /**
     * @var
     */
    public $entropy;

    /**
     * @param string $prefix
     * @param bool $entropy
     */
    public function __construct($prefix = '', $entropy = false)
    {
        $uuid = str_replace('-', '', Str::uuid()->toString());
        $this->uuid = uniqid($prefix, $entropy).$uuid;
    }

    /**
     * Limit the UUID by a number of characters
     *
     * @param $length
     * @param int $start
     * @return $this
     */
    public function limit($length, $start = 0)
    {
        $this->uuid = substr($this->uuid, $start, $length);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->uuid;
    }
}
