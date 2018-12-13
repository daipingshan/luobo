<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/6/11
 * Time: 15:56
 */

namespace Admin\Controller;


class IndexController extends CommonController {

    /**
     * 后台首页
     */
    public function index() {
        $this->display();
    }

    /**
     * 保存密码
     */
    public function updatePass() {
        if (!IS_AJAX) {
            $this->error('非法请求');
        }
        $password = I('post.password', '', 'trim');
        if (empty($password)) {
            $this->error('密码不能为空！');
        }
        $res = M('admin')->where(array('id' => $this->user_id))->save(array('password' => encrypt_pwd($password)));
        if ($res !== false) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败！');
        }
    }

}