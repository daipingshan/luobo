<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2018/8/7
 * Time: 22:48
 *
 *
 *  status
 *
 * 1：投递成功
 * 2：被查看
 * 3：邀请面试
 * 4：邀请投递
 * 5：用户同意
 * 6：用户拒绝
 * 7：感兴趣
 * 8: 邀请入职
 */

namespace Home\Controller;


use Think\Think;

class UserController extends CommonController
{
    public $user_info;
    public $user_id;
    public $user_info_id;
    public $invitations = 0;
    protected $checkUser = false;

    public function __initialize(){
        //切换身份至个人
        session('identity', 1);
        parent::_checkUser();



        $this->user_info = M('user_info')->where(array('user_id' => $this->user_id))->find();


        //检查个人资料
        if(empty(I('post.act')) && empty($this->user_info)){
            //$this->assign('alerts','请先添加个人资料');
            $this->redirect('user/guide','', 0);
        }


        $this->user_info_id = $this->user_info['id'];

        //城市信息
        $this->assign('cities', $this->_Cities());

        //邀请信息
        $invitations_c['user_info_id'] = $this->user_info_id;
        $invit_status = M('company_invitation')->where($invitations_c)->getField("id,status");
        $this->invitations = array_count_values($invit_status);
        $this->assign('invitations',$this->invitations);



    }
    //第一次引导页面
    public function guide(){

        parent::_checkUser();

        $model = I('get.model',0,'int');
        $userinfo = M('user_info')->where(array('user_id' => $this->user_id))->find();
        $edu = C('EDUCATION');
        //资料为空
        if($model >= 2 && empty($userinfo)){
             $this->redirect('user/guide',array('model' => 0), 0);
        }
        if($model == 1 && !empty($userinfo)){
            $this->redirect('user/guide',array('model' => 2), 0);
        }

        $cities = $this->_getCities();
        $cities_json = json_encode($cities);
        $this->assign('cities_json', $cities_json);
        $this->assign('cities', $this->cities);
        $this->assign('model', $model);
        $this->assign('edu', $edu);
        $this->display();
    }
    //会员中心
    public function member(){
        self::__initialize();
        $this->assign('user_info', $this->user_info);
        $this->assign('title', '会员中心');
        $this->display();
    }
    //手机个人中心
    public function mobile(){
        self::__initialize();
        $this->assign('user_info', $this->user_info);
        $this->assign('title', '个人中心');
        $this->display();
    }

    public function password(){
        self::__initialize();
        $this->assign('user_info', $this->user_info);
        $this->assign('title', '修改密码');
        $this->display();
    }

    //我的简历
    public function vitae()
    {
        self::__initialize();
        if(empty($this->user_info)){
            $this->redirect('user/info_edit', array('alert'=>1), 0);
        }
        //教育经历
        $educations = M('user_education')->where(array('user_id' => $this->user_info_id))->select();
        //dump($educations);
        //工作经历
        $user_cvitaes = M('user_curriculum_vitae')->where(array('user_id' => $this->user_info_id))->select();
        //作品
        $works = M('user_works')->where(array('user_id' => $this->user_info_id))->select();
        //资料完成度
        $integrity_degree  = self::IntegrityDegree($educations,$user_cvitaes,$works);

        $this->assign('site', $works);
        $this->assign('integrity_degree', $integrity_degree);
        $this->assign('title', "我的简历");
        $this->assign('works', $works);
        $this->assign('user_cvitaes', $user_cvitaes);
        $this->assign('educations', $educations);
        $this->assign('user_info', $this->user_info);
        $this->display();
    }

    //个人资料展示
    public function info_display()
    {
        self::__initialize();
        $this->assign('education' ,  C('EDUCATION'));
        $this->assign('work_time', C('WORK_EXPERIENCE'));
        $this->assign('user_info', $this->user_info);
        $this->display('info_display');
    }
    //个人资料补充

