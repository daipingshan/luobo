<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/10/15
 * Time: 16:15
 */

namespace Admin\Controller;

/**
 * Class ShopController
 *
 * @package Admin\Controller
 */
class ShopController extends CommonController {

    /**
     * 萝卜门店
     */
    public function index() {
        if (IS_AJAX) {
            $mobile = I('get.mobile', '', 'trim');
            $name   = I('get.name', '', 'trim');
            $page   = I('get.page', 1, 'int');
            $limit  = I('get.limit', 50, 'int');
            $model  = M('shops');
            $where  = array();
            if ($name) {
                $where['s.name'] = $name;
            }
            if ($mobile) {
                $where['u.mobile'] = $mobile;
            }
            $join      = "left join rd_user u ON u.id=s.user_id";
            $count     = $model->alias('s')->join($join)->where($where)->count('s.id');
            $data      = $model->alias('s')->join($join)->where($where)->field('s.*,u.mobile')->page($page)->limit($limit)->order('s.id desc')->select();
            $city_data = $this->_getCityCache();
            $admin_data= $this->_getAdminCache();
            foreach ($data as &$val) {
                $val['city_info'] = $city_data[$val['province_id']]['name'] . '-' . $city_data[$val['city_id']]['name'] . '-' . $city_data[$val['district_id']]['name'] . '-' . $val['address'];
                $val['admin_name'] = $admin_data[$val['admin_id']] ? $admin_data[$val['admin_id']] : '';
            }
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 设置萝卜门店状态
     */
    public function setShopStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id      = I('post.id', 0, 'int');
        $status  = I('post.status', 0, 'int');
        $content = I('post.content', '', 'trim');
        $info    = M('shops')->where(array('id' => $id))->find();;
        if (empty($id) || empty($info)) {
            $this->error('门店信息不存在！');
        }
        if ($status == 2 || $status == 3) {
            if (empty($content)) {
                $this->error('审核失败或封禁原因不能为空！');
            }
        }
        $save_data = array('status' => $status, 'fail_reason' => $content,'admin_id'=>$this->user_id);
        $res       = M('shops')->where(array('id' => $id))->save($save_data);
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 门店业绩
     */
    public function shopOrder() {
        if (IS_AJAX) {
            $mobile    = I('get.mobile', '', 'trim');
            $user_name = I('get.user_name', '', 'trim');
            $shop_id   = I('get.shop_id', '', 'trim');
            $name      = I('get.name', '', 'trim');
            $page      = I('get.page', 1, 'int');
            $limit     = I('get.limit', 50, 'int');
            $model     = M('shops_orders');
            $where     = array();
            if ($mobile) {
                $where['o.tel'] = $mobile;
            }
            if ($shop_id > 0) {
                $where['o.shop_id'] = $shop_id;
            }
            if ($user_name) {
                $where['o.user_name'] = $user_name;
            }
            if ($name) {
                $where['s.name'] = $name;
            }
            $count = $model->alias('o')->join('left join rd_shops s ON s.id = o.shop_id')->where($where)->count('o.id');
            $data  = $model
                ->alias('o')
                ->join(array('left join rd_shops s ON s.id = o.shop_id', 'left join rd_project p ON o.project_id=p.id'))
                ->field('o.*,s.name as shop_name,p.name as project_name')
                ->where($where)
                ->page($page)
                ->limit($limit)
                ->order('o.id desc')
                ->select();
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 门店粉丝
     */
    public function userList() {
        $user_id = I('get.user_id', 0, 'int');
        if (IS_AJAX) {
            $model  = M('user');
            $page   = I('get.page', 1, 'int');
            $limit  = I('get.limit', 50, 'int');
            $mobile = I('get.mobile', '', 'trim');
            $status = I('get.status', 0, 'int');
            if ($user_id > 0) {
                $where = array('u.invite_uid' => $user_id);
                if ($mobile) {
                    $where['u.mobile'] = $mobile;
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
                    $val['create_time']     = date('Y-m-d H:i:s', $val['create_time']);
                    $val['is_user_info']    = $val['user_info_id'] > 0 ? 1 : 0;
                    $val['is_company_info'] = $val['company_info_id'] > 0 ? 1 : 0;
                    $val['is_shop']         = $val['shop_id'] > 0 ? 1 : 0;
                }
            } else {
                $data  = array();
                $count = 0;
            }
            $this->output($data, $count);
        } else {
            $model            = M('user_info');
            $where            = array('u.invite_uid' => $user_id);
            $shop             = M('shops')->where(array('user_id' => $user_id))->find();
            $user_info_num    = $model->alias('u_i')->join('left join rd_user u ON u.id = u_i.user_id')->where($where)->count('u_i.id');
            $company_info_num = M('company_info')->alias('c_i')->join('left join rd_user u ON u.id = c_i.user_id')->where($where)->count('c_i.id');
            $shop_num         = M('shops')->alias('s')->join('left join rd_user u ON u.id = s.user_id')->where($where)->count('s.id');
            $join             = array('left join rd_user_info u_i ON u_i.user_id = u.id', 'left join rd_company_info c_i ON c_i.user_id = u.id', 'left join rd_shops s ON s.user_id = u.id');
            $user_num         = M('user')->alias('u')->join($join)->where(array('u.invite_uid' => $user_id, '_string' => 's.id is null AND c_i.id is null AND u_i.id is null'))->count('u.id');
            $all_user_num     = M('user')->where(array('invite_uid' => $user_id))->count('id');
            $income           = M('shops_orders')->where(array('user_id' => $user_id, 'check_up' => 1))->sum('income');
            $month_income     = M('shops_orders')->where(array('user_id' => $user_id, 'check_up' => 1, 'add_time' => array('egt', date('Y-m') . '-01')))->sum('income');
            $assign           = array(
                'shop'             => $shop,
                'all_user_num'     => $all_user_num > 0 ? $all_user_num : 0,
                'user_num'         => $user_num > 0 ? $user_num : 0,
                'user_info_num'    => $user_info_num > 0 ? $user_info_num : 0,
                'company_info_num' => $company_info_num > 0 ? $company_info_num : 0,
                'shop_num'         => $shop_num > 0 ? $shop_num : 0,
                'income'           => $income > 0 ? $income : 0.00,
                'month_income'     => $month_income > 0 ? $month_income : 0.00
            );
            $this->assign($assign);
            $this->display();
        }
    }

    /**
     * 设置业绩状态
     */
    public function setShopOrderStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('get.id', 0, 'int');
        $status = I('get.status', 0, 'int');
        $info   = M('shops_orders')->where(array('id' => $id))->find();;
        if (empty($id) || empty($info)) {
            $this->error('业绩信息不存在');
        }
        $res = M('shops_orders')->where(array('id' => $id))->save(array('check_up' => $status, 'examinant_id' => $this->user_id));
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 设置业绩打款确认
     */
    public function setShopOrderPayStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id   = I('get.id', 0, 'int');
        $info = M('shops_orders')->where(array('id' => $id))->find();;
        if (empty($id) || empty($info)) {
            $this->error('业绩信息不存在！');
        }
        if ($info['check_up'] != 1) {
            $this->error('业绩状态必须为通过才能确认打款操作！');
        }
        $res = M('shops_orders')->where(array('id' => $id))->save(array('pay_status' => 1));
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 其他业绩
     */
    public function shopReport() {
        $cate = $this->_getShopReportCateCache();
        if (IS_AJAX) {
            $mobile  = I('get.mobile', '', 'trim');
            $cate_id = I('get.cate_id', '', 'trim');
            $name    = I('get.name', '', 'trim');
            $page    = I('get.page', 1, 'int');
            $limit   = I('get.limit', 50, 'int');
            $model   = M('shops_report');
            $where   = array();
            if ($name) {
                $where['s.name'] = $name;
            }
            if ($cate_id) {
                $where['s.cat_id'] = $cate_id;
            }
            if ($mobile) {
                $where['u.mobile'] = $mobile;
            }
            $join  = "left join rd_user u ON u.id=s.user_id";
            $count = $model->alias('s')->join($join)->where($where)->count('s.id');
            $data  = $model->alias('s')->join($join)->where($where)->field('s.*,u.mobile')->page($page)->limit($limit)->order('s.id desc')->select();
            foreach ($data as &$val) {
                $val['cate_name'] = $cate[$val['cat_id']]['name'];
            }
            $this->output($data, $count);
        } else {
            $this->assign('cate', $cate);
            $this->display();
        }
    }

    /**
     * 设置其他业绩状态
     */
    public function setShopReportStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('get.id', 0, 'int');
        $status = I('get.status', 0, 'int');
        $info   = M('shops_report')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('其他业绩信息不存在');
        }
        $res = M('shops_report')->where(array('id' => $id))->save(array('check' => $status));
        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    /**
     * 业绩分类列表
     */
    public function reportCate() {
        if (IS_AJAX) {
            $page  = I('get.page', 1, 'int');
            $limit = I('get.limit', 10, 'int');
            $model = M('shops_report_cats');
            $where = array();
            $count = $model->where($where)->count('id');
            $data  = $model->where($where)->page($page - 1)->limit($limit)->order('id desc')->select();
            $this->output($data, $count);
        } else {
            $this->display();
        }
    }

    /**
     * 保存业绩分类
     */
    public function saveShopReportCate() {
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
            $res        = M('shops_report_cats')->save($data);
        } else {
            $res = M('shops_report_cats')->add($data);
        }
        if ($res !== false) {
            S('shop_report_cate', null);
            $this->success('保存成功');
        } else {
            $this->error('保存失败！');
        }
    }

    /**
     * 设置业绩分类状态
     */
    public function setShopReportCateStatus() {
        if (!IS_AJAX) {
            $this->error('非法请求！');
        }
        $id     = I('get.id', 0, 'int');
        $status = I('get.status', 0, 'int');
        $text   = $status == 0 ? '禁用' : '启用';
        $info   = M('shops_report_cats')->where(array('id' => $id))->find();
        if (empty($id) || empty($info)) {
            $this->error('其他业绩信息不存在');
        }
        $res = M('shops_report_cats')->where(array('id' => $id))->save(array('status' => $status));
        if ($res) {
            $this->success($text . '成功');
        } else {
            $this->error($text . '失败');
        }
    }


}