<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2018/8/7
 * Time: 22:48
 */

namespace Home\Controller;

use Home\Controller\UserController;

class JobsController extends CommonController
{
    protected $checkUser = false;

    public function jobslist()
    {
        $id = I('get.id', null, 'int');
        $company = M('company_info')->where(array('user_id'=>$id))->find();

        if(!$company || (empty($id) && !isset($id))){
            $this->error('没找到这个企业，尝试换个岗位看看',U('search/index'),5);
        }

        $company_user_id = $company['user_id'];
        //dump($company['label']);
        $company['label'] = explode(',',$company['label']);

        $label = M('company_label')->index('id')->select();
        //热门岗位
        //查询数量
        $count  = M('recruit')->where(array('user_id' => $company_user_id)) ->count();
        $Page  = new \Think\Page($count,$this->limit);
        $recruits = M('recruit')->field('id, name, salary_min, salary_max, create_time, city_id, province_id, district_id, experience')
            ->where(array('user_id' => $company_user_id, 'status' => array('neq', 4)))
            ->order('refresh_time desc,id desc')
            ->limit($Page->firstRow,$Page->listRows)
            ->select();


        $city = $this->_Cities();
        $this->assign('company_size', C('ENTERPRISE_SCALE'));
        $this->assign('scales', C('scales'));
        $this->assign('city', $city);


        $this->assign('label', $label);
        $this->assign('cities',$this->_Cities());
        $this->assign('info', $company);
        $this->assign('hr_pic', $company['hr_qrcode']);
        $this->assign('recruits', $recruits);
        $this->assign('title', $company['company_name'].'在招的职位列表');
        $this->assign('count',$count);
        $this->assign('Page',$Page);
        $this->assign('search_hide', true);
        $this->display();
    }
    /**
     * 公司
     */
    public function company()
    {
        $id = I('get.id', null, 'int');
        $company = M('company_info')->where(array('id'=>$id))->find();
        if(!$company || (empty($id) && !isset($id))){
            $this->error('没找到这个企业，尝试换个岗位看看',U('search/index'),5);
        }

        $company_user_id = $company['user_id'];
        $count  = M('recruit')->where(array('user_id' => $company_user_id)) ->count();

        //dump($company['label']);
        $company['label'] = explode(',',$company['label']);

        $label = $this->_getCompanyLabelCache();

        //热门岗位
        $recruits = M('recruit')->field('id,name,salary_min,salary_max,create_time')
            ->where(array('user_id' => $company_user_id, 'status' => array('neq', 4)))
            ->order('create_time desc')
            ->limit(0,3)
            ->select();
        //公司环境
        $pics = M('pic')->where(array( 'user_id' => $company_user_id, 'tpye' => 'environment'))->limit(4)->select();



        $region = $this->city[$company['province_id']]['name'] . '--' . $this->city[$company['city_id']]['name'] . '--' . $this->city[$company['district_id']]['name'];


        $city = $this->_Cities();
        $this->assign('company_size', C('ENTERPRISE_SCALE'));
        $this->assign('scales', C('scales'));
        $this->assign('city', $city);
        $this->assign('region', $region);
        $this->assign('pics', $pics);
        $this->assign('label', $label);
        $this->assign('info', $company);        $this->assign('hr_pic', $company['hr_qrcode']);
        $this->assign('recruits', $recruits);
        $this->assign('title', $company['company_name']);
        $this->assign('count',$count);
        $this->assign('search_hide', true);
        if($company['company_meal'] == 3){
            $this->display('company_gold');
        }else{
            $this->display();
        }
    }

    /**
     * 职位详情
     */
    public function detail()
    {
        $id = I('get.id', null, 'int');
        if(empty($id) && !isset($id)){

            $this->error('这份工作已经下架了，尝试换个岗位看看',U('search/index'),5);
        }


        $label = M('company_label')->index('id')->select();
        //简历信息
        $recruit = M('recruit')->where(array('id' => $id))->find();
        //dump($recruit);
        //企业信息
        $company = M('company_info')->where(array('user_id'=>$recruit['user_id']))->find();
        $company['label'] = explode(',',$company['label']);
        $count  = M('recruit')->where(array('user_id' => $company['user_id'])) ->count();
        //dump($company);
        //企业照片
        $pics = M('pic')->where(array('user_id'=>$recruit['user_id']))->limit(0,5)->select();
        //检测是否投递
        if(parent::delivered(session('user_info_id'),$id)) {
            $this->assign('delivered', true);
        }
        $city = $this->_Cities();
        $this->assign('company_size', C('ENTERPRISE_SCALE'));
        $this->assign('scales', C('scales'));
        $this->assign('city', $city);


        $this->assign('pics',$pics);
        $this->assign('label', $label);
        $this->assign('info', $company);
        $this->assign('hr_pic', $company['hr_qrcode']);
        $this->assign('education', C('EDUCATION'));
        $this->assign('recruit', $recruit);
        $this->assign('count',$count);
        $this->assign('title', $recruit['name']);
        $this->assign('search_hide', true);
        $this->display();
    }


}