    //个人资料更改
    public function info_edit()
    {
        self::__initialize();
        if (I('post.act') == 'edit') {
            //基础信息
            $user_info =  $this->user_info;
            $user_info['real_name'] = empty(I('post.real_name')) ? $this->user_info['real_name'] : I('post.real_name');
            $user_info['sex'] = empty(I('post.sex')) ? $this->user_info['sex'] : I('post.sex');
            $user_info['birthday'] = empty(I('post.birthday')) ? $this->user_info['birthday'] : I('post.birthday');
            $user_info['work_time'] = empty(I('post.work_time')) ? $this->user_info['work_time'] : I('post.work_time');
            $user_info['job_name'] = empty(I('post.job_name')) ? $this->user_info['job_name'] : I('post.job_name');
            $user_info['province_id'] = empty(I('post.province_id')) ? $this->user_info['province_id'] : I('post.province_id');
            $user_info['city_id'] = empty(I('post.city_id')) ? $this->user_info['city_id'] : I('post.city_id');
            $user_info['district_id'] = empty(I('post.district_id')) ? $this->user_info['district_id'] : I('post.district_id');
            //求职意向
            $user_info['work_type'] = empty(I('post.work_type')) ? $this->user_info['work_type'] : I('post.work_type');
            $user_info['job_type'] = empty(I('post.job_type')) ? $this->user_info['job_type'] : I('post.job_type');
            $user_info['education'] = empty(I('post.education')) ? $this->user_info['education'] : I('post.education');
            $user_info['salary'] =empty(I('post.salary')) ? $this->user_info['salary'] : I('post.salary');
            $user_info['post_time'] = empty(I('post.post_time')) ? $this->user_info['post_time'] : I('post.post_time');

            if (self::set_info($user_info)) {
                if(IS_AJAX){
                    $result = ['status' => 'success', 'msg' => ''];
                    $this->ajaxReturn($result);
                }

                if(!$this->user_info){
                    $alerts[] = "账号已经建立好啦！";
                    $this->redirect('user/vitae',$alerts, 0);
                }else{
                    $alerts[] = "资料已修改";
                    $this->redirect('user/info_display',$alerts, 0);
                }

            }else{
                $result = ['status' => 'fail', 'msg' => '添加失败'];
                $this->ajaxReturn($result);
            };

        }

        //dump($cityes);
        $this->assign('alerts', $alerts);
        $this->assign('user_info', $this->user_info);
        $this->display();
    }

    protected function set_info($user_info)
    {
        self::__initialize();
        $userinfo = M('user_info');
        if($this->user_info_id == ''){
            $user_info['user_id'] = session('user_id');
            $user_info['default_resume'] = 1;
            $user_info['display'] = 1;
            $result = $userinfo->add($user_info);
            session('user_info_id',$result);
            $this->user_info_id = $result;
        }else{

            $userinfo->where('id =' . $this->user_info_id)->save($user_info);
           /// echo $userinfo->getLastSql();

        }

        return true;
    }
    //用户中心
    public function index()
    {

        self::__initialize();
        $type = I('get.type') ?? '';
        switch ($type) {
            case 'normal':
                $condition['status'] = 1;
                $title = "投递成功";
                break;

            case 'interested' :
                $condition['status'] = 2;
                $title = "被查看";
                break;
            case 'anAudition' :
                $condition['status'] = 3;
                $title = "面试邀请";
                break;
            case 'invite' :
                $condition['status'] = 4;
                $title = "邀请投递";
                break;

            case 'refuse' :
                $condition['status'] = 6;
                $title = "用户拒绝";
                break;

            case 'collect' :
                $condition['status'] = 7;
                $title = "岗位收藏";
                break;
            case 'success' :
                $condition['status'] = 8;
                $title = "邀请入职";
                break;
            case 'recommend' :
                $condition['status'] = 9;
                $title = "推荐岗位";
                break;
            default:
                $condition['status'] = 1;
                $title = "投递成功";
                break;

        }
        //dump($type);
        //简历id
        $invite_id = $this->user_info['id'];
        //面试邀请
        $condition['user_info_id'] = $invite_id;
        $company_invitation = M("company_invitation");

        $count = $company_invitation->where($condition)->count();
        $Page = $this->pages($count, $this->limit);
        $invite_items = $company_invitation->where($condition)
                                            ->order('add_time desc')
                                            ->limit($Page->firstRow . ',' . $Page->listRows)
                                            ->select();
        //dump($company_invitation->getLastSql());
        //dump($invite_items);


        $this->assign('title', $title);
        $this->assign('user_info', $this->user_info);
        $this->assign('invite_items', $invite_items);
        $this->assign('page', $this->bootstrap_page_style($Page->show()));// 赋值分页输出
        $this->assign('type', $type);
        $this->display();
    }

