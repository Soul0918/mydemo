<?php

namespace Common\Lib\Cos;

/**
 * Class Cosapi
 * @package Common\Lib\Cos
 */
class Cosapi
{
    const VERSION = 'v1.0.0';

    public static function __callStatic($name, $arguments)
    {
        $name .= 'Command';
        $factory = CosFactory::factory($name);
        return $factory->run($arguments);
    }
}