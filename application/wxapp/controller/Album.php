<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/25
 * Time: 21:31
 */
namespace app\wxapp\controller;
use think\controller;

class Album extends controller
{

    /**
     * 获取所有图册列表
     * @return mixed
     */
    public function listAlbum() {
        $user_id = input('get.user_id/d',0);
        $userLikes = array();
        // 获取图册列表
        $albums = model('album')->listAlbum(array(),input('get.page/d',1),input('get.pageSize/d',20));

        // 获取图册相关用户列表
        $userIdList = array_unique(array_column(json_decode($albums),'user_id'));
        $users = model('user')->listUser($userIdList);

        // 获取当前登录用户的图册关注数据
        if($user_id) {
            $albumIdList = array_unique(array_column(json_decode($albums),'album_id'));
            $userLikes = model('relalike')->listLike($user_id,$albumIdList);
        }

        // 将用户信息、关注信息写入图册中
        foreach ($albums as $index => $item) {
            $findLikes = 0;
            // 将用户姓名、头像写入图册中
            foreach ($users as $key => $value) {
                if($item['user_id'] == $value['user_id']) {
                    $albums[$index]['user_id'] = $value['user_id'];
                    $albums[$index]['user_name'] = $value['nickName'];
                    $albums[$index]['avatar_url'] = $value['avatarUrl'];
                }
            }
            // 将用户关注信息写入图册中
            foreach ($userLikes as $p => $q) {
                if($item['album_id'] == $q['album_id']) {
                    // 关注
                    $albums[$index]['is_collect'] = 1;
                    $findLikes = 1;
                }
            }
            if(!$findLikes) {
                // 未关注
                $albums[$index]['is_collect'] = 0;
            }
            if($item['cover_url'] =='' || $item['cover_url'] == null || !$item['cover_url']) {

                $cover_image = model('image')->getCoverImage($item['album_id']);
                if($cover_image) {
                    $albums[$index]['cover_url'] = $cover_image['image_url'];
                }
            }
            $albums[$index]['photo_num'] = model('image')->getImageCount($item['album_id']);

        }
        return $albums;
    }

    public function getAlbum() {
        $user_id = input('get.user_id/d',0);
        $album_id = input('get.album_id/d',0);

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
        // 获取图册信息
        $albumInfo = model('album')->getAlbum($album_id);
        return json(array(
            'code' => 200,
            'message' => '图册信息获取成功',
            'data' => array(
                'albumInfo' => $albumInfo,
            ),
        ));
    }

    /**
     * 删除图册
     * @return \think\response\Json
     */
    public function deleteAlbum() {
        $user_id = input('delete.user_id/d',0);
        $album_id = input('delete.album_id/d',0);

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
        // 删除图册信息
        $deleteAlbum = model('album')->deleteAlbum($album_id);
        return json(array(
            'code' => 200,
            'message' => '图册删除成功',
            'data' => array(
                'deleteAlbum' => $deleteAlbum,
            ),
        ));
    }

