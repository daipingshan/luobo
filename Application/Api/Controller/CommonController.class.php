<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2016/12/28
 * Time: 15:07
 */

namespace Api\Controller;

use Common\Controller\CommonBaseController;
use Common\Org\AliYunOss as OSS;

/**
 * Class CommonAction
 */
class CommonController extends CommonBaseController {

    /**
     * 检测用户是否需要登录
     *
     * @var bool
     */
    protected $checkUser = true;


    /**
     * @var int
     */
    protected $user_id = 0;

    /**
     * @var int
     */
    protected $mobile = 0;

    public function __construct() {
        parent::__construct();
        if ($this->checkUser) {
            $this->_checkUser();
        }
    }

    /**
     * 验证函数
     *
     * @access private
     */
    protected function _checkUser() {
        $token = I('request.token', '', 'trim');
        if (empty($token)) {
            $this->output('请登录', 'not_login');
        }
        $user_data = S($token);
        if (!$user_data) {
            $user_data = M('trainer')->where(array('token' => $token))->find();
            if (!empty($user_data)) {
                S($user_data['token'], $user_data);
            }
        }
        if (!$user_data) {
            $this->output('账号已被管理员禁用', 'not_login');
        }
        $this->user_id = $user_data['id'];
    }

    /**
     * @param string $msg
     * @param string $code
     * @param array $data
     */
    protected function output($msg = '请求错误', $code = 'fail', $data = array()) {
        $json_data = array('code' => $code, 'msg' => $msg, 'data' => $data);
        ob_clean();
        die(json_encode($json_data));
    }


    /**
     * 上传项目图片
     */
    public function uploadImg() {
        $Object = new OSS();
        $data   = $Object->uploadObject();
        if ($data['code'] == 1) {
            $res = array("img_url" => get_img_url($data['url']));
            $this->output('上传成功', 'success', $res);
        } else {
            $this->output('上传失败，' . $data['info']);
        }
    }

}