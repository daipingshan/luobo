<?php
/**
 * Created by PhpStorm.
 * User: Leborn
 * Date: 2018/8/22
 * Time: 14:24
 */

namespace Home\Controller;

use Common\Controller\CommonBaseController;
use Think\Page;
/**
 * 公共基础类库
 * Class CommonController
 *
 * @package Home\Controller
 */
class CommonController extends CommonBaseController
{

    /**
     * @var bool
     */
    protected $checkUser = true;

    /**
     * @var 基本账户ID
     */
    protected $user_id = 0;
    /**
     * @var 企业信息ID
     */
    protected $company_id = 0;

    protected $company = 0;
    /**
     * @var 个人账户ID
     */
    protected $user_info_id = 0;

    public $site_config;

    /**
     * @var int
     */
    protected $limit = 10;

    public function __construct()
    {
        parent::__construct();

        $is_ie = strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE');

        $link = $this->_getLink();
        $positions = $this->_getPositions();
        $this->assign('positions', $positions);
        $this->assign('link', $link);
        $this->assign('is_ie', $is_ie);

        $this->site_config = unserialize(M('config')->getFieldById(1, 'content'));

        if ($this->checkUser) {
            $this->_checkUser();
        }
    }


    /**
     * 验证函数
     *
     * @access private
     */
    protected function _checkUser()
    {
        $user_id = session('user_id');

        if ($user_id > 0) {
            $this->user_id = $user_id;
            $today_zero = strtotime(date('Y-m-d', time()));
            $where['create_time'] = array('between', array($today_zero, time()));
            $where['user_id'] = $this->user_id;
            $recruit_count = M('recruit')->where($where)->count('id');
            $info = M('company_info')->where(array('user_id' => $user_id))->find();
            $this->company = $info;
            $this->company_id = $info['id'];
            $this->company_label = explode(',', $info['label']);
            $this->cnt_recruit = $recruit_count;
            $count = M('recruit')->where(array('user_id' => $user_id))->count('id');
            if ($count > 0){
                M('company_info')->where(array('id' => $user_id))->save(array('credit' => '3'));
            }

            $this->city = $this->_Cities();
            $extreprise_scale = C('ENTERPRISE_SCALE');
            $my_amount = M('user')->where('id = '.$this->user_id)->getField('amount');
            $recruit_count =  M('recruit')->where(array('user_id' => $this->user_id, 'status' => array('neq', '4')))->count();
            $this->assign('my_amount',$my_amount);
            $this->assign('company_scales', C('SCALES'));
            $this->assign('entreprise_scale', $extreprise_scale[$info['company_size']]);
            $this->assign('recruit_count',$recruit_count);
            $this->assign('user', $this->user_data);
            $this->assign('info', $info);
            $this->assign('district', $city[$info['city_id']]['name']);
            $this->assign('count', $this->cnt_recruit);
            $this->assign('company_label', $this->company_label);
        } else {
            if (IS_AJAX) {
                $this->error('登录已失效，请刷新页面');
            } else {
                if( session('identity') == 3 ){
                    $this->redirect('Shop/login');
                }
                $this->redirect('Auth/login');
            }
        }
    }

    protected function _check_amount($need)
    {

        $amount = M('user')->where('id = ' . $this->user_id)->getField('amount');
        if (!$amount || $need > $amount) {
            return false;
        }
        return true;
    }

    /**
     * @param string $msg
     * @param string $code
     * @param array $data
     */
    protected function output($msg = '请求错误', $code = 'fail', $data = array())
    {
        $json_data = array('code' => $code, 'msg' => $msg, 'data' => $data);
        ob_clean();
        die(json_encode($json_data));
    }

