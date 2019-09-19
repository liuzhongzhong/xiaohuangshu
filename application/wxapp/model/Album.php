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
    public function listAlbum($data = array(), $page = 1, $pageSize = 20) {
        return Album::where('is_private','neq',1)->page($page,$pageSize)->order(array('create_time' => 'desc','album_id' => 'desc'))->all($data);
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
            return Album::where('user_id',$user_id)->page($page,$pageSize)->order(array('create_time' => 'desc','album_id' => 'desc'))->select();
        }
    }

    /**
     * 根据图册ID批量获取图册信息
     * @param array $data
     * @return mixed
     */
    public function listAlbumByIDS($data = array()) {
        return Album::order(array('create_time' => 'desc','album_id' => 'desc'))->select($data);
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
     * 跟新图册信息
     * @param int $album_id
     * @param array $data
     * @return int|string
     */
    public function updateAlbum($album_id = 0 ,$data = array()) {
        if(!$album_id || !$data) {
            exception('Album Model updateAlbum 数据为空');
        }
        return Album::where('album_id',$album_id)->update($data);
    }
}