    /**
     * 关注/取消关注
     * @return \think\response\Json
     */
    public function updateIsLike() {
        $returnData = array();
        $user_id = input('put.user_id/d',0);
        $album_id = input('put.album_id/d',0);


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
        // 查询现有关注关系
        $isLike = model('relalike')->getRelaLike($user_id,$album_id);
        if($isLike) {
            // 已关注
            // 删除关注关联信息
            $deleteRelaLike = model('relalike')->deleteRelaLike(array($isLike['relalike_id']));
            if($deleteRelaLike == 1) {
                // 删除成功，将图册中关注人数减1
                $updateAlbum = model('album')->updateAlbumFiledDec($album_id,'like_num');
                if($updateAlbum == 1) {
                    // 关注人数更新成功
                    $returnData = array(
                        'code' => 201,
                        'message' => '取消关注成功',
                        'data' => array(),
                    );
                }else {
                    // 关注人数更新失败，恢复关注关系
                    $saveRelaLike = model('relalike')->saveRelaLike(array("user_id" => $user_id, "album_id" => $album_id));
                    $returnData = array(
                        'code' => 400,
                        'message' => '取消关注失败',
                        'data' => array(),
                    );
                }
            }
        }else {
            // 未关注
            // 新增关注关联信息
            $saveRelaLike = model('relalike')->saveRelaLike(array("user_id" => $user_id, "album_id" => $album_id));
            if($saveRelaLike) {
                // 新增成功，将图册中喜欢人数加1
                $updateAlbum = model('album')->updateAlbumFiledInc($album_id,'like_num');
                if($updateAlbum == 1) {
                    // 关注人数更新成功
                    $returnData = array (
                        'code' => 201,
                        'message' => '关注成功',
                        'data' => array(
                            'relalike_id' => $saveRelaLike,
                        ),
                    );
                }else {
                    // 关注人数更新失败，恢复关注关系
                    $deleteRelaLike = model('relalike')->deleteRelaLike(array($saveRelaLike));
                    $returnData = array (
                        'code' => 400,
                        'message' => '关注失败',
                        'data' => array(),
                    );
                }
            }
        }
        return json($returnData);
    }


    /**
     * 图册查看次数自增
     * @return \think\response\Json
     */
    public function updateViewNum() {
        $album_id = input('put.album_id/d',0);
        if(!$album_id) {
            return json(array(
                'code' => 400,
                'message' => '图册ID获取失败',
                'data' => array(),
            ));
        }
        $updateViewNum = model('album')->updateAlbumFiledInc($album_id,'view_num');
        return json(array(
            'code' => 201,
            'message' => '查看次数更新成功',
            'data' => array(
                'updateViewNum' => $updateViewNum,
            ),
        ));
    }

    /**
     * 获取用户相册列表
     * @return \think\response\Json
     */
    public function listUserAlbum() {
        $user_id = input('get.user_id/d',0);
        $page = input('get.page/d',1);
        $pageSize = input('get.pageSize/d',10);
        if(!$user_id) {
            return json(array(
                'code' => 400,
                'message' => '用户ID获取失败',
            ));
        }
        $userAlbumList = model('album')->listUserAlbum($user_id,$page,$pageSize);
        foreach ($userAlbumList as $index => $item) {
            if($item['cover_url'] =='' || $item['cover_url'] == null || !$item['cover_url']) {
                $cover_image = model('image')->getCoverImage($item['album_id']);
                if($cover_image) {
                    $userAlbumList[$index]['cover_url'] = $cover_image['image_url'];
                }
            }
            $userAlbumList[$index]['photo_num'] = model('image')->getImageCount($item['album_id']);
        }
        if($userAlbumList) {
            return json(array(
                'code' => 200,
                'message' => '用户相册获取成功',
                'data' => array(
                    'userAlbumList' => $userAlbumList,
                )
            ));
        }else {
            return json(array(
                'code' => 400,
                'message' => '用户相册获取失败',
            ));
        }

    }

