<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2019/8/30
 * Time: 16:23
 */
namespace app\common\model;

class Wxpay
{
    /**
     * 预支付,调取统一下单接口
     * @return \think\response\Json
     */
    public function getAdvancePayment($openid = '', $total_fee = 0, $body = '') {
        // 随机生成商户系统内部订单号
        $out_trade_no = $this->createNonceStr();
        // 拼装请求接口参数
        $advanceData = array(
            'appid' => config('wechat_small_application')['appid'],    // 小程序ID
            'mch_id' => config('wechat_pay')['account'],               // 微信支付分配的商户号
            'nonce_str' => $this->createNonceStr(),                          // 随机字符串，长度要求在32位以内
            'body' => $body,                                                 // 商品简单描述
            'out_trade_no' => $out_trade_no,                                 // 商户系统内部订单号，要求32个字符内，只能是数字、大小写字母_-|*且在同一个商户号下唯一
            'total_fee' => $total_fee,                                       // 订单总金额，单位为分
            'spbill_create_ip' => request()->ip(),                           // 调用微信支付API的机器IP
            'notify_url' => config('wxpay_api')['notify_url'],         // 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数
            'trade_type' => 'JSAPI',                                         // 小程序取值如下：JSAPI
            'openid' => $openid,                                             // 用户的openID，小程序必填
        );

        // 通过签名算法计算得出的签名值
        $advanceData['sign'] = $this->signature($advanceData);
        // 将数组转换XML格式
        $advanceData = $this->arrayToXML($advanceData);
        //  向微信服务器发起http请求
        $advanceResult = $this->http(config('wxpay_api')['unifiedorder'],$advanceData,'POST',array("Content-Type: text/html"),true);
        //  将XML格式数据解析为数组
        $advanceResult = $this->xmlToArray($advanceResult);
        // 请求结果判断
        if($advanceResult['return_code'] == 'FAIL') {
            // 统一下单接口调取失败
            return array(
                'code' => 0,
                'message' => $advanceResult['return_msg'],
            );
        }else if($advanceResult['return_code'] == 'SUCCESS') {
            // 统一下单接口调取成功
            // 拼装sign签名数组
            $paySignData = array(
                'appId' => config('wechat_small_application')['appid'],    // 小程序ID
                'timeStamp' => time(),                                           // 时间戳
                'nonceStr' => $advanceResult['nonce_str'],                       // 随机字符串
                'package' => 'prepay_id=' . $advanceResult['prepay_id'],         // 数据包
                'signType' => 'MD5',                                             // 签名算法，暂支持 MD5
            );
            // 换取加密后支付签名sign值
            $paySign = $this->signature($paySignData);
            // 预支付数据准备、整合至数组中
            $paySignResult = array(
                'paySign' => $paySign,                                       // 支付签名
                'packages' => 'prepay_id=' . $advanceResult['prepay_id'],    // 统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
                'nonceStr' => $advanceResult['nonce_str'],                   // 随机字符串，长度为32个字符以下
                'timeStamp' => strval(time()),                               // 时间戳从1970年1月1日00:00:00至今的秒数,即当前的时间
                'out_trade_no' => $out_trade_no,                             // 商户订单号
            );
            // 返回数据
            return array(
                'code' => 1,
                'message' => $advanceResult['return_msg'],
                'data' => $paySignResult,
            );
        }
    }


    /**
     * 发送http请求
     * @param $url
     * @param $params
     * @param string $method
     * @param array $header
     * @param bool $multi
     * @return mixed
     * @throws Exception
     */
    private function http($url, $params, $method = 'GET', $header = array(), $multi = false){
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                //$opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }

    /**
     * 获取签名算法
     */
    protected function signature($arr = array()){
        //  将集合M内非空参数值的参数按照参数名ASCII码从小到大排序（字典序）
        ksort($arr);
        //  使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串stringA
        $stringA = '';
        foreach ($arr as $key => $vaule) {
            if($vaule != '' && !is_null($vaule))
                $stringA = $stringA . $key . '=' . $vaule .'&';
        }
        //  在stringA最后拼接上key得到stringSignTemp字符串
        $stringSignTemp = $stringA . 'key=' . config('wechat_pay')['secret'];
        //  对stringSignTemp进行MD5运算
        $sign = md5($stringSignTemp);
        //  将得到的字符串所有字符转换为大写，得到sign值signValue
        $signValue = strtoupper($sign);
        return $signValue;
    }

    /**
     * 将数组转为XML
     * @param $arr
     * @return string
     */
    protected function arrayToXML($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val){
            if(is_array($val)){
                $xml.="<".$key.">".arrayToXml($val)."</".$key.">";
            }else{
                $xml.="<".$key.">".$val."</".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 生成16位随机字符串
     * @param int $length
     * @return string
     */
    protected function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * xml转为数组
     * @param string $xml
     * @return mixed
     */
    protected function xmlToArray($xml = '') {
        $arr = json_decode( json_encode( simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),true);
        return $arr;
    }
}