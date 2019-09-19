<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/26
 * Time: 14:49
 */

namespace app\wxapp\controller;

use think\Controller;
use think\Model;

class Subject extends Controller
{
    /**
     * 获取专题信息列表
     * @return mixed
     */
    public function listSubject() {
        return model('subject')->listSubject();
    }
}