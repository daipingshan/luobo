<?php
/**
 * Created by PhpStorm.
 * User: mapanpan
 * Date: 2018/8/1
 * Time: 16:30
 */

namespace Home\Controller;

use Think\Verify;

/**
 * Class IndexController
 *
 * @package Company\Controller
 */
class AuthController extends CommonController
{

    protected $checkUser = false;

    //切换身份
    public function switch_identity()
    {

        if (session('identity') == 1) {
            //切换到企业
            session('identity', 2);
            $this->redirect('Company/manage', null, 0);
        } else {
            //切换到个人
            session('identity', 1);
            $this->redirect('User/index', null, 0);
        }

    }

    /**
     * 验证码
     */
    public function verify()
    {
        $config = [
            'fontSize' => 19, // 验证码字体大小
            'length' => 4, // 验证码位数
            'imageH' => 35,
            'imageW' => 140
        ];
        $Verify = new Verify($config);
        $Verify->codeSet = '0123456789';
        $Verify->entry();
    }

    /**
     * 验证码校验
     */
    public function check_verify($code)
    {
        $verify = new Verify();
        $res = $verify->check($code);
        return $res;
    }

    /**
     * 邮件发送
     */
    public function sendEmail()
    {
        $to_mail = I('post.mail', '', 'trim');
        $title = I('post.title', '', 'trim');
        $content = I('post.content', '', 'trim');
        $send_res = send_mail($to_mail, $title, $content);
        if ($send_res) {
            $this->success('发送成功！');
        } else {
            $this->error('发送失败');
        }
    }

    /**
     * 发送短信
     */
    public function sendCode()
    {
        $mobile = I('post.mobile', '', 'trim');
        $code = rand(100000, 999999);

        $send_res = send_sms($mobile, $code);
//        $send_res = $code;
        if ($send_res) {
            session("mobilecode", $code);//session存储手机号+验证码
            $this->success('发送成功！' );
        } else {
            $this->error('发送失败');
        }
    }

