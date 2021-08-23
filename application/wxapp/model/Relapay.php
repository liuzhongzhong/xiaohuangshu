<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/28
 * Time: 11:37
 */

namespace app\wxapp\model;

use think\Model;

class Relapay extends Model
{
    protected $pk = 'relapay_id';
    protected $table = 'rela_pay';

    /**
     * 根据用户ID、图册ID获取打赏记录
     * @param int $user_id
     * @param int $album_id
     * @return array|null|\PDOStatement|string|Model
     */
    public function getRelaPay($user_id = 0, $album_id = 0) {
        if(!$user_id || !$album_id) {
            exception('Relapay Model getRelaPay ID为空');
        }
        $condition = array(
            'user_id' => $user_id,
            'album_id' => $album_id,
        );
        return Relapay::where($condition)->order(array('create_time' => 'desc','relapay_id' => 'desc'))->find();
    }

    /**
     * 根据用户ID获取打赏记录
     * @param int $user_id
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function listUserRelaPay($user_id = 0, $page = 0, $pageSize = 0) {
        if(!$user_id) {
            exception('Relapay Model listUserRelaPay UserID为空');
        }
        if(!$page || !$pageSize) {
            return Relapay::where('user_id',$user_id)->order(array('create_time' => 'desc','relapay_id' => 'desc'))->select();
        }else {
            return Relapay::where('user_id',$user_id)->order(array('create_time' => 'desc','relapay_id' => 'desc'))->page($page,$pageSize)->select();
        }
    }

    /**
     * 创建打赏记录
     * @param array $data
     * @return bool
     */
    public function saveRelaPay($data = array()) {
        if(!$data) {
            exception('Relapay Model saveRelaPay data为空');
        }
        return Relapay::save($data);
    }
}