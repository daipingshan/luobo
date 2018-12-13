<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/10/9
 * Time: 14:09
 */

namespace Admin\Controller;

/**
 * 萝卜项目
 * Class ProjectController
 *
 * @package Admin\Controller
 */
class ProjectController extends CommonController {


    /**
     * 招聘信息
     */
    public function index() {
        $project_type_data = $this->_getProjectTypeCache();
        if (IS_AJAX) {
            $type_id = I('get.project_type_id', 0, 'int');
            $mobile  = I('get.mobile', '', 'trim');
            $name    = I('get.name', '', 'trim');
            $page    = I('get.page', 1, 'int');
            $limit   = I('get.limit', 50, 'int');
            $model   = M('project');
            $where   = array();
            if ($type_id) {
                $where['project_type_id'] = $type_id;
            }
            if ($mobile) {
                $where['tel'] = $mobile;
            }
            if ($name) {
                $where['name'] = $name;
            }
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('sort asc,id desc')->select();
            foreach ($data as &$val) {
                $val['create_time']       = date('Y-m-d', $val['create_time']);
                $val['project_type_name'] = isset($project_type_data[$val['project_type_id']]) ? $project_type_data[$val['project_type_id']]['name'] : '';
            }
            $this->output($data, $count);
        } else {
            $this->assign('project_type_data', $project_type_data);
            $this->display();
        }
    }

    /**
     * 添加萝卜项目招聘信息
     */
    public function add() {
        $city_data         = M('city')->where(array('parent_id' => 0))->order('sort asc')->getField('id,name');
        $project_type_data = $this->_getProjectTypeCache();
        $this->assign('city_data', $city_data);
        $this->assign('project_type_data', $project_type_data);
        $this->display();
    }

    /**
     * 编辑招聘信息
     */
    public function update() {
        $id   = I('get.id', 0, 'int');
        $info = M('project')->find($id);
        if (empty($id) || empty($info)) {
            $this->assign('error_info', '招聘信息不存在！');
        } else {
            $city_cache                = $this->_getCityCache();
            $project_type_data         = $this->_getProjectTypeCache();
            $info['city_info']         = $city_cache[$info['province_id']]['name'] . '-' . $city_cache[$info['city_id']]['name'] . '-' . $city_cache[$info['district_id']]['name'];
            $info['project_type_info'] = $project_type_data[$info['project_type_id']]['name'];
            $this->assign('info', $info);
            $city_data = M('city')->where(array('parent_id' => 0))->order('sort asc')->getField('id,name');
            $this->assign('city_data', $city_data);
            $this->assign('project_type_data', $project_type_data);
        }
        $this->display();
    }

    /**
     * 保存招聘信息
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id              = I('post.id', 0, 'int');
        $project_type_id = I('post.project_type_id', 0, 'int');
        $province_id     = I('post.province_id', 0, 'int');
        $city_id         = I('post.city_id', 0, 'int');
        $district_id     = I('post.district_id', 0, 'int');
        $name            = I('post.name', '', 'trim');
        $image           = I('post.image', '', 'trim');
        $salary_min      = I('post.salary_min', '', 'trim');
        $salary_max      = I('post.salary_max', '', 'trim');
        $contacts        = I('post.contacts', '', 'trim');
        $tel             = I('post.tel', '', 'trim');
        $sort            = I('post.sort', 9999, 'int');
        $subsidy         = I('post.subsidy', 0, 'int');
        $rebate          = I('post.rebate', 0, 'int');
        $rebate_desc     = I('post.rebate_desc', '', 'trim');
        $address         = I('post.address', '', 'trim');
        $content         = I('post.content', '', 'trim');
        if (empty($project_type_id) && $id == 0) {
            $this->error('请选择萝卜分类！');
        }
        if (empty($province_id) && $id == 0) {
            $this->error('请选择省份！');
        }
        if (empty($city_id) && $id == 0) {
            $this->error('请选择城市！');
        }
        if (empty($district_id) && $id == 0) {
            $this->error('请选择区域！');
        }
        if (empty($name)) {
            $this->error('项目名称不能为空！');
        }
        if (empty($image)) {
            $this->error('请上传项目logo！');
        }
        if (empty($contacts)) {
            $this->error('联系人不能为空！');
        }
        if (empty($tel)) {
            $this->error('联系电话不能为空！');
        }

        if (empty($address)) {
            $this->error('工作地址不能为空！');
        }
        if (empty($content)) {
            $this->error('招聘信息不能为空！');
        }
        $data = array(
            'admin_id'    => $this->user_id,
            'admin_name'  => session('admin_user_info')['username'],
            'name'        => $name,
            'image'       => $image,
            'salary_min'  => $salary_min,
            'salary_max'  => $salary_max,
            'contacts'    => $contacts,
            'tel'         => $tel,
            'sort'        => $sort,
            'subsidy'     => $subsidy,
            'rebate'      => $rebate,
            'rebate_desc' => $rebate_desc,
            'address'     => $address,
            'content'     => $content,
        );
        if ($id > 0) {
            $data['id'] = $id;
            if ($project_type_id > 0) {
                $data['project_type_id'] = $project_type_id;
            }
            if ($province_id > 0 && $city_id > 0 && $district_id > 0) {
                $data['province_id'] = $province_id;
                $data['city_id']     = $city_id;
                $data['district_id'] = $district_id;
            }
            $res = M('project')->save($data);
        } else {
            $data['project_type_id'] = $project_type_id;
            $data['province_id']     = $province_id;
            $data['city_id']         = $city_id;
            $data['district_id']     = $district_id;
            $data['create_time']     = time();
            $res                     = M('project')->add($data);
        }
        if ($res !== false) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 获取城市信息
     */
    public function getCity() {
        $id   = I('get.id', 0, 'int');
        $data = M('city')->where(array('parent_id' => $id))->order('sort asc')->getField('id,name');
        $html = '';
        foreach ($data as $key => $val) {
            $html .= "<option value='{$key}'>{$val}</option>";
        }
        $this->output(array('html' => $html));
    }

