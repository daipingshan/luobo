<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2018/8/7
 * Time: 23:07
 */

namespace Home\Controller;


use foo\Foo;

class CompanyController extends CommonController
{

    public function __construct() {
        parent::__construct();
        //切换身份至企业
        session('identity', 2);
        //设定企业信息资料
        $this->assign('search_mode','vitae');

    }

    public function __initialize(){
        if(empty($this->company_id) && ACTION_NAME != 'edit'){
            //$this->assign('alerts','请先添加企业信息资料');
            $this->redirect('company/edit','', 0);
        }
        $this->status = $this->_getCompanyInfo($this->company_id);
    }

    /**
     * 企业信息资料不完整
     */
    public function info()
    {
        $company_info = M('company_info')->where(array('id' => $this->company_id))->find();

        $this->assign('city', $company_info['city_id']);
        $this->assign('hr_name', $company_info['hr_name']);
        $this->assign('tel', $company_info['tel']);
        $this->display();
    }

    /**
     * 资质认证
     */
    public function authentication(){

        if($_POST){
            $company_name = I('post.company_name', '', 'trim');
            $short_name = I('post.short_name', '', 'trim');
            $business_lincense = I('post.business_lincense', '', 'trim');

            if ($company_name == '' || $business_lincense == '') {
                $res = array('code' => -1, 'msg' => '请上传认证图片或填写企业名称', 'data' => '');
                $this->ajaxReturn($res);
            }

            $data = array(
                'id' => session('company_id'),
                'company_name' => $company_name,
                'short_name' => $short_name,
                'business_lincense' => $business_lincense,
            );

            if($this->company['is_check'] != 0){
                $data['is_check'] = 4;
            }
            $save_lincense = M('company_info')->save($data);

            if($save_lincense !== false){
                $res = array('code' => 0, 'msg' => '资质信息更新成功', 'data' => U('company/index'));
            }else{
                $res = array('code' => -1, 'msg' => '资质信息更新失败，请稍后重试', 'data' => U('company/index'));
            }
            $this->ajaxReturn($res);
        }

        $this->assign('is_check', $this->company['is_check']);
        $this->assign('info', $this->company);
        $this->display();
    }

    /**
     * 删除公司图片
     */
    public function delPic(){
        self::__initialize();
        $id = I('post.id');
        $pic = M("pic");
        $condition['id'] = $id;
        $condition['user_id'] = $this->user_id;
        $item = $pic->where($condition)->find();
        if ($item == NULL) {
            $result = ['status' => 'fail', 'msg' => '没有匹配的数据'];
            $this->ajaxReturn($result);
        } else {
            $pic->where('id =' . $id)->delete();
            $result = ['status' => 'success', 'msg' => ''];
            $this->ajaxReturn($result);
        }
    }

    /**
     * 上传公司图片
     */
    public function uploadPic(){
        $width = I('post.width', C('WIDTH'), 'trim');
        $height = I('post.height', C('HEIGHT'), 'trim');
        $type = I('post.type', '', 'trim');
        $upload_img = $this->uploadImgCompany($width, $height);

        $res = array('code' => -1, 'msg' => '上传失败', 'data' => '');
        if ($upload_img['code'] != -1) {// 上传错误提示错误信息
            $pic = $upload_img['data'][0];
            $where = array('user_id' => $this->user_id);
            if ($type == 'logo') {
                $logo_res = M('company_info')->where($where)->save(array('avatar' => $pic));
                if (empty($logo_res)){
                    $res['msg'] = "企业的logo更新失败";
                }
                $res = array('code' => 0, 'msg' => '企业的logo更新成功', 'data' => $pic, 'url' => '');
            } elseif ($type == 'coenv') {
                $data = array(
                    'user_id' => $this->user_id,
                    'type' => 'environment',
                    'create_time' => time(),
                    'status' => 0,
                    'pic' => $pic,
                );
                $coenv_res = M('pic')->add($data);
                if (empty($coenv_res)){
                    $res['msg'] = "公司环境图片更新失败";
                }
                $res = array('code' => 0, 'msg' => '公司环境图片更新成功', 'url' => '','data' => $pic);
            } elseif ($type == 'coauth') {
                $auth_res = M('company_info')->where($where)->save(array('business_lincense'=>$pic));
                if (empty($auth_res)){
                    $res['msg'] = "企业的营业执照更新失败";
                }
                $res = array('code' => 0, 'msg' => '企业的营业执照更新成功', 'data' => $pic, 'url' => '');
            } elseif ($type == 'hr') {
                $hr_res = M('company_info')->where($where)->save(array('hr_qrcode'=>$pic));
                if (empty($hr_res)){
                    $res['msg'] = "HR二维码更新失败";
                }
                $res = array('code' => 0, 'msg' => 'HR二维码更新成功', 'data' => $pic, 'url' => '');
            }
        }

        $this->ajaxReturn($res);
    }

