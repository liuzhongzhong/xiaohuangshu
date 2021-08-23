<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2020/2/26
 * Time: 20:35
 */

namespace app\wxapp\controller;

use think\Controller;
use app\common\model;

class Share extends Controller
{
    /**
     * 用户分享记录写入
     * @return \think\response\Json
     */
    public function saveShareInfo() {
        $user_id = input('post.user_id/d',12);
        $album_id = input('post.album_id/d',2);
        if(!$user_id || !$album_id) {
            return json(array(
                'code' => 400,
                'message' => '用户或图册ID为空',
            ));
        }
        // 将用户分享信息写入数据库
        // 绑定用户与图册的分享关系
        $relaShare = model('relashare')->saveRelaShare(array('user_id' => $user_id, 'album_id' => $album_id));
        if($relaShare) {
            // 修改图册中付款人数
            return json(array(
                'code' => 200,
                'message' => '分享信息保存成功',
            ));
        }else {
            return json(array(
                'code' => 400,
                'message' => '打赏关系绑定失败',
            ));
        }
    }
}