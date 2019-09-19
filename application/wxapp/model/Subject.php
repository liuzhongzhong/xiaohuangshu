<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/26
 * Time: 22:10
 */

namespace app\wxapp\model;

use think\Model;
use think\model\concern\SoftDelete;

class Subject extends Model
{
    // 主键id名
    use SoftDelete;
    protected $pk = 'subject_id';
    // 软删除字段
    protected $deleteTime = 'delete_time';

    /**
     * 获取所有专题列表
     * @return mixed
     */
    public function listSubject() {
        return Subject::all();
    }
}