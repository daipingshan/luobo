<?php
/**
 * Created by PhpStorm.
 * User: mapanpan
 * Date: 2018/5/2
 * Time: 14:37
 */

return array(
    'LIMIT'             => 20,
    'REFRESH'           => 0.5,
    'DO_TOP'            => 30,
    'COMPANY_LABEL'     => 6,
    'RECRU_LABEL'       => 3,
    'DO_TOP_TIME'       => 60 * 60 * 24,
    'TOINDEX' => array(
        '1' => '1',
        '2' => '3',
        '3' => '7',
        '4' => '30',
    ),

    // 会员免费发布岗位的数量
    'LIMIT_ZERO_COUNT' => 5,
    'LIMIT_ONE_COUNT' => 20,
    'LIMIT_TWO_COUNT' => 30,
    'LIMIT_THREE_COUNT' => 50,


    'TMPL_PARSE_STRING' => array(
        '__JS_PATH__'    => '/Public/Home/js',
        '__CSS_PATH__'   => '/Public/Home/css',
        '__IMAGE_PATH__' => '/Public/Home/images',
        '__FONT_PATH__'  => '/Public/Home/fonts',
        '__LAYUI_PATH__' => '/Public/Admin/lib/layui',
        '__UE_EDITOR_PATH__' => '/Public/Home/lib/',
    ),

    'PIC_URL' => 'http://' . $_SERVER['SERVER_NAME'],

    'GET_ACCESS_TOKEN'  => 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=AAAAAA&secret=SSSSSS',
    'LOGIN_URL'         => 'https://www.luobowang.cn/Auth/wechatLoginBack',
    'GET_CODE'          => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=AAAAAA&redirect_uri=UUUUUU&response_type=code&scope=snsapi_userinfo&state=MMMMMM#wechat_redirect",
    'GET_CODE_SCAN'     => "https://open.weixin.qq.com/connect/qrconnect?appid=AAAAAA&redirect_uri=UUUUUU&response_type=code&scope=snsapi_login&state=MMMMMM#wechat_redirect",
    'GET_OPENID'        => 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=AAAAAA&secret=SSSSSS&code=CCCCCC&grant_type=authorization_code',
    'GET_USER_INFO'     => 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=TTTTTT&openid=OOOOOO&lang=zh_CN',
    'LOGIN_SUCCESS_URL' => 'https://www.luobowang.cn/User/index',
    'REGISTER_URL'      => 'https://www.luobowang.cn/Auth/register/id/IIIIII',
);