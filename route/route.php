<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 获取所有图册列表


/**
 * 图册
 */
// 获取所有图册列表
Route::get('api/album/albums','wxapp/album/listalbum');
// 获取单个图册信息
Route::get('api/album/album','wxapp/album/getalbum');
// 获取某用户创建图册列表
Route::get('api/album/user','wxapp/album/listUserAlbum');
// 获取某用户收藏图册列表
Route::get('api/album/like','wxapp/album/listLikeAlbum');
// 获取某用户打赏图册列表
Route::get('api/album/pay','wxapp/album/listPayAlbum');
// 获取图册中图片列表
Route::get('api/album/images','wxapp/image/listimages');
// 获取图册中图片真实地址
Route::get('api/album/urls','wxapp/image/listimageurl');
// 获取专题图册列表
Route::get('api/album/subject','wxapp/album/listRelaSubjectAlbum');


// 创建图册
Route::post('api/album/album','wxapp/album/saveAlbum');
// 打赏图册
Route::post('api/album/pay','wxapp/wxpay/savePayInfo');


// 收藏/取消收藏图册
Route::put('api/album/like','wxapp/album/updateislike');
// 新增图册的查看次数
Route::put('api/album/viewtimes','wxapp/album/updateViewNum');
// 修改图册信息
Route::put('api/album/album','wxapp/album/updatealbum');
// 修改图册封面
Route::put('api/album/cover','wxapp/album/updatealbumcover');


// 删除图册
Route::delete('api/album/album','wxapp/album/deletealbum');
// 批量删除图册中图片
Route::delete('api/album/images','wxapp/image/deleteImages');


/**
 * 专题
 */

// 获取专题列表
Route::get('api/subject/subjects','wxapp/subject/listsubject');


/**
 * 图片
 */

// 上传图片
Route::post('api/image/image','wxapp/image/uploadImage');


/**
 * 支付
 */

// 调用微信支付
Route::get('api/wxpay/pay','wxapp/wxpay/getAdvancePayment');


/**
 * 用户
 */

// 获取用户信息
Route::get('api/user/user','wxapp/user/getUser');


// 登录
Route::post('api/login/login','wxapp/user/login');
// 保存用户信息
Route::post('api/user/user','wxapp/user/saveUser');

// 分享
Route::post('api/album/share','wxapp/share/saveShareInfo');

return [

];
