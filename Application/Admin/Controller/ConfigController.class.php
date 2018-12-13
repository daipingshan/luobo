<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/7/4
 * Time: 11:29
 */

namespace Admin\Controller;

/**
 * 系统设置
 * Class ConfigController
 *
 * @package Admin\Controller
 */
class ConfigController extends CommonController {

    /**
     * 读取系统配置信息
     */
    public function index() {
        $content = M('config')->getFieldById(1, 'content');
        $content = unserialize($content);
        $this->assign('content', $content);
        $this->display();
    }

    /**
     * 编辑系统设置
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $content = M('config')->getFieldById(1, 'content');
        $content = unserialize($content);
        $data    = I('post.', '', 'trim');
        if ($content) {
            $data = array_merge($content, $data);
        }
        $post_content = serialize($data);
        $res          = M('config')->where(array('id' => 1))->save(array('content' => $post_content));
        if ($res !== false) {
            S('config', null);
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }
}