    /**
     * 检测用户是否已投递过该岗位
     */
    public function delivered($user_info_id, $recruit_id,$model=0)
    {
        $company_invitation = M('company_invitation');
        $invitation_map['user_info_id'] = $user_info_id;
        $invitation_map['recruit_id'] = $recruit_id;
        $invitation_map['model'] = $model;
        $invitation_map['status'] = array('NEQ', 7);
        $invit_data = $company_invitation->where($invitation_map)->find();
        //dump($company_invitation->getLastSql());
        if ($invit_data) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取底部链接
     */
    protected function _getLink()
    {
        $link = S('link');
        if (empty($link)) {
            $link = M('link')->order('sort asc')->select();
            S('link', $link);
        }
        return $link;
    }

    /**
     * 获取职位分类
     */
    protected function _getPositions()
    {
        $data = S('positions');
        if (empty($data)) {
            $data = M('position_type')->getField('id,name');
            S('positions', $data);
        }
        return $data;
    }
    /**
     * 获取城市信息
     */
    protected function _Cities()
    {
        $data = S('cities');
        if (empty($data)) {
            $data = M('city')->where('level = 2')->getField('id,name');
            S('cities', $data);
        }
        return $data;
    }

    /**
     * 获取岗位标签
     */
    protected function _Position_label()
    {
        $data = S('position_label');
        if (empty($data)) {
            $data = M('position_label')->getField('id,name');
            S('position_label', $data);
        }
        return $data;
    }

    /**
     * 获取分类信息
     */
    protected function _getPositionType()
    {
        $data = S('position_type');
        if (empty($data)) {
            $data = M('position_type')->where(array('parent_id' => 0))->order('sort asc')->select();
            foreach ($data as &$position) {
                $son_data = M('position_type')->where(array('parent_id' => $position['id']))->select();
                foreach ($son_data as &$val) {
                    $val['son_data'] = M('position_type')->where(array('parent_id' => $val['id']))->select();
                }
                $position['son_data'] = $son_data;
            }
            S('position_type', $data);
        }
        return $data;
    }

    /**
     * 获取城市信息
     */
    protected function _getCities()
    {
        $data = S('province');
        if (empty($data)) {
            $data = M('city')->field('id,name,pinyin')->where('level = 1')->order('sort asc')->select();
            foreach ($data as &$position) {
                $child = M('city')->field('id,name,pinyin')->where(array('parent_id' => $position['id']))->select();
                foreach ($child as &$val) {
                    $val['child'] = M('city')->field('id,name,pinyin')->where(array('parent_id' => $val['id']))->select();
                }
                $position['child'] = $child;
            }
            S('province', $data);
        }
        return $data;
    }

    /**
     * 获取城市与区信息
     */
    protected function cities_regions ()
    {
        $data = S('cities_regions');
        if (empty($data)) {
            $data = M('city')->where('level = 2')->order('sort asc')->getField('id,name,parent_id');

            foreach ($data as &$position) {
                $child = M('city')->where(array('parent_id' => $position['id']))->getField('id,name,parent_id');
                $position['child'] = $child;
            }
            S('cities_regions', $data);
        }
        //dump($data);
        return $data;
    }

    /**
     * @param $type_id
     * @return mixed
     */
    protected function _getAdvert($type_id)
    {
        $data = S('advert');
        if (empty($data)) {
            $db_data = M('advert')->where(array('begin_time' => array('lt', time()), 'end_time' => array('gt', time())))->order('sort asc')->select();
            foreach ($db_data as $val) {
                $data[$val['advert_type_id']][] = $val;
            }
            S('advert', $data);
        }
        return $data[$type_id] ?: array();
    }

    //计算年龄
    public function calcAge($birthday)
    {
        $iage = 0;
        if (!empty($birthday)) {
            $year = date('Y', strtotime($birthday));
            $month = date('m', strtotime($birthday));
            $day = date('d', strtotime($birthday));
            $now_year = date('Y');
            $now_month = date('m');
            $now_day = date('d');
            if ($now_year > $year) {
                $iage = $now_year - $year - 1;
                if ($now_month > $month) {
                    $iage++;
                } else if ($now_month == $month) {
                    if ($now_day >= $day) {
                        $iage++;
                    }
                }
            }
        }
        return $iage;
    }


    /**
     * 微信授权
     * @param $mid
     */
    public function _authorize($mid){
        $auth_url = C('GET_CODE');
        $appid = C('WECHAT_APPID');
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false){
            $auth_url = C('GET_CODE_SCAN');
            $appid = C('WECHAT_APPID_OPEN');
        }
        $url = str_replace('AAAAAA', $appid, $auth_url);
        $url = str_replace('UUUUUU', urlencode(C('LOGIN_URL')), $url);
        $auth_url = str_replace('MMMMMM', $mid, $url);
        redirect($auth_url);
    }
    /**
     * @param $totalRows
     * @param $listRows
     * @param array $map
     * @param int $rollPage
     * @return Page
     */
    protected function pages($totalRows, $listRows, $map = array(), $rollPage = 5) {
        $Page = new Page($totalRows, $listRows, '', MODULE_NAME . '/' . ACTION_NAME);
        if ($map && IS_POST) {
            foreach ($map as $key => $val) {
                $val             = urlencode($val);
                $Page->parameter .= "$key=" . urlencode($val) . '&';
            }
        }
        if ($rollPage > 0) {
            $Page->rollPage = $rollPage;
        }
        $Page->lastSuffix = false;
        $Page->setConfig('header','<li class="disabled hwh-page-info"><a>共<em>%TOTAL_ROW%</em>个岗位  </a></li>');
        $Page->setConfig('prev','<span aria-hidden="true">«</span>');
        $Page->setConfig('next','<span aria-hidden="true">»</span>');
        $Page->setConfig('theme','%UP_PAGE% %LINK_PAGE% %DOWN_PAGE% ');
        return $Page;
    }

