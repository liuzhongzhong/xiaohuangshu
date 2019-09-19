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

// 获取所有相册列表
Route::get('api/album/albums','wxapp/album/listalbum');

Route::get('api/album/album','wxapp/album/getalbum');

Route::get('api/subject/subjects','wxapp/subject/listsubject');

Route::put('api/album/like','wxapp/album/updateislike');
// 修改图册信息
Route::put('api/album/album','wxapp/album/updatealbum');
// 修改图册封面
Route::put('api/album/cover','wxapp/album/updatealbumcover');
// 删除图册
Route::delete('api/album/album','wxapp/album/deletealbum');
Route::get('api/album/images','wxapp/image/listimages');
// 批量删除图片
Route::delete('api/album/images','wxapp/image/deleteImages');
Route::get('api/album/urls','wxapp/image/listimageurl');

Route::put('api/album/viewtimes','wxapp/album/updateViewNum');

Route::post('api/login/login','wxapp/user/login');

Route::post('api/user/user','wxapp/user/saveUser');

Route::get('api/user/user','wxapp/user/getUser');

Route::get('api/album/user','wxapp/album/listUserAlbum');
Route::get('api/album/like','wxapp/album/listLikeAlbum');
Route::get('api/album/pay','wxapp/album/listPayAlbum');

Route::get('api/wxpay/pay','wxapp/wxpay/getAdvancePayment');
Route::post('api/album/pay','wxapp/wxpay/savePayInfo');

Route::get('api/album/subject','wxapp/album/listRelaSubjectAlbum');

Route::post('api/album/album','wxapp/album/saveAlbum');
Route::post('api/image/image','wxapp/image/uploadImage');

return [

];
