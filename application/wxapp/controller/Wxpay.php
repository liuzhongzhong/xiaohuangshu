<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/30
 * Time: 15:48
 */

namespace app\wxapp\controller;


use think\Controller;
use app\common\model;

class Wxpay extends Controller
{

    /**
     * 获取预支付信息
     * @return \think\response\Json
     */
    public function getAdvancePayment() {
        $user_id = input('get.user_id/d',12);
        $album_id = input('get.album_id/d',2);
        if(!$user_id || !$album_id) {
            return json(array(
                'code' => 400,
                'message' => '用户或图册ID为空',
            ));
        }

        // 获取用户信息
        $userInfo = model('user')->getUser($user_id);
        // 获取图册信息
        $albumInfo = model('album')->getAlbum($album_id);

        if($userInfo && $albumInfo) {
            // 实例化微信支付类
            $wxpay = new model\Wxpay();
            // 调用预支付接口
            $paySignResult = $wxpay->getAdvancePayment($userInfo['openid'],$albumInfo['pay_money'],'小黄书—打赏图册');
            // 判断调用状态
            if($paySignResult['code'] == 1) {
                return json(array(
                    'code' => 200,
                    'message' => '统一下单接口调用成功',
                    'data' => $paySignResult['data'],
                ));
            }else {
                return json(array(
                    'code' => 400,
                    'message' => $paySignResult['message'],
                ));
            }
        }else {
            // 返回错误信息
            return json(array(
                'code' => 400,
                'message' => '用户或图册信息获取失败',
            ));
        }
    }

    /**
     * 用户打赏记录写入
     * @return \think\response\Json
     */
    public function savePayInfo() {
        $user_id = input('post.user_id/d',12);
        $album_id = input('post.album_id/d',2);
        if(!$user_id || !$album_id) {
            return json(array(
                'code' => 400,
                'message' => '用户或图册ID为空',
            ));
        }
        // 将用户支付信息写入数据库

        // 绑定用户与图册的支付关系
        $relaPay = model('relapay')->saveRelaPay(array('user_id' => $user_id, 'album_id' => $album_id));
        if($relaPay) {
            // 修改图册中付款人数
            $updateAlbum = model('album')->updateAlbumFiledInc($album_id,'pay_num');
            if($updateAlbum) {
                return json(array(
                    'code' => 200,
                    'message' => '打赏信息保存成功',
                ));
            }else {
                return json(array(
                    'code' => 200,
                    'message' => '图册打赏人数更新失败',
                ));
            }
        }else {
            return json(array(
                'code' => 400,
                'message' => '打赏关系绑定失败',
            ));
        }
    }
}