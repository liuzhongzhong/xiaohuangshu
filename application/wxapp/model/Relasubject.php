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
            exception('Relasubject Model listRelaSubject subjectID为空');
        }
        return Relasubject::where('subject_id',$subject_id)->order(array('create_time' => 'desc','album_id' => 'desc'))->page($page,$pageSize)->select();
    }

    /**
     * 创建专题信息
     * @param array $data
     * @return mixed
     */
    public function saveRelaSubject($data = array()) {
        if(!$data) {
            exception('Relasubject Model saveRelaSubject 数据为空');
        }
        $relaSubject = new Relasubject();
        $relaSubject->save($data);
        return $relaSubject;
    }

    /**
     * 获取单个专题记录信息
     * @param int $album_id
     * @return mixed
     */
    public function getRelaSubject($album_id = 0) {
        if(!$album_id) {
            exception('Relasubject Model getRelaSubject ID为空');
        }else{
            $value = Relasubject::where('album_id',$album_id)->find();
            return $value;
        }
    }

    /**
     * 更新专题记录信息
     * @param int $album_id
     * @param array $data
     * @return int|string
     */
    public function updateRelaSubject($relasubject_id = 0 ,$data = array()) {
        if(!$relasubject_id || !$data) {
            exception('Relasubject Model updateRelaSubject 数据为空');
        }
        $value = Relasubject::where('relasubject_id',$relasubject_id)->update($data);
        return $value;
    }

}