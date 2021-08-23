<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2020/3/3
 * Time: 19:43
 */
namespace app\admin\Controller;
use think\Controller;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use app\common\Model;

class Upload extends Controller
{
    public function index() {
        $albumList =  json_decode(model('album')->listAlbumNoPage(),true);
        $this->assign('albums',$albumList);
        return $this->fetch();
    }

    public function upload() {
        $imageArray = input('post.valArr',array());
        $album_id = input('post.album_id/d',0);

        if($album_id == 0) {
            return json(array(
                'code' => 400,
                'status' => 0,
                'message' => '请选择图册',
            ));
        }
        $imageDataList = [];
        foreach ($imageArray as $index => $item) {
            if($item != 'undefined') {
                $tempArray = [];
                $tempArray = explode('|',$item);
                $imageDataList[] = array(
                    'album_id' => $album_id,
                    'image_name' => substr($tempArray[0],41),
                    'image_url' => $tempArray[0],
                    'width' => $tempArray[1],
                    'height' => $tempArray[2],
                );
            }
        }

        $saveImage = model('image')->saveAllImage($imageDataList);
        if(!$saveImage) {
            return json(array(
                'code' => 400,
                'status' => 0,
                'message' => '上传失败',
            ));
        }
        return json(array(
            'code' => 200,
            'status' => 1,
            'message' => '上传成功',
            'data' => array(
                'image_id' => $saveImage,
            ),
        ));

    }

    /**
     * 上传图片到七牛云
     * @return \think\response\Json
     */
    /*public function uploadImage() {
        // 实例化上传对象信息
        $file = request()->file('image');
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

        return json(array(
            'status' => 1,
            'message' => '图片上传成功',
            'res' => config('qiniu')['prefix_url'] . $imageInfo['data'] . '|' . $imageWidth . '|' . $imageHeight,
        ));
    }*/

    /**
     * 上传图片到阿里云
     * @return \think\response\Json
     */
    /*public function uploadImage() {
        // 实例化上传对象信息
        $file = request()->file('image');
        // 获取图片大小信息
        list($imageWidth,$imageHeight) = getimagesize ($file->getRealPath());
        if($file){
            $alioss = new model\Alioss();
            $res = $alioss->uploadFile('album',$file);
            if($res['code']==200){
                //图片路径
                $image_name = $res['data'];
                return json(array(
                    'status' => 1,
                    'message' => '图片上传成功',
                    'res' => config('alioss')['url'] . $image_name . '|' . $imageWidth . '|' . $imageHeight,
                ));
            }else {
                return json(array(
                    'code' => 400,
                    'message' => '上传失败',
                ));
            }
        }
    }*/

    /**
     * 上传图片到腾讯云
     * @return \think\response\Json
     */
    public function uploadImage() {
        // 实例化上传对象信息
        $file = request()->file('image');
        // 获取图片大小信息
        list($imageWidth,$imageHeight) = getimagesize ($file->getRealPath());
        if($file){
            $txcos = new model\Txcos();
            $res = $txcos->uploadFile('album',$file);

            if($res['code']==200){
                //图片路径
                $image_name = $res['data'];
                return json(array(
                    'status' => 1,
                    'message' => '图片上传成功',
                    'res' => config('txcos')['url'] . $image_name . '|' . $imageWidth . '|' . $imageHeight,
                ));
            }else {
                return json(array(
                    'code' => 400,
                    'message' => '上传失败',
                ));
            }
        }
    }
}
