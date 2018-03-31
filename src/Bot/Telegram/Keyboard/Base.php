<?php
namespace Bot\Telegram\Keyboard;

use Zend\ComponentInstaller\Collection;

/**
 *
 * @author rusakov.vv
 *
 */
class Base extends Collection
{
    /**
     * Dynamically build params.
     *
     * @param string $method
     * @param array  $args
     *
     * @return $this
     */
    public function __call($method, $args)
    {
        $property = substr($method, 3);
        $this->items[$property] = $args[0];
        return $this;
    }
}

