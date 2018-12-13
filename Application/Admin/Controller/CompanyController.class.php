<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/10/8
 * Time: 10:00
 */

namespace Admin\Controller;

use Think\Exception;

/**
 * Class CompanyController
 *
 * @package Admin\Controller
 */
class CompanyController extends CommonController {


    /**
     * 企业用户
     */
    public function index() {
        if (IS_AJAX) {
            $mobile   = I('get.mobile', '', 'trim');
            $email    = I('get.email', '', 'trim');
            $name     = I('get.name', '', 'trim');
            $is_check = I('get.is_check', '', 'trim');
            $status   = I('get.status', '', 'trim');
            $page     = I('get.page', 1, 'int');
            $limit    = I('get.limit', 50, 'int');
            $model    = M('company_info');
            $where    = array();
            if ($mobile) {
                $where['u.mobile'] = $mobile;
            }
            if ($email) {
                $where['u.email'] = $email;
            }
            if ($name) {
                $where['i.company_name'] = $name;
            }
            if ($is_check !== '') {
                $where['i.is_check'] = $is_check;
            }
            if ($status) {
                $where['i.status'] = $status;
            }
            $admin_data= $this->_getAdminCache();
            $count = $model->alias('i')->join('left join rd_user u ON u.id = i.user_id')->where($where)->count('i.id');
            $data  = $model->alias('i')->join('left join rd_user u ON u.id = i.user_id')->field('i.*,u.*,i.id as id')->where($where)->page($page)->limit($limit)->order('i.id desc')->select();
            foreach ($data as &$val) {
                 $val['admin_name'] = $admin_data[$val['admin_id']] ? $admin_data[$val['admin_id']] : '';

            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 查看企业基本信息
     */
    public function partnerInfo() {
        $user_id         = I('get.user_id', 0, 'int');
        $info            = M('company_info')->where(array('user_id' => $user_id))->find();
        $company_flow    = M('company_flow')->where(array('user_id' => $user_id))->select();
        $recruit_data[0] = M('recruit')->where(array('status' => 0, 'user_id' => $user_id))->select();
        $recruit_data[1] = M('recruit')->where(array('status' => 1, 'user_id' => $user_id))->select();
        $recruit_data[2] = M('recruit')->where(array('status' => 2, 'user_id' => $user_id))->select();
        $admin_data= $this->_getAdminCache();
        foreach ($recruit_data as &$val) {
            foreach ($val as &$v) {
                $v['experience_view']    = C('WORK_EXPERIENCE')[$v['experience']];
                $v['limit_educate_view'] = C('EDUCATION')[$v['limit_educate']];
                $v['nature_view']        = C('WORK_NATURE')[$v['nature']];
                $v['admin_name']         = $admin_data[$v['admin_id']] ? $admin_data[$v['admin_id']] : '';

            }
        }
        $this->assign(array('info' => $info, 'flow' => $company_flow, 'recruit_data' => $recruit_data));
        $this->display();
    }

    /**
     * 设置账号状态
     */
    public function setPartnerStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('get.id', 0, 'int');
        $status = I('get.status', 0, 'int');
        $text   = $status == 1 ? '禁用' : '启用';
        $info   = M('company_info')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('账号信息不存在');
        }
        $res = M('company_info')->where(array('id' => $id))->save(array('status' => $status));
        if ($res) {
            if ($status == 1) {
                M('recruit')->where(array('user_id' => $info['user_id']))->save(array('status' => 1));
            }
            $this->success($text . '成功');
        } else {
            $this->error($text . '失败');
        }
    }

    /**
     * 设置商铺首页推荐
     */
    public function setPartnerRecommend() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id           = I('get.id', 0, 'int');
        $is_recommend = I('get.is_recommend', 0, 'int');
        $info         = M('company_info')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('账号信息不存在');
        }
        if ($info['company_meal'] != 3) {
            $this->error('只有钻石会员才有资格设置商铺推荐！');
        }
        $res = M('company_info')->where(array('id' => $id))->save(array('is_recommend' => $is_recommend));
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 设置企业套餐
     */
    public function setPartnerMeal() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $meal = I('post.meal', 0, 'int');
        $info = M('company_info')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('账号信息不存在');
        }
        if (empty($meal)) {
            $this->error('请选择企业套餐');
        }
        $res = M('company_info')->where(array('id' => $id))->save(array('company_meal' => $meal));
        if ($res !== false) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 设置企业评分
     */
    public function setPartnerCredit() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('post.id', 0, 'int');
        $credit = I('post.credit', 0, 'int');
        $info   = M('company_info')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('账号信息不存在');
        }
        if (empty($credit)) {
            $this->error('请选择企业评分');
        }
        $res = M('company_info')->where(array('id' => $id))->save(array('credit' => $credit));
        if ($res !== false) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 删除企业
     */
    public function delCompany() {
        $this->error('该功能已关闭！');
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('company_info')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('企业信息不存在！');
        }
        $res = M('company_info')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 等待审核企业
     */
    public function examine() {
        if (IS_AJAX) {
            $mobile = I('get.mobile', '', 'trim');
            $email  = I('get.email', '', 'trim');
            $name   = I('get.name', '', 'trim');
            $page   = I('get.page', 1, 'int');
            $limit  = I('get.limit', 50, 'int');
            $model  = M('company_info');
            $where  = array('i.is_check' => array('in', '0,4'));
            if ($mobile) {
                $where['u.mobile'] = $mobile;
            }
            if ($email) {
                $where['u.email'] = $email;
            }
            if ($name) {
                $where['i.company_name'] = $name;
            }
            $count = $model->alias('i')->join('left join rd_user u ON u.id = i.user_id')->where($where)->count('i.id');
            $data  = $model->alias('i')->join('left join rd_user u ON u.id = i.user_id')->field('i.*,u.*,i.id as id')->where($where)->page($page)->limit($limit)->order('i.id desc')->select();
            foreach ($data as &$val) {
                $val['add_time'] = date('Y-m-d', $val['add_time']);
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 设置账号状态
     */
    public function setCompany() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id       = I('request.id', 0, 'int');
        $is_check = I('request.is_check', 0, 'int');
        $text     = $is_check == 1 ? '审核资料成功' : '审核资料失败';
        $content  = I('request.content', '', 'trim');
        $info     = M('company_info')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('账号信息不存在');
        }
        if ($is_check == 2 && empty($content)) {
            $this->error('审核失败原因不能为空！');
        }
        $data = array('is_check' => $is_check, 'examine_time' => time(),'admin_id'=>$this->user_id);
        if ($is_check == 2) {
            $data['fail_reason'] = $content;
        } else {
            if ($info['is_check'] == 0) {
                $data['credit'] = 2;
            }
        }
        $res = M('company_info')->where(array('id' => $id))->save($data);
        if ($res) {
            $this->success($text);
        } else {
            $this->error('操作失败');
        }
    }


    /**
     * 设置招聘推荐
     */
    public function setRecruitRecommend() {
        $this->error('该功能已关闭！');
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id          = I('post.id', 0, 'int');
        $expire_time = I('post.expire_time', '', 'trim');
        $info        = M('recruit')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('招聘信息不存在');
        }
        if (empty($expire_time)) {
            $this->error('请选择过期时间');
        }
        $res = M('recruit')->where(array('id' => $id))->save(array('is_recommend' => 1, 'recommend_expire_time' => strtotime($expire_time)));
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 设置账号状态
     */
    public function setRecruitStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id      = I('post.id', 0, 'int');
        $content = I('post.content', '', 'trim');
        $info    = M('recruit')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('招聘信息不存在');
        }
        if (empty($content)) {
            $this->error('违规原因不能为空！');
        }
        $data = array('status' => 2, 'reason' => $content,'admin_id'=>$this->user_id);
        $res  = M('recruit')->where(array('id' => $id))->save($data);
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 职位标签
     */
    public function label() {
        $model = M('company_label');
        $where = array();
        $data  = $model->where($where)->order('sort asc, id desc')->select();
        $this->assign('data', array_chunk($data, 4));
        $this->display();
    }

    /**
     * 保存职位标签分类
     */
    public function saveLabel() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $name = I('post.name', '', 'trim');
        $sort = I('post.sort', 255, 'int');
        if (empty($name)) {
            $this->error('公司标签不能为空！');
        }
        $data = array('name' => $name, 'sort' => $sort);
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('company_label')->save($data);
        } else {
            $res = M('company_label')->add($data);
        }
        if ($res !== false) {
            S('company_label', null);
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除分类
     */
    public function delLabel() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('company_label')->find($id);
        if (!$id || empty($info)) {
            $this->error('公司标签不存在！');
        }
        $res = M('company_label')->delete($id);
        if ($res) {
            S('company_label', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 未审核岗位推荐到首页列表信息
     */
    public function recruitList() {
        if (IS_AJAX) {
            $page  = I('get.page', 1, 'int');
            $limit = I('get.limit', 50, 'int');
            $model = M('recruit');
            $where = array('is_recommend' => 1, 'is_check' => 0, 'recommend_expire_time' => array('lt', time()));
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('id desc')->select();
            foreach ($data as &$v) {
                $v['experience_view']    = C('WORK_EXPERIENCE')[$v['experience']];
                $v['limit_educate_view'] = C('EDUCATION')[$v['limit_educate']];
                $v['nature_view']        = C('WORK_NATURE')[$v['nature']];
                $v['create_time']        = date('Y-m-d H:i:s', $v['create_time']);
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 审核招聘推荐到首页
     */
    public function recruitCheck() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('recruit')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('招聘信息不存在');
        }
        $day         = intval($info['toindex_time']) > 0 ? intval($info['toindex_time']) : 1;
        $amount      = $day * C('COMPANY')['base']['index'];
        $user_amount = M('user')->getFieldById($info['user_id'], 'amount');
        $user_amount = intval($user_amount);
        if ($amount > $user_amount) {
            $this->error("企业余额不足，企业余额{$user_amount}，推荐首页扣除{$amount}，联系企业充值后操作");
        }
        $model = M();
        $model->startTrans();
        try {
            $expire_time = time() + $day * 86400;
            $data        = array('is_check' => 1, 'recommend_expire_time' => $expire_time);
            M('recruit')->where(array('id' => $id))->save($data);
            M('user')->where(array('id' => $info['user_id']))->setDec('amount', $amount);
            $represent = "企业招聘信息推荐到首页，后台审核操作";
            $this->_addUserFlow($info['user_id'], $represent, session('admin_user_info')['username'], $user_amount, '-' . $amount, $this->user_id);
            if ($model->commit()) {
                $this->success('设置成功');
            } else {
                throw new \Exception($model->getError());
            }
        } catch (\Exception $e) {
            $this->error('设置失败' . $e->getMessage());
        }
    }
}