    //分页样式，这个是过滤了layui的
    public function bootstrap_page_style($page_html){
        if ($page_html) {
            $page_show = str_replace('<div>','<nav aria-label="Page navigation"><ul class="pagination">',$page_html);
            $page_show = str_replace('</div>','</ul></nav>',$page_show);
            $page_show = str_replace('<span class="layui-laypage-curr">','<li class="active"><a>',$page_show);
            $page_show = str_replace('</span>','</a></li>',$page_show);
            $page_show = str_replace('<a class="layui-laypage-prev"','<li><a',$page_show);
            $page_show = str_replace('<a class="layui-laypage-next"','<li><a',$page_show);
            $page_show = str_replace(array('<a class="num"','<a class="prev"','<a class="next"','<a class="end"','<a class="first"'),'<li><a',$page_show);
            $page_show = str_replace('</a>','</a></li>',$page_show);
        }
        return $page_show;
    }

    /**
     * 获取access_token
     * @DateTime 2018-10-08
     * @return   [type]               [description]
     */
    public function _getAccessToken() {
        $token_where = array('expire_time' => array('gt', time()), 'id' => 1);
        $access_token = M('token')->where($token_where)->getField('access_token');
        $WeiXin = new \Common\Org\WeiXin();
        if (!$access_token) {
            $access_token = $WeiXin->getAccessToken();
            if ($access_token !== false) {
                $save_token = array('access_token' => $access_token, 'expire_time' => time() + 7200, 'id' => 1);
                M('token')->save($save_token);
            }
        }
        return $access_token;
    }

    /*
     *  检测用户基本资料填写情况
     */
    public function _getCompanyInfo($uid)
    {
        $company_info = M('company_info')->where(array('id' => $uid))->find();
        if ($company_info['province_id'] == '' || $company_info['district_id'] == '' || $company_info['city_id'] == '') {
            return false;
        } elseif ($company_info['hr_name'] == '') {
            return false;
        } elseif ($company_info['tel'] == '') {
            return false;
        }
        return true;
    }

    protected function formatDate($time){
        $rtime = date ( "m-d H:i", $time );
        $htime = date ( "H:i", $time );

        $time = time () - $time;

        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor ( $time / 60 );
            $str = $min . '分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor ( $time / (60 * 60) );
            $str = $h . '小时前 ' . $htime;
        } elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor ( $time / (60 * 60 * 24) );
            if ($d == 1)
                $str = '昨天 ' . $rtime;
            else
                $str = '前天 ' . $rtime;
        } else {
            $str = $rtime;
        }
        return $str;
    }


}