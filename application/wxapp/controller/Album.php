<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/25
 * Time: 21:31
 */
namespace app\wxapp\Controller;
use think\Controller;

class Album extends Controller
{

    /**
     * 获取所有图册列表
     * @return mixed
     */
    public function listAlbum() {
        $user_id = input('get.user_id/d',0);
        $userLikes = array();

        // 获取是否隐藏相册
        $disabledAlbum = model('general')->listGeneral();

        // 获取图册列表
        $albums = model('album')->listAlbum(array(),input('get.page/d',1),input('get.pageSize/d',20),$disabledAlbum[0]['value']);

        // 获取图册相关用户列表
        $userIdList = array_unique(array_column(json_decode($albums),'user_id'));
        $users = model('user')->listUser($userIdList);

        // 获取图册相关虚拟用户列表
        $virtualUserIdList = array_unique(array_column(json_decode($albums),'virtual_user'));
        $virtualUsers = model('virtualuser')->listUser($virtualUserIdList);

        // 获取当前登录用户的图册关注数据
        if($user_id) {
            $albumIdList = array_unique(array_column(json_decode($albums),'album_id'));
            $userLikes = model('relalike')->listLike($user_id,$albumIdList);
        }

        // 将用户信息、关注信息写入图册中
        foreach ($albums as $index => $item) {
            $findLikes = 0;
            // 将用户姓名、头像写入图册中
            if($albums[$index]['virtual_user'] == 0) {
                // 真实用户
                foreach ($users as $key => $value) {
                    if($item['user_id'] == $value['user_id']) {
                        $albums[$index]['user_type'] = 0; //真实用户
                        $albums[$index]['user_id'] = $value['user_id'];
                        $albums[$index]['user_name'] = $value['nickName'];
                        $albums[$index]['avatar_url'] = $value['avatarUrl'];
                        break;
                    }
                }
            }else {
                // 虚拟用户
                foreach ($virtualUsers as $key => $value) {
                    if($item['virtual_user'] == $value['user_id']) {
                        $albums[$index]['user_type'] = 1; //虚拟用户
                        $albums[$index]['user_id'] = $value['user_id'];
                        $albums[$index]['user_name'] = $value['nickName'];
                        $albums[$index]['avatar_url'] = $value['avatarUrl'];
                        break;
                    }
                }
            }

            // 将用户关注信息写入图册中
            foreach ($userLikes as $p => $q) {
                if($item['album_id'] == $q['album_id']) {
                    // 关注
                    $albums[$index]['is_collect'] = 1;
                    $findLikes = 1;
                    break;
                }
            }
            if(!$findLikes) {
                // 未关注
                $albums[$index]['is_collect'] = 0;
            }
            if($item['cover_url'] == '' || $item['cover_url'] == null || !$item['cover_url']) {
                $cover_image = model('image')->getCoverImage($item['album_id']);
                if($cover_image) {
                    if(substr($cover_image['image_url'],0,12) == 'http://image') {
                        $albums[$index]['cover_url'] = $cover_image['image_url'] . '?' . config('custom_list');
                    }else {
                        $albums[$index]['cover_url'] = $cover_image['image_url'] . '?' . config('custom_list_alioss');
                    }
                    $albums[$index]['cover_url_width'] = $cover_image['width'];
                    $albums[$index]['cover_url_height'] = $cover_image['height'];
                }
            }else {
                // 优化图片体积
                if(substr($item['cover_url'],0,12) == 'http://image') {
                    $albums[$index]['cover_url'] = $item['cover_url'] . '?' . config('custom_list');
                }else {
                    $albums[$index]['cover_url'] = $item['cover_url'] . '?' . config('custom_list_alioss');
                }
                if($item['cover_url_bak'] != null || $item['cover_url_bak'] != '') {
                    if(substr($item['cover_url_bak'],0,12) == 'http://image') {
                        $albums[$index]['cover_url_bak'] = $item['cover_url_bak'] . '?' . config('custom_list');
                    }else {
                        $albums[$index]['cover_url_bak'] = $item['cover_url_bak'] . '?' . config('custom_list_alioss');
                    }
                }
            }
            // 获取图册内图片数量
            $albums[$index]['photo_num'] = model('image')->getImageCount($item['album_id']);
        }
        return $albums;
    }

    /**
     * 获取单个图册信息
     * @return \think\response\Json
     */
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
        if(substr($albumInfo['cover_url'],0,12) == 'http://image') {
            $albumInfo['cover_url'] = $albumInfo['cover_url'] . '?' . config('custom_list');
        }else {
            $albumInfo['cover_url'] = $albumInfo['cover_url'] . '?' . config('custom_list_alioss');
        }

