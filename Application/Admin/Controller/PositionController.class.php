<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/8/2
 * Time: 11:12
 */

namespace Admin\Controller;

/**
 * Class PositionController
 *
 * @package Admin\Controller
 */
class PositionController extends CommonController {

    /**
     * 职位分类列表
     */
    public function index() {
        $parent_list = M('position_type')->where(array('parent_id' => 0))->order('sort asc')->select();
        foreach ($parent_list as &$position) {
            $son_data = M('position_type')->where(array('parent_id' => $position['id']))->select();
            foreach ($son_data as &$val) {
                $val['son_data'] = M('position_type')->where(array('parent_id' => $val['id']))->select();
            }
            $position['son_data'] = $son_data;
        }
        $this->assign('data', $parent_list);
        $this->display();
    }

    /**
     * 保存职位分类
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id        = I('post.id', 0, 'int');
        $parent_id = I('post.parent_id', 0, 'int');
        $name      = I('post.name', '', 'trim');
        $is_hot    = I('post.is_hot', '', 'trim');
        $sort      = I('post.sort', 255, 'int');
        $icon      = I('post.icon', '', 'trim');
        if (empty($name)) {
            $this->error('职位名称不能为空！');
        }
        $data = array('parent_id' => $parent_id, 'name' => $name, 'is_hot' => $is_hot, 'sort' => $sort, 'icon' => $icon);
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('position_type')->save($data);
        } else {
            $res = M('position_type')->add($data);
        }
        if ($res !== false) {
            S('position_type', null);
            S('position', null);
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除分类
     */
    public function del() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('position_type')->find($id);
        if (!$id || empty($info)) {
            $this->error('职位信息不存在！');
        }
        $count = M('rd_recruit')->where(array('position_type_id' => $id))->count('id');
        if ($count > 0) {
            $this->error('该职位下存在招聘信息，不能删除！');
        }
        $res = M('position_type')->delete($id);
        if ($res) {
            S('position_type', null);
            S('position', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 职位标签
     */
    public function label() {
        $model = M('position_label');
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
            $this->error('职位标签不能为空！');
        }
        $data = array('name' => $name, 'sort' => $sort);
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('position_label')->save($data);
        } else {
            $res = M('position_label')->add($data);
        }
        if ($res !== false) {
            S('position_label', null);
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
        $info = M('position_label')->find($id);
        if (!$id || empty($info)) {
            $this->error('职位标签不存在！');
        }
        $res = M('position_label')->delete($id);
        if ($res) {
            S('position_label', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


}