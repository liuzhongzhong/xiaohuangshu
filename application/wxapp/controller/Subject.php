<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/26
 * Time: 14:49
 */

namespace app\wxapp\controller;

use think\controller;
use think\Model;

class Subject extends Controller
{
    public function listSubject() {
        return model('subject')->listSubject();
    }
}