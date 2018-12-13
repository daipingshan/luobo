<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 14:37
 */

return array(
    'AUTH_ON'           => true,
    'AUTH_ID'           => array(),
    'AUTH_COMMON'       => array('Index/index', '/Common/uploadImg', '/Common/uploadEditImg', '/Auth/updatePassword'),
    'TMPL_PARSE_STRING' => array(
        '__JS_PATH__'        => '/Public/Admin/js',
        '__CSS_PATH__'       => '/Public/Admin/css',
        '__IMAGE_PATH__'     => '/Public/Admin/images',
        '__FONT_PATH__'      => '/Public/Admin/fonts',
        '__LAYUI_PATH__'     => '/Public/Admin/lib/layui',
        '__UE_EDITOR_PATH__' => '/Public/Admin/Editor/',
    ),
);