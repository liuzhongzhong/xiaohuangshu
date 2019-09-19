<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/26
 * Time: 16:32
 */

namespace app\wxapp\model;

use think\Model;
use think\model\concern\SoftDelete;

class User extends Model
{
    // 主键id名
    use SoftDelete;
    protected $pk = 'user_id';
    // 软删除字段
    protected $deleteTime = 'delete_time';

    /**
     * 获取用户列表
     * @param array $data
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function listUser($data = array()) {
        return User::order(array('create_time' => 'desc','user_id' => 'desc'))->all($data);
    }

    /**
     * 获取用户信息
     * @param int $user_id
     * @return mixed
     */
    public function getUser($user_id = 0) {
        if(!$user_id) {
            exception('User Model getUser ID为空');
        }
        return User::get($user_id);
    }

    /**
     * 根据openid查找用户信息
     * @param string $openid
     * @return array|null|\PDOStatement|string|Model
     */
    public function getUserByOpenID($openid = '') {
        if(!$openid) {
            exception('User Model getUserByOpenID openid为空');
        }
        return User::where('openid',$openid)->find();
    }

    /**
     * 保存用户信息
     * @param array $data
     * @return bool
     */
    public function saveUser($data = array()) {
        if(!$data) {
            exception('User Model saveUser data为空');
        }
        return User::create($data)->user_id;
    }

    public function updateUser($user_id = 0, $data = array()) {
        if(!$user_id || !$data) {
            exception('User Model updateUser data为空');
        }
        return User::where('user_id',$user_id)->update($data);
    }
}