<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/7/30
 * Time: 17:23
 */

namespace Admin\Controller;


class AuthController extends CommonController {

    /**
     * 权限管理
     */
    public function index() {
        $parent_list = M('auth_rule')->where(array('parent_id' => 0))->index('id')->order('sort asc')->select();
        $list        = M('auth_rule')->where(array('parent_id' => array('gt', 0)))->order('sort asc')->select();
        $parent_arr  = array();
        foreach ($parent_list as $rule) {
            $parent_arr[$rule['id']] = $rule['title'];
        }
        foreach ($list as $rule) {
            $parent_list[$rule['parent_id']]['son_data'][] = $rule;
        }
        $this->assign('parent_arr', $parent_arr);
        $this->assign('data', $parent_list);
        $this->display();
    }


    /**
     * 保存权限
     */
    public function saveAuth() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id        = I('post.id', 0, 'int');
        $parent_id = I('post.parent_id', 0, 'int');
        $name      = I('post.name', '', 'trim');
        $title     = I('post.title', '', 'trim');
        $icon      = I('post.icon', '', 'trim');
        $sort      = I('post.sort', 255, 'int');
        $display   = I('post.display', 1, 'int');
        if (empty($title)) {
            $this->error('权限名称不能为空！');
        }
        $data = array('name' => $name, 'title' => $title, 'icon' => $icon, 'sort' => $sort, 'display' => $display);
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('auth_rule')->save($data);
        } else {
            $data['parent_id'] = $parent_id;
            $res               = M('auth_rule')->add($data);
        }
        if ($res !== false) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 更新权限状态
     */
    public function setAuthStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $info = M('auth_rule')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('权限信息不存在！');
        }
        $status = $info['status'] == 0 ? 1 : 0;
        $msg    = $status == 1 ? '启用' : '禁用';
        $res    = M('auth_rule')->save(array('status' => $status, 'id' => $id));
        if ($res) {
            $this->success($msg . '成功');
        } else {
            $this->error($msg . '失败！');
        }
    }

    /**
     * 权限组列表
     */
    public function authGroupList() {
        if (IS_AJAX) {
            $page      = I('get.page', 1, 'int');
            $count     = M('auth_group')->count('id');
            $start_num = ($page - 1) * $this->limit;
            $data      = M('auth_group')->order('id desc')->limit($start_num, $this->limit)->select();
            $this->output($data, $count);
        }
        $this->display();
    }

    /**
     * 添加或修改权限组模板
     */
    public function authGroupDisplay() {
        $id = I('get.id', 0, 'int');
        if ($id > 0) {
            $info = M('auth_group')->find($id);
            $this->assign('info', $info);
        }
        $parent_list = M('auth_rule')->where(array('parent_id' => 0))->index('id')->order('sort asc')->select();
        $list        = M('auth_rule')->where(array('parent_id' => array('gt', 0)))->order('sort asc')->select();
        foreach ($list as $rule) {
            $parent_list[$rule['parent_id']]['son_data'][] = $rule;
        }
        $this->assign('id', $id);
        $this->assign('data', $parent_list);
        $this->display();
    }


    /**
     * 保存权限组
     */
    public function saveAuthGroup() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('post.id', 0, 'int');
        $title  = I('post.title', '', 'trim');
        $remark = I('post.remark', '', 'trim');
        $rules  = I('post.rules', '', 'trim');
        if (empty($title)) {
            $this->error('权限组名称不能为空！');
        }
        if (empty($rules)) {
            $this->error('请选择权限！');
        }
        $data = array('title' => $title, 'remark' => $remark, 'rules' => implode(',', $rules));
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('auth_group')->save($data);
        } else {
            $res = M('auth_group')->add($data);
        }
        if ($res !== false) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 更新权限状态
     */
    public function setGroupStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $info = M('auth_group')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('权限组信息不存在！');
        }
        $status = $info['status'] == 0 ? 1 : 0;
        $msg    = $status == 1 ? '启用' : '禁用';
        $res    = M('auth_group')->save(array('status' => $status, 'id' => $id));
        if ($res) {
            $this->success($msg . '成功');
        } else {
            $this->error($msg . '失败！');
        }
    }
}