    /**
     * 登录
     */
    public function login()
    {
        if (IS_AJAX) {
            $account = I('post.account', '', 'trim');
            $password = I('post.password', '', '');

            $user_id = '';

            if (is_mobile($account)) {
                $where = array(
                    'mobile' => $account,
                    'password' => encrypt_pwd($password)
                );
                $user_id = M('user')->where($where)->getField('id');
            } else if (is_email($account)) {
                $user_id = M('user')->where(array('email' => $account, 'password' => encrypt_pwd($password)))->getField('id');
            } else  {
                $this->ajaxReturn(array('code' => -1, 'msg' => '您输入的账号有误,请重新输入', 'data' => ''));
            }

            if (empty($user_id)) {
                $this->ajaxReturn(array('code' => -2, 'msg' => '账户信息有误,请重新输入。', 'data' => ''));
            }
            $user = M('user_info')->where(array('user_id' => $user_id))->getField('id');
            $company = M('company_info')->where(array('user_id' => $user_id))->getField('id');
            $user_data = M('user')->where(array('id' => $user_id))->field('id,mobile,email,amount')->find();
            //基本系信息
            session('user', $user_data);
            //个人资料id
            session('user_id', $user_id);
            //个人资料id
            session('user_info_id', $user);
            //公司资料id
            session('company_id', $company);


            //如果是商铺登录就跳转
            if( session('identity') == 3 ){
                $this->ajaxReturn(array('code' => 1, 'msg' => 'success', 'data' => U('Shop/index')));
            }
            //身份
            if($user){
                session('identity', 1);
            }else{
                session('identity', 2);
            }


            if (empty($user) && empty($company)) {
                $this->ajaxReturn(array('code' => 1, 'msg' => 'success', 'data' => U('Auth/choose', array('user_id' => $user_id))));
            } else {
                $this->ajaxReturn(array('code' => 0, 'msg' => 'success', 'data' => U('Index/index')));
            }

        } else {

            $is_browser = 0;
            if (isMobile()) {
                $is_browser = 1;
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
                    $is_browser = 2;
                }
            }

            $this->assign('is_browser', $is_browser);
            $this->assign('title', '用户登录');
            $this->display();

        }
    }

    /**
     * 忘记密码
     */
    public function forgetPwd()
    {
        if (IS_AJAX) {
            $mobile = I('post.mobile', '', 'trim');
            $sms_code = I('post.sms_code', '', 'trim');
            $new_pwd = I('post.new_pwd', '', '');
            $confirm_pwd = I('post.confirm_pwd', '', 'trim');

            $id = M('user')->where(array('mobile' => $mobile))->getField('id');


            if (empty($id)) {
                $res = array('code' => -1, 'msg' => '该用户不存在，请重新检查', 'url' => U('Auth/forgetPwd'));
                $this->ajaxReturn($res);
            }

            if ($new_pwd != $confirm_pwd) {
                $res = array('code' => -2, 'msg' => '新密码与确认密码不一致，请重新输入', 'url' => U('Auth/forgetPwd'));
                $this->ajaxReturn($res);
            }

            $mobile_code = session('mobilecode');

            if ($mobile_code != $sms_code) {
                $res = array('code' => -3, 'msg' => '验证码不正确，请确认后重新输入', 'url' => U('Auth/forgetPwd'));
                $this->ajaxReturn($res);
            }

            $data = array(
                'password' => encrypt_pwd($new_pwd),
            );
            $user_res = M('user')->where(array('mobile' => $mobile))->save($data);
            if ($user_res === false) {
                $res = array('code' => -4, 'msg' => '系统内部出现问题，请稍后重试', 'url' => U('Auth/forgetPwd'));
                $this->ajaxReturn($res);
            } else {
                $res = array('code' => 0, 'msg' => '密码修改成功', 'url' => U('Auth/login'));
                $this->ajaxReturn($res);
            }
        } else {
            $this->assign('title', '忘记密码');
            $this->display();
        }
    }

    /**
     * 注册
     */
    public function register()
    {
        if (IS_AJAX) {
            // $id  说明是微信注册未绑定手机号的用户， $uid  是邀请注册的用户
            $id = I('post.id', 0, 'int');
            $uid = I('post.uid', 0, 'int');
            $mobile = I('post.mobile', '', 'trim');
            $verify_code = I('post.verify_code', '', '');
            $sms_code = I('post.sms_code', '', 'trim');

            $user = M('user')->where(array('mobile' => $mobile))->find();

            if ($user) {
                $res = array('code' => -4, 'msg' => '该手机号已经注册，请输入新的手机号!', 'data' => U('Auth/register'));
                $this->ajaxReturn($res);
            }

            $mobile_code = session('mobilecode');

            if (!is_mobile($mobile)) {

                $res = array('code' => -1, 'msg' => '您的手机号格式不正确，请确认后重新输入!', 'data' => '');
                $this->ajaxReturn($res);

            } else if ($mobile_code != $sms_code) {

                $res = array('code' => -3, 'msg' => '您的短信验证码有误，请重新输入!', 'data' => '');
                $this->ajaxReturn($res);

            }

            if (!$this->check_verify($verify_code)) {

                $res = array('code' => -2, 'msg' => '您的图形验证码有误，请重新输入!', 'data' => '');
                $this->ajaxReturn($res);

            }

            //  普通用户注册
            if (empty($id) && empty($uid)) {

                $data = array('mobile' => $mobile, 'create_time' => time());
                $new_user = M('user')->add($data);

            } else if ($id && empty($uid)) {

                // 微信用户注册
                M('user')->where(array('id' => $id))->save(array('mobile' => $mobile));

            } else if (empty($id) && $uid) {



                $_data = array(
                    'mobile' => $mobile,
                    'invite_uid' => $uid,
                    'create_time' => time(),
                );

                /*
                 *
                 * 邀请者账号被封禁后被邀请着修改为空。
                 *
                 * */
                $status = M('user')->where(array('id' => $uid))->getField('status');
                if ($status == 3) {
                    $_data['invite_uid'] = 0;
                }


                $new_user = M('user')->add($_data);

            }

            session('mobilecode', '');
            session('mobile',$mobile);
            $res = array(
                'code' => 0,
                'msg' => '恭喜您，注册成功，请设置您的登录密码！',
                'data' => U('Auth/setPassword')
            );

            $this->ajaxReturn($res);
        } else {

            $id = I('get.id', 0, 'int');
            $is_browser = 0;
            if (isMobile()) {
                $is_browser = 1;
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
                    $is_browser = 2;
                }
            }

            $title = "用户注册";
            if ($id != 0) {
                $title = "绑定手机号";
            }

            $this->assign('title', $title);
            $this->assign('id', $id);
            $this->assign('is_browser', $is_browser);
            $this->assign('uid', I('get.uid', 0, 'int'));
            $this->display();
        }
    }

    /**
     * 微信用户绑定手机号
     */
    public function bindMobile()
    {
        $id = I('post.id', '', 'trim');
        $mobile = I('post.mobile', '', 'trim');
        $sms_code = I('post.sms_code', '', 'trim');

        $mobile_code = session('mobilecode');

        if (!is_mobile($mobile)) {
            $res = array('code' => -1, 'msg' => '您的手机号格式不正确，请确认后重新输入!', 'data' => '');
            $this->ajaxReturn($res);
        } else if ($mobile_code != $sms_code) {
            $res = array('code' => -2, 'msg' => '您的短信验证码有误，请重新输入!', 'data' => '');
            $this->ajaxReturn($res);
        }

        $res = array('code' => -1, 'msg' => '手机号已绑定', 'data' => '');
        $user = M('user')->where(array('mobile' => $mobile))->find();
        $wechat_user = M('user')->where(array('id' => $id))->find();
        if ($user) {
            if ($user['unionid'] == '') {
                $data = array('unionid' => $wechat_user['unionid'], 'third_type' => 'wechat');
                $update_user = M('user')->where(array('mobile' => $mobile))->save($data);
                $res = array('code' => 0, 'msg' => '恭喜您，绑定成功，请设置您的登录密码！', 'data' => U('Auth/setPassword'));
            } else {
                $res = array('code' => -3, 'msg' => '该手机号已经绑定微信，请更换手机号!', 'data' => U('Auth/register'));
            }
            $user_id = $user['id'];
            M('user')->where(array('id' => $id))->delete();
        } else if (empty($user)) {
            $data = array('mobile' => $mobile);
            $update_user = M('user')->where(array('id' => $id))->save($data);
            $user_id = $id;
            $res = array('code' => 0, 'msg' => '恭喜您，绑定成功，请设置您的登录密码！', 'data' => U('Auth/setPassword'));
        }

        $user = M('user_info')->where(array('user_id' => $user_id))->getField('id');
        $company = M('company_info')->where(array('user_id' => $user_id))->getField('id');
        $user_data = M('user')->where(array('id' => $user_id))->field('id,mobile,email,amount')->find();
        //基本系信息
        session('user', $user_data);
        //个人资料id
        session('user_id', $user_id);
        //公司资料id
        session('company_id', $company);


        //身份
        if($user){
            session('identity', 1);
        }else{
            session('identity', 2);
        }

        session('mobilecode', '');
        session('mobile',$mobile);


        $this->ajaxReturn($res);
    }

    /**
     * 检测用户手机号是否注册
     * @DateTime 2018-10-25
     * @return   [type]     [description]
     */
    public function checkMobile()
    {
        $mobile = I('post.mobile', '', 'trim');

        $res = array('code' => 0, 'msg' => '该手机号未注册，可以正常注册');
        $count_uid = M('user')->where(array('mobile' => $mobile, 'unionid' => array('neq', '')))->count('id');
        if ($count_uid > 0) {
            $res = array('code' => -1, 'msg' => '该手机号已经注册，请您更换手机号？');
        }
        $this->ajaxReturn($res);
    }

    /**
     * 重置密码
     */
    public function ResetPassword()
    {
        if (IS_AJAX) {
            $id = session('user_id');
            $pwd = I('post.pwd', '', 'trim');
            $pwd2 = I('post.pwd2', '', 'trim');
            $old_pwd = I('post.old_pwd', '', 'trim');
            $match = '/^([a-z]+(?=[0-9])|[0-9]+(?=[a-z]))[a-z0-9]+$/i';

            $map['id'] = $id;
            $map['password'] = encrypt_pwd($old_pwd);
            $result = M('user')->where($map)->find();
            if(!$result){
                $res = array('status' => 2, 'msg' => '旧密码错误', 'data' => U('User/password'));
                $this->ajaxReturn($res);
            }
            if($pwd2 == $old_pwd){
                $res = array('status' => 1, 'msg' => '新旧密码不能相同。', 'data' => U('User/password'));
                $this->ajaxReturn($res);
            }
            if(mb_strlen($pwd) <= 6){
                $res = array('status' => 1, 'msg' => '密码过于简单，不能少于6位。切需要带有英文字母', 'data' => U('User/password'));
                $this->ajaxReturn($res);
            }
            if(!preg_match($match,$pwd)){
                $res = array('status' => 1, 'msg' => '密码过于简单，不能少于6位。切需要带有英文字母', 'data' => U('User/password'));
                $this->ajaxReturn($res);
            }
            if($pwd !== $pwd2){
                $res = array('status' => 2, 'msg' => '两次密码不一致', 'data' => U('User/password'));
                $this->ajaxReturn($res);
            }

            $data = array(
                'id' => $id,
                'password' => encrypt_pwd($pwd),
                'create_time' => time()
            );
            $user_id = M('user')->save($data);

            if (empty($user_id)) {
                $res = array('status' => 3, 'msg' => 'error：未获取到正常反馈结果!', 'data' => U('User/password'));
                $this->ajaxReturn($res);
            } else {
                $user = M('user_info')->where(array('user_id' => $user_id))->getField('id');
                $company = M('company_info')->where(array('user_id' => $user_id))->getField('id');
                $user_data = M('user')->where(array('id' => $user_id))->field('id,mobile,email,amount')->find();
                //基本系信息
                session('user', $user_data);
                //个人资料id
                session('user_id', $user_id);
                //公司资料id
                session('company_id', $company);


                //如果是商铺登录就跳转
                if( session('identity') == 3 ){
                    $this->ajaxReturn(array('code' => 1, 'msg' => 'success', 'data' => U('Shop/index')));
                }
                //身份
                if($user){
                    session('identity', 1);
                }else{
                    session('identity', 2);
                }

                if (empty($user) && empty($company)) {
                    $this->ajaxReturn(array('code' => 1, 'msg' => 'success', 'data' => U('Auth/choose')));
                } else {
                    $this->ajaxReturn(array('code' => 0, 'msg' => 'success', 'data' => U('Index/index')));
                }
            }
        } else {
            $this->redirect(U('Auth/login'), null, 0);
        }
    }

    /**
     * 设定密码
     */
    public function setPassword()
    {
        $mobile = session('mobile');
        $match = '/^([a-z]+(?=[0-9])|[0-9]+(?=[a-z]))[a-z0-9]+$/i';
        //检查手机号码
        if($mobile == '' || empty($mobile)){
            $this->display('Auth/login');
        }

        if (IS_AJAX) {
            $pwd = I('post.pwd', '', 'trim');
            $pwd2 = I('post.pwd2', '', 'trim');
            if(mb_strlen($pwd) <= 6 || !preg_match($match,$pwd)){
                $res = array('code' => -1, 'msg' => '密码过于简单，不能少于6位,且需要带有英文字母', 'data' => U('User/password'));
                $this->ajaxReturn($res);
            }

            if($pwd !== $pwd2){
                $res = array('code' => -2, 'msg' => '两次密码不一致,请确认后重试！', 'data' => U('User/password'));
                $this->ajaxReturn($res);
            }

            $user_id = M('user')->where(array('mobile' => $mobile))->getField('id');
            if (empty($user_id)) {
                $data = array(
                    'mobile' => $mobile,
                    'password' => encrypt_pwd($pwd),
                    'create_time' => time()
                );
                $user_id = M('user')->add($data);
            } else {
                $data = array(
                    'password' => encrypt_pwd($pwd),
                    'create_time' => time()
                );
                $res = M('user')->where(array('mobile' => $mobile))->save($data);
            }
            if (empty($user_id)) {
                $res = array('code' => -3, 'msg' => '设定密码时发生了错误!请稍后重试', 'data' => U('Auth/register'));
                $this->ajaxReturn($res);
            } else {
                $user = M('user_info')->where(array('user_id' => $user_id))->getField('id');
                $company = M('company_info')->where(array('user_id' => $user_id))->getField('id');
                $user_data = M('user')->where(array('id' => $user_id))->field('id,mobile,email,amount')->find();
                //基本系信息
                session('user', $user_data);
                //个人资料id
                session('user_id', $user_id);
                //公司资料id
                session('company_id', $company);


                //如果是商铺登录就跳转
                if( session('identity') == 3 ){
                    $this->ajaxReturn(array('code' => 0, 'msg' => '恭喜您，注册成功!跳转商铺中...', 'data' => U('Shop/index')));
                }
                //身份
                if($user){
                    session('identity', 1);
                }else{
                    session('identity', 2);
                }

                if (empty($user) && empty($company)) {
                    $this->ajaxReturn(array('code' => 0, 'msg' => '恭喜您，注册成功！请您选择用户类型', 'data' => U('Auth/choose')));
                } else {
                    $this->ajaxReturn(array('code' => 0, 'msg' => '恭喜您，注册成功！', 'data' => U('Index/index')));
                }
            }
        } else {
            $this->assign('title', '设定密码');
            $this->display();

        }
    }

    /**
     * 修改密码
     */
    public function password()
    {
        parent::_checkUser();
        $this->assign('title', '修改密码');
        $this->display();
    }
    /**
     * 账户充值
     */
    public function account()
    {
        //$this->redirect('Index/404',array('errors'=>"功能整改中"), 0);
        parent::_checkUser();
        $amount = M('user')->where('id='.$this->user_id)->getField('amount');
        $this->assign('amount', $amount);
        $this->display();
    }
    /**
     * 退出登录
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('Auth/login');
    }


    public function wexinLogin()
    {
        $uid = I('get.uid', '', 'trim');
        $this->_authorize($uid);
    }

    /**
     * 微信登录
     */
    public function wechatLoginBack(){
        $msg = '授权失败！';
        if (isset($_GET['code']) && trim($_GET['code'])) {
            $code = trim($_GET['code']);
            $invite_uid = trim($_GET['state']);
            $appid = C('WECHAT_APPID');
            $secret = C('WECHAT_SECRET');
            if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false){
                $appid = C('WECHAT_APPID_OPEN');
                $secret = C('WECHAT_SECRET_OPEN');
            }
            $url = str_replace('AAAAAA', $appid, C('GET_OPENID'));
            $url = str_replace('SSSSSS', $secret, $url);
            $token_url = str_replace('CCCCCC', $code, $url);
            $http = new \Common\Org\Http();
            $res = json_decode($http->post($token_url));
            if (isset($res->access_token) && $res->access_token) {
                $unionid = $res->unionid;
                $openid = $res->openid;
                $info = M('user')->where(array('unionid'=>$unionid))->find();
                if(empty($info)){
                    $access_token = $this->_getAccessToken();
                    file_put_contents('/tmp/token.log', '----' . var_export($access_token, true) . '----', FILE_APPEND);
                    $url = str_replace('TTTTTT', $access_token, C('GET_USER_INFO'));
                    $user_info_url = str_replace('OOOOOO', $openid, $url);
                    $http = new \Common\Org\Http();
                    $res = json_decode($http->post($user_info_url));
                    if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false){
                        $openid = '';
                    }
                    $data = array(
                        'unionid'     => $unionid,
                        'openid'      => $openid,
                        'third_type'  => 'wechat',
                        'invite_uid'  => $invite_uid,
                        'create_time' => time(),
                        'headimgurl'  => $res->headimgurl,
                        'nickname'    => $res->nickname,
                    );
                    $user_res = M('user')->add($data);
                    $msg='授权成功！';
                    $url = str_replace('IIIIII', $user_res, C('REGISTER_URL'));
                    redirect($url);
                } else {
                    if ($info['mobile'] == '') {
                        $url = str_replace('IIIIII', $info['id'], C('REGISTER_URL'));
                        redirect($url);
                    }
                    M('user')->where(array('unionid' => $unionid))->save(array('openid' => $openid,));
                    $user = M('user_info')->where(array('user_id' => $info['id']))->getField('id');
                    $company = M('company_info')->where(array('user_id' => $info['id']))->getField('id');
                    $user_data = M('user')->where(array('id' => $info['id']))->field('id,mobile,email,amount')->find();
                    //基本系信息
                    session('user', $user_data);
                    //个人资料id
                    session('user_id', $info['id']);
                    //公司资料id
                    session('company_id', $company);
                    //身份
                    session('identity', 1);
                    $msg='授权成功！';
                    if (empty($user) && empty($company)) {
                        redirect(U('Auth/choose', array('user_id' => $info['id'])));
                    } else {
                        redirect(U('User/index'));
                    }
                }
            }
        }
    }

}