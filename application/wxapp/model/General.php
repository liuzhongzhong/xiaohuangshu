<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2020/4/2
 * Time: 21:59
 */

namespace app\wxapp\model;


use think\Model;


class General extends Model
{
    // 主键id名

    /**
     * 获取所有专题列表
     * @return mixed
     */
    public function listGeneral() {
        return General::all();
    }
}