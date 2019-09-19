<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/9/4
 * Time: 13:48
 */

namespace app\common\model;

use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;


class Qiniu
{

    public function uploadImage($file = array()) {
        if(!$file) {
            return array(
                'code' => 0,
                'message' => '文件对象为空',
            );
        }
        // 获取上传图片文件及路径信息
        $fileInfo = $this->getFileInfo($file);
        // 获取上传服务器后文件信息
        $imageInfo = $this->uploadTOServer($fileInfo['key'],$fileInfo['filePath']);
        // 返回数据
        return array(
            'code' => 1,
            'message' => '上传成功',
            'data' => $imageInfo,
        );
    }

    /**
     * 获取上传文件路径及名称信息
     * @param array $file
     * @return int|string
     */
    protected function getFileInfo($file = array()) {
        // 获取图片的本地路径
        $filePath = $file->getRealPath();
        // 获取文件后缀
        $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
        // 上传到七牛后保存的文件名
        $key =config('qiniu')['prefix_name'] . '/' . date('Ymd') . '/' . substr(md5($file->getRealPath()) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;
        // 返回文件名
        return array(
            'key' => $key,
            'filePath' => $filePath
        );
    }

    /**
     * 上传数据到七牛云
     * @param string $key
     * @param string $filePath
     * @return int|string
     */
    protected function uploadTOServer($key = '', $filePath = '') {
        // 初始化签权对象
        $auth = new Auth(config('qiniu')['access_key'], config('qiniu')['secret_key']);
        // 生成上传Token
        $token = $auth->uploadToken(config('qiniu')['bucket_name']);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        // 返回数据
        if ($err !== null) {
            return -1;
        } else {
            return $ret['key'];
        }
    }

    // 删除文件
    public function deleteImage($key = '') {
        // 初始化签权对象
        $auth = new Auth(config('qiniu')['access_key'], config('qiniu')['secret_key']);
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        $err = $bucketManager->delete(config('qiniu')['bucket_name'], $key);
        if ($err) {
            return array(
                'code' => 0,
                'message' => '删除失败',
            );
        }
        return array(
            'code' => 1,
            'message' => '删除成功',
        );
    }

    /**
     * 批量上传文件
     * @param array $keys
     * @return array
     */
    public function deleteImages($keys = array()) {
        $auth = new Auth(config('qiniu')['access_key'], config('qiniu')['secret_key']);
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        //每次最多不能超过1000个
        $ops = $bucketManager->buildBatchDelete(config('qiniu')['bucket_name'], $keys);
        list($ret, $err) = $bucketManager->batch($ops);
        if ($err) {
            return array(
                'code' => 0,
                'message' => '批量删除失败',
            );
        } else {
            return array(
                'code' => 1,
                'message' => '批量删除成功',
            );
        }
    }
}