<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2018/8/7
 * Time: 22:48
 */

namespace Home\Controller;

use Home\Controller\UserController;

class ProjectController extends CommonController {
    private $user_info;
    private $model = 1;
    protected $checkUser = false;

    public function __initialize() {
        //切换身份至个人
        session('identity', 1);
        parent::_checkUser();

        $this->user_info = M('user_info')->where(array('user_id' => $this->user_id))->find();

        //设定个人资料 1
        if (empty(I('post.act')) && empty($this->user_info)) {
            $this->redirect('user/guide', '', 0);
        }
        $this->user_info_id = $this->user_info['id'];

        //查询企业邀请信息
        $invitations_c['user_info_id'] = $this->user_info_id;
        $invitations_c['status']       = 4;

        $this->invitations             = M('company_invitation')->where($invitations_c)->count("id");
        $this->assign('invitations', $this->invitations);
        $this->assign('city_array',parent::_Cities());

    }

    /**
     * 职位详情
     */
    public function detail() {

        $id = I('get.id', null, 'int');
        if (empty($id) && !isset($id)) {

            $this->error('这份工作已经下架了，尝试换个岗位看看', U('search/index'), 5);
        }
        $city = $this->_Cities();
        //简历信息
        $project                = M('project')->where(array('id' => $id))->find();
        $project['rebate_desc'] = str_replace(array("\r\n", "\r", "\n"), "<br>", $project['rebate_desc']);
        //检测是否投递
        if (parent::delivered($this->user_info_id, $id, $this->model)) {
            $this->assign('delivered', true);
        }
        $this->assign('city', $city);
        $this->assign('item', $project);
        $this->assign('education', C('EDUCATION'));
        $this->assign('search_hide', true);
        $this->assign('title', $project['name']);
        $this->display();
    }

    /**
     * 收藏
     */

    /**
     * 投递
     */
    public function set_poj_resume() {
        self::__initialize();
        $recruit_id = I('id', null, 'int');
        if (empty($recruit_id) && $recruit_id == '') {
            $this->error('这份工作已经下架了，尝试换个岗位看看', U('search/index'), 5);
        }

        $company_invitation = M('company_invitation');

        if (parent::delivered($this->user_info_id, $recruit_id, $type = 1)) {
            $this->error('您已经投递过该岗位，耐心等待反馈', U('User/index'), 5);
        }

        $project = M('project')->where('id = ' . $recruit_id)->find();

        if (!$project) {
            $this->error('这个岗位已经下架了，代码：463', U('search/index'), 5);
        }

        $data['company_id']   = 1;
        $data['recruit_id']   = $project['id'];
        $data['recruit_name'] = $project['name'];
        $data['company_name'] = "萝卜项目";
        $data['user_name']    = $this->user_info['real_name'];
        if ($project['salary_min'] == 0) {
            $data['salary'] = '面议';
        } else {
            $data['salary'] = $project['salary_min'] . 'k~' . $project['salary_max'] . 'k';
        }
        $data['address']      = $project['address'];
        $data['user_info_id'] = $this->user_info_id;
        $data['add_time']     = strtotime('now');
        $data['status']       = 1;
        $data['model']        = $this->model;
        //        dump($data);
        //        exit();
        $company_invitation->data($data)->add();
        $this->redirect('User/index', '', 0);
    }


}