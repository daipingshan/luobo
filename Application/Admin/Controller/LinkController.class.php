<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/9/4
 * Time: 16:33
 */

namespace Admin\Controller;

/**
 * Class LinkController
 *
 * @package Admin\Controller
 */
class LinkController extends CommonController {

    /**
     * 友情链接
     */
    public function index() {
        if (IS_AJAX) {
            $name  = I('get.name', '', 'trim');
            $page  = I('get.page', 1, 'int');
            $limit = I('get.limit', 10, 'int');
            $model = M('link');
            $where = array();
            if ($name) {
                $where['name'] = $name;
            }
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('sort asc, id desc')->select();
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 保存友情链接
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('post.id', 0, 'int');
        $name = I('post.name', '', 'trim');
        $url  = I('post.url', '', 'trim');
        $sort = I('post.sort', 0, 'int');
        if (empty($name)) {
            $this->error('友情链接名称不能为空！');
        }
        if (empty($url)) {
            $this->error('友情链接地址不能为空！');
        }
        $data = array('name' => $name, 'url' => $url, 'sort' => $sort);
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('link')->save($data);
        } else {
            $res = M('link')->add($data);
        }
        if ($res !== false) {
            S('link', null);
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除友情链接
     */
    public function del() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('link')->find($id);
        if (!$id || empty($info)) {
            $this->error('友情链接信息不存在！');
        }
        $res = M('link')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}