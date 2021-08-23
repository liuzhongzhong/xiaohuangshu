<?php


namespace app\common\model;

use think\Controller;
use think\Image;
use Qcloud\Cos\Client;
use Qcloud\Cos\Exception;

class Txcos
{
    public static function uploadFile($mulu,$file) {
        $cosClient = new Client(array(
            'region' => config('txcos')['region'], //存储桶地域
            'credentials'=> array(
                'secretId'  => config('txcos')['secretId'] ,  //云API密钥 SecretId"
                'secretKey' => config('txcos')['secretKey'],  //云API密钥 SecretKey"
                )
            )
        );

        try {
            if ($file) {
                $imageFile = Image::open($file);
                $keyName = date('Ymd') . '/' . sha1(date('YmdHis', time()) . uniqid()) . '.' . $imageFile->type(); // 文件名
                $result = $cosClient->putObject(array(
                    'Bucket' => config('txcos')['bucket'] , //存储桶
                    'Key' => $keyName,
                    'Body' => $file));
                $uploadRes = ['code'=>200,'msg'=>'上传成功','data'=>$keyName];
            }
        } catch (Exception $e) {
            $uploadRes = ['code'=>0,'msg'=>$e->getMessage(),'data'=>''];
        }
        return $uploadRes;
    }
}
