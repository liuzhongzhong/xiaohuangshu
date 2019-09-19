<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/26
 * Time: 21:37
 */

namespace app\wxapp\model;

use think\Model;

class Relalike extends Model
{
    protected $pk = 'relalike_id';
    protected $table = 'rela_like';

    /**
     * 查找全部关注记录
     * @param int $user_id
     * @param array $data
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function listLike($user_id = 0 ,$data = array()) {
        $conditionData = array();
        if($user_id) {
            $conditionData = array(
                'user_id' => $user_id,
                'album_id' => $data,
            );
        }
        return Relalike::where($conditionData)->order(array('create_time' => 'desc','relalike_id' => 'desc'))->select();
    }

    /**
     * 查找单个关注记录
     * @param int $user_id
     * @param int $album_id
     * @return array|null|\PDOStatement|string|Model
     */
    public function getRelaLike($user_id = 0, $album_id = 0) {
        $condition = array();
        if($user_id && $album_id) {
            $condition = array(
                'user_id' => $user_id,
                'album_id' => $album_id,
            );
        }else {
            exception('Relalike Model getRelaLike ID为空');
        }
        return Relalike::where($condition)->find();
    }

    /**
     * 删除关注关系
     * @param array $relalike_id
     * @return bool
     */
    public function deleteRelaLike($relalike_id = array()) {
        if(!$relalike_id) {
            exception('Relalike Model deleteRelaLike ID为空');
        }
        return Relalike::destroy($relalike_id);
    }

    /**
     * 新建关注关系
     * @param array $data
     * @return mixed
     */
    public function saveRelaLike($data = array()) {
        if(!$data) {
            exception('Relalike Model saveRelaLike 数据为空');
        }
        return Relalike::create($data)->relalike_id;
    }

    /**
     * 获取用户
     * @param int $user_id
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getUserRelaLike($user_id = 0, $page = 0, $pageSize = 0) {
        if(!$user_id) {
            exception('Relalike Model getUserRelaLike UserID为空');
        }

        if(!$page || !$pageSize) {
            return Relalike::where('user_id',$user_id)->order(array('create_time' => 'desc','relalike_id' => 'desc'))->select();
        }else {
            return Relalike::where('user_id',$user_id)->order(array('create_time' => 'desc','relalike_id' => 'desc'))->page($page,$pageSize)->select();
        }
    }
}