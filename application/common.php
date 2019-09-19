<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 发送GET请求
 * @param $url
 * @return mixed
 */
function curlGet($url){
    //初始化
    $ch = curl_init();
    //设置选项，包括URL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    //打印获得的数据
    return $output;
}

///**
// * 发送http请求
// * @param $url
// * @param $params
// * @param string $method
// * @param array $header
// * @param bool $multi
// * @return mixed
// * @throws Exception
// */
//function http($url, $params, $method = 'GET', $header = array(), $multi = false){
//    $opts = array(
//        CURLOPT_TIMEOUT        => 30,
//        CURLOPT_RETURNTRANSFER => 1,
//        CURLOPT_SSL_VERIFYPEER => false,
//        CURLOPT_SSL_VERIFYHOST => false,
//        CURLOPT_HTTPHEADER     => $header
//    );
//    /* 根据请求类型设置特定参数 */
//    switch(strtoupper($method)){
//        case 'GET':
//            //$opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
//            $opts[CURLOPT_URL] = $url;
//            break;
//        case 'POST':
//            //判断是否传输文件
//            $params = $multi ? $params : http_build_query($params);
//            $opts[CURLOPT_URL] = $url;
//            $opts[CURLOPT_POST] = 1;
//            $opts[CURLOPT_POSTFIELDS] = $params;
//            break;
//        default:
//            throw new Exception('不支持的请求方式！');
//    }
//    /* 初始化并执行curl请求 */
//    $ch = curl_init();
//    curl_setopt_array($ch, $opts);
//    $data  = curl_exec($ch);
//    $error = curl_error($ch);
//    curl_close($ch);
//    if($error) throw new Exception('请求发生错误：' . $error);
//    return  $data;
//}
//
///**
// * 获取签名算法
// */
//function signature($arr = array()){
//    //  将集合M内非空参数值的参数按照参数名ASCII码从小到大排序（字典序）
//    ksort($arr);
//    //  使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串stringA
//    $stringA = '';
//    foreach ($arr as $key => $vaule) {
//        if($vaule != '' && !is_null($vaule))
//            $stringA = $stringA . $key . '=' . $vaule .'&';
//    }
//    //  在stringA最后拼接上key得到stringSignTemp字符串
//    $stringSignTemp = $stringA . 'key=' . config('wechat_pay')['secret'];
//    //  对stringSignTemp进行MD5运算
//    $sign = md5($stringSignTemp);
//    //  将得到的字符串所有字符转换为大写，得到sign值signValue
//    $signValue = strtoupper($sign);
//    return $signValue;
//}
//
///**
// * 将数组转为XML
// * @param $arr
// * @return string
// */
//function arrayToXML($arr){
//    $xml = "<xml>";
//    foreach ($arr as $key=>$val){
//        if(is_array($val)){
//            $xml.="<".$key.">".arrayToXml($val)."</".$key.">";
//        }else{
//            $xml.="<".$key.">".$val."</".$key.">";
//        }
//    }
//    $xml.="</xml>";
//    return $xml;
//}
//
///**
// * 生成16位随机字符串
// * @param int $length
// * @return string
// */
//function createNonceStr($length = 16) {
//    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
//    $str = "";
//    for ($i = 0; $i < $length; $i++) {
//        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
//    }
//    return $str;
//}
//
///**
// * xml转为数组
// * @param string $xml
// * @return mixed
// */
//function xmlToArray($xml = '') {
//    $arr = json_decode( json_encode( simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
//    return $arr;
//}