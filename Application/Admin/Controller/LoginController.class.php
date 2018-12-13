<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:24
 */

namespace Admin\Controller;


use Common\Controller\CommonBaseController;
use Think\Verify;

/**
 * 用户登录
 * Class LoginController
 *
 * @package Admin\Controller
 */
class LoginController extends CommonBaseController {

    /**
     * @var bool
     */
    protected $checkUser = false;

    /**
     * 用户登录
     */
    public function index() {
        if (session('admin_user_info')) {
            $this->redirect('Auth/index');
        }
        $this->display();
    }

    /**
     * 登录请求
     */
    public function doLogin() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $username = I('post.username', '', 'trim');
        $password = I('post.password', '', 'trim');
        $code     = I('post.code', '', 'trim');
        if (empty($username)) {
            $this->error('账号不能为空！');
        }
        if (empty($password)) {
            $this->error('密码不能为空！');
        }
        $verify = new Verify();
        if (!$verify->check($code)) {
            $this->error('验证码错误');
        }
        $info = M('admin')->where(array('username' => $username, 'password' => encrypt_pwd($password)))->find();
        if (empty($info)) {
            $this->error('账号或密码错误！');
        }
        M('admin')->where(array('id' => $info['id']))->save(array('last_time' => time(), 'last_ip' => get_client_ip()));
        session('admin_user_id', $info['id']);
        session('admin_user_info', $info);
        $this->success('登录成功');
    }

    /**
     * 用户退出
     */
    public function logout() {
        S('admin_rule_id_' . session('admin_user_id'), null);
        S('admin_auth_data_' . session('admin_user_id'), null);
        S('admin_menu_' . session('admin_user_id'), null);
        S('admin_auth_' . session('admin_user_id'), null);
        session_destroy();
        $this->redirect('index');
    }

    /**
     * 验证码
     */
    public function verify() {
        $config = array(
            'fontSize' => 18,    // 验证码字体大小
            'length'   => 4,     // 验证码位数
            'useNoise' => false, // 关闭验证码杂点
            'imageW'   => 140,
            'imageH'   => 35,
        );
        $Verify = new Verify($config);
        $Verify->entry();
    }

}