<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/25
 * Time: 21:50
 */

namespace app\wxapp\model;

use think\Model;
use think\model\concern\SoftDelete;

class Album extends Model
{
    use SoftDelete;
    // 主键id名
    protected $pk = 'album_id';
    // 软删除字段
    protected $deleteTime = 'delete_time';

    /**
     * 获取所有相册信息
     * @return mixed
     */
    public function listAlbum($data = array(), $page = 1, $pageSize = 20,$disabledAlbum = 0) {
//        $orderCondition = array(
//            'recommend_num' => 'desc',
//            'pay_num' => 'desc',
//            'like_num' => 'desc',
//            'view_num' => 'desc',
//            'create_time' => 'desc',
//            'album_id' => 'desc'
//        );
//        return Album::where('is_private','neq',1)->page($page,$pageSize)->order($orderCondition)->all($data);
        if($disabledAlbum == 0) {
            $unionLeft = 'select * from album where create_time >= DATE_SUB(NOW(),INTERVAL 7 DAY) AND is_private != 1 AND delete_time IS NULL OR recommend_num > 0 ORDER BY recommend_num DESC,update_time DESC, create_time DESC, like_num DESC, view_num DESC limit 999999999';
            $unionRight = 'select * from album where create_time < DATE_SUB(NOW(),INTERVAL 7 DAY) AND is_private != 1 AND delete_time IS NULL AND recommend_num = 0 ORDER BY like_num DESC, view_num DESC,update_time DESC,create_time DESC limit 999999999';
        }else {
            $unionLeft = 'select * from album where create_time >= DATE_SUB(NOW(),INTERVAL 7 DAY) AND disabled != 1 AND is_private != 1 AND delete_time IS NULL OR recommend_num > 0 ORDER BY recommend_num DESC,update_time DESC, create_time DESC, like_num DESC, view_num DESC limit 999999999';
            $unionRight = 'select * from album where create_time < DATE_SUB(NOW(),INTERVAL 7 DAY) AND disabled != 1 AND is_private != 1 AND delete_time IS NULL AND recommend_num = 0 ORDER BY like_num DESC, view_num DESC,update_time DESC,create_time DESC limit 999999999';
        }

        $albumData =  Album::union([$unionLeft,$unionRight],true)->where('is_private','eq',2)->page($page,$pageSize)->select();
        return $albumData;

        /**
         * select * from (
        (select * from album where create_time >= DATE_SUB(NOW(),INTERVAL 7 DAY) OR recommend_num > 0 ORDER BY recommend_num DESC, create_time DESC, like_num DESC, view_num DESC limit 999999999)
        UNION ALL
        (select * from album where create_time < DATE_SUB(NOW(),INTERVAL 7 DAY) AND recommend_num = 0 ORDER BY like_num DESC, view_num DESC,create_time DESC limit 999999999)
        ) tab where tab.is_private != 1 AND `delete_time` IS NULL LIMIT 0,10 ;
         */
    }

    /**
     * 获取单个相册信息
     * @param int $album_id
     * @return mixed
     */
    public function getAlbum($album_id = 0) {
        if(!$album_id) {
            exception('Album Model getAlbum ID为空');
        }else{
            return Album::get($album_id);
        }

    }

    /**
     * 删除单个相册信息
     * @param int $album_id
     * @return bool
     */
    public function deleteAlbum($album_id = 0) {
        if(!$album_id) {
            exception('Album Model deleteAlbum ID为空');
        }else{
            return Album::destroy($album_id);
        }
    }

    /**
     * 图册字段自增
     * @param int $album_id
     * @param string $filedName
     * @return int|true
     */
    public function updateAlbumFiledInc($album_id = 0, $filedName = '') {
        if(!$album_id || !$filedName) {
            exception('Album Model updateAlbum 数据为空');
        }else {
            return Album::where('album_id',$album_id)->setInc($filedName);
        }
    }

    /**
     * 图册字段自减
     * @param int $album_id
     * @param string $filedName
     * @return int|true
     */
    public function updateAlbumFiledDec($album_id = 0, $filedName = '') {
        if(!$album_id || !$filedName) {
            exception('Album Model updateAlbum 数据为空');
        }else {
            return Album::where('album_id',$album_id)->setDec($filedName);
        }
    }

    /**
     * 根据用户ID获取图册信息
     * @param int $user_id
     * @param int $page
     * @param int $pageSize
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function listUserAlbum($user_id = 0, $page = 1, $pageSize = 10) {
        if(!$user_id) {
            exception('Album Model listUserAlbum 数据为空');
        }else {
            return Album::where('user_id',$user_id)->page($page,$pageSize)->order(array('update_time' => 'desc','create_time' => 'desc','album_id' => 'desc'))->select();
        }
    }

    /**
     * 根据图册ID批量获取图册信息
     * @param array $data
     * @return mixed
     */
    public function listAlbumByIDS($data = array(),$neq = 0,$disabledAlbum = 0) {
        $orderCondition = array(
            'recommend_num' => 'desc',
            'like_num' => 'desc',
            'view_num' => 'desc',
            'update_time' => 'desc',
            'create_time' => 'desc',
            'album_id' => 'desc'
        );
        $condition = [];
        if($disabledAlbum == 1) {
            $condition[]=['disabled','neq',1];
        }

        if($neq != 0) {
            $condition[]=['is_private','neq',1];
            $resault = Album::where($condition)->order($orderCondition)->select($data);
        }else {
            $resault = Album::where($condition)->order($orderCondition)->select($data);
        }

        return $resault;

    }

    /**
     * 创建图册
     * @param array $data
     * @return mixed
     */
    public function saveAlbum($data = array()) {
        if(!$data) {
            exception('Album Model saveAlbum 数据为空');
        }
        $album = new Album();
        $album->save($data);
        return $album->album_id;
    }

    /**
     * 更新图册信息
     * @param int $album_id
     * @param array $data
     * @return int|string
     */
    public function updateAlbum($album_id = 0 ,$data = array()) {
        if(!$album_id || !$data) {
            exception('Album Model updateAlbum 数据为空');
        }
        $value = Album::where('album_id',$album_id)->update($data);
        return $value;

    }
}