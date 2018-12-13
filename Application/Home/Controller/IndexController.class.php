<?php

namespace Home\Controller;

class IndexController extends CommonController {

    protected $checkUser = false;
    protected $condition;
    public    $url;

    /**
     * 首页
     */
    public function index() {
        $advert = $this->_getAdvert(1);
        $type   = $this->_getPositionType();
        $this->assign(array('advert' => $advert, 'type' => $type, 'menu' => 'index', 'info' => $this->company));
        $this->display();
    }

    /**
     * 获取公司信息
     */
    public function getCompany() {
        $company_id = M('company_info')
            ->where(array('recommend_expire_time' => array('egt', time()), 'is_recommend' => 0, 'status' => 0, 'is_check' => 1))
            ->getField('id', true);
        if (count($company_id) > 9) {
            $company_id = array_flip($company_id);
            $company_id = array_rand($company_id, 9);
        }
        $data = array();
        if (count($company_id) > 0) {
            $data       = M('company_info')->where(array('id' => array('in', $company_id)))->select();
            $meal_data  = C('COMPANY');
            $scale_data = C('SCALES');
            $label_data = $this->_getCompanyLabelCache();
            foreach ($data as &$val) {
                $label_arr         = explode(',', $val['label']);
                $val['scale_name'] = $scale_data[$val['scale']];
                $val['label_html'] = "";
                foreach ($label_arr as $key => $label) {
                    if ($key < 3) {
                        $val['label_html'] .= '<div class="col-md-4 col-xs-4"><a class="cp_mark">' . $label_data[$label]['name'] . '</a></div>';
                    }
                }
                $val['num'] = M('recruit')->where(array('user_id' => $val['user_id'], 'status' => 0))->count('id');
                if ($val['company_meal'] == 1) {
                    $val['meal_name'] = $meal_data['1888']['name'];
                } else if ($val['company_meal'] == 2) {
                    $val['meal_name'] = $meal_data['5888']['name'];
                } else if ($val['company_meal'] == 3) {
                    $val['meal_name'] = $meal_data['8888']['name'];
                } else {
                    $val['meal_name'] = '普通会员';
                }
            }
        }

        $this->output('ok', 'success', $data);
    }

    /**
     * 获取招聘信息
     */
    public function getRecruit() {
        $type         = I('get.type', 'hot', 'trim');
        $nature       = I('get.nature', 'full_time', 'trim');
        $data         = array();
        $work_data    = C('WORK_EXPERIENCE');
        $educate_data = C('EDUCATION');
        switch ($type) {
            case 'hot' :
                $where      = array('status' => 0, 'is_recommend' => 1, 'recommend_expire_time' => array('egt', time()));
                $recruit_id = M('recruit')->where($where)->getField('id', true);
                if (count($recruit_id) > 9) {
                    $recruit_id = array_flip($recruit_id);
                    $recruit_id = array_rand($recruit_id, 9);
                }
                if (count($recruit_id) > 0) {
                    $data = M('recruit')->alias('r')->field('r.*,c.id as company_id,c.company_name,c.short_name,c.avatar,c.hr_qrcode')->where(array('r.id' => array('in', $recruit_id)))->join('left join rd_company_info c ON c.user_id = r.user_id')->select();
                }
                break;
            case 'new' :
                $data = M('recruit')->alias('r')->field('r.*,c.id as company_id,c.company_name,c.short_name,c.avatar,c.hr_qrcode')->where(array('r.status' => 0))->join('left join rd_company_info c ON c.user_id = r.user_id')->order('r.refresh_time desc,r.create_time desc')->limit(9)->select();
                break;
            case 'self' :
                $where['r.user_id'] = 1000;
                $recruit_id         = M('project')->where(array('status' => 1, 'is_recommend' => 1))->getField('id', true);
                if (count($recruit_id) > 9) {
                    $recruit_id = array_flip($recruit_id);
                    $recruit_id = array_rand($recruit_id, 9);
                }
                if (count($recruit_id) > 0) {
                    $data = M('project')->where(array('id' => array('in', $recruit_id)))->select();
                    foreach ($data as &$val) {
                        $val['city_name']     = $this->_getCityCache()[$val['id']]['name'];
                        $val['position_name'] = $this->_getProjectTypeCache()[$val['project_type_id']]['name'];
                    }
                }
                break;
            case 'school' :
                $where      = array('r.status' => 0, 'r.type' => 'school', 'r.nature' => $nature);
                $recruit_id = M('recruit')->alias('r')->where($where)->getField('id', true);
                if (count($recruit_id) > 9) {
                    $recruit_id = array_flip($recruit_id);
                    $recruit_id = array_rand($recruit_id, 9);
                }
                if (count($recruit_id) > 0) {
                    $data = M('recruit')->alias('r')->field('r.*,c.id as company_id,c.company_name,c.short_name,c.avatar,c.hr_qrcode')->where(array('r.id' => array('in', $recruit_id)))->join('left join rd_company_info c ON c.user_id = r.user_id')->select();
                }
                break;
            default:
                break;
        }
        foreach ($data as &$val) {
            if ($type == 'self') {
                $val['create_time'] = date('m-d', $val['create_time']);
            } else {
                $val['create_time'] = date('m-d H:i', $val['create_time']);
            }
            if ($val['short_name']) {
                $val['company_name'] = $val['short_name'];
            }
            $val['work_name']    = $work_data[$val['experience']];
            $val['educate_name'] = $educate_data[$val['limit_educate']];
        }

        $this->output('ok', 'success', $data);
    }