    /**
     * 企业资料
     */
    public function index()
    {
        self::__initialize();
        $user_id = $this->user_id;
        $honor_pic = M('pic')->field('id,pic')
            ->where(array('user_id' => $user_id, 'status' => '0', 'type' => 'honor'))
            ->select();
        $environment_pic = M('pic')->field('id,pic')
            ->where(array('user_id' => $user_id, 'status' => '0', 'type' => 'environment'))
            ->select();
        $label = M('company_label')->index('id')->select();

        $img = array(
            'honor' => $honor_pic,
            'environment' => $environment_pic,
            'hr_pic' => $this->company['hr_qrcode'],
        );

        $this->assign('used_label', C('COMPANY_LABEL'));
        $this->assign('img', $img);
        $this->assign('city', $this->city);
        $this->assign('label', $label);
        $this->assign('title', '企业资料首页');
        $this->display();
    }

    /**
     * 编辑企业资料
     */
    public function edit()
    {
        $user_id = $this->user_id;
        if (IS_AJAX) {
            $company_name = I('post.company_name', '', 'trim');
            $short_name = I('post.short_name', '', 'trim');
            $link = I('post.link', '', 'trim');
            $desc = I('post.desc', '', 'trim');
            $industry_field = I('post.industry_field', '', 'trim');
            $scale = I('post.scale', '', 'trim');
            $company_size = I('post.company_size', '', 'trim');
            $province_id = I('post.province_id', '', 'trim');
            $city_id = I('post.city_id', '', 'trim');
            $district_id = I('post.district_id', '', 'trim');
            $company_address = I('post.company_address', '', 'trim');
            $hr_name = I('post.hr_name', '', 'trim');
            $tel = I('post.tel', '', 'trim');
            $type = I('post.type', '', 'trim');

            $res = array('code' => -1, 'msg'=>'',  'data'=>'');

            if (mb_strlen($industry_field) > 12) {
                $res['msg'] = '行业类别不得超过12个字';
                $this->ajaxReturn($res);
            }

            if ($type == 'new') {
                if ($company_name == '') {
                    $res['msg'] = '企业名称不得为空';
                    $this->ajaxReturn($res);
                } else if($short_name == '') {
                    $res['msg'] = '企业简称不得为空';
                    $this->ajaxReturn($res);
                }
            }

            if($desc == '') {
                $res['msg'] = '企业简介不得为空';
                $this->ajaxReturn($res);
            } else if($industry_field == '') {
                $res['msg'] = '行业类别不得为空';
                $this->ajaxReturn($res);
            } else if($scale == '') {
                $res['msg'] = '企业性质不得为空';
                $this->ajaxReturn($res);
            } else if($company_size == '') {
                $res['msg'] = '企业规模不得为空';
                $this->ajaxReturn($res);
            } else if($province_id == '') {
                $res['msg'] = '省份不得为空';
                $this->ajaxReturn($res);
            } else if($city_id == '') {
                $res['msg'] = '城市不得为空';
                $this->ajaxReturn($res);
            } else if($district_id == '') {
                $res['msg'] = '地区不得为空';
                $this->ajaxReturn($res);
            } else if($company_address == '') {
                $res['msg'] = '企业地址不得为空';
                $this->ajaxReturn($res);
            } else if($hr_name == '') {
                $res['msg'] = 'hr名称不得为空';
                $this->ajaxReturn($res);
            } else if($tel == '') {
                $res['msg'] = 'hr电话不得为空';
                $this->ajaxReturn($res);
            }
            $data = array(
                'user_id' => $user_id,
                'link' => $link,
                'desc' => $desc,
                'industry_field' => $industry_field,
                'scale' => $scale,
                'company_size' => $company_size,
                'province_id' => $province_id,
                'city_id' => $city_id,
                'district_id' => $district_id,
                'company_address' => $company_address,
                'hr_name' => $hr_name,
                'tel' => $tel,
            );

            $info = M('company_info')->where(array('user_id' => $user_id))->getField('id');

            if (empty($info)){
                //编辑资料无法编辑企业名称，企业名称要去资质认证中修改。
                $data['company_name'] = $company_name;
                $data['short_name'] = $short_name;
                $data['credit'] = 0;
                $data['create_time'] = time();
                $data['is_check'] = 0;
                $info_res = M('company_info')->add($data);
                session('company_id',$info_res);
            } else {
                $info_res = M('company_info')->where(array('user_id' => $user_id))->save($data);
            }
            if (empty($info_res)){
                $res['msg'] = '企业信息提交失败，请稍后重试';
                $this->ajaxReturn($res);
            } else {
                $res = array('code' => 0, 'msg'=> '修改成功', 'data' => U('Company/index'));
                $this->ajaxReturn($res);
            }

        } else {

            $province = M('city')->where(array('parent_id' => 0))->select();
            $city_name = M('city')->where(array('id' => $this->company['city_id']))->getField('name');
            $district_name = M('city')->where(array('id' => $this->company['district_id']))->getField('name');

            $this->assign('province', $province);
            $this->assign('city_name', $city_name);
            $this->assign('district_name', $district_name);
            $this->assign('scales', C('SCALES'));
            $this->assign('entrprise', C('ENTERPRISE_SCALE'));
            $this->assign('company_id', $this->company_id);
            $this->assign('title', '企业信息');
            $this->display();
        }
    }

