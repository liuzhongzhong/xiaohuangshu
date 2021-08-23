<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2020/2/25
 * Time: 23:20
 */

namespace app\wxapp\model;

use think\Model;

class Relashare extends Model
{
    protected $pk = 'relashare_id';
    protected $table = 'rela_share';

    /**
     * 根据用户ID、图册ID获取分享记录
     * @param int $user_id
     * @param int $album_id
     * @return array|null|\PDOStatement|string|Model
     */
    public function getRelaShare($user_id = 0, $album_id = 0) {
        if(!$user_id || !$album_id) {
            exception('Relapay Model getRelaShare ID为空');
        }
        $condition = array(
            'user_id' => $user_id,
            'album_id' => $album_id,
        );
        return Relashare::where($condition)->order(array('create_time' => 'desc','relashare_id' => 'desc'))->find();
    }

    /**
     * 根据用户ID获取分享记录
     * @param int $user_id
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function listUserRelaShare($user_id = 0, $page = 0, $pageSize = 0) {
        if(!$user_id) {
            exception('Relapay Model listUserRelaShare UserID为空');
        }
        if(!$page || !$pageSize) {
            return Relashare::where('user_id',$user_id)->order(array('create_time' => 'desc','relashare_id' => 'desc'))->select();
        }else {
            return Relashare::where('user_id',$user_id)->order(array('create_time' => 'desc','relashare_id' => 'desc'))->page($page,$pageSize)->select();
        }
    }

    /**
     * 创建分享记录
     * @param array $data
     * @return bool
     */
    public function saveRelaShare($data = array()) {
        if(!$data) {
            exception('Relapay Model saveRelaShare data为空');
        }
        return Relashare::save($data);
    }
}