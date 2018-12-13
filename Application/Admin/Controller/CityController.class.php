<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/9/3
 * Time: 14:30
 */

namespace Admin\Controller;

/**
 * Class CityController
 *
 * @package Admin\Controller
 */
class CityController extends CommonController {

    /**
     * 城市列表
     */
    public function index() {
        $db_data   = M('city')->order('id asc')->select();
        $temp_data = array();
        foreach ($db_data as $key => $val) {
            $temp_data[$val['parent_id']][] = $val;
        }
        $data = $temp_data[0];
        foreach ($data as &$val) {
            if ($temp_data[$val['id']]) {
                foreach ($temp_data[$val['id']] as &$v) {
                    if ($temp_data[$v['id']]) {
                        $v['son_data'] = $temp_data[$v['id']];
                    }
                }
                $val['son_data'] = $temp_data[$val['id']];
            }
        }
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 保存分类
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id        = I('post.id', 0, 'int');
        $parent_id = I('post.parent_id', 0, 'int');
        $name      = I('post.name', '', 'trim');
        $pinyin    = I('post.pinyin', '', 'trim');
        $sort      = I('post.sort', 255, 'int');
        if (empty($name)) {
            $this->error('城市名称不能为空！');
        }
        $data = array('parent_id' => $parent_id, 'name' => $name, 'pinyin' => $pinyin, 'sort' => $sort);
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('city')->save($data);
        } else {
            $res = M('city')->add($data);
        }
        if ($res !== false) {
            S('city', null);
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
        $info = M('city')->find($id);
        if (!$id || empty($info)) {
            $this->error('城市信息不存在！');
        }
        $res = M('city')->delete($id);
        if ($res) {
            S('city', null);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}