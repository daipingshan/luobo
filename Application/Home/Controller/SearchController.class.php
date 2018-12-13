<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2018/8/7
 * Time: 22:48
 *
 *
 *  status

 */

namespace Home\Controller;


class SearchController extends CommonController{

    public $checkUser = false;
    public $user_info;
    public $user_info_id;
    public $limit = 16;             //查询行数
    public $condition = array();    //查询条件
    public $url;                    //URL
    public $search_list;            //条件列表

    protected $recruits;

    public function __initialize()
    {
        C('URL_HTML_SUFFIX','');

        $this->user_info     = M('user_info')->where(array('id' => $this->user_id))->find();
        $this->user_id       = $this->user_info['user_id'];
        $this->user_info_id  = $this->user_info['id'];
        $this->recruits      = M('recruit');
        $this->company_info  = M('company_info');
        $this->assign('job_label',parent::_Position_label());
        $this->assign('company_label',parent::_getCompanyLabelCache());
        $this->assign('city_array',parent::_Cities());
        $this->assign('cities',$this->cities_regions());
        $this->assign('high_edu',C('EDUCATION'));
        $this->assign('salary',C('SCALES'));
        $this->assign('cp_size',C('ENTERPRISE_SCALE'));
        $this->assign('com_experience',C('WORK_EXPERIENCE'));

    }
    //搜索简历
    public function vitae(){
        self::vitae_criteria();
        $items = self::get_vitae();
        //生成url
        $newurl = U(ACTION_NAME, $this->url);
        //dump($this->site_config);
        $advert = $this->_getAdvert(5);
        $this->assign('advert',$advert);
        $this->assign('search_mode','vitae');
        $this->assign('site_config',$this->site_config);
        $this->assign('company_label',$this->company_label);
        $this->assign('url',$newurl);
        $this->assign('search_list',$this->search_list);
        $this->assign('inventory',$this->url);
        $this->assign('title',"搜索简历");
        $this->assign('count',$items['count']);
        $this->assign('show',$items['show']);
        $this->assign('items',$items['items']);
        $this->assign('educations',C('EDUCATION'));
        $this->display();
    }
    //搜索企业
    public function company(){
        self::company_criteria();
        $items = self::get_company();
        //生成url
        $newurl = U(ACTION_NAME, $this->url);
        //dump($this->site_config);

        $this->assign('search_mode','company');
        $this->assign('site_config',$this->site_config);
        $this->assign('company_label',$this->company_label);
        $this->assign('url',$newurl);
        $this->assign('search_list',$this->search_list);
        $this->assign('inventory',$this->url);
        $this->assign('title',"搜索企业");
        $this->assign('count',$items['count']);
        $this->assign('show',$items['show']);
        $this->assign('jobs',$items['items']);
        $this->display();
    }
    //搜索置顶工作
    public function recruit_top(){
        //查询三个
        $limit = 3;
        $recruit_top = M('recruit_top');
        $map['position_id'] = '';
        $map['city_id'] = '';
        $result = $recruit_top->where($map)->limit(0,3)->getField('recruit_top');
        return $result;

    }
    //搜索工作
    public function index(){
        self::search_criteria();
        $items = self::get_recruit();
        $recommend_items = self::com_recommend_job();
        //生成url
        $newurl = U(ACTION_NAME, $this->url);
        //dump($newurl);
        //dump($this->url);
        $advert = $this->_getAdvert(5);

        $this->assign('advert',$advert);
        $this->assign('url',$newurl);
        $this->assign('search_list',$this->search_list);
        $this->assign('recommend_items',$recommend_items);
        $this->assign('inventory',$this->url);
        $this->assign('title',"搜索岗位");
        $this->assign('count',$items['count']);
        $this->assign('show',$items['show']);
        $this->assign('jobs',$items['items']);
        $this->display();
    }
    //获取简历
    protected function get_vitae(){
        $vitae_table = M('user_info');
        self::__initialize();
        //查询正常数据
        $this->condition['status'] = 0;
        //用户允许被搜索
        $this->condition['display'] = 1;
        //查询数量
        $count  = $vitae_table->where($this->condition)->count();
        //dump($vitae_table->getLastSql());
        $Page   = $this->pages($count,$this->limit);
        //给分页增加条件
        foreach($this->condition as $key => $val) {
            $Page->parameter[$key] = urlencode($val);
        }
        //获取数据
        $data['items'] = $vitae_table->where($this->condition)
                                    ->order('updated_at desc,id desc')
                                    ->limit($Page->firstRow,$Page->listRows)
                                    ->select();
        //数据处理
        foreach($data['items'] as $k => $v){
            //dump($v);
        }
        //dump($data['items']);
        //dump($vitae_table->getLastSql());

        $show = parent::bootstrap_page_style($Page->show());

        $data['count'] = $count;
        $data['show']  = $show;

        return $data;
    }
    //获取企业信息
    public function get_company(){
        self::__initialize();
        //查询正常数据
        $this->condition['rd_company_info.is_check'] = 1;
        $this->condition['rd_company_info.status'] = 0;
        //查询数量
        $count  = $this->company_info
                       ->where($this->condition)
                       ->count();

        $Page   = $this->pages($count,$this->limit);
        //给分页增加条件
        foreach($this->condition as $key => $val) {

            $Page->parameter[$key] = urlencode($val);

        }
        //获取数据
        $data['items'] = $this->company_info
                              ->field('
                                          rd_company_info.id,
                                          rd_company_info.status,
                                          rd_company_info.short_name,
                                          rd_company_info.company_name,
                                          rd_company_info.avatar,
                                          rd_company_info.hr_qrcode,
                                          rd_company_info.label,
                                          rd_company_info.company_size,
                                          rd_company_info.is_check,
                                          rd_company_info.scale,
                                          rd_company_info.industry_field
                                      ')
                              ->where($this->condition)

                              ->order('rd_company_info.update_time desc,id desc')
                              ->limit($Page->firstRow,$Page->listRows)
                              ->select();
        //dump($this->company_label);
        //本来是想在模板里只进行一次处理，但是TP出现了莫名奇妙的数组转换问题。
        foreach($data['items'] as  $k => $v){
            $data['items'][$k]['label'] = explode(',',$data['items'][$k]['label']);
        }
        //dump($data['items']);
        //dump($this->company_info->getLastSql());

        $show = parent::bootstrap_page_style($Page->show());

        $data['count'] = $count;
        $data['show']  = $show;

        return $data;
    }
    //获取职位
    protected function get_recruit(){

        self::__initialize();
        //查询正常数据
        $this->condition['rd_recruit.status'] = 0;
        //查询数量
        $count  = $this->recruits
                        ->where($this->condition)
                        ->join('rd_company_info com ON rd_recruit.user_id = com.user_id')
                        ->count();

        $Page   = $this->pages($count,$this->limit);
        //给分页增加条件
        foreach($this->condition as $key => $val) {

            $Page->parameter[$key] = urlencode($val);

        }
        //获取数据
        $data['items'] = $this->recruits
                        ->field(
                                'rd_recruit.*,
                                com.id                as com_id,
                                com.company_name      as corporate_name,
                                com.city_id           as cityid,
                                com.avatar            as com_avatar,
                                com.company_size      as com_size,
                                com.scale             as com_scale,
                                com.industry_field    as com_if,
                              
                                com.short_name')
                        ->join('rd_company_info com  ON rd_recruit.user_id = com.user_id')
                        ->where($this->condition)
                        ->order('rd_recruit.refresh_time desc, rd_recruit.id desc')
                        ->limit($Page->firstRow,$Page->listRows)
                        ->select();
        //dump($this->recruits->getLastSql());

        $show = parent::bootstrap_page_style($Page->show());

        $data['count'] = $count;
        $data['show']  = $show;

        return $data;
    }
    /**
     *
     * 企业搜索规则
     *
     **/
    public function company_criteria(){

        //dump($this->site_config);
        //企业人数
        $this->search_list['staffs']  = array('不限','20以下','20~50人','50~150人','150~500人','500人以上');

        //融资信息
        $this->search_list['scale'] = C('SCALES');

        //dump($this->search_list);

        //公司名称
        $keyword = trim(I('get.keyword',null,'htmlspecialchars'));
        if($keyword != null){
            $this->condition['short_name|company_name'] = array('like','%'.$keyword.'%');
            $this->url['keyword'] = $keyword;
        }
        //省
        $province_id = I('get.province',null,'int');
        if($province_id != null && $province_id != 0){
            $this->condition['province_id'] = array('EQ',$province_id);
            $this->url['province'] = $province_id;

        }
        //市
        $city = I('get.city',null,'int');
        if($city != null && $city != 0){
            $this->condition['city_id'] = array('EQ',$city);
            $this->url['city'] = $city;
        }
        //地区
        $district_id = I('get.district',null,'int');
        if($district_id != null && $district_id != 0){
            $this->condition['district_id'] = array('EQ',$district_id);
            $this->url['district'] = $district_id;
        }

        //企业人数
        $staffs = I('get.staffs',null,'int');
        if($staffs != null && $staffs != 0){
            switch ($staffs)
            {
                case 1 :
                    $this->condition['company_size'] = array(array('EGT',1),array('ELT',20));
                    $this->url['staffs'] = 1;
                    break;
                case 2 :
                    $this->condition['company_size'] = array(array('EGT',20),array('ELT',50));
                    $this->url['staffs'] = 2;
                    break;
                case 3 :
                    $this->condition['company_size'] = array(array('EGT',50),array('ELT',150));
                    $this->url['staffs'] = 3;
                    break;
                case 4 :
                    $this->condition['company_size'] = array(array('EGT',150),array('ELT',500));
                    $this->url['staffs'] = 4;
                    break;
                case 5 :
                    $this->condition['company_size'] = array('EGT',500);
                    $this->url['staffs'] = 5;
                    break;
                default:
                    $this->condition['company_size'] = array('EGT',1);
                    $this->url['staffs'] = 0;
            }
        }
        //福利待遇
        /*
         *  未开放
              $trade = I('get.scale',null,'int');
                if($trade != null && $trade != 0){
                     条件处理
                    $this->url['scale'] = $trade;
                }
        */
        //融资信息/企业性质
        $scale = I('get.scale',null,'int');
        if($scale != null){
            $this->condition['scale'] = array('EQ',$scale);
            $this->url['scale'] = $scale;
        }
    }

    /**
     * 职位搜索规则
     *
     **/
    protected function search_criteria(){

        //dump($this->site_config);
        $this->search_list['experience']  = $this->site_config['WORK_EXPERIENCE'];
        //城市
        $this->search_list['city']  = parent::_Cities();
        $this->search_list['salary']  = array('面议'=>1,'2K~5K'=>2,'5K~8K'=>3,'8K~15K'=>4,'15K以上'=>5);
        $this->search_list['education'] = $this->site_config['EDUCATION'];
        $this->search_list['nature'] = $this->site_config['WORK_NATURE'];
        //dump($this->search_list);

        //jobtitle
        $keyword = trim(I('get.keyword',null,'htmlspecialchars'));
        if($keyword != null){
            $this->condition['rd_recruit.name'] = array('like','%'.$keyword.'%');
            $this->url['keyword'] = $keyword;
        }
        //分类
        $category_id = trim(I('get.category_id',null,'int'));
        if($category_id != null){
            $this->condition['rd_recruit.position'] = array('eq',$category_id);
            $this->url['category_id'] = $category_id;
        }
        //要求学历
        $experience = I('get.experience',null,'int');
        if($experience != null && $experience != 0){
            $this->condition['rd_recruit.experience'] = array('EQ',$experience);
            $this->url['experience'] = $experience;
        }

        //省
        $province_id = I('get.province',null,'int');
        if($province_id != null && $province_id != 0){
            $this->condition['rd_recruit.province_id'] = array('EQ',$province_id);
            $this->url['province'] = $province_id;

        }
        //市
        $city = I('get.city',null,'int');
        if($city != null && $city != 0){
            $this->condition['rd_recruit.city_id'] = array('EQ',$city);
            $this->url['city'] = $city;
        }
        //地区
        $district_id = I('get.district',null,'int');
        if($district_id != null && $district_id != 0){
            $this->condition['rd_recruit.district_id'] = array('EQ',$district_id);
            $this->url['district'] = $district_id;
        }

        //薪资
        $salary = I('get.salary',null,'int');
        if($salary != null && $salary != 0){
            switch ($salary)
            {
                case 1 :
                    $this->condition['rd_recruit.salary_min'] = array('eq',0);
                    $this->url['salary'] = 1;
                    break;
                case 2 :
                    $this->condition['rd_recruit.salary_min'] = array('BETWEEN','2,5');
                    $this->url['salary'] = 2;
                    break;
                case 3 :
                    $this->condition['rd_recruit.salary_min'] = array('BETWEEN','5,8');
                    $this->url['salary'] = 3;
                    break;
                case 4 :
                    $this->condition['rd_recruit.salary_min'] = array('BETWEEN','8,15');
                    $this->url['salary'] = 4;
                    break;
                case 5 :
                    $this->condition['rd_recruit.salary_min'] = array('BETWEEN','15,3000');
                    $this->url['salary'] = 5;
                    break;
                default:
                    $this->condition['rd_recruit.salary_min'] = array('eq',0);
                    $this->condition['rd_recruit.salary_min'] = array('eq',0);
                    $this->url['salary'] = 0;
            }
        }
        //学历
        $education = I('get.education',null,'int');
        if($education != null && $education != 0){
            $this->condition['rd_recruit.limit_educate'] = array('EQ',$education);
            $this->url['education'] = $education;
        }
        //性质
        $nature = I('get.nature',null,'string');

        if($nature != null){

            switch ($nature){
                case "full_time" :
                    $this->condition['rd_recruit.nature'] = array('EQ',"full_time");
                    $this->url['nature'] = $nature;
                    break;
                case "part_time" :
                    $this->condition['rd_recruit.nature'] = array('EQ',"part_time");
                    $this->url['nature'] = $nature;
                    break;
                case "internship" :
                    $this->condition['rd_recruit.nature'] = array('EQ',"internship");
                    $this->url['nature'] = $nature;
                    break;
                default:
                    $this->url['nature'] = null;
            }
        }
    }
    /**
     * 简历搜索规则
     *
     **/
    protected function vitae_criteria(){

        //dump($this->site_config);
        $this->search_list['experience']  = $this->site_config['WORK_EXPERIENCE'];
        $this->search_list['city']  = M('city')->order('sort asc')->limit(12)->select();
        $this->search_list['salary']  = array('不限','1K~5K','5K~8K','8K~15K','15K~30K','30K以上');
        $this->search_list['education'] = $this->site_config['EDUCATION'];
        $this->search_list['nature'] = $this->site_config['WORK_NATURE'];
        //dump($this->search_list);

        //期望工作
        $keyword = trim(I('get.keyword',null,'htmlspecialchars'));
        if($keyword != null){
            $this->condition['job_name'] = array('like','%'.$keyword.'%');
            $this->url['keyword'] = $keyword;
        }
        //学历
        $education = I('get.education',null,'int');
        if($education != null && $education != 0){
            $this->condition['education'] = array('EQ',$education);
            $this->url['education'] = $education;
        }
        //所在城市
        $city = I('get.city',null,'int');
        if($city != null && $city != 0){
            $this->condition['city'] = array('EQ',$city);
            $this->url['city'] = $city;
        }
        //期望薪资
        $salary = I('get.salary',null,'int');
        if($salary != null && $salary != 0){
            switch ($salary)
            {
                case 1 :
                    $this->condition['salary'] = array('BETWEEN','1,5');
                    $this->url['salary'] = 1;
                    break;
                case 2 :
                    $this->condition['salary'] = array('BETWEEN','5,8');
                    $this->url['salary'] = 2;
                    break;
                case 3 :
                    $this->condition['salary'] = array('BETWEEN','8,15');
                    $this->url['salary'] = 3;
                    break;
                case 4 :
                    $this->condition['salary'] = array('BETWEEN','15,30');
                    $this->url['salary'] = 4;
                    break;
                case 5 :
                    $this->condition['salary'] = array('gt',30);
                    $this->url['salary'] = 5;
                    break;
                default:
                    $this->condition['salary'] = array('gt',1);
                    $this->url['salary'] = 0;
            }
        }
        //学历
        $education = I('get.education',null,'int');
        if($education != null && $education != 0){
            $this->condition['education'] = array('EQ',$education);
            $this->url['education'] = $education;
        }


    }
    //企业推荐岗位
    protected function com_recommend_job(){
        self::__initialize();
        $re_top = M('recruit_top');
        self::search_criteria();
        //市
        if($this->condition['rd_recruit.city'] != ''){
            $map['city_id'] = $this->condition['rd_recruit.city'];
        }
        //省
        if($this->condition['rd_recruit.province_id'] != ''){
            $map['province_id'] = $this->condition['rd_recruit.province_id'];
        }
        //区
        if($this->condition['rd_recruit.district_id'] != ''){
            $map['district_id'] = $this->condition['rd_recruit.district_id'];
        }
        //岗位
        if($this->condition['rd_recruit.position_id'] != ''){
            $map['position_id'] = $this->condition['rd_recruit.position_id'];
        }
        //过期时间
        $map['expire_time'] = array('gt',(string) strtotime('now'));
        //设置随机开始值
        $count = $re_top->where($map)->count();
        if(!$count){
            return false;
        }
        $start = 0;
        if($count > 3){
            $start = rand(1,$count - 3);
        }
        //查询在推荐的岗位
        $recruit_top = $re_top->where($map)->limit($start,3)->getField('recruit_id',true);
//        dump($re_top->getLastSql());
//        dump($count);
//        dump($recruit_top);

        $recruits_map['rd_recruit.id'] = array('IN',$recruit_top);

        //
        //获取数据
        $items = $this->recruits
            ->field(
                'rd_recruit.*,
                                com.id                as com_id,
                                com.company_name      as corporate_name,
                                com.city_id           as cityid,
                                com.avatar            as com_avatar,
                                com.company_size      as com_size,
                                com.scale             as com_scale,
                                com.industry_field    as com_if,
                              
                                com.short_name')
            ->join('rd_company_info com  ON rd_recruit.user_id = com.user_id')
            ->where($recruits_map)
            ->order('rd_recruit.refresh_time desc, rd_recruit.id desc')
            ->limit(0,3)
            ->select();
//        dump($items);
        return $items;


    }

}