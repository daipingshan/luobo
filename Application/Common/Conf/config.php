<?php
$config = array(
    'ENCODE'          => 'Bluo',
    'PWD_ENCRYPT_STR' => 'luo_bo',
    'CSS_VER'         => 1,
    'JS_VER'          => 1,
    'WIDTH'           => 90,
    'HEIGHT'          => 90,
    //'SHOW_PAGE_TRACE' => true,              // 显示页面Trace信息
    'SHOW_ERROR_MSG'  => true,
    'COOKIE_PREFIX'   => 'care_',
    'COOKIE_EXPIRE'   => 86400 * 7,
    'COOKIE_PATH'     => '/',
    /* URL设置 */
    'URL_MODEL'       => 2,                  //URL模式

    'MODULE_ALLOW_LIST'     => array('Home', 'Admin', 'Api'),
    'DEFAULT_MODULE'        => 'Home',

    /*自定义配置*/
    'LOAD_EXT_CONFIG'       => 'db,cache',

    //url 区分大小写
    'URL_CASE_INSENSITIVE'  => false,
    /* 子域名配置 */
    'APP_SUB_DOMAIN_DEPLOY' => 1,             // 开启子域名配置
    'APP_SUB_DOMAIN_RULES'  => array(
        'admin'      => 'Admin',
        'admin29xk'  => 'Admin',
        'luoboadmin' => 'Admin',
        'www' => 'Home',
        'tp' => 'Home'
    ),

    // 配置邮件发送服务器
    'MAIL_HOST'             => 'smtp.yeah.net',//smtp服务器的名称
    'MAIL_SMTPAUTH'         => true, //启用smtp认证
    'MAIL_USERNAME'         => 'grannis@yeah.net',//你的邮箱名
    'MAIL_FROM'             => 'grannis@yeah.net',//发件人地址
    'MAIL_FROMNAME'         => '萝卜网',//发件人姓名
    'MAIL_PASSWORD'         => 'bmw66990',//邮箱密码
    'MAIL_CHARSET'          => 'utf-8',//设置邮件编码
    'MAIL_ISHTML'           => true, // 是否HTML格式邮件

    // 阿里云短信配置
    'ACCESS_KEY_ID'         => 'LTAIzOlAyZZInC4f',
    'ACCESS_KEY_SECRET'     => 'oqCl7fvq5NasryyaGAECjeu1IWySwR',
    'SIGN_NAME'             => '萝卜网',
    'FORGET_PASSWORD_CODE ' => 'SMS_140070160',
    'LOGIN_CODE'            => 'SMS_140065149',

    //  支付相关配置
    //  微信支付相关配置
    'WECHAT_APPID_OPEN'     => 'wx36b951b7c38d4f78',
    'WECHAT_SECRET_OPEN'    => 'c7d2cef75b0c78f52af5560ebeca6bfc',
    'WECHAT_APPID'          => 'wx2e2f2a3416df4e7a',
    'WECHAT_SECRET'         => '7c12948cd37f1adbe39601609910e36a',
    'WECHAT_MCHID'          => '1514239221',
    'WECHAT_KEY'            => 'skdl12009sucnjkn12n129xNNNjds9qi',
    //  支付宝相关配置
    'ALIPAY_APPID'          => '',
    'ALIPAY_PRIVATE_KEY'    => '',
    'ALIPAY_PUBILC_KEY'     => '',
    'ALIPAY_NOTIFY_URL'     => '',
    'ALIPAY_RETURN_URL'     => '',
    'ALIPAY_GATEWAY_URL'    => '',
);
return $config;

