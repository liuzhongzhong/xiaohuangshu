<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/25
 * Time: 21:31
 */

namespace app\wxapp\controller;

use think\Controller;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\common\Model;

class Image extends Controller
{
    /**
     * 获取相册图片列表
     * @return \think\response\Json
     */
    public function listImages() {
        $user_id = input('get.user_id/d',2);
        $album_id = input('get.album_id/d',2);
        $page = input('get.page/d',1);
        $pageSize = input('get.pageSize/d',50);
        $isPay = 0; // 0未付费，1已付费
        $payAlbum = 0; // 0免费相册，1付费相册
        if(!$album_id) {
            return json(array(
                'code' => 410,
                'message' => '图册ID为空',
                'data' => array(),
            ));
        }

        // 获取图册信息
        $albumInfo = model('album')->getAlbum($album_id);
        // 获取用户信息
        $userInfo = model('user')->getUser($albumInfo['user_id']);
        // 获取当前登录用户关注信息
        $relaLike = model('relalike')->getRelaLike($user_id,$album_id);

        $albumInfo['user_name'] = $userInfo['nickName'];
        $albumInfo['avatar_url'] = $userInfo['avatarUrl'];

        if($relaLike) {
            $albumInfo['is_collect'] = 1;
        }else {
            $albumInfo['is_collect'] = 0;
        }

        if($albumInfo['cover_url'] =='' || $albumInfo['cover_url'] == null || !$albumInfo['cover_url']) {
            $cover_image = model('image')->getCoverImage($albumInfo['album_id']);
            if($cover_image) {
                $albumInfo['cover_url'] = $cover_image['image_url'];
            }
        }

        // 获取图片列表
        $imageList = model('image')->listImages($album_id,$page,$pageSize);

        // 判断是否付费相册
        if($albumInfo['is_pay'] == 1 && $user_id != $albumInfo['user_id']) {
            // 付费相册
            $payAlbum = 1;
            // 判断用户是否已付费
            $relaUserPay = model('relapay')->getRelaPay($user_id,$album_id);
            if($relaUserPay) {
                // 已付费
                $isPay = 1;
            }else {
                // 未付费
                $isPay = 0;
            }
        }else {
            // 非付费相册
            $payAlbum = 0;
        }

        if($imageList) {
            foreach ($imageList as $key => $value) {
                $imageList[$key]['checked'] = false;
                if($payAlbum == 1) {
                    // 付费相册
                    if($isPay == 1) {
                        // 已付费
                        $imageList[$key]['image_url'] = $value['image_url'] . '?' . config('custom_list');
                    }else {
                        // 未付费
                        if($key < 4 && $page == 1) {

                            $imageList[$key]['image_url'] = $value['image_url'] . '?' . config('custom_list');
                        }else {
                            $imageList[$key]['image_url'] = $value['image_url'] . '?' . config('blur_list');
                        }
                    }
                }else {
                    // 免费相册
                    $imageList[$key]['image_url'] = $value['image_url'] . '?' . config('custom_list');
                }
                if($key%2 == 0) {
                    if($key+1 < count($imageList)) {
                        // 存在右边图片
                        if($value['width'] <= $value['height']) {
                            // 左边图片  宽 < 高
                            if($imageList[$key+1]['width'] <= $imageList[$key+1]['height']) {
                                // 右边图片  宽 < 高
                                $imageList[$key]['layoutModel'] = 1;
                                $imageList[$key+1]['layoutModel'] = 1;
                            }else if($imageList[$key+1]['width'] > $imageList[$key+1]['height']) {
                                // 右边图片  宽 > 高
                                $imageList[$key]['layoutModel'] = 2;
                                $imageList[$key+1]['layoutModel'] = 2;
                            }
                        }else if($value['width'] > $value['height']) {
                            // 左边图片  宽 > 高
                            if($imageList[$key+1]['width'] <= $imageList[$key+1]['height']) {
                                // 右边图片  宽 < 高
                                $imageList[$key]['layoutModel'] = 3;
                                $imageList[$key+1]['layoutModel'] = 3;
                            }else if($imageList[$key+1]['width'] > $imageList[$key+1]['height']) {
                                // 右边图片  宽 > 高
                                $imageList[$key]['layoutModel'] = 4;
                                $imageList[$key+1]['layoutModel'] = 4;
                            }
                        }
                    }else {
                        //不存在右边图片，最后一张
                        if($value['width'] <= $value['height']) {
                            // 宽 < 高
                            $imageList[$key]['layoutModel'] = 5;
                        }else if($value['width'] > $value['height']) {
                            // 宽 > 高
                            $imageList[$key]['layoutModel'] = 6;
                        }
                    }
                }
            }

            return json(array(
                'code' => 200,
                'message' => '图片获取成功',
                'data' => array(
                    'imageList' => $imageList,
                    'payAlbum' => $payAlbum,
                    'isPay' => $isPay,
                    'albumInfo' => $albumInfo,
                ),
            ));
        }else {
            return json(array(
                'code' => 410,
                'message' => '图片获取失败',
                'data' => array(),
            ));
        }
    }

