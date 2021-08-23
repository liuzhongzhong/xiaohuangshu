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

// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用名称
    'app_name'               => '',
    // 应用地址
    'app_host'               => '',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'Asia/Shanghai',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'strip_tags',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空模块名
    'empty_module'           => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法前缀
    'use_action_prefix'      => false,
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // HTTPS代理标识
    'https_agent_name'       => '',
    // IP代理获取标识
    'http_agent_ip'          => 'X-REAL-IP',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由延迟解析
    'url_lazy_route'         => false,
    // 是否强制使用路由
    'url_route_must'         => false,
    // 合并路由规则
    'route_rule_merge'       => false,
    // 路由是否完全匹配
    'route_complete_match'   => false,
    // 使用注解路由
    'route_annotation'       => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],
    // 是否开启路由缓存
    'route_check_cache'      => false,
    // 路由缓存的Key自定义设置（闭包），默认为当前URL和请求类型的md5
    'route_check_cache_key'  => '',
    // 路由缓存类型及参数
    'route_cache_option'     => [],

    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => Env::get('think_path') . 'tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => Env::get('think_path') . 'tpl/dispatch_jump.tpl',

    // 异常页面的模板文件
    'exception_tmpl'         => Env::get('think_path') . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // 图片处理接口参数
    'custom_list' => 'imageMogr/v2/auto-orient/thumbnail/!80p/quality/60/interlace/1|imageslim',
    'custom_image' => 'imageMogr/v2/auto-orient/quality/100/interlace/1|imageslim',
    'blur_image' => 'imageMogr/v2/auto-orient/blur/30x200/quality/80/interlace/1|imageslim',
    'blur_list' => 'imageMogr/v2/auto-orient/thumbnail/!80p/blur/20x200/quality/50/interlace/1|imageslim',
    'watermark' => 'imageView2/0/q/75|watermark/2/text/5omT6LWP5p-l55yL6auY5riF5Zu-5YaM/font/5b6u6L2v6ZuF6buR/fontsize/800/fill/I0ZGRkZGRg==/dissolve/100/gravity/Center/dx/10/dy/10|imageslim',
    'slim_image' => '|imageslim',

    'custom_list_alioss' => 'x-oss-process=style/general_list',
    'custom_image_alioss' => 'x-oss-process=style/original_graph',
    'blur_image_alioss' => 'x-oss-process=style/fuzzy_graph',
    'blur_list_alioss' => 'x-oss-process=style/fuzzy_list',

    'blur_image_txcos' => 'imageMogr2/blur/30x5',


    // 微信小程序信息
    'wechat_small_application' => array(
        'appid' => 'wxe830310b5e068d6e',
        'appsecret' => '4ba63832260e963cc61069da4191de32',
    ),

    /*// 微信商户号
    'wechat_pay' => array(
        'account' => '1544669891',  // 微信商户号
        'secret' => 'WxczTmhXrAmqUBBrrggmZc285sXa99vN',  //微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置
    ),*/

    // 微信商户号
    'wechat_pay' => array(
        'account' => '1611631681',  // 微信商户号
        'secret' => 'WxczTmhXrAmqUBBrrggmZc285sXa99vN',  //微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置
    ),

    // 微信支付API地址
    'wxpay_api' => array(
        'unifiedorder' => 'https://api.mch.weixin.qq.com/pay/unifiedorder',    // 统一下单
        'notify_url' => 'http://www.weixin.qq.com/wxpay/pay.php',   // 回调地址
    ),

    /*//七牛云秘钥
    'qiniu' => array(
        'access_key' => 'VrKJPcQnEVCE2uav-EGcIOkrCitcFlvsT2Gb94Ka',
        'secret_key' => 'AnZn9ccvgViG88qjuTmkz14O9Ql5xO-0dKUBTHGw',
        'bucket_name' => 'xiaohuangshu',
        'prefix_name' => 'album',
        'prefix_url' => 'http://image.xiaohuangshu.xianshikeji.com/',
    ),*/

    // 七牛云秘钥
    'qiniu' => array(
        'access_key' => 'MgkXrkN4SbMHwRIhveKZVC524NMNJ1MHnIs7--UA',
        'secret_key' => 'GDBut87QJRCkia88VTZphZ4LnTgIbCImLd_-WqI5',
        'bucket_name' => 'xiaohuangshu2',
        'prefix_name' => 'album',
        'prefix_url' => 'http://image.lzz.l0v3.cn/',
    ),

    //阿里云OSS配置
    'alioss'        =>[
        'KeyId'      => 'LTAI4FpK7mKriRP9rLnhtPFM',  //AccessKey ID
        'KeySecret'  => 'ZaTaEsC4kisPM3wVX5HE6n1YMDJ2bf ',  //Access Key Secret
        'EndPoint'   => 'oss-cn-beijing.aliyuncs.com',  //外网访问节点
        'Bucket'     => 'sams-member',  //Bucket名称
        'url'        => 'http://sams-member.oss-cn-beijing.aliyuncs.com/',
    ],

    //腾讯云OSS配置
    'txcos'        =>[
        'secretId'      => 'AKIDJOAb3DvX1D8lGmAyfOq2wfV4LMOBNtiW',  //云API密钥 SecretId
        'secretKey'  => 'g5KcdNprrHS0ZwlT2LPDk7GAxub16yZt',  //云API密钥 SecretKey
        'region'   => 'ap-shanghai',  //存储桶地域
        'bucket'     => 'sexbook-1258218746',  //Bucket名称
        'url'        => 'http://sexbook-1258218746.cos.ap-shanghai.myqcloud.com/',
    ],

];


