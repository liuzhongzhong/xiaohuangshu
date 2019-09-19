<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/25
 * Time: 21:32
 */

namespace app\wxapp\controller;


use think\Controller;
use app\wxapp\common;

class User extends Controller
{
    // 小程序用户登录
    public function Login() {
        $code = input('post.code/s','','htmlspecialchars');
        $wxLogin = new common\Wxlogin();
        $wxLoginInfo = $wxLogin->getOpenID($code);
        if(!$wxLoginInfo) {
            return json(array(
                'code' => 500,
                'message' => '登录失败',
            ));
        }

        // 判断是否存在该用户，有则更新，无则新建
        $hasUser = model('user')->getUserByOpenID($wxLoginInfo['openid']);
        if(!$hasUser) {
            // 不存在用户
            $saveUser = model('user')->saveUser($wxLoginInfo);
            if(!$saveUser) {
                return json(array(
                    'code' => 500,
                    'message' => '用户信息保存失败',
                    'data' => array(
                        'user_id' => $saveUser,
                    ),
                ));
            }
            $user_id = $saveUser;
        }else {
            $user_id = $hasUser['user_id'];
        }

        return json(array(
            'code' => 200,
            'message' => '登录成功',
            'data' => array(
                'user_id' => $user_id,
            ),
        ));
    }

    /**
     * 保存用户信息
     * @return \think\response\Json
     */
    public function saveUser() {
        $user_id = input('post.user_id/d',0);
        $userInfo = json_decode(input('post.userInfo',array()),true);

        if(!$user_id || !$userInfo) {
            return json(array(
                'code' => 500,
                'message' => '用户信息为空',
            ));
        }

        // 获取用户信息
        $oldUserInfo = model('user')->getUser($user_id);
        $oldUserInfo = json_decode($oldUserInfo,true);

        // 比较是否内容相同
        if($oldUserInfo['nickName'] == $userInfo['nickName'] &&
            $oldUserInfo['gender'] == $userInfo['gender'] &&
            $oldUserInfo['language'] == $userInfo['language'] &&
            $oldUserInfo['city'] == $userInfo['city'] &&
            $oldUserInfo['province'] == $userInfo['province'] &&
            $oldUserInfo['country'] == $userInfo['country'] &&
            $oldUserInfo['avatarUrl'] == $userInfo['avatarUrl']){
            // 即相互都不存在差集，那么这两个数组就是相同的了，多数组也一样的道理
            return json(array(
                'code' => 200,
                'message' => '用户信息保存成功',
            ));
        }else {
            // 更新用户信息
            $saveUser = model('user')->updateUser($user_id,$userInfo);
            if($saveUser == 1) {
                return json(array(
                    'code' => 200,
                    'message' => '用户信息保存成功',
                ));
            }else {
                return json(array(
                    'code' => 500,
                    'message' => '用户信息保存失败',
                ));
            }
        }
    }

    /**
     * 获取用户信息
     * @return \think\response\Json
     */
    public function getUser() {
        $user_id = input('get.user_id/d',0);
        if(!$user_id) {
            return json(array(
                'code' => 500,
                'message' => '用户ID为空',
            ));
        }
        $userInfo = model('user')->getUser($user_id);
        if($userInfo) {
            return json(array(
                'code' => 200,
                'message' => '用户信息获取成功',
                'data' => array(
                    'userInfo' => $userInfo,
                )
            ));
        }else {
            return json(array(
                'code' => 500,
                'message' => '用户信息获取失败',
            ));
        }
    }
}