    /**
     * 获取用户关注相册列表
     * @return \think\response\Json
     */
    public function listLikeAlbum() {
        $user_id = input('get.user_id/d',0);
        $page = input('get.page/d',1);
        $pageSize = input('get.pageSize/d',10);
        if(!$user_id) {
            return json(array(
                'code' => 400,
                'message' => '用户ID获取失败',
            ));
        }
        // 获取关注记录
        $relaLike = model('relalike')->getUserRelaLike($user_id,$page,$pageSize);
        if($relaLike) {
            // 获取关注的图册ID
            $albumIdList = array_unique(array_column(json_decode($relaLike,true),'album_id'));
            // 根据图册ID批量获取图册列表
            $albumList = model('album')->listAlbumByIDS($albumIdList);
            if($albumList) {
                foreach ($albumList as $key => $value) {
                    $imageUrlList = array();
                    $imageUrlList = model('image')->listImages($value['album_id'],1,5);
                    if($imageUrlList) {
                        $imageUrlList = array_column(json_decode($imageUrlList,true),'image_url');
                        foreach ($imageUrlList as $p => $q) {
                            $imageUrlList[$p] = $q . '?' . config('custom_list');
                        }
                    }
                    $albumList[$key]['imageUrlList'] = $imageUrlList;
                    if($value['cover_url'] =='' || $value['cover_url'] == null || !$value['cover_url']) {
                        $cover_image = model('image')->getCoverImage($value['album_id']);
                        if($cover_image) {
                            $userAlbumList[$key]['cover_url'] = $cover_image['image_url'];
                        }
                    }
                    $albumList[$key]['photo_num'] = model('image')->getImageCount($value['album_id']);
                }
                return json(array(
                    'code' => 200,
                    'message' => '关注图册获取成功',
                    'data' => array(
                        'likeAlbumList' => $albumList,
                    )
                ));
            }else {
                return json(array(
                    'code' => 400,
                    'message' => '关注图册获取失败',
                ));
            }
        }else {
            return json(array(
                'code' => 400,
                'message' => '未有关注图册',
            ));
        }


    }

    /**
     * 获取用户打赏图册列表
     * @return \think\response\Json
     */
    public function listPayAlbum() {
        $user_id = input('get.user_id/d',0);
        $page = input('get.page/d',1);
        $pageSize = input('get.pageSize/d',10);
        if(!$user_id) {
            return json(array(
                'code' => 400,
                'message' => '用户ID获取失败',
            ));
        }
        // 获取关注记录
        $relaPay = model('relapay')->listUserRelaPay($user_id,$page,$pageSize);
        if($relaPay) {
            // 获取打赏的图册ID
            $albumIdList = array_unique(array_column(json_decode($relaPay,true),'album_id'));
            // 根据图册ID批量获取图册列表
            $albumList = model('album')->listAlbumByIDS($albumIdList);
            foreach ($albumList as $key => $value) {
                $albumList[$key]['photo_num'] = model('image')->getImageCount($value['album_id']);
            }
            if($albumList) {
                return json(array(
                    'code' => 200,
                    'message' => '打赏图册获取成功',
                    'data' => array(
                        'payAlbumList' => $albumList,
                    )
                ));
            }else {
                return json(array(
                    'code' => 400,
                    'message' => '打赏图册获取失败',
                ));
            }
        }else {
            return json(array(
                'code' => 400,
                'message' => '未有打赏图册',
            ));
        }
    }

    /**
     *
     * @return \think\response\Json
     */
    public function listRelaSubjectAlbum() {
        $user_id = input('get.user_id/d',13);
        $subject_id = input('get.subject_id/d',1);
        $page = input('get.page/d',1);
        $pageSize = input('get.pageSize/d',10);

        $userLikes = array();
        // 获取关联信息
        $relaAlbumList  = model('Relasubject')->listRelaSubject($subject_id,$page,$pageSize);

        if($relaAlbumList) {
            // 获取z专题的图册ID
            $albumIdList = array_unique(array_column(json_decode($relaAlbumList,true),'album_id'));
            // 根据图册ID批量获取图册列表
            $albumList = model('album')->listAlbumByIDS($albumIdList);


            foreach ($albumList as $index => $item) {
                if($item['cover_url'] =='' || $item['cover_url'] == null || !$item['cover_url']) {
                    $cover_image = model('image')->getCoverImage($item['album_id']);
                    if($cover_image) {
                        $albumList[$index]['cover_url'] = $cover_image['image_url'];
                    }
                }
                $albumList[$index]['photo_num'] = model('image')->getImageCount($item['album_id']);
            }
            if($albumList) {
                return json(array(
                    'code' => 200,
                    'message' => '专题图册获取成功',
                    'data' => array(
                        'subjectlList' => $albumList,
                    )
                ));
            }else {
                return json(array(
                    'code' => 400,
                    'message' => '专题图册获取失败',
                ));
            }
        }else {
            return json(array(
                'code' => 400,
                'message' => '专题相册关联系获取失败',
            ));
        }
    }