    /**
     * 萝卜项目
     */
    public function project() {

        $projects    = M('project');
        $this->limit = 12;
        //行业
        $type = parent::_getProjectTypeCache();
        /*****搜索条件 start ****/
        //薪资条件
        $salary = I('get.salary', null, 'int');
        if ($salary != null && $salary != 0) {
            switch ($salary) {
                case 1 :
                    $this->condition['salary_min'] = array('eq', 0);
                    $this->url['salary']           = 1;
                    break;
                case 2 :
                    $this->condition['salary_min'] = array('BETWEEN', '2000,5000');
                    $this->url['salary']           = 2;
                    break;
                case 3 :
                    $this->condition['salary_min'] = array('BETWEEN', '5000,8000');
                    $this->url['salary']           = 3;
                    break;
                case 4 :
                    $this->condition['salary_min'] = array('BETWEEN', '8000,15000');
                    $this->url['salary']           = 4;
                    break;
                case 5 :
                    $this->condition['salary_min'] = array('BETWEEN', '15000,3000000');
                    $this->url['salary']           = 5;
                    break;
                default:
                    $this->condition['salary_min'] = array('eq', 0);
                    $this->condition['salary_min'] = array('eq', 0);
                    $this->url['salary']           = 0;
            }
        }
        //岗位
        $position_id = I('get.position', null, 'int');
        if ($position_id != null) {
            $this->condition['project_type_id'] = $position_id;
            $this->url['position']          = $position_id;
        }

        //省
        $province_id = I('get.province', null, 'int');
        if ($province_id != null && $province_id != 0) {
            $this->condition['province_id'] = array('EQ', $province_id);
            $this->url['province']          = $province_id;
        }
        //市
        $city = I('get.city', null, 'int');
        if ($city != null && $city != 0) {
            $this->condition['city_id'] = array('EQ', $city);
            $this->url['city']          = $city;
        }
        //地区
        $district_id = I('get.district', null, 'int');
        if ($district_id != null && $district_id != 0) {
            $this->condition['district_id'] = array('EQ', $district_id);
            $this->url['district']          = $district_id;
        }
        $this->condition['status'] = 1;
        /*****搜索条件 end ****/


        $count = $projects->where($this->condition)->count();
        $Page  = $this->pages($count, $this->limit);
        $items = $projects->where($this->condition)
            ->order('sort asc,id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        //dump($projects->getLastSql());
        //岗位只查有的
        if ($count != 0) {
            $position_ids = array_column($items, 'position_id');
            $position_ids = implode(',', $position_ids);
            $city_ids     = array_unique(array_column($items, 'city_id'));
            //$positions    = M('position_type')->where('id in(' . $position_ids . ')')->getField('id,name');
        } else {
            $positions = null;

        }

        //筛选条件
        $search_list['city']      = $city_ids;
        $search_list['positions'] = $type;
        $search_list['salary']    = array('面议', '2K~5K', '5K~8K', '8K~15K', '15K以上');
        /*筛选条件*/


        $postinons = $type;

        //给分页增加条件
        foreach ($this->condition as $key => $val) {
            $Page->parameter[$key] = urlencode($val);
        }

        //生成url
        $newurl = U(ACTION_NAME, $this->url);
        $advert = $this->_getAdvert(3);

        $this->assign('url', $newurl);
        $this->assign('inventory', $this->url);
        $this->assign('page', $this->bootstrap_page_style($Page->show()));// 赋值分页输出
        $this->assign('title', '萝卜项目');// 赋值分页输出
        $this->assign('search_hide', true);
        $this->assign('cities', parent::_Cities());
        $this->assign(array('type' => $type, 'advert' => $advert, 'menu' => 'project', 'search_list' => $search_list, 'items' => $items, 'postinons' => $postinons));
        $this->display();
    }

    /**
     * 萝卜校招
     */
    public function school() {
        $advert = $this->_getAdvert(2);
        //dump($advert);
        $type   = $this->_getPositionType();
        $this->assign(array('advert' => $advert, 'type' => $type, 'menu' => 'school'));
        $this->display();
    }

    /**
     * 企业商铺
     */
    public function company() {

        $advert = $this->_getAdvert(4);
        $type   = $this->_getPositionType();

        $compay = new SearchController();
        $compay->company_criteria();
        //$compay->condition['rd_company_info.is_recommend'] = array('EQ', 1);
        $items                                             = $compay->get_company();
        //dump($this->_getCities());

        $newurl = U(ACTION_NAME, $compay->url);
        $this->assign('scales', C('SCALES'));
        $this->assign('size', C('ENTERPRISE_SCALE'));
        $this->assign('company_label', parent::_Position_label());
        $this->assign('search_mode', 'company');
        $this->assign(array('advert' => $advert, 'type' => $type, 'menu' => 'company'));
        $this->assign('url', $newurl);
        $this->assign('search_list', $compay->search_list);
        $this->assign('inventory', $compay->url);
        $this->assign('title', "企业商铺");
        $this->assign('count', $items['count']);
        $this->assign('show', $items['show']);
        $this->assign('jobs', $items['items']);
        $this->assign('search_hide', true);
        $this->display();
    }




}