    //企业邀请投递
    public function invite()
    {
        self::__initialize();
        $type = I('get.type') ?? 'invite';
        //简历id
        $invite_id = $this->user_info['id'];
        //面试邀请
        $condition['user_info_id'] = $invite_id;
        $condition['status'] = 4;

        $company_invitation = M("company_invitation");
        $invite_items = $company_invitation->where($condition)
                        ->order('add_time desc')
                        ->limit(30)
                        ->select();
        //dump($invite_items);
        $this->assign('title', "邀请投递");
        $this->assign('user_info', $this->user_info);
        $this->assign('invite_items', $invite_items);
        $this->assign('type', $type);
        $this->display();
    }
    //用户处理投递
    public function the_invitation()
    {
        self::__initialize();
        if(I('post.invitation_id') == ''){
            $result = ['status' => 'fail', 'msg' => '无效操作'];
            $this->ajaxReturn($result);
        }

        if(I('post.type') == 1){
            $msg = "已接受企业投递邀请";
        }else if(I('post.type') == 6){
            $msg = "您拒绝了投递";
        }else{
            $result = ['status' => 'fail', 'msg' => '无效的操作状态'];
            $this->ajaxReturn($result);
        }

        $company_invitation = M("company_invitation");
        $condition['id'] = I('post.invitation_id');
        $condition['user_info_id'] = $this->user_info['id'];
        $condition['status'] = 4;

        $invite_item = $company_invitation->where($condition)->find();
        if ($invite_item == NULL) {
            $result = ['status' => 'fail', 'msg' => '没有匹配的数据'];
            $this->ajaxReturn($result);
        } else {

            $company_invitation->status = I('post.type');
            $company_invitation->where('id = ' . $invite_item['id'])->save();
            $result = ['status' => 'success', 'msg' => $msg];
            $this->ajaxReturn($result);
        }


    }

