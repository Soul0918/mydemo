<?php


namespace Common\Controller\Extra;
use Api\Lib\ErrorCode;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;

trait AccessToken
{
    protected $issuer = 'gd-hc.com.cn';

    protected $audience = 'gd-hc.com.cn';

    /**
     * @var Token
     */
    protected $access_token;

    protected $token_builder;

    protected $type;

    /**
     * 数据
     * @var array
     */
    protected $data;

    protected $user_id;

    protected $debug = true;

    /**
     * @return Builder
     */
    public function getTokenBuilder()
    {
        if (is_null($this->token_builder)) {
            $this->token_builder = (new Builder());
            $this->token_builder->setIssuer($this->issuer);
            $this->token_builder->setAudience($this->audience);
        }

        return $this->token_builder;
    }

    /**
     * @param $appid
     * @param $appsecret
     * @param array $params
     * @return mixed
     */
    public function getAccessToken($appid,$appsecret,$params=[])
    {
        $sha256 = new Sha256();
        $builder = $this->getTokenBuilder();
        $builder->setIssuedAt(time())
            ->setNotBefore(time() + 60)
            ->setExpiration(time() + 7200)
            ->setId($appid);
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                $builder->set($key, $param);
            }
        }
        $builder->sign($sha256, $appsecret);
        $access_token = $builder->getToken();
        return $access_token;
    }

    /**
     * 检测AccessToken
     * @param $access_token
     */
    public function checkAccessToken($access_token)
    {
        if ($access_token) {
            $token = (new Parser())->parse((string)$access_token);
            $this->access_token = $token;
            $data = new ValidationData();
            $data->setId($token->getClaim('jti'));
            $data->setIssuer($this->issuer);
            $data->setAudience($this->audience);
            $data->setCurrentTime(time()+60);
            if (!$token->validate($data)) {
                $this->res([],-2,ErrorCode::getMsg(-2));
            }

            $appsecret = $this->getAppsecret();
            if (!$token->verify(new Sha256(), $appsecret)) {
                $this->res([],-1,ErrorCode::getMsg(-1));
            }

            if (!$this->debug) {
                //判断来源是否为token认证的网址
                if (!empty($_SERVER['HTTP_ORIGIN']) && $origin = $_SERVER['HTTP_ORIGIN']) {
                    if (strpos($origin, $this->issuer) === false) {
                        $this->res([],-1,ErrorCode::getMsg(-1));
                    }
                }
            }
        } else {
            if (!in_array(ACTION_NAME,['access_token','userinfo', 'testquery', 'index', 'download_file', 'config', 'opendoorintegral2','download','startads','wxzzcode', 'update_state'], true)) {
                $this->res([],-1,ErrorCode::getMsg(-1));
            }
        }
    }

    /**
     * 获取appid
     * @return mixed
     */
    public function getAppid()
    {
        if (!is_null($this->access_token)) {
            $appid = $this->access_token->getClaim('jti');
            return $appid;
        }
    }

    /**
     * 获取appsecert
     * @return int
     */
    public function getAppsecret()
    {
        $appid = $this->getAppid();
        return '1';
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * 获取用户id
     * @return mixed
     */
    public function getUserId()
    {
        if (is_null($this->access_token)) {
            return false;
        }
        if (!is_null($this->user_id)) return $this->user_id;

        try {
            $this->setUserId($this->access_token->getClaim('userId', false));
        } catch (\Exception $e) {
            $this->setUserId(false);
        }
//        if (!$this->user_id) {
//            if ($this->getOpenId() && $this->getWxid()) {
//                $user_id = D('UserWxMapping')->where(['openid' => $this->getOpenId(), 'wx_id' => $this->getWxid()])->getField('user_id');
//                $this->setUserId($user_id ?: false);
//            }
//        }
        return $this->user_id;
    }

    /**
     * 获取api类型
     * @return mixed
     */
    public function getType()
    {
        if (is_null($this->access_token)) {
            return false;
        }
        try {
            $this->type = $this->access_token->getClaim('type');
        } catch (\Exception $e) {
            $this->type = false;
        }
        return $this->type;
    }
}