    /**
     * 职位管理
     */
    public function manage()
    {
        self::__initialize();
        $p = I('get.p', 0, 'intval');

        $user_id = $this->user_id;

        $company = $this->company;
        $label = M('company_label')->index('id')->select();
        $where = array('user_id' => $user_id, 'status' => array('neq', '4'));
        $count = M('recruit')->where($where)->count('id');
        $page = $this->pages($count, C('LIMIT'));
        $recruit = M('recruit')->where($where)->limit($page->firstRow . ',' . $page->listRows)->order('create_time desc')->select();

        foreach ($recruit as &$val) {
            $map = array('recruit_id' => $val['id'], 'status' => '1', 'company_id' => $this->company_id);
            $val['deliver'] = M('company_invitation')->where($map)->count('id');
        }

        $hr_pic = C('PIC_URL').$this->company  ['hr_qrcode'];
        $amount = M('user')->where(array('id' => $user_id))->getField('amount');


        //  发布职位 -2 请升级会员,达上限  -1，当天发布数量已达上限，请明天在发布 0 正常发布岗位
        //  company_meal = 0  5条每天   =1   20条每天   =2  30条每天  =3  50条每天
        $limit_zero_count = C('COMPANY')[0]['send_num'];
        $limit_one_count = C('COMPANY')[1888]['send_num'];
        $limit_two_count = C('COMPANY')[5888]['send_num'];
        $limit_three_count = C('COMPANY')[8888]['send_num'];

        $is_publish = 0;
        $today_zero = strtotime(date('Y-m-d', time()));
        $where = array(
            'user_id' => $user_id,
            'create_time' => array('between', array($today_zero, time())),
        );
        $count_recruit = M('recruit')->where($where)->count('id');
        $level = $company['company_meal'];
        $free_recruit = 0;
        switch ($level) {
            case 0:
                $free_recruit = $limit_zero_count - $count_recruit;
                if ($free_recruit < 0){
                    $is_publish = -2;
                }
                break;
            case 1:
                $free_recruit = $limit_one_count - $count_recruit;
                if ($free_recruit < 0){
                    $is_publish = -2;
                }
                break;
            case 2:
                $free_recruit = $limit_two_count - $count_recruit;
                if ($free_recruit < 0){
                    $is_publish = -2;
                }
                break;
            case 3:
                $free_recruit = $limit_three_count - $count_recruit;
                if ($free_recruit < 0){
                    $is_publish = -1;
                }
                break;
            default:
                $is_publish = 0;
        }

        //  企业判定
        $is_defect = 0;
        if ($this->status == false) {
            $is_defect = -1;
        } else if ($company['is_check'] == 0) {
            $is_defect = -2;
        } else if ($company['is_check'] == 2) {
            $is_defect = -3;
        }

        // 刷新次数
        $is_refresh = 0;
        $today_zero = strtotime(date('Y-m-d', time()));
        $where = array(
            'company_id' => $company['id'],
            'refresh_time' => array('between', array($today_zero, time())),
        );
        $count_refresh = M('update_recruit')->where($where)->count('id');
        $level = $company['company_meal'];

        $refresh_zero_count = C('COMPANY')[0]['refresh_num'];
        $refresh_one_count = C('COMPANY')[1888]['refresh_num'];
        $refresh_two_count = C('COMPANY')[5888]['refresh_num'];
        $refresh_three_count = C('COMPANY')[8888]['refresh_num'];

        // 免费刷新次数
        $free_refresh = 0;
        switch ($level) {
            case 0:
                $free_refresh = $refresh_zero_count - $count_refresh;
                if ($free_refresh < 0){
                    $is_refresh = -2;
                }
                break;
            case 1:
                $free_refresh = $refresh_one_count - $count_refresh;
                if ($free_refresh < 0){
                    $is_refresh = -2;
                }
                break;
            case 2:
                $free_refresh = $refresh_two_count - $count_refresh;
                if ($free_refresh < 0){
                    $is_refresh = -2;
                }
                break;
            case 3:
                $free_refresh = $refresh_three_count - $count_refresh;
                if ($free_refresh < 0){
                    $is_refresh = -1;
                }
                break;
            default:
                $is_refresh = 0;
        }

        $this->assign('now', time());
        $this->assign('education', C('EDUCATION'));
        $this->assign('toindex', C('COMPANY.base'));
        $this->assign('is_publish', $is_publish);
        $this->assign('free_recruit', $free_recruit);
        $this->assign('is_refresh', $is_refresh);
        $this->assign('free_refresh', $free_refresh);
        $this->assign('is_defect', $is_defect);
        $this->assign('amount', $amount);
        $this->assign('label', $label);
        $this->assign('hr_pic', $hr_pic);
        $this->assign('recruit', $recruit);
        $this->assign('page', $page);
        $this->assign('title', '职位管理');
        $this->display();
    }

