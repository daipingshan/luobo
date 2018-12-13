<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/7/30
 * Time: 15:45
 */

namespace Admin\Controller;

/**
 * 管理员控制器
 * Class AdminController
 *
 * @package Admin\Controller
 */
class AdminController extends CommonController {

    /**
     * 系统管理员
     */
    public function index() {
        if (IS_AJAX) {
            $username = I('get.username', '', 'trim');
            $page     = I('get.page', 1, 'int');
            $where    = array();
            if ($username) {
                $where['username|realname'] = array('like', "%{$username}%");
            }
            $count     = M('admin')->where($where)->count('id');
            $start_num = ($page - 1) * $this->limit;
            $data      = M('admin')->where($where)->order('id desc')->limit($start_num, $this->limit)->select();
            foreach ($data as &$val) {
                $val['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
                if ($val['last_time'] > 0) {
                    $val['last_time'] = date('Y-m-d H:i:s', $val['last_time']);
                }
            }
            $this->output($data, $count);
        }
        $auth_group_data = M('auth_group')->where(array('status' => 1))->select();
        $this->assign('auth_group_data', json_encode($auth_group_data));
        $this->display();
    }

    /**
     * 获取用户权限
     */
    public function getUserAuthGroup() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $uid = I('get.uid', 0, 'int');
        if (empty($uid)) {
            $this->error('管理员编号异常，无法授权');
        }
        $data = M('auth_group_access')->where(array('uid' => $uid))->getField('group_id', true);
        $this->success(array('data' => $data ? : array()));
    }

    /**
     * 管理员授权
     */
    public function userAuth() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $uid      = I('post.uid', 0, 'int');
        $group_id = I('post.group_id', '', 'trim');
        if (empty($uid)) {
            $this->error('管理员编号异常，无法授权！');
        }
        if (empty($group_id)) {
            $this->error('请选择管理员对应权限组');
        }
        $data = array();
        foreach ($group_id as $val) {
            $data[] = array('uid' => $uid, 'group_id' => $val);
        }
        M('auth_group_access')->where(array('uid' => $uid))->delete();
        $add_res = M('auth_group_access')->addAll($data);
        if ($add_res) {
            $this->success('授权成功');
        } else {
            $this->error('授权失败！');
        }
    }


    /**
     * 保存账号
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id        = I('post.id', 0, 'int');
        $username  = I('post.username', '', 'trim');
        $password  = I('post.password', '', 'trim');
        $real_name = I('post.realname', '', 'trim');
        if (empty($username)) {
            $this->error('账号名称不能为空！');
        }
        if ($id == 0 && empty($password)) {
            $this->error('密码不能为空！');
        }
        if (empty($real_name)) {
            $this->error('真实姓名不能为空！');
        }
        $data = array('username' => $username, 'realname' => $real_name);
        if ($password) {
            $data['password'] = encrypt_pwd($password);
        }
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('admin')->save($data);
        } else {
            $data['add_time'] = time();
            $res              = M('admin')->add($data);
        }
        if ($res !== false) {
            S('admin',null);
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 更新账号状态
     */
    public function setStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $info = M('admin')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('账号不存在！');
        }
        $status = $info['status'] == 0 ? 1 : 0;
        $msg    = $status == 1 ? '启用' : '禁用';
        $res    = M('admin')->save(array('status' => $status, 'id' => $id));
        if ($res) {
            $this->success($msg . '成功');
        } else {
            $this->error($msg . '失败！');
        }
    }

    /**
     * 删除管理员账号
     */
    public function del() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('admin')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('账号不存在！');
        }
        $res = M('admin')->delete($id);
        if ($res) {
            S('admin',null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


}