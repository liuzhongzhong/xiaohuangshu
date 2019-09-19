<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/25
 * Time: 19:54
 */

namespace app\wxapp\Controller;
use Essence\Essence;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
//        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V5.1<br/><span style="font-size:30px">19载初心不改（2006-2018） - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';

    }

    public function hello()
    {
        $essence = new Essence();
//        $meida = $essence->extract('https://twitter.com/i/status/1166215049087606785');
        $meida = $essence->extract('https://youtu.be/lbX_t9X9v9U');
        print_r($meida);
    }
}
