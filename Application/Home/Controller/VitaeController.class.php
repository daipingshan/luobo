<?php
/**
 * Created by PhpStorm.
 * User: Guonan
 * Date: 2018/8/7
 * Time: 22:48
 * 简历相关功能
 */

namespace Home\Controller;

class VitaeController extends CommonController
{

    /**
     * 用户的权限处理
     */
    public function __initialize($id, $user_id)
    {

        // 查看用户简历的联系方式的权限
        $permission = false;
        $button_status = false;
        $identity = session('identity');
        // 区分企业用户还是个人用户
        if ($identity == 1) {
            if ($this->user_id == $user_id) {
                $permission = true;
                $button_status = true;
            }
        } else if ($identity == 2) {
            // 查看企业是否邀请过该用户简历
            $invite_status = $is_collect = false;

            $inviation = M('company_invitation')->where(array('user_info_id' => $id, 'company_id' => $this->company_id))->find();
            $collect = M('company_collect')->where(array('company_id' => $this->company_id, 'user_info_id' => $id))->find();
            if (!empty($inviation)) {
                $invite_status = true;
                if ($inviation['status'] != 2 && $inviation['status'] != 4) {
                    $permission = true;
                }
            }

            if (!empty($collect)) {
                $is_collect = true;
            }
            
            $button_status = true;

            //  企业用户的发布的职位
            $company_recruit = M('recruit')->where(array('user_id' => $this->user_id, 'status' => 0))->field('id, name')->index('id')->select();

            $this->assign('company_recruit', $company_recruit);
            $this->assign('is_collect', $is_collect);
            $this->assign('invite_status', $invite_status);
        }

        $this->assign('identity', $identity);
        $this->assign('cities', $this->_Cities());
        $this->assign('permission', $permission);
        $this->assign('button_status', $button_status);
    }

    /**
     * 简历详情
     */
    public function detail()
    {
        $id = I('get.id', '', 'trim');

        $id = (int) str_replace(C('ENCODE'),'',base64_decode($id));

        // 简历基本信息
        $resume = M('user_info')->where(array('id' => $id))->find();
        //  简历用户基本资料
        $resume_info = M('user')->field('id, mobile, email')->where(array('id' => $resume['user_id']))->find();
        // 教育经历
        $map['user_id'] = $resume['id'];
        $education = M('user_education')->field('school_name, start_year, end_year, major, education')->where($map)->select();
        // 工作经历
        $experience_job = M('user_curriculum_vitae')->field('company_name, position_name, start_time, end_time, content')->where($map)->select();
        // 作品展示
        $product = M('user_works')->field('works_name, works_pic')->where($map)->select();

        self::__initialize($id, $resume['user_id']);

        foreach ($education as &$val) {
            $val['education'] = C('HIGH_EDUCATION')[$val['education']];
        }
        foreach ($product as &$val) {
            $val['zuopintupian'] = C('PIC_URL').$val['works_pic'];
        }
        $education_json = json_encode($education);
        $experience_job_json = json_encode($experience_job);
        $product_json = json_encode($product);

        //收藏
        $data = array(
            //  简历详情
            'resume' => $resume,
            'user' => $resume_info,
            //  教育经历
            'education' => $education,
            //  工作经历
            'experience_job' => $experience_job,
            //  作品展示
            'product' => $product,
        );

        $this->assign('education_json', $education_json);
        $this->assign('experience_job_json', $experience_job_json);
        $this->assign('product_json', $product_json);
        $this->assign('data', $data);
        $this->assign('title', $resume['real_name'] . '的简历');
        $this->display();
    }

    /**
     * 发送邀请
     */
    public function sendInvitation()
    {
        $id = I('post.id', '', 'trim');
        $user_info_id = I('post.user_info_id', '', 'trim');
        $recruit_id = I('post.recruit_id', '', 'trim');
        $invite_time = I('post.invite_time', '', 'trim');

        $invite_time = strtotime($invite_time);

        $data = array(
            'company_id' => $this->company_id,
            'recruit_id' => $recruit_id,
            'user_info_id' => $user_info_id,
            'invite_time' => $invite_time,
            'status' => 4,
            'model' => 0,
        );
        $res = M('company_invitation')->add($data);
        if ($res) {
            $invitation_res = array('code'=>0, 'msg'=>'邀请面试发送成功！', 'data'=>$data);
        } else{
            $invitation_res = array('code'=>-1, 'msg'=>'邀请面试发送失败，请稍后重试', 'data'=>'');
        }
        $this->ajaxReturn($invitation_res);
    }

    /**
     * 收藏简历
     */
    public function doCollect(){
        $recruit_id = I('get.id', 0, 'trim');

        $collect = M('company_invitation')->where(array('user_info_id' => $recruit_id))->find();

        $amount = M('user')->where(array('id' => $this->user_id))->getField('amount');

        if (empty($collect)) {
            $pay_amount = C('COMPANY')['base']['down'];
            if(!parent::_check_amount($pay_amount)){
                $res = array('status' => '-1','msg'=>'余额不足');
                $this->ajaxReturn($res);
            };
        }

        if($recruit_id != 0){
            //扣费
            if (empty($collect)) {
                $amount_res = M('user')->where(array('id' => $this->user_id))->setDec('amount', $pay_amount);
                if($amount_res){
                    $data = array(
                        'company_id' => $this->company_id,
                        'type' => 'user',
                        'user_info_id' => $recruit_id,
                        'recruit_id' => $recruit_id,
                        'recruit_type' => 'online',
                        'add_time' => time(),
                        'status' => 0,
                    );

                    $res = M('company_collect')->add($data);
                    if($res){
                        $pay_amount = -$pay_amount;
                        $title = '收藏用户简历!';
                        $represent = '企业进行了简历收藏操作';
                        $this->_addUserFlow($this->user_id, $represent, $title, $amount, $pay_amount, $this->user_id);
                        $res = array('status' => '1','msg'=>'success', 'data' => $data);
                    }else{
                        \Think\Log::record('更新操作失败 id'.$this->user_id.'费用'.$amount);
                        $res = array('status' => '-1','msg'=>'sql_refresh_fail');
                    }
                }else{
                    \Think\Log::record('操作扣费失败 id'.$this->user_id.'费用'.$amount);
                    $res = array('status' => '-1','msg'=>'sql_pay_fail');
                }
            } else {
                $data = array(
                    'company_id' => $this->company_id,
                    'type' => 'user',
                    'user_info_id' => $recruit_id,
                    'recruit_id' => $recruit_id,
                    'recruit_type' => 'online',
                    'add_time' => time(),
                    'status' => 0,
                );
                $res = M('company_collect')->add($data);
                if($res){
                    $title = '收藏用户简历!';
                    $represent = '企业进行了简历收藏操作';
                    $this->_addUserFlow($this->user_id, $represent, $title, $amount, 0, $this->user_id);
                    $res = array('status' => '1','msg'=>'success', 'data'=> $data);
                }else{
                    \Think\Log::record('更新操作失败 id'.$this->user_id.'费用'.$amount);
                    $res = array('status' => '-1','msg'=>'sql_refresh_fail');
                }
            }
        }
        $this->ajaxReturn($res);
    }

}