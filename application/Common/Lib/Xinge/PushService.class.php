<?php


namespace Common\Lib\Xinge;


/**
 * Class PushService
 * @package Common\Lib\Xinge
 */
class PushService extends XingeApp
{
    /**
     * @var
     */
    protected static $access_id_android;

    /**
     * @var
     */
    protected static $access_key_android;

    /**
     * @var
     */
    protected static $secret_key_android;

    /**
     * @var
     */
    protected static $access_id_ios;

    /**
     * @var
     */
    protected static $access_key_ios;

    /**
     * @var
     */
    protected static $secret_key_ios;

    /**
     * @return mixed
     */
    public static function getAccessIdAndroid()
    {
        if (is_null(self::$access_id_android)) {
            $options = get_site_options();
            self::$access_id_android = $options['xinge_access_id_android'];
        }

        return self::$access_id_android;
    }

    /**
     * @param mixed $access_id_android
     */
    public static function setAccessIdAndroid($access_id_android)
    {
        self::$access_id_android = $access_id_android;
    }

    /**
     * @return mixed
     */
    public static function getAccessKeyAndroid()
    {
        if (is_null(self::$access_key_android)) {
            $options = get_site_options();
            self::$access_key_android = $options['xinge_access_key_android'];
        }

        return self::$access_key_android;
    }

    /**
     * @param mixed $access_key_android
     */
    public static function setAccessKeyAndroid($access_key_android)
    {
        self::$access_key_android = $access_key_android;
    }

    /**
     * @return mixed
     */
    public static function getSecretKeyAndroid()
    {
        if (is_null(self::$secret_key_android)) {
            $options = get_site_options();
            self::$secret_key_android = $options['xinge_secret_key_android'];
        }

        return self::$secret_key_android;
    }

    /**
     * @param mixed $secret_key_android
     */
    public static function setSecretKeyAndroid($secret_key_android)
    {
        self::$secret_key_android = $secret_key_android;
    }

    /**
     * @return mixed
     */
    public static function getAccessIdIos()
    {
        if (is_null(self::$access_id_ios)) {
            $options = get_site_options();
            self::$access_id_ios = $options['xinge_access_id_ios'];
        }

        return self::$access_id_ios;
    }

    /**
     * @param mixed $access_id_ios
     */
    public static function setAccessIdIos($access_id_ios)
    {
        self::$access_id_ios = $access_id_ios;
    }

    /**
     * @return mixed
     */
    public static function getAccessKeyIos()
    {
        if (is_null(self::$access_key_ios)) {
            $options = get_site_options();
            self::$access_key_ios = $options['xinge_access_key_ios'];
        }

        return self::$access_key_ios;
    }

    /**
     * @param mixed $access_key_ios
     */
    public static function setAccessKeyIos($access_key_ios)
    {

        self::$access_key_ios = $access_key_ios;
    }

    /**
     * @return mixed
     */
    public static function getSecretKeyIos()
    {
        if (is_null(self::$secret_key_ios)) {
            $options = get_site_options();
            self::$secret_key_ios = $options['xinge_secret_key_ios'];
        }

        return self::$secret_key_ios;
    }

    /**
     * @param mixed $secret_key_ios
     */
    public static function setSecretKeyIos($secret_key_ios)
    {
        self::$secret_key_ios = $secret_key_ios;
    }

    /**
     * @param $title
     * @param $content
     * @param $token
     * @return array|mixed
     */
    public static function PushTokenAndroid($title, $content, $token)
    {
        return parent::PushTokenAndroid(self::getAccessIdAndroid(), self::getSecretKeyAndroid(), $title, $content, $token);
    }

    /**
     * @param $title
     * @param $content
     * @param $account
     * @return array|mixed
     */
    public static function PushAccountAndroid($title, $content, $account)
    {
        return parent::PushAccountAndroid(self::getAccessIdAndroid(), self::getSecretKeyAndroid(), $title, $content, $account);
    }

    /**
     * @param $title
     * @param $content
     * @return array|mixed
     */
    public static function PushAllAndroid($title, $content)
    {
        return parent::PushAllAndroid(self::getAccessIdAndroid(), self::getSecretKeyAndroid(), $title, $content);
    }

    /**
     * @param $title
     * @param $content
     * @param $tag
     * @return mixed
     */
    public static function PushTagAndroid($title, $content, $tag)
    {
        return parent::PushTagAndroid(self::getAccessIdAndroid(), self::getSecretKeyAndroid(), $title, $content, $tag);
    }

    /**
     * @param $content
     * @param $token
     * @param $environment
     * @return array|mixed
     */
    public static function PushTokenIos($content, $token, $environment)
    {
        return parent::PushTokenIos(self::getAccessIdIos(), self::getSecretKeyIos(), $content, $token, $environment);
    }

    /**
     * @param $content
     * @param $account
     * @param $environment
     * @return array|mixed
     */
    public static function PushAccountIos($content, $account, $environment)
    {
        return parent::PushAccountIos(self::getAccessIdIos(), self::getSecretKeyIos(), $content, $account, $environment);
    }

    /**
     * @param $content
     * @param $environment
     * @return array|mixed
     */
    public static function PushAllIos($content, $environment)
    {
        return parent::PushAllIos(self::getAccessIdIos(), self::getSecretKeyIos(), $content, $environment);
    }

    /**
     * @param $content
     * @param $tag
     * @param $environment
     * @return mixed
     */
    public static function PushTagIos($content, $tag, $environment)
    {
        return parent::PushTagIos(self::getAccessIdIos(), self::getSecretKeyIos(), $content, $tag, $environment);
    }

    public static function PushTokenIosMessage($custom,$token,$environment)
    {
        $push = new XingeApp(self::getAccessIdIos(), self::getSecretKeyIos());
        $mess = new MessageIOS();
        $mess->setCustom($custom);
        $mess->setType(MessageIOS::TYPE_REMOTE_NOTIFICATION);
        $ret = $push->PushSingleDevice($token, $mess, $environment);
        return $ret;
    }

    public static function PushAccountIosMessage($custom,$account,$environment)
    {
        $push = new XingeApp(self::getAccessIdIos(), self::getSecretKeyIos());
        $mess = new MessageIOS();
        $mess->setCustom($custom);
        $mess->setType(MessageIOS::TYPE_REMOTE_NOTIFICATION);
        $ret = $push->PushSingleAccount(0, $account, $mess, $environment);
        return $ret;
    }
}