    /**
     * 招呼模版
     */
    public function templateInfo()
    {
        self::__initialize();
        $label = M('company_label')->index('id')->select();

        $where = array('company_id' => $this->company_id);
        $template = M('template')->where($where)->order('id desc')->find();

        $hr_pic = C('PIC_URL').$this->company['hr_qrcode'];
        //dump($template);

        $this->assign('hr_pic', $hr_pic);
        $this->assign('label', $label);
        $this->assign('template', $template);
        $this->assign('title', '招呼模版');
        $this->display();

    }

    /**
     * 添加招呼模版
     */
    public function addTemplate()
    {
        self::__initialize();
        $company_id = $this->company_id;
        $desc = I('post.desc', '', 'trim');
        $item = M('template')->where('company_id = '.$company_id)->find();

        $data = array(
            'company_id' => $company_id,
            'desc' => $desc,
            'status' => 0
        );

        if($item){
            $where = array('id' => $item['id']);
            $info = M('template')->where($where)->save($data);
        }else{
            $info = M('template')->add($data);
        }

        if (empty($info)) {
            $this->error('模版添加失败');
        }

        $this->redirect('Company/templateInfo','', 0);

    }

//    /**
//     * 编辑招呼模版
//     */
//    public function editTemplate()
//    {
//        $id = I('get.id', '', 'int');
//        $desc = I('get.desc', '', 'trim');
//
//        $where = array('id' => $id);
//        $data = array('desc' => $desc);
//        $info = M('template')->where($where)->save($data);
//        $this->redirect(U('Company/templateInfo'),'', 0);
//
//    }

    /**
     *  消费记录
     */
    public function consume()
    {
        $id = I('get.id', 0, 'intval');

        $where = array('company_id' => $id);
        $consume_res = M('company_flow')->where($where)->select();
        $this->output('ok', 0, $consume_res);
    }

    /**
     * 更新公司基本信息
     */
    public function updateInfo()
    {
        $type = I('post.type', '', 'trim');
        $content = I('post.content', '', 'trim');
        $label = I('post.label', '', 'trim');
        $long_lat = I('post.long_lat', '', 'trim');

        $where['user_id'] = $this->user_id;
        $save_res = '';
        if ($type == 1){
            $data['desc'] = $content;
            $save_res = M('company_info')->where($where)->save($data);
        } elseif ($type == 2) {
            $data['company_address'] = $content;
            $data['long_lat'] = $long_lat;
            $save_res = M('company_info')->where($where)->save($data);
        } elseif ($type == 3) {
            $data['label'] = $label;
            $save_res = M('company_info')->where($where)->save($data);
        }

        if ($save_res !== false){
            $return = array('code' => 0, 'msg' => '信息更新成功', 'data' => U('Company/index'));
        } else {
            $return = array('code' => -1, 'msg' => '信息更新失败', 'data' => U('Company/index'));
        }
        $this->ajaxReturn($return);
    }

