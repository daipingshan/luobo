<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2018/8/7
 * Time: 22:48
 */

namespace Home\Controller;


use foo\Foo;

class RecruitController extends CommonController
{

    public function __initialize()
    {

        $company      = $this->company;
        $this->status = $this->_getCompanyInfo($this->company_id);
        //  企业判定
        $is_defect = 0;
        if ($this->status == false) {
            $this->redirect('company/edit', '', 0);
        } elseif ($company['is_check'] == 0) {
            $this->redirect('company/edit', '', 0);
        } elseif ($company['is_check'] == 2) {
            $this->redirect('company/edit', '', 0);
        }
    }

    /**
     * 企业简历库
     */
    public function workLifePaper()
    {
        $status = I('get.status', '1', 'trim');
        $p      = I('get.p', '1', 'int');

        $position_type = M('position_type')->where(array('parent_id' => 0))->select();
        $hr_pic        = C('PIC_URL') . $this->company['hr_qrcode'];
        $label         = M('company_label')->index('id')->select();

        $where['company_id'] = $this->company_id;
        if ($status == 'collect') {
            $count    = M('company_collect')->where($where)->count();
            $page     = $this->pages($count, C('LIMIT'));
            $res_data = M('company_collect')->field('update_time, user_info_id')->where($where)->limit($page->firstRow
                . ',' . $page->listRows)->select();
        } else {
            if ($status == '1') {
                $where['status'] = '1';
            } elseif ($status == '2') {
                $where['status'] = '2';
            } elseif ($status == '3') {
                $where['status'] = '3';
            } elseif ($status == '7') {
                $where['status'] = '7';
            } elseif ($status == '8') {
                $where['status'] = '8';
            }
            $count    = M('company_invitation')->where($where)->count();
            $page     = $this->pages($count, C('LIMIT'));
            $res_data = M('company_invitation')->field('id, update_time, user_info_id')->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)->order('update_time desc')->select();
        }

        $data = array();
        foreach ($res_data as $val) {
            $rwhere['id'] = $val['user_info_id'];
            $info         = M('user_info')->where($rwhere)->find();
            $data[]       = array(
                'id'            => $val['id'],
                'delivery_time' => $val['add_time'],
                'headimgrul'    => M('user')->where(array('id' => $info['user_id']))->getField('headimgurl'),
                'info'          => $info,
            );
        }