    //新增&修改教育经历
    public function add_education()
    {
        self::__initialize();
        $education = M("user_education");
        $education_item['user_id'] = $this->user_info_id;
        $education_item['school_name'] = I('post.school_name');
        $education_item['start_year'] = I('post.start_year');
        $education_item['end_year'] = I('post.end_year');
        $education_item['major'] = I('post.major');
        $education_item['education'] = I('post.education');

        $id = I('post.id', null);

        if (isset($id) && !empty($id)) {
            $education_item['id'] = $id;
            $result_id = $education->save($education_item);
        } else {
            $result_id = $education->data($education_item)->add();

        }


        if(IS_AJAX){
            if ($result_id) {
                $result = ['status' => 'success', 'msg' => ''];
                $this->ajaxReturn($result);
            } else {

                $result = ['status' => 'fail', 'msg' => '添加失败'];
                $this->ajaxReturn($result);
            }
        }else{
            if ($result_id) {
                $this->redirect('user/vitae', null, 0);
            } else {
                $this->error('新增失败');
            }
        }

    }
    //删除教育经历
    public function del_education()
    {
        self::__initialize();
        $id = I('post.id');
        $education = M("user_education");
        $condition['id'] = $id;
        $condition['user_id'] = $this->user_info['id'];

        $item = $education->where($condition)->find();
        if ($item == NULL) {
            $result = ['status' => 'fail', 'msg' => '没有匹配的数据'];
            $this->ajaxReturn($result);
        } else {

            $education->where('id =' . $id)->delete();
            $result = ['status' => 'success', 'msg' => ''];
            $this->ajaxReturn($result);
        }

    }
    //删除工作经历
    public function del_curriculum_vitae()
    {
        self::__initialize();
        $id = I('post.id');
        $education = M("user_curriculum_vitae");
        $condition['id'] = $id;
        $condition['user_id'] = $this->user_info['id'];

        $item = $education->where($condition)->find();
        if ($item == NULL) {
            $result = ['status' => 'fail', 'msg' => '没有匹配的数据'];
            $this->ajaxReturn($result);
        } else {

            $education->where('id =' . $id)->delete();
            $result = ['status' => 'success', 'msg' => ''];
            $this->ajaxReturn($result);
        }

    }
    //新增&修改工作经历
    public function add_curriculum_vitae()
    {
        self::__initialize();
        $education = M("user_curriculum_vitae");

        $data['user_id'] = $this->user_info_id;
        $data['company_name'] = I('post.company_name');
        $data['position_name'] = I('post.position_name');
        $data['start_time'] = I('post.start_time');
        $data['end_time'] = I('post.end_time');
        $data['content'] = I('post.content');
        $data['salary'] = I('post.salary');

        $id = I('post.id', null);

        if (isset($id) && !empty($id)) {
            $data['id'] = $id;
            $result_id = $education->save($data);
        } else {
            $result_id = $education->data($data)->add();
        }
        if(IS_AJAX){
            if ($result_id) {
               $result = ['status' => 'success', 'msg' => ''];
               $this->ajaxReturn($result);
            } else {

                $result = ['status' => 'fail', 'msg' => '添加失败'];
                $this->ajaxReturn($result);
            }
        }else{
            if ($result_id) {
                $this->redirect('user/vitae', null, 0);
            } else {
                $this->error('新增失败');
            }
        }


    }
    //新增&修改作品经历
    public function add_work()
    {
        self::__initialize();
        $works = M("user_works");

        /*图片上传*/
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->savePath = date("ymd"); // 设置附件上传（子）目录
        $info = $upload->uploadOne($_FILES['works_pic']);
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功 获取上传文件信息
            $url = $info['savepath'] . $info['savename'];
        }
        //图片处理（下个版本制作）


        $data['user_id'] = $this->user_info_id;
        $data['works_name'] = I('post.works_name');
        $data['works_pic'] = '/Uploads/' . $url;

        $id = I('post.id', null);
        if (isset($id) && !empty($id)) {
            $data['id'] = $id;
            $result_id = $works->save($data);
        } else {
            $result_id = $works->data($data)->add();
        }