    /**
     * 更新图片
     */
    public function updateCompanyImg()
    {
        $type = I('post.type', '', 'trim');
        $pic = I('post.pic', '', 'trim');

        $user_id = $this->user_id;
        if ($type == 'hrqrcode') {
            $hr_res = M('company_info')->where(array('user_id'=>$user_id))->save(array('hr_qrcode'=>$pic));
            if (empty($hr_res)){
                $this->error('HR二维码更新失败');
            } else {
                redirect(U('index'));
            }
            exit;
        } elseif ($type == 'logo') {
            $logo_res = M('company_info')->where(array('user_id'=>$user_id))->save(array('avatar' => $pic));
            if (empty($logo_res)){
                $this->error('企业的logo更新失败');
            } else {
                redirect(U('index'));
            }
            exit;
        }

        $pic_res = M('pic')->where(array('user_id'=>$user_id, 'type'=>$type))->find();
        $data =array(
            'user_id' =>$user_id,
            'pic' =>$pic,
            'type' => $type,
            'create_time' =>time(),
            'status' => 0
        );
        if (empty($pic_res)) {
            $res = M('pic')->add($data);
        } else {
            $res = M('pic')->where(array('user_id'=>$user_id, 'type'=>$type))->save($data);
        }

        if (empty($res)){
            $this->error('信息更新失败');
        } else {
            redirect(U('index'));
        }
    }

