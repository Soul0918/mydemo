<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/28
 * Time: 10:41
 */

namespace Common\Lib\Cos;


class Auth
{
    const AUTH_URL_FORMAT_ERROR = -1;
    const AUTH_SECRET_ID_KEY_ERROR = -2;

    /**
     * Create reusable signature for listDirectory in $bucket or uploadFile into $bucket.
     * If $filepath is not null, this signature will be binded with this $filepath.
     * This signature will expire at $expiration timestamp.
     * Return the signature on success.
     * Return error code if parameter is not valid.
     */
    public static function createReusableSignature($appId,$secretId,$secretKey,$expiration, $bucket, $filepath = null)
    {
        if (empty($appId) || empty($secretId) || empty($secretKey)) {
            return self::AUTH_SECRET_ID_KEY_ERROR;
        }

        if (empty($filepath)) {
            return self::createSignature($appId, $secretId, $secretKey, $expiration, $bucket, null);
        } else {
            if (preg_match('/^\//', $filepath) == 0) {
                $filepath = '/' . $filepath;
            }
            return self::createSignature($appId, $secretId, $secretKey, $expiration, $bucket, $filepath);
        }
    }

    /**
     * Create nonreusable signature for delete $filepath in $bucket.
     * This signature will expire after single usage.
     * Return the signature on success.
     * Return error code if parameter is not valid.
     */
    public static function createNonreusableSignature($appId,$secretId,$secretKey,$bucket, $filepath)
    {
        if (empty($appId) || empty($secretId) || empty($secretKey)) {
            return self::AUTH_SECRET_ID_KEY_ERROR;
        }

        if (preg_match('/^\//', $filepath) == 0) {
            $filepath = '/' . $filepath;
        }
        $fileId = '/' . $appId . '/' . $bucket . $filepath;

        return self::createSignature($appId, $secretId, $secretKey, 0, $bucket, $fileId);
    }

    public static function getPornDetectSign($appid,$secretId,$secretKey,$bucket,$url = null)
    {
        if (empty($secretId) || empty($secretKey)) {
            return false;
        }

        $expired = time() + 1000;
        $current = time();
        $srcStr = 'a='.$appid.'&b='.$bucket.'&k='.$secretId.'&t='.$current.'&e='.$expired;
        $signStr = base64_encode(hash_hmac('SHA1', $srcStr, $secretKey, true).$srcStr);

        return $signStr;
    }

    /**
     * A helper function for creating signature.
     * Return the signature on success.
     * Return error code if parameter is not valid.
     */
    private static function createSignature($appId, $secretId, $secretKey, $expiration, $bucket, $fileId)
    {
        if (empty($secretId) || empty($secretKey)) {
            return self::AUTH_SECRET_ID_KEY_ERROR;
        }

        $now = time();
        $random = rand();
        $plainText = "a=$appId&k=$secretId&e=$expiration&t=$now&r=$random&f=$fileId&b=$bucket";
        $bin = hash_hmac('SHA1', $plainText, $secretKey, true);
        $bin = $bin.$plainText;

        $signature = base64_encode($bin);
        return $signature;
    }
}