    /**
     * 获取城市信息
     */
    public function getPosition() {
        $id   = I('get.id', 0, 'int');
        $data = M('position_type')->where(array('parent_id' => $id))->order('sort asc')->getField('id,name');
        $html = '';
        foreach ($data as $key => $val) {
            $html .= "<option value='{$key}'>{$val}</option>";
        }
        $this->output(array('html' => $html));
    }

    /**
     * 设置萝卜项目首页推广
     */
    public function setProjectRecommend() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id           = I('get.id', 0, 'int');
        $is_recommend = I('get.is_recommend', 0, 'int');
        $info         = M('project')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('招聘信息不存在');
        }
        $res = M('project')->where(array('id' => $id))->save(array('is_recommend' => $is_recommend));
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 设置萝卜项目首页推广
     */
    public function setProjectStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('post.id', 0, 'int');
        $status = I('post.status', 0, 'int');
        $info   = M('project')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('招聘信息不存在');
        }
        $res = M('project')->where(array('id' => $id))->save(array('status' => $status));
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 投递记录
     */
    public function projectRecords() {
        if (IS_AJAX) {
            $status       = I('get.status', 0, 'int');
            $user_name    = I('get.user_name', '', 'trim');
            $recruit_name = I('get.recruit_name', '', 'trim');
            $page         = I('get.page', 1, 'int');
            $limit        = I('get.limit', 50, 'int');
            $model        = M('company_invitation');
            $where        = array('company_id' => 1);
            if ($status) {
                $where['status'] = $status;
            }
            if ($user_name) {
                $where['user_name'] = $user_name;
            }
            if ($recruit_name) {
                $where['recruit_name'] = $recruit_name;
            }
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('id desc')->select();
            foreach ($data as &$val) {
                $val['url'] = C('BASE')['domain'] . "/Vitae/detail.html?id=" . base64_encode($val['user_info_id'] . C('ENCODE'));
            }
            foreach ($data as &$val) {
                $val['add_time'] = date('Y-m-d', $val['add_time']);
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 设置投递状态
     */
    public function setProjectRecordsStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('get.id', 0, 'int');
        $status = I('get.status', 0, 'int');
        $info   = M('company_invitation')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('投递信息不存在');
        }
        $res = M('company_invitation')->where(array('id' => $id))->save(array('status' => $status, 'update_time' => time()));
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 萝卜分类列表
     */
    public function typeList() {
        if (IS_AJAX) {
            $page  = I('get.page', 1, 'int');
            $limit = I('get.limit', 10, 'int');
            $model = M('project_type');
            $where = array();
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('id desc')->select();
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 保存萝卜分类
     */
    public function saveType() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $name = I('post.name', '', 'trim');
        if (empty($name)) {
            $this->error('广告分类名称不能为空！');
        }
        $data = array(
            'name' => $name,
        );
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('project_type')->save($data);
        } else {
            $res = M('project_type')->add($data);
        }
        if ($res !== false) {
            S('project_type', null);
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除广告分类
     */
    public function delType() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('project_type')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('广告分类信息不存在！');
        }
        $count = M('project')->where(array('project_type_id' => $id))->count('id');
        if ($count > 0) {
            $this->error("该萝卜分类下有{$count}条招聘信息，请先删除招聘信息后再删除萝卜分类！");
        }
        $res = M('project_type')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}