    /**
     * 自动更新图片的长宽高
     */
    public function autoImagesWH() {
        $imageList = model('image')->listAllImages();
        foreach ($imageList as $key => $value) {
            $imageInfo= array();
            $imageInfo = getimagesize($value['image_url']);
            $saveAllImage = model('image')->updateImages($value['image_id'],array('width' => $imageInfo[0],'height' => $imageInfo[1]));
        }
        var_dump($imageList);
    }

    /**
     * 获取图片的真实地址
     * @return \think\response\Json
     */
    public function listImageUrl() {
        $album_id = input('get.album_id/d',2);
        $user_id = input('get.user_id/d',1);
        $isPay = 0; // 0未付费，1已付费
        $payAlbum = 0; // 0免费相册，1付费相册
        $imageUrlList = array();

        // 判断是否付费相册
        $albumInfo = model('album')->getAlbum($album_id);
        if($albumInfo['is_pay'] == 1 && $user_id != $albumInfo['user_id']) {
            // 付费相册
            $payAlbum = 1;
            // 判断用户是否已付费
            $relaUserPay = model('relapay')->getRelaPay($user_id,$album_id);
            if($relaUserPay) {
                // 已付费
                $isPay = 1;
            }else {
                // 未付费
                $isPay = 0;
            }
        }else {
            // 非付费相册
            $payAlbum = 0;
        }

        $imageNoLimitList = model('image')->listImagesNoLimit($album_id);
        $imageUrlList = array_column(json_decode($imageNoLimitList),'image_url');
        foreach ($imageUrlList as $index => $item) {
            if($payAlbum == 1) {
                // 付费相册
                if($isPay == 1) {
                    // 已付费
                    $imageUrlList[$index] = $imageUrlList[$index] . '?' . config('custom_image');
                }else {
                    // 未付费
                    if($index < 4) {
                        $imageUrlList[$index] = $imageUrlList[$index] . '?' . config('custom_image')  . config('slim_image');
                    }else {
                        $imageUrlList[$index] = $imageUrlList[$index] . '?' . config('blur_image');
                    }
                }
            }else {
                // 免费相册
                $imageUrlList[$index] = $imageUrlList[$index] . '?' . config('custom_image');
            }
        }

        return json(array(
            'code' => 200,
            'message' => '图片获取成功',
            'data' => array(
                'imageUrlList' => $imageUrlList,
            ),
        ));
    }

    /**
     * 上传图片至七牛云
     * @return \think\response\Json
     */
    public function uploadImage() {
        // 实例化上传对象信息
        $file = request()->file('image');
        $album_id = input('post.album_id/d',2);

        // 获取图片大小信息
        list($imageWidth,$imageHeight) = getimagesize ($file->getRealPath());

        $qiniu = new model\Qiniu();
        $imageInfo = $qiniu->uploadImage($file);
        if($imageInfo['code'] == 0 || $imageInfo['data'] == -1) {
            return json(array(
                'code' => 400,
                'message' => $imageInfo['message'],
            ));
        }

        // 图片信息
        $imageData = array(
            'album_id' => $album_id,
            'image_name' => $imageInfo['data'],
            'image_url' => config('qiniu')['prefix_url'] . $imageInfo['data'],
            'width' => $imageWidth,
            'height' => $imageHeight,
        );

        // 图片信息保存到数据库中
        $saveImage = model('image')->saveImage($imageData);
        $albumCoverImage = model('album')->getAlbum($album_id);
        if($albumCoverImage['cover_url'] =='' || $albumCoverImage['cover_url'] == null || !$albumCoverImage['cover_url']) {
            $albumData = array(
                'cover_url' => $imageData['image_url'],
            );
            $saveAlbum = model('album')->updateAlbum($album_id,$albumData);
        }
        if(!$saveImage) {
            return json(array(
                'code' => 400,
                'message' => '上传失败',
            ));
        }
        return json(array(
            'code' => 200,
            'message' => '上传成功',
            'data' => array(
                'image_id' => $saveImage,
            ),
        ));
    }

    /**
     * 删除七牛云中图片
     */
    public function deleteImage() {
        $image_name = 'album/20190904/e33fa201909041447137758.PNG';
        $qiniu = new model\Qiniu();
        $imageInfo = $qiniu->deleteImage($image_name);
    }

    /**
     * 批量删除图册中图片
     * @return \think\response\Json
     */
    public function deleteImages() {
        $user_id = input('delete.user_id/d',0);
        $album_id = input('delete.album_id/d',0);
        $imageIdList = json_decode(input('delete.imageIdList',array()),true);

        if(!$user_id) {
            return json(array(
                'code' => 402,
                'message' => '请先登录',
                'data' => array(),
            ));
        }
        if(!$album_id) {
            return json(array(
                'code' => 400,
                'message' => '图册ID为空',
                'data' => array(),
            ));
        }

        if(!$imageIdList) {
            return json(array(
                'code' => 400,
                'message' => '删除图片ID为空',
                'data' => array(),
            ));
        }
        // 删除图片
        $deleteImage = model('image')->deleteImages($imageIdList);
        return json(array(
            'code' => 200,
            'message' => '图册删除成功',
            'data' => array(
                'deleteImage' => $deleteImage,
            ),
        ));
    }
}