        if ($result_id) {

            $this->redirect('user/vitae', null, 0);
        } else {
            $this->error('新增失败');

        }

    }
    //删除工作经历
    public function del_work()
    {
        self::__initialize();
        $id = I('post.id');
        $education = M("user_works");

        $condition['id'] = $id;
        $condition['user_id'] = $this->user_info['id'];

        $item = $education->where($condition)->find();
        if ($item == NULL) {
            $result = ['status' => 'fail', 'msg' => '没有匹配的数据'];
            $this->ajaxReturn($result);
        } else {

            $education->where('id =' . $id)->delete();
            $result = ['status' => 'success', 'msg' => ''];
            $this->ajaxReturn($result);
        }

    }
    //修改个人简介
    public function user_info_edit_about()
    {
        self::__initialize();
        $user_info = M("user_info");

        $user_info->content = I('post.individual');
        if(!$user_info->where('id ='.$this->user_info_id)->save()){
            $this->error('更新失败 3025', 'user/vitae', 0);
        };
        $this->redirect('user/vitae', null, 0);


    }
    public function info_privacy(){
        self::__initialize();
        if(I('post.edit') == true){
            $data['display'] = I('post.display',1,'int');
            $user_info = M('user_info');
            $user_info->display = $data['display'];
            $user_info->where('id = '.$this->user_info_id)->save($data);
            $alert = "设置已更改";
            $this->assign('alert',$alert);

            $this->redirect('user/info_privacy', array('alert'=>$alert), 0);
        }
        $this->assign('title','隐私设置');
        $this->assign('user_info',$this->user_info);
        $this->display();
    }
    //修改投递模式
    public function change_vitae_type()
    {
        self::__initialize();
        $user_info = M("user_info");

        $vitaetype = I('post.resume_type');
        if ($vitaetype == "online") {
            $user_info->default_resume = 1;
        } else {
            $user_info->default_resume = 0;
        }
        if(!$user_info->where('id= '.$this->user_info_id)->save()){
            $result = ['status' => 'error', 'msg' => ''];
//            dump($user_info->getLastSql());
//            dump($user_info->getDbError());
            $this->ajaxReturn($result);
        }

        $result = ['status' => 'success', 'msg' => ''];
        $this->ajaxReturn($result);


    }
    //投递简历
    public function set_resume()
    {
        self::__initialize();
        $recruit_id = I('recruit_id', null, 'int');
        if (empty($recruit_id) && $recruit_id == '') {
            $this->error('这份工作已经下架了，尝试换个岗位看看', U('search/index'), 5);
        }

        $company_invitation = M('company_invitation');

        if(parent::delivered($this->user_info_id,$recruit_id)){
            $this->error('您已经投递过该岗位，耐心等待反馈', U('User/index'), 5);
        }

        $result = M('recruit')
            ->field('com.id as com_id,com.company_name,rd_recruit.id as re_id,rd_recruit.name as re_name,rd_recruit.salary_min,rd_recruit.salary_max')
            ->join('rd_company_info com  ON rd_recruit.user_id = com.user_id')
            ->where('rd_recruit.id = ' . $recruit_id)
            ->find();
        //dump(M('recruit')->getLastSql());
        if(!$result){
            $this->error('这个岗位已经下架了，代码：463', U('search/index'), 5);
        }

        $data['company_id']   = $result['com_id'];
        $data['recruit_id']   = $result['re_id'];
        $data['recruit_name'] = $result['re_name'];
        $data['company_name'] = $result['company_name'];
        $data['user_name']    = $result['com_id'];
        $data['salary']       = $result['salary_min'] . 'k~' . $result['salary_max'].'k';
        $data['user_info_id'] = $this->user_info_id;
        $data['add_time']     = strtotime('now');
        $data['status']       = 1;
        $company_invitation->data($data)->add();
        $this->redirect('user/index', null, 0);
    }
    //资料完整度
    public function IntegrityDegree($educations = null,$user_cvitaes = null,$works = null){
        self::__initialize();
        $num = 10;
        $user_info = $this->user_info;
        //真名
        if($user_info['real_name'] != ''){
            $num += 5;
        }
        //工作经验
        if($user_info['work_type'] != ''){
            $num += 5;
        }
        //生日
        if($user_info['birthday'] != ''){
            $num += 5;
        }
        //头像
        if($user_info['avatar'] != ''){
            $num += 10;
        }
        //月薪
        if($user_info['salary'] != ''){
            $num += 5;
        }
        //个人简介
        if($user_info['content'] != ''){
            $num += 10;
        }
        //求学经历
        if(count($educations) != 0){
            $num += 20;
        }
        //工作经历
        if(count($user_cvitaes)  != 0){
            $num += 20;
        }
        //作品集
        if(count($works)  != 0){
            $num += 10;
        }


        return $num;
    }

    //头像上传
    public function user_avator(){
        self::__initialize();
        $options['upload_dir'] = "./Uploads/avator/";
        $options['singleFileUploads'] = true;
        $options['imageCrop'] = true ;
        $upload_handler = new \Org\Util\UploadHandler($options);
        $result = $upload_handler->get_response();
        $user_info = M('user_info');
        $user_info->avator = $result['files'][0]->thumbnailUrl;
        $user_info->where('id= '.$this->user_info_id)->save();
    }
}