<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/9/1
 * Time: 12:30
 */

namespace app\wxapp\model;


use think\Model;
use think\model\concern\SoftDelete;

class Relasubject extends Model
{
    use SoftDelete;
    protected $pk = 'relasubject_id';
    protected $table = 'rela_subject';
    // 软删除字段
    protected $deleteTime = 'delete_time';

    /**
     * 获取专题关联图册列表
     * @param int $subject_id
     * @param int $page
     * @param int $pageSize
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function listRelaSubject($subject_id = 0, $page = 1, $pageSize = 20) {
        if(!$subject_id) {
            exception('Relapay Model listRelaSubject subjectID为空');
        }
        return Relasubject::where('subject_id',$subject_id)->page($page,$pageSize)->select();
    }
}