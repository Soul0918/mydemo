<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/28
 * Time: 10:49
 */

namespace Common\Lib\Cos;

/**
 * Cosapi Factory
 * Class CosFactory
 * @package Common\Lib\Cos
 */
class CosFactory
{

    protected static $config = [
        'APP_ID' => '',
        'SECRET_ID' => '',
        'SECRET_KEY' => '',
        'BUCKET' => '',
        'TIMEOUT' => 180,
    ];

    public static function factory($className='', $config=[])
    {
        if (!empty($className)) {
            $className = "Common\\Lib\\Cos\\Commands\\".$className;
        }

        if (empty($config)) {
            self::$config = array_merge(self::$config,$config,C('UPLOAD_TYPE_CONFIG'));
        }

        if (class_exists($className)) {
            $class = new $className(self::$config);
            return $class;
        }

        throw new \Exception('类不存在');
    }
}