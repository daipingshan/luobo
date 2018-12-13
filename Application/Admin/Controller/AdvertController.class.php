<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/7/31
 * Time: 14:56
 */

namespace Admin\Controller;

/**
 * 广告管理
 * Class AdvertController
 *
 * @package Admin\Controller
 */
class AdvertController extends CommonController {

    /**
     * 广告列表
     */
    public function index() {
        $type_data = M('advert_type')->getField('id,name');
        if (IS_AJAX) {
            $title   = I('get.title', '', 'trim');
            $type_id = I('get.type_id', 0, 'int');
            $page    = I('get.page', 1, 'int');
            $limit   = I('get.limit', 10, 'int');
            $model   = M('advert');
            $where   = array();
            if ($title) {
                $where['title'] = array('like', "%{$title}%");
            }
            if ($type_id) {
                $where['advert_type_id'] = $type_id;
            }
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('sort asc, id desc')->select();
            foreach ($data as &$val) {
                $val['type_view']  = $type_data[$val['advert_type_id']];
                $val['begin_time'] = date('Y-m-d', $val['begin_time']);
                $val['end_time']   = date('Y-m-d', $val['end_time']);
            }
            $this->output($data, $count);
        } else {
            $this->assign('type_data', $type_data);
            $this->display();
        }
    }

    /**
     * 保存广告
     */
    public function saveAdvert() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $type_id      = I('post.type_id', 0, 'int');
        $title        = I('post.title', '', 'trim,strip_tags');
        $img_url      = I('post.img_url', '', 'trim');
        $location_url = I('post.location_url', '', 'trim');
        $begin_time   = I('post.begin_time', '', 'trim');
        $end_time     = I('post.end_time', '', 'trim');
        $sort         = I('post.sort', 255, 'int');
        $id           = I('post.id', 0, 'int');
        if (empty($type_id)) {
            $this->error('请选择广告分类！');
        }
        if (empty($title)) {
            $this->error('广告标题不能为空！');
        }
        if (empty($img_url)) {
            $this->error('请上传广告图片！');
        }
        if (empty($begin_time)) {
            $this->error('请选择广告开始时间！');
        }
        if (empty($end_time)) {
            $this->error('请选择广告结束时间！');
        }
        $data = array(
            'advert_type_id' => $type_id,
            'title'          => $title,
            'img_url'        => $img_url,
            'location_url'   => $location_url,
            'begin_time'     => strtotime($begin_time),
            'end_time'       => strtotime($end_time),
            'sort'           => $sort,
        );
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('advert')->save($data);
        } else {
            $data['add_time'] = time();
            $res              = M('advert')->add($data);
        }
        if ($res !== false) {
            S('advert', null);
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除广告
     */
    public function delAdvert() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('advert')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('广告信息不存在！');
        }
        $res = M('advert')->delete($id);
        if ($res) {
            S('advert', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 广告分类列表
     */
    public function advertTypeList() {
        if (IS_AJAX) {
            $page  = I('get.page', 1, 'int');
            $limit = I('get.limit', 10, 'int');
            $model = M('advert_type');
            $where = array();
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page)->limit($limit)->order('id desc')->select();
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 保存广告分类
     */
    public function saveAdvertType() {
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
            $res        = M('advert_type')->save($data);
        } else {
            $res = M('advert_type')->add($data);
        }
        if ($res !== false) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除广告分类
     */
    public function delAdvertType() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('advert_type')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('广告分类信息不存在！');
        }
        $count = M('advert')->where(array('advert_type_id' => $id))->count('id');
        if ($count > 0) {
            $this->error("该广告分类下有{$count}条广告信息，请先删除广告后再删除广告分类！");
        }
        $res = M('advert_type')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

}