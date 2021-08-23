<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/27
 * Time: 15:43
 */

namespace app\common\model;


use think\Model;
use think\model\concern\SoftDelete;

class Image extends Model
{
    use SoftDelete;
    // 主键id名
    protected $pk = 'image_id';
    // 软删除字段
    protected $deleteTime = 'delete_time';

    /**
     * 获取相册图片
     * @param int $album_id
     * @param int $page
     * @param int $pageSize
     * @return mixed
     */
    public function listImages($album_id = 0, $page = 1, $pageSize = 20) {
        if(!$album_id) {
            exception('Image Model listImages ID为空');
        }
        return Image::where('album_id',$album_id)->page($page,$pageSize)->select();
    }

    /**
     * 获取某相册全部图片
     * @param int $album_id
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function listImagesNoLimit($album_id = 0) {
        if(!$album_id) {
            exception('Image Model listAllImages ID为空');
        }
        return Image::where('album_id',$album_id)->select();
    }

    /**
     * 获取所有图片
     * @return mixed
     */
    public function listAllImages() {
        return Image::all();
    }

    /**
     * 更新图片信息
     * @param int $image_id
     * @param array $data
     * @return int|string
     */
    public function updateImages($image_id = 0, $data = array()) {
        if(!$image_id || !$data) {
            exception('Image Model saveAllImages data为空');
        }
        return Image::where('image_id',$image_id)->update($data);
    }

    /**
     * 保存图片
     * @param array $data
     * @return int|string
     */
    public function saveImage($data = array()) {
        if(!$data) {
            exception('Image Model saveImage 数据为空');
        }
        $image = new Image();
        $image->save($data);
        return $image->image_id;
    }

    public function getCoverImage($album_id = 0) {
        if(!$album_id) {
            exception('Image Model getCoverImage ID为空');
        }
        return Image::where('album_id',$album_id)->find();
    }

    /**
     * 批量保存图片
     * @param array $data
     * @return int|string
     */
    public function saveAllImage($data = array()) {
        if(!$data) {
            exception('Image Model saveAllImage 数据为空');
        }
        $image = new Image();

        return $image->saveAll($data);
    }


    /**
     * 删除一个或多个图片
     * @param int $image_id
     * @return bool
     */
    public function deleteImages($image_id = 0) {
        if(!$image_id) {
            exception('Image Model deleteImages ID为空');
        }
        return Image::destroy($image_id);
    }

    /**
     * 获取图片信息
     * @param int $image_id
     * @return mixed
     */
    public function getImage($image_id = 0) {
        if(!$image_id) {
            exception('Image Model getImage ID为空');
        }else{
            return Image::get($image_id);
        }
    }

    /**
     * 获取图册中图片数量
     * @param int $album_id
     * @return float|string
     */
    public function getImageCount($album_id = 0) {
        if(!$album_id) {
            exception('Image Model getImageCount ID为空');
        }
        return Image::where('album_id',$album_id)->count();
    }
}