    /**
     * 刷新和置顶
     */
    public function doRefresh()
    {
        $id = I('post.id', '0', 'trim');
        $type = I('post.type', '', 'trim');
        $toindex = I('post.toindex_time', 0, 'trim');

        $user = M('user');
        $amount = $user->where(array('id' => $this->user_id))->getField('amount');
        session('user')['amount'] = $amount;
        $company_config = C('COMPANY')['base'];

        $msg = 'success';
        //刷新
        $flow = array();
        if ($type == 'refresh' && $id != 0) {

            $is_refresh = 0;
            $today_zero = strtotime(date('Y-m-d', time()));
            $where = array(
                'company_id' => $this->company['id'],
                'refresh_time' => array('between', array($today_zero, time())),
            );
            $count_refresh = M('update_recruit')->where($where)->count('id');
            $level = $this->company['company_meal'];

            switch ($level) {
                case 0:
                    if ($count_refresh >=  C('LIMIT_ZERO_COUNT')){
                        $is_refresh = -2;
                    }
                    break;
                case 1:
                    if ($count_refresh >= C('LIMIT_ONE_COUNT')){
                        $is_refresh = -2;
                    }
                    break;
                case 2:
                    if ($count_refresh >= C('LIMIT_TWO_COUNT')){
                        $is_refresh = -2;
                    }
                    break;
                case 3:
                    if ($count_refresh >= C('LIMIT_THREE_COUNT')){
                        $is_refresh = -1;
                    }
                    break;
                default:
                    $is_refresh = 0;
            }

            if ($is_refresh == 0) {
                $data['refresh_time'] =  time();
                M('recruit')->where(array('id' => $id,'user_id'=>$this->user_id))->save($data);
                $update_data = array(
                    'type' => 'refresh',
                    'recruit_id' => $id,
                    'company_id' => $this->company_id,
                    'refresh_time' => time()
                );
                M('update_recruit')->add($update_data);
                $res = array('code' => '0','msg'=>'success');
            } else {
                if ($amount < $company_config['refresh']) {
                    $res = array('code' => -1, 'msg'=>'金额不足',  'data'=>'');
                    $this->ajaxReturn($res);
                }
                $title = '刷新岗位!';
                $represent = '用户操作职位刷新操作';
                $data['refresh_time'] =  time();
                //扣费
                $amount_res = $user->where('id = ' . $this->user_id)->setDec('amount', $company_config['refresh']);

                if($amount_res){
                    //更新操作
                    $res = M('recruit')->where(array('id' => $id,'user_id'=>$this->user_id))->save($data);
                    if($res){
                        $update_data = array(
                            'type' => 'refresh',
                            'recruit_id' => $id,
                            'company_id' => $this->company_id,
                            'refresh_time' => time()
                        );
                        M('update_recruit')->add($update_data);
                        $pay_amount = - $company_config['refresh'];
                        $this->_addUserFlow($this->user_id, $represent,$title, $amount, $pay_amount, $this->user_id);
                        $res = array('code' => '0','msg'=>'success');
                    }else{
                        \Think\Log::record('更新操作失败 id'.$this->user_id.'费用'.$amount);
                        $res = array('code' => '-1','msg'=>'sql_refresh_fail');
                    }
                }else{
                    \Think\Log::record('操作扣费失败 id'.$this->user_id.'费用'.$amount);
                    $res = array('code' => '-1','msg'=>'sql_pay_fail');
                }
            }


        } elseif ($type == 'dotop'){
            if ($amount < $company_config['top']) {
                $res = array('code' => -1, 'msg'=>'置顶失败,萝卜不足，请充值!',  'data'=>'');
                $this->ajaxReturn($res);
            }

            $top = M('recruit_top')->where(array('recruit_id' => $id))->find();
            if ($top && ($top['expire_time'] > time())) {
                $top_res = array('code' => -1, 'msg'=>'当前职位在置顶中，请置顶其他职位!',  'data'=>'');
                $this->ajaxReturn($top_res);
            }

            $recruit = M('recruit')->where(array('id' => $id))->find();
            $amount_res = $user->where(array('id' => $this->user_id))->setDec('amount', $company_config['top']);
            if (empty($amount_res)){
                $res = array('code' => -1, 'msg'=>'置顶失败,扣费失败，请重试!',  'data'=>'');
                $this->ajaxReturn($res);
            } else {
                $time = C('DO_TOP_TIME') + time();
                $data = array(
                    'province_id' => $recruit['province_id'],
                    'city_id' => $recruit['city_id'],
                    'district_id' => $recruit['district_id'],
                    'recruit_id' => $id,
                    'position_id' => $recruit['position_id'],
                    'expire_time' => $time,
                );
                if (empty($top)) {
                    M('recruit_top')->add($data);
                } else {
                    M('recruit_top')->where(array('recruit_id' => $id))->save($data);
                }
                $title = '置顶岗位!';
                $represent = '用户操作职位置顶操作';
                $this->_addUserFlow($this->user_id, $represent, $title, $amount, $company_config['top'], $this->user_id);
                $res = array('code' => 0, 'msg'=>'置顶成功!',  'data'=>'');
            }
        } else if ($type == 'toindex') {
            $toindex_money = C('COMPANY.base');
            if ($amount < $toindex_money['index']) {
                $res = array('code' => -1, 'msg'=>'置顶失败,萝卜不足，请充值!',  'data'=>'');
                $this->ajaxReturn($res);
            }

            $count = M('recruit')->where(array('id' => $id, 'recommend_expire_time' => array('gt', time())))->getField('id');
            if ($count != 0){
                $res = array('code' => -1, 'msg'=>'推荐失败，该职位已在首页中!',  'data'=>'');
                $this->ajaxReturn($res);
            }
            $data = array('is_recommend' => 1, 'toindex_time' => $toindex, 'is_check' => 0);
            $toindex_res = M('recruit')->where(array('id' => $id))->save($data);
            if (empty($toindex_res)){
                $res = array('code' => -2, 'msg' => '推荐失败，请稍后重试!',  'data' => '');
            } else {
                $res = array('code' => 0, 'msg' => '推荐成功!',  'data' => '');
            }
        } else {
            $res = array('code' => '-1','msg'=>'fail'.$id);
        }

        $this->ajaxReturn($res);

    }

    /**
     * 刷新和置顶
     */
    public function setStatus()
    {
        $id = I('post.id', '0', 'trim');
        $status = I('post.status', '', 'trim');

        $status_res = M('recruit')->where(array('id' => $id))->save(array('status' => $status));

        if ($status_res) {
            $res = array('code' => '0','msg'=>'set status'.$id.'ok', 'data' => '');
        } else {
            $res = array('code' => '-1','msg'=>'fail'.$id);
        }

        $this->ajaxReturn($res);

    }

    /**
     * 获取城市和地区
     */
    public function getRegionList()
    {
        $area_id = I('post.area', '', 'intval');
        $area = M('city')->where(array('parent_id' => $area_id))->select();

        $res = array('code'=>0, 'msg'=>'success', 'data'=>$area);

        $this->ajaxReturn($res);
    }
    
}
