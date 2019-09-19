<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/29
 * Time: 14:15
 */
namespace app\wxapp\common;

class Wxlogin
{
    /**
     * 获取OpenID
     * @param string $code
     * @return int|mixed
     */
    public function getOpenID($code = '') {
        if(!$code || $code == '') {
            return 0;
        }
        $apiUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid='.config('wechat_small_application')['appid'].'&secret='.config('wechat_small_application')['appsecret'].'&js_code='.$code.'&grant_type=authorization_code';
        return json_decode(curlGet(trim($apiUrl)),true);
    }
}