        if($albumInfo['cover_url_bak'] != null || $albumInfo['cover_url_bak'] != '') {
            if(substr($albumInfo['cover_url_bak'],0,12) == 'http://image') {
                $albumInfo['cover_url_bak'] = $albumInfo['cover_url_bak'] . '?' . config('custom_list');
            }else {
                $albumInfo['cover_url_bak'] = $albumInfo['cover_url_bak'] . '?' . config('custom_list_alioss');
            }
        }
        // 获取图册专题信息
        $relaSubject = model('relasubject')->getRelaSubject($album_id);
        $subjectIndex = -1;
        if($relaSubject) {
            // 获取专题信息
            $subjectList = model('subject')->listSubject();
            foreach ($subjectList as $index => $item) {
                if($item['subject_id'] == $relaSubject['subject_id']) {
                    $subjectIndex = $index;
                    break;
                }
            }
        }
        $albumInfo['subjectIndex'] = ($subjectIndex == -1)? 0 : ($subjectIndex + 1);
        return json(array(
            'code' => 200,
            'message' => '图册信息获取成功',
            'data' => array(
                'albumInfo' => $albumInfo,
            ),
        ));
    }

    /**
     * 删除某个图册
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
     * 关注/取消关注图册
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
     * 自增图册查看次数
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
        // 查看次数加1
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
     * 获取某用户的相册列表
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
                    if(substr($cover_image['image_url'],0,12) == 'http://image') {
                        $userAlbumList[$index]['cover_url'] = $cover_image['image_url'] . '?' . config('custom_list');
                    }else {
                        $userAlbumList[$index]['cover_url'] = $cover_image['image_url'] . '?' . config('custom_list_alioss');
                    }

                }
            }else {
                if(substr($item['cover_url'],0,12) == 'http://image') {
                    $userAlbumList[$index]['cover_url'] = $item['cover_url'] . '?' . config('custom_list');
                }else {
                    $userAlbumList[$index]['cover_url'] = $item['cover_url'] . '?' . config('custom_list_alioss');
                }

                if($item['cover_url_bak'] != null || $item['cover_url_bak'] != '') {
                    if(substr($item['cover_url_bak'],0,12) == 'http://image') {
                        $userAlbumList[$index]['cover_url_bak'] = $item['cover_url_bak'] . '?' . config('custom_list');
                    }else {
                        $userAlbumList[$index]['cover_url_bak'] = $item['cover_url_bak'] . '?' . config('custom_list_alioss');
                    }

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

        // 获取是否隐藏相册
        $disabledAlbum = model('general')->listGeneral();
        // 获取关注记录
        $relaLike = model('relalike')->getUserRelaLike($user_id,$page,$pageSize);
        if($relaLike) {
            // 获取关注的图册ID
            $albumIdList = array_unique(array_column(json_decode($relaLike,true),'album_id'));
            // 根据图册ID批量获取图册列表
            $albumList = model('album')->listAlbumByIDS($albumIdList,0,$disabledAlbum[0]['value']);
            if($albumList) {
                foreach ($albumList as $key => $value) {
                    $imageUrlList = array();
                    $imageUrlList = model('image')->listImages($value['album_id'],1,5);
                    if($imageUrlList) {
                        $imageUrlList = array_column(json_decode($imageUrlList,true),'image_url');
                        foreach ($imageUrlList as $p => $q) {
                            if(substr($q,0,12) == 'http://image') {
                                $imageUrlList[$p] = $q . '?' . config('custom_list');
                            }else {
                                $imageUrlList[$p] = $q . '?' . config('custom_list_alioss');
                            }

                        }
                    }
                    $albumList[$key]['imageUrlList'] = $imageUrlList;
                    if($value['cover_url'] =='' || $value['cover_url'] == null || !$value['cover_url']) {
                        $cover_image = model('image')->getCoverImage($value['album_id']);
                        if($cover_image) {
                            if(substr($cover_image['image_url'],0,12) == 'http://image') {
                                $userAlbumList[$key]['cover_url'] = $cover_image['image_url'] . '?' . config('custom_list');
                            }else {
                                $userAlbumList[$key]['cover_url'] = $cover_image['image_url'] . '?' . config('custom_list_alioss');
                            }

                        }
                    }else {
                        if(substr($value['cover_url'],0,12) == 'http://image') {
                            $userAlbumList[$key]['cover_url'] = $value['cover_url'] . '?' . config('custom_list');
                        }else {
                            $userAlbumList[$key]['cover_url'] = $value['cover_url'] . '?' . config('custom_list_alioss');
                        }

                        if($value['cover_url_bak'] != null || $value['cover_url_bak'] != '') {
                            if(substr($value['cover_url_bak'],0,12) == 'http://image') {
                                $userAlbumList[$key]['cover_url_bak'] = $value['cover_url_bak'] . '?' . config('custom_list');
                            }else {
                                $userAlbumList[$key]['cover_url_bak'] = $value['cover_url_bak'] . '?' . config('custom_list_alioss');
                            }
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

        $disabledAlbum = model('general')->listGeneral();
        // 获取关注记录
        $relaPay = model('relapay')->listUserRelaPay($user_id,$page,$pageSize);
        if($relaPay) {
            // 获取打赏的图册ID
            $albumIdList = array_unique(array_column(json_decode($relaPay,true),'album_id'));
            // 根据图册ID批量获取图册列表
            $albumList = model('album')->listAlbumByIDS($albumIdList,0,$disabledAlbum[0]['value']);
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
     * 获取专题图册列表
     * @return \think\response\Json
     */
    public function listRelaSubjectAlbum() {
        $user_id = input('get.user_id/d',13);
        $subject_id = input('get.subject_id/d',1);
        $page = input('get.page/d',1);
        $pageSize = input('get.pageSize/d',10);
        $userLikes = array();

        $disabledAlbum = model('general')->listGeneral();
        // 获取关联信息
        $relaAlbumList  = model('Relasubject')->listRelaSubject($subject_id,$page,$pageSize);
        if($relaAlbumList) {
            // 获取z专题的图册ID
            $albumIdList = array_unique(array_column(json_decode($relaAlbumList,true),'album_id'));
            // 根据图册ID批量获取图册列表
            $albumList = model('album')->listAlbumByIDS($albumIdList,1,$disabledAlbum[0]['value']);
            foreach ($albumList as $index => $item) {
                if($item['cover_url'] =='' || $item['cover_url'] == null || !$item['cover_url']) {
                    $cover_image = model('image')->getCoverImage($item['album_id']);
                    if($cover_image) {
                        if(substr($cover_image['image_url'],0,12) == 'http://image') {
                            $albumList[$index]['cover_url'] = $cover_image['image_url']  . '?' . config('custom_list');
                        }else {
                            $albumList[$index]['cover_url'] = $cover_image['image_url']  . '?' . config('custom_list_alioss');
                        }

                    }
                }else {
                    if(substr($item['cover_url'],0,12) == 'http://image') {
                        $albumList[$index]['cover_url'] = $item['cover_url'] . '?' . config('custom_list');
                    }else {
                        $albumList[$index]['cover_url'] = $item['cover_url'] . '?' . config('custom_list_alioss');
                    }

                    if($item['cover_url_bak'] != null || $item['cover_url_bak'] != '') {
                        if(substr($item['cover_url_bak'],0,12) == 'http://image') {
                            $albumList[$index]['cover_url_bak'] = $item['cover_url_bak'] . '?' . config('custom_list');
                        }else {
                            $albumList[$index]['cover_url_bak'] = $item['cover_url_bak'] . '?' . config('custom_list_alioss');
                        }

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

    /**
     * 创建一个图册
     * @return \think\response\Json
     */
    public function saveAlbum() {
        $user_id = input('post.user_id/d',13);
        $is_private = input('post.is_private/d',0);
        $is_pay = input('post.is_pay/d',0);
        $pay_money = input('post.pay_money/d',0);
        $name = input('post.name/s','');
        $subject_id = input('post.subject_id/d',0);

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
        // 判断是否有专题信息
        if($subject_id != 0) {
            // 写入专题信息
            $subjectData = array(
                'subject_id' => $subject_id,
                'album_id' => $album_id,
            );
            $relaSubject = model('relasubject')->saveRelaSubject($subjectData);
            if(!$relaSubject) {
                return json(array(
                    'code' => 400,
                    'message' => '专题绑定失败',
                    'data' => array(
                        'album_id' => $album_id,
                    )
                ));
            }
        }
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
     * 更新图册信息
     * @return \think\response\Json
     */
    public function updateAlbum() {
        $user_id = input('put.user_id/d',13);
        $is_private = input('put.is_private/d',0);
        $is_pay = input('put.is_pay/d',0);
        $pay_money = input('put.pay_money/d',0);
        $name = input('put.name/s','');
        $album_id = input('put.album_id/d',0);
        $subject_id = input('post.subject_id/d',0);

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
        $new_album_id = model('album')->updateAlbum($album_id,$albumData);
        // 判断是否有专题信息
        if($subject_id != 0) {
            // 写入专题信息
            $subjectData = array(
                'subject_id' => $subject_id,
                'album_id' => $album_id,
            );
            // 获取专题信息记录
            $relaSubject = model('relasubject')->getRelaSubject($album_id);
            if($relaSubject) {
                // 有专题记录，更新专题记录
                if($relaSubject['subject_id'] != $subject_id) {
                    $newRelaSubject = model('relasubject')->updateRelaSubject($relaSubject['relasubject_id'],$subjectData);
                    if(!$newRelaSubject) {
                        return json(array(
                            'code' => 400,
                            'message' => '专题修改失败',
                            'data' => array(
                                'album_id' => $album_id,
                            )
                        ));
                    }
                }
            }else {
                // 没有专题记录，创建专题信息
                $newRelaSubject = model('relasubject')->saveRelaSubject($subjectData);
                if(!$newRelaSubject) {
                    return json(array(
                        'code' => 400,
                        'message' => '专题绑定失败',
                        'data' => array(
                            'album_id' => $album_id,
                        )
                    ));
                }
            }

        }
        if($new_album_id || $newRelaSubject) {
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
        $cover_type = input('put.cover_type/d',0);

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
        if($cover_type == 0) {
            $albumData = array(
                'cover_url' => $imageInfo['image_url'],
                'cover_url_width' => $imageInfo['width'],
                'cover_url_height' => $imageInfo['height'],
            );
        } else {
            $albumData = array(
                'cover_url_bak' => $imageInfo['image_url'],
            );
        }

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