        $this->assign('label', $label);
        $this->assign('hr_pic', $hr_pic);
        $this->assign('position_type', $position_type);
        $this->assign('work', $data);
        $this->assign('title', '查看简历');
        $this->assign('page', $page->show());
        $this->display();
    }

    /**
     * 搜索简历
     */
    public function searchInfo()
    {
        $job_name   = I('get.name', '', 'trim');
        $work_time  = I('get.work_time', '', 'trim');
        $city       = I('get.city', '', 'trim');
        $salary_min = I('get.salary_min', '', 'int');
        $salary_max = I('get.salary_max', '', 'int');
        $education  = I('get.education', '', 'trim');
        $job_type   = I('get.job_type', '', 'trim');
        $p          = I('get.p', '', 'trim');


        $where = $search = '';
        if ( ! empty($job_name)) {
            $where['job_name']  = array('like', '%' . $job_name . '%');
            $search['job_name'] = $job_name;
        }
        if ( ! empty($work_time)) {
            $where['work_time']  = array('elt', $work_time);
            $search['work_time'] = $work_time;
        }
        if ( ! empty($city)) {
            $where['city']  = $city;
            $search['city'] = $city;
        }
        if ( ! empty($salary_min)) {
            $where['salary_min']  = $salary_min;
            $search['salary_min'] = $salary_min;
        }
        if ( ! empty($salary_max)) {
            $where['salary_max']  = $salary_max;
            $search['salary_max'] = $salary_max;
        }
        if ( ! empty($education)) {
            $where['education']  = $education;
            $search['education'] = $education;
        }
        if ( ! empty($job_type)) {
            $where['job_type']  = $job_type;
            $search['job_type'] = $job_type;
        }
        if ( ! empty($p)) {
            $search['p'] = $p;
        }

        $count     = M('user_info')->where($where)->count();
        $page      = $this->pages($count, C('LIMIT'));
        $user_info = M('user_info')->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();


        $this->assign('search', $search);
        $this->assign('recruit', $user_info);
        $this->assign('search', $search);
        $this->assign('page', $page->show());
        $this->assign('title', '搜索简历');
        $this->display();
    }

    /**
     * 编辑招聘信息
     */
    public function edit()
    {
        $id      = I('post.id', '', '');
        $user_id = $this->user_id;
        if ($_POST) {
            $data           = array(
                'type'            => I('post.type', '', 'trim'),
                'name'            => I('post.name', '', 'trim'),
                'nature'          => I('post.nature', '', 'trim'),
                'position'        => I('post.position', '', 'trim'),
                'desc'            => I('post.desc', '', 'trim'),
                'label'           => I('post.label', '', 'trim'),
                'experience'      => I('post.experience', '', 'trim'),
                'limit_educate'   => I('post.limit_educate', '', 'trim'),
                'salary_min'      => I('post.salary_min', '', 'trim'),
                'salary_max'      => I('post.salary_max', '', 'trim'),
                'job_excellence'  => I('post.job_excellence', '', 'trim'),
                'address'         => I('post.address', '', 'trim'),
                'contacts'        => I('post.contacts', '', 'trim'),
                'contacts_mobile' => I('post.contacts_mobile', '', 'trim'),
                'create_time'     => time(),
            );

            if (mb_strlen($data['job_excellence']) > 20) {
                $res = array('code' => -1, 'msg' => '职位添加失败，行业类别不得超过12个字!', 'data' => '');
                $this->ajaxReturn($res);
            }

            if (mb_strlen($data['name']) > 9) {
                $res = array('code' => -1, 'msg' => '职位添加失败，职位名称不得超过9个字!', 'data' => '');
                $this->ajaxReturn($res);
            }

            $map['id']      = array('eq', $id);
            $map['user_id'] = array('eq', $user_id);
            $recruit_res    = M('recruit')->where($map)->data($data)->save();

            if (empty($recruit_res)) {
                $res = array('code' => -1, 'msg' => '职位添加失败!', 'data' => '');
            } else {
                $res = array('code' => 0, 'msg' => '职位更新成功!', 'data' => U('Company/manage'));
            }
            $this->ajaxReturn($res);
        } else {
            $id = I('get.id', '', '');

            $recruit        = M('recruit')->where(array('id' => $id))->find();
            $position_type  = M('position_type')->where(array('parent_id' => 0))->select();
            $label          = M('company_label')->index('id')->select();
            $position_label = M('position_label')->index('id')->select();

            $this->assign('used_label', C('RECRU_LABEL'));
            $this->assign('position_label', $position_label);
            $this->assign('edcuation', C('EDUCATION'));
            $this->assign('work_type', C('WORK_NATURE'));
            $this->assign('expreience', C('WORK_EXPERIENCE'));
            $this->assign('recruit', $recruit);
            $this->assign('label', $label);
            $this->assign('position_type', $position_type);
            $this->assign('title', '编辑职位');
            $this->display();
        }
    }

    /**
     * 发布招聘信息
     */
    public function sendRecruitmentInfo()
    {
        self::__initialize();
        $user_id = $this->user_id;

        if (IS_AJAX) {
            $province_id     = I('post.province_id', '0', 'trim');
            $city_id         = I('post.city_id', '0', 'trim');
            $district_id     = I('post.district_id', '0', 'trim');
            $name            = I('post.name', '', 'trim');
            $type            = I('post.type', '', 'trim');
            $nature          = I('post.nature', '', 'trim');
            $position        = I('post.position', '', 'trim');
            $desc            = I('post.desc', '', 'trim');
            $label           = I('post.label', '', 'trim');
            $experience      = I('post.experience', '', 'trim');
            $limit_educate   = I('post.limit_educate', '', 'trim');
            $salary_min      = I('post.salary_min', '', 'trim');
            $salary_max      = I('post.salary_max', '', 'trim');
            $job_excellence  = I('post.job_excellence', '', 'trim');
            $address         = I('post.address', '', 'trim');
            $contacts        = I('post.contacts', '', 'trim');
            $contacts_mobile = I('post.contacts_mobile', '', 'trim');

            if (mb_strlen($job_excellence) > 20) {
                $res = array('code' => -1, 'msg' => '职位添加失败，行业类别不得超过12个字!', 'data' => '');
                $this->ajaxReturn($res);
            }

            if (mb_strlen($name) > 9) {
                $res = array('code' => -1, 'msg' => '职位添加失败，职位名称不得超过9个字!', 'data' => '');
                $this->ajaxReturn($res);
            }

            $data = array(
                'user_id'         => $user_id,
                'province_id'     => $province_id,
                'city_id'         => $city_id,
                'district_id'     => $district_id,
                'type'            => $type,
                'name'            => $name,
                'nature'          => $nature,
                'position'        => $position,
                'desc'            => $desc,
                'label'           => $label,
                'experience'      => $experience,
                'limit_educate'   => $limit_educate,
                'salary_min'      => $salary_min,
                'salary_max'      => $salary_max,
                'job_excellence'  => $job_excellence,
                'address'         => $address,
                'contacts'        => $contacts,
                'contacts_mobile' => $contacts_mobile,
                'create_time'     => time(),
                'status'          => 0
            );

            $recruit_res = M('recruit')->add($data);

            if (empty($recruit_res)) {
                $res = array('code' => -1, 'msg' => '职位添加失败，请稍后重试!', 'data' => '');
                $this->ajaxReturn($res);
            } else {
                $amount = M('user')->where(array('id' => $user_id))->getField('amount');
                $this->_addUserFlow($user_id, '添加职位', $user_id, $amount, $amount, '');
                $res = array(
                    'code' => 0,
                    'msg'  => '职位添加成功!',
                    'data' => U('Company/manage', array('user_id' => $user_id))
                );
                $this->ajaxReturn($res);
            }
        } else {

            $position_type  = M('position_type')->where(array('parent_id' => 0))->select();
            $position_label = M('position_label')->index('id')->select();
            $label          = M('company_label')->index('id')->select();

            $desc = array(
                'company_position_display' => C('COMPANY_DISPLAY.position_display'),
                'company_welfare_display'  => C('COMPANY_DISPLAY.welfare_display'),
                'user_self_display'        => C('USER_DISPLAY.self_display'),
            );

            $this->assign('used_label', C('RECRU_LABEL'));
            $this->assign('edcuation', C('EDUCATION'));
            $this->assign('work_type', C('WORK_NATURE'));
            $this->assign('expreience', C('WORK_EXPERIENCE'));
            $this->assign('position_label', $position_label);
            $this->assign('desc', $desc);
            $this->assign('label', $label);
            $this->assign('position_type', $position_type);
            $this->assign('title', '发布新职位');
            $this->display();
        }
    }

    /**
     * 删除招聘信息
     */
    public function del()
    {
        $id = I('get.id', '', '');

        $res = M('recruit')->where(array('id' => $id))->save(array('status' => '4'));
        if ($res) {
            $this->redirect('Company/manage');
        } else {
            dump('删除失败');
            die;
        }

    }

    /**
     * 获取二级职位分类
     */
    public function getPositionType()
    {
        $position_id = I('post.position_id', '', 'intval');

        $data = M('position_type')->where(array('parent_id' => $position_id))->select();
        $res  = array('code' => 0, 'msg' => 'success', 'data' => $data);
        $this->ajaxReturn($res);
    }

    /**
     * 修改查看状态
     */
    public function editStatus()
    {
        $id   = I('post.id', '', 'trim');
        $type = I('post.type', '', 'trim');

        $user_info_id = M('company_invitation')->where(array('id' => $id))->getField('user_info_id');
        if ($type == 'detail') {
            $invite_res = M('company_invitation')->where(array('id' => $id))->save(array('status' => 2));
        } elseif ($type == 'invite') {
            $invite_res = M('company_invitation')->where(array('id' => $id))->save(array('status' => 8));
        } elseif ($type == 'pass') {
            $invite_res = M('company_invitation')->where(array('id' => $id))->save(array('status' => 9));
        }

        if (empty($invite_res)) {
            $res = array('code' => -1, 'msg' => '操作失败', 'url' => U('Recruit/workLifePaper', array('id' => $id)));
        } else {
            $res = array(
                'code' => 0,
                'msg'  => '操作成功',
                'url'  => U('Vitae/detail', array('id' => base64_encode($user_info_id . C(ENCODE)), 'detail' => $id))
            );
        }
        $this->ajaxReturn($res);
    }

    /**
     * 岗位状态
     * postition
     */
    public function positionStatus()
    {
        $name          = I('get.name', '', 'trim');
        $type          = I('get.type', '', 'trim');
        $limit_educate = I('get.limit_educate', '', 'int');
        $page          = I('get.page', 1, 'intval');

        $this->output('ok', 0);
    }

    /**
     * 查看岗位投递情况sendRecruitmentInfo
     */
    public function checkPost()
    {
        $status = I('get.status', '', 'trim');
        $page   = I('get.page', 1, 'intval');
        $this->output('ok', 0);
    }


}