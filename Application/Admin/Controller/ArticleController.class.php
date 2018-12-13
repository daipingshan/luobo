<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/9/4
 * Time: 16:31
 */

namespace Admin\Controller;

/**
 * Class ArticleController
 *
 * @package Admin\Controller
 */
class ArticleController extends CommonController {

    /**
     * 文章分类
     */
    public function type() {
        $db_data   = M('article_type')->order('sort desc,id asc')->select();
        $temp_data = array();
        foreach ($db_data as $key => $val) {
            $temp_data[$val['parent_id']][] = $val;
        }
        $data = $temp_data[0];
        foreach ($data as &$val) {
            if ($temp_data[$val['id']]) {
                $val['son_data'] = $temp_data[$val['id']];
            }
        }
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 保存职位分类
     */
    public function saveType() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id        = I('post.id', 0, 'int');
        $parent_id = I('post.parent_id', 0, 'int');
        $name      = I('post.name', '', 'trim');
        $sort      = I('post.sort', 255, 'int');
        if (empty($name)) {
            $this->error('文章分类名称不能为空！');
        }
        $data = array('parent_id' => $parent_id, 'name' => $name, 'sort' => $sort);
        if ($id > 0) {
            $data['id'] = $id;
            $res        = M('article_type')->save($data);
        } else {
            $res = M('article_type')->add($data);
        }
        if ($res !== false) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除分类
     */
    public function delType() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('article_type')->find($id);
        if (!$id || empty($info)) {
            $this->error('文章分类信息不存在！');
        }
        if ($info['parent_id'] > 0) {
            $count = M('article')->where(array('article_type_id' => $id))->count('id');
            if ($count > 0) {
                $this->error('该分类下已有文章，请先删除文章！');
            }
        } else {
            $count = M('article_type')->where(array('parent_id' => $info['parent_id']))->count('id');
            if ($count > 0) {
                $this->error('该分类有二级分类，请先删除二级分类！');
            }
        }
        $res = M('article_type')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 文章列表
     */
    public function index() {
        if (IS_AJAX) {
            $title = I('get.title', '', 'trim');
            $page  = I('get.page', 1, 'int');
            $limit = I('get.limit', 10, 'int');
            $model = M('article');
            $where = array();
            if ($title) {
                $where['a.title'] = array('like', "%{$title}%");
            }
            $count = $model->alias('a')->where($where)->count('id');
            $data  = $model->alias('a')->join('left join rd_article_type t ON t.id = a.article_type_id')->field('a.*,t.name')->where($where)->page($page)->limit($limit)->order('sort asc, id desc')->select();
            foreach ($data as &$val) {
                $val['add_time'] = date('Y-m-d', $val['add_time']);
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 获取文章二级分类
     */
    public function getArticleType() {
        if (!IS_AJAX) {
            $this->success(array('data' => array()));
            exit;
        }
        $parent_id = I('get.parent_id', 0, 'int');
        if (empty($parent_id)) {
            $this->success(array('data' => array()));
            exit;
        }
        $data = M('article_type')->where(array('parent_id' => $parent_id))->order('sort asc,id desc')->select();
        $this->success(array('data' => $data));
        exit;
    }

    /**
     * 添加文章
     */
    public function add() {
        $type_data = M('article_type')->where(array('parent_id' => 0))->getField('id,name');
        $this->assign('type_data', $type_data);
        $this->display('article');
    }

    /**
     * 编辑文章
     */
    public function update() {
        $id   = I('get.id', 0, 'int');
        $info = M('article')->find($id);
        $this->assign('info', $info);
        $this->display('article');
    }

    /**
     * 保存文章
     */
    public function save() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $type_id = I('post.type_id', 0, 'int');
        $title   = I('post.title', '', 'trim,strip_tags');
        $img     = I('post.img', '', 'trim');
        $content = I('post.content', '', 'trim');
        $sort    = I('post.sort', 255, 'int');
        $id      = I('post.id', 0, 'int');
        if (empty($type_id) && $id == 0) {
            $this->error('请选择文章分类！');
        }
        if (empty($title)) {
            $this->error('文章标题不能为空！');
        }
        if (empty($img)) {
            $this->error('请上传文章配图！');
        }
        if (empty($content)) {
            $this->error('文章内容不能为空！');
        }
        $data = array(
            'article_type_id' => $type_id,
            'title'           => $title,
            'img'             => $img,
            'content'         => $content,
            'sort'            => $sort,
        );
        if ($id > 0) {
            $data['id'] = $id;
            unset($data['article_type_id']);
            $res = M('article')->save($data);
        } else {
            $data['add_time'] = time();
            $res              = M('article')->add($data);
        }
        if ($res !== false) {
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 删除文章
     */
    public function del() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('article')->find($id);
        if (empty($id) || empty($info)) {
            $this->error('文章信息不存在！');
        }
        $res = M('article')->delete($id);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}