    public function saveAlbum() {
        $user_id = input('post.user_id/d',13);
        $is_private = input('post.is_private/d',0);
        $is_pay = input('post.is_pay/d',0);
        $pay_money = input('post.pay_money/d',0);
        $name = input('post.name/s','');

        if(!$user_id) {
            return json(array(
                'code' => 400,
                'message' => '用户ID获取失败',
            ));
        }

        if(!$name) {
            return json(array(
                'code' => 400,
                'message' => '图册名不能为空',
            ));
        }

        $albumData = array(
            'user_id' => $user_id,
            'is_private' => $is_private,
            'is_pay' => $is_pay,
            'pay_money' => $pay_money,
            'name' => $name,
        );

        $album_id = model('album')->saveAlbum($albumData);

        if($album_id) {
            return json(array(
                'code' => 200,
                'message' => '图册创建成功',
                'data' => array(
                    'album_id' => $album_id,
                )
            ));
        }else {
            return json(array(
                'code' => 400,
                'message' => '图册创建失败',
            ));
        }
    }

    /**
     * 更新相册信息
     * @return \think\response\Json
     */
    public function updateAlbum() {
        $user_id = input('put.user_id/d',13);
        $is_private = input('put.is_private/d',0);
        $is_pay = input('put.is_pay/d',0);
        $pay_money = input('put.pay_money/d',0);
        $name = input('put.name/s','');
        $album_id = input('put.album_id/d',0);

        if(!$user_id) {
            return json(array(
                'code' => 400,
                'message' => '用户ID获取失败',
            ));
        }

        if(!$album_id) {
            return json(array(
                'code' => 400,
                'message' => '图册ID获取失败',
            ));
        }

        if(!$name) {
            return json(array(
                'code' => 400,
                'message' => '图册名不能为空',
            ));
        }

        $albumData = array(
            'is_private' => $is_private,
            'is_pay' => $is_pay,
            'pay_money' => $pay_money,
            'name' => $name,
        );

        // 更新图册信息
        $album_id = model('album')->updateAlbum($album_id,$albumData);

        if($album_id) {
            return json(array(
                'code' => 200,
                'message' => '图册修改成功',
                'data' => array(
                    'album_id' => $album_id,
                )
            ));
        }else {
            return json(array(
                'code' => 400,
                'message' => '图册修改失败',
            ));
        }
    }


    /**
     * 修改图册封面照片
     * @return \think\response\Json
     */
    public function updateAlbumCover() {
        $user_id = input('put.user_id/d',13);
        $album_id = input('put.album_id/d',0);
        $image_id = input('put.image_id/d',0);

        if(!$user_id) {
            return json(array(
                'code' => 400,
                'message' => '用户ID获取失败',
            ));
        }

        if(!$album_id) {
            return json(array(
                'code' => 400,
                'message' => '图册ID获取失败',
            ));
        }

        if(!$image_id) {
            return json(array(
                'code' => 400,
                'message' => '图片ID获取失败',
            ));
        }


        // 根据图片ID获取图片信息
        $imageInfo = model('image')->getImage($image_id);
        if(!$imageInfo) {
            return json(array(
                'code' => 400,
                'message' => '图片信息获取失败',
            ));
        }
        // 封装封面信息
        $albumData = array(
            'cover_url' => $imageInfo['image_url'],
        );
        // 更新图册信息
        $album_id = model('album')->updateAlbum($album_id,$albumData);

        if($album_id) {
            return json(array(
                'code' => 200,
                'message' => '图册修改成功',
                'data' => array(
                    'album_id' => $album_id,
                )
            ));
        }else {
            return json(array(
                'code' => 400,
                'message' => '图册修改失败',
            ));
        }
    }

}