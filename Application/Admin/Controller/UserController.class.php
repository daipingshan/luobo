<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/9/6
 * Time: 14:16
 */

namespace Admin\Controller;

/**
 * Class UserController
 *
 * @package Admin\Controller
 */
class UserController extends CommonController {

    /**
     * 萝卜用户
     */
    public function index() {
        if (IS_AJAX) {
            $mobile = I('get.mobile', '', 'trim');
            $email  = I('get.email', '', 'trim');
            $name   = I('get.name', '', 'trim');
            $page   = I('get.page', 1, 'int');
            $limit  = I('get.limit', 50, 'int');
            $model  = M('user_info');
            $where  = array();
            if ($mobile) {
                $where['u.mobile'] = $mobile;
            }
            if ($email) {
                $where['u.email'] = $email;
            }
            if ($name) {
                $where['i.real_name'] = $name;
            }
            $count = $model->alias('i')->join('left join rd_user u ON u.id = i.user_id')->where($where)->count('i.id');
            $data  = $model->alias('i')->join('left join rd_user u ON u.id = i.user_id')->field('i.*,u.*,i.id as id')->where($where)->page($page)->limit($limit)->order('i.id desc')->select();
            foreach ($data as &$val) {
                $val['create_time'] = date('Y-m-d H:i:s', $val['create_time']);
                $val['age']         = date('Y') - date('Y', strtotime($val['birthday']));
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }


    /**
     * 查看用户信息
     */
    public function userInfo() {
        $user_id     = I('get.user_id', 0, 'int');
        $info        = M('user_info')->alias('i')->join('left join rd_user u ON u.id = i.user_id')->where(array('i.user_id' => $user_id))->find();
        $user_work   = M('user_curriculum_vitae')->where(array('user_id' => $user_id))->select();
        $user_school = M('user_education')->where(array('user_id' => $user_id))->select();
        $this->assign(array('info' => $info, 'work' => $user_work, 'school' => $user_school));
        $this->display();

    }

    /**
     * 设置账号状态
     */
    public function setUserStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('get.id', 0, 'int');
        $status = I('get.status', 0, 'int');
        $text   = $status == 1 ? '禁用' : '启用';
        $info   = M('user_info')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('账号信息不存在');
        }
        $res = M('user_info')->where(array('id' => $id))->save(array('status' => $status));
        if ($res) {
            $this->success($text . '成功');
        } else {
            $this->error($text . '失败');
        }
    }


    /**
     * 会员充值
     */
    public function userList() {
        if (IS_AJAX) {
            $mobile = I('get.mobile', '', 'trim');
            $email  = I('get.email', '', 'trim');
            $name   = I('get.name', '', 'trim');
            $status = I('get.status', 0, 'int');
            $page   = I('get.page', 1, 'int');
            $limit  = I('get.limit', 50, 'int');
            $model  = M('user');
            $where  = array();
            if ($mobile) {
                $where['u.mobile'] = $mobile;
            }
            if ($email) {
                $where['u.email'] = $email;
            }
            if ($name) {
                $where['u_i.real_name|c_i.company_name|s.name'] = $name;
            }
            if ($status) {
                if ($status == 4) {
                    $where['s.id'] = array('gt', 0);
                } elseif ($status == 3) {
                    $where['c_i.id'] = array('gt', 0);
                } elseif ($status == 2) {
                    $where['u_i.id'] = array('gt', 0);
                } else {
                    $where['_string'] = "s.id is null AND c_i.id is null AND u_i.id is null";
                }
            }
            $join  = array('left join rd_user_info u_i ON u_i.user_id = u.id', 'left join rd_company_info c_i ON c_i.user_id = u.id', 'left join rd_shops s ON s.user_id = u.id');
            $count = $model->alias('u')->where($where)->join($join)->count('u.id');
            $data  = $model->alias('u')->field('u.*,u_i.real_name as user_name,u_i.id as user_info_id,c_i.company_name,c_i.id as company_info_id,s.name as shop_name,s.id as shop_id')->where($where)->join($join)->page($page)->limit($limit)->order('id desc')->select();
            foreach ($data as &$val) {
                $val['create_time']     = date('Y-m-d', $val['create_time']);
                $val['is_user_info']    = $val['user_info_id'] > 0 ? 1 : 0;
                $val['is_company_info'] = $val['company_info_id'] > 0 ? 1 : 0;
                $val['is_shop']         = $val['shop_id'] > 0 ? 1 : 0;
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 设置账号状态
     */
    public function setUserAmount() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id        = I('post.id', 0, 'int');
        $amount    = I('post.amount', 0, 'int');
        $represent = I('post.represent', '', 'trim');
        $info      = M('user')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('会员信息不存在');
        }
        if (empty($amount)) {
            $this->error('充值金额不能为空');
        }
        if (empty($represent)) {
            $this->error('充值说明不能为空');
        }
        $res = M('user')->where(array('id' => $id))->setInc('amount', $amount);
        if ($res) {
            $this->_addUserFlow($info['id'], $represent, session('admin_user_info')['username'], $info['amount'], $amount, $this->user_id);
            $this->success('充值成功');
        } else {
            $this->error('充值失败');
        }
    }

    /**
     * 设置邀请人ID
     */
    public function setUserInviteUid() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id         = I('post.id', 0, 'int');
        $invite_uid = I('post.invite_uid', 0, 'int');
        $info       = M('user')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('会员信息不存在');
        }
        if (empty($invite_uid)) {
            $this->error('邀请人ID不能为空');
        }
        $res = M('user')->where(array('id' => $id))->setField('invite_uid', $invite_uid);
        if ($res !== false) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 查看用户交易记录
     */
    public function getUserFlow() {
        if (IS_AJAX) {
            $user_id = I('get.id', 0, 'int');
            $page    = I('get.page', 1, 'int');
            $limit   = I('get.limit', 50, 'int');
            $model   = M('user_flow');
            $where   = array('user_id' => $user_id);
            $count   = $model->where($where)->count('id');
            $data    = $model->where($where)->page($page)->limit($limit)->order('id desc')->select();
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 职位标签
     */
    public function label() {
        $model = M('user_label');
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
            $res        = M('user_label')->save($data);
        } else {
            $res = M('user_label')->add($data);
        }
        if ($res !== false) {
            S('user_label', null);
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
        $info = M('user_label')->find($id);
        if (!$id || empty($info)) {
            $this->error('公司标签不存在！');
        }
        $res = M('user_label')->delete($id);
        if ($res) {
            S('user_label', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


}