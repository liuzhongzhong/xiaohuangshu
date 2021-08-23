<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2020/2/25
 * Time: 16:13
 */

namespace app\wxapp\model;

use think\Model;
use think\model\concern\SoftDelete;

class Virtualuser extends Model
{
    use SoftDelete;
    // 主键id名
    protected $pk = 'user_id';
    // 软删除字段
    protected $deleteTime = 'delete_time';
    protected $table = 'virtual_user';
    /**
     * 获取虚拟用户列表
     * @param array $data
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function listUser($data = array()) {
        $value = Virtualuser::order(array('create_time' => 'desc','user_id' => 'desc'))->all($data);
        return $value;
    }

    /**
     * 获取虚拟用户信息
     * @param int $user_id
     * @return mixed
     */
    public function getUser($user_id = 0) {
        if(!$user_id) {
            exception('User Model getUser ID为空');
        }
        return Virtualuser::get($user_id);
    }
}