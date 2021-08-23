<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2020/4/16
 * Time: 14:14
 */

namespace app\common\model;
use think\Controller;
use think\Image;
require_once '../extend/aliyun-oss/autoload.php';

use OSS\Core\OssException;
use OSS\OssClient;

class Alioss
{
    public static function uploadFile($mulu,$file) {
        $resResult = Image::open($file);
        try {
            $KeyId = config('alioss')['KeyId'];
            $KeySecret = config('alioss')['KeySecret'];
            $EndPoint = config('alioss')['EndPoint'];
            $Bucket = config('alioss')['Bucket'];
            //实例化
            $ossClient = new OssClient($KeyId, $KeySecret, $EndPoint);
            //sha1加密 生成文件名 连接后缀
            $fileName = $mulu . '/' . date('Ymd') . '/' . sha1(date('YmdHis', time()) . uniqid()) . '.' . $resResult->type();
            //执行阿里云上传
            $result = $ossClient->uploadFile($Bucket, $fileName, $file->getInfo()['tmp_name']);
            //图片地址:$result['info']['url']
            $arr = ['code'=>200,'msg'=>'上传成功','data'=>$fileName];
        } catch (OssException $e) {
            $arr = ['code'=>0,'msg'=>$e->getMessage(),'data'=>''];
        }
        return $arr;
    }
}