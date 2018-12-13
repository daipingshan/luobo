<?php
/**
 * Created by PhpStorm.
 * User: Guonan
 * Date: 2018/10/11
 * Time: 15:48
 * 门店系统
 */

namespace Home\Controller;

class ShopController extends CommonController
{

    protected $checkUser = false;
    protected $shop_info;
    protected $permit = false;
    public function __initialize(){
        //切换身份至门店
        session('identity', 3);
        parent::_checkUser();

        $this->shop_info = M('shops')->where(array('user_id' => $this->user_id))->find();
        if(empty($this->shop_info) || $this->shop_info['status'] == 3){
            $this->redirect('Shop/guide','', 0);
        }
        $user_count = self::user_count();
        $income = self::my_income();
        $qrcode = self::qrcode();

        $this->assign('url',$qrcode);
        $this->assign('income',$income);
        $this->assign('user_count',$user_count);
        $this->assign('shop_info',$this->shop_info);
        $this->assign('search_hide',true);
    }
    //登录
    public function login(){
        session('identity', 3);
        $this->display();
    }
    public function set_info(){
        parent::_checkUser();
        $shop = M('shops');
        $shop_info = $shop->where('user_id ='.$this->user_id)->find();
        if (I('post.act') == 'edit') {
            //基础信息
            $shop->user_id = session('user_id');
            $shop->name = empty(I('post.name')) ? null : I('post.name');
            $shop->shop_type = empty(I('post.shop_type')) ? 3 : I('post.shop_type');
            $shop->address = empty(I('post.address')) ? null : I('post.address');
            $shop->id_img = empty(I('post.id_img')) ? null : I('post.id_img');
            $shop->id_code = empty(I('post.id_code')) ? null : I('post.id_code');
            $shop->status = 0;
            $shop->id_img = empty(I('post.id_img')) ? null : I('post.id_img');
            $shop->province_id = empty(I('post.province_id')) ? null : I('post.province_id');
            $shop->city_id = empty(I('post.city_id')) ? null : I('post.city_id');
            $shop->district_id = empty(I('post.district_id')) ? null : I('post.district_id');
            $shop->created_at = date('Y-m-d H:i:s');
            if($shop->id_img == null){
                $result = ['status' => 'fail', 'msg' => '未获取到图片'];
                $this->ajaxReturn($result);
            }
            if($shop->address == null){
                $result = ['status' => 'fail', 'msg' => '未获取到地址'];
                $this->ajaxReturn($result);
            }
            if($shop->name == null){
                $result = ['status' => 'fail', 'msg' => '未填写用户名'];
                $this->ajaxReturn($result);
            }
            if($shop->id_code == null){
                $result = ['status' => 'fail', 'msg' => '身份证信息不正确'];
                $this->ajaxReturn($result);
            }
            if($shop->user_id == null){
                $result = ['status' => 'fail', 'msg' => '未登录'];
                $this->ajaxReturn($result);
            }
            //dump($shop_info);
            if($shop_info['id'] != ''){
                $shop->id = $shop_info['id'];
                $x = $shop->save();
            }else{
                $x = $shop->add();
            }

            $result = ['status' => 'success', 'msg' => ''];
            $this->ajaxReturn($result);
        }
    }
    //开通门店
    public function guide(){
        parent::_checkUser();
        $shop = M('shops');
        $result = $shop->where('user_id ='.$this->user_id)->find();

        if($result['status'] == 1){
            $this->redirect('Shop/index','', 0);
        }

        $cities = $this->_getCities();
        $cities_json = json_encode($cities);
        $this->assign('cities_json', $cities_json);
        $this->assign('shop_info', $result);
        $this->display();
    }
    //门店主页
    public function index(){

        self::__initialize();
        if($this->shop_info['status'] == 1){
            $orders = M('shops_orders');
            $map['rd_shops_orders.shop_id'] = $this->shop_info['id'];

            $count  = $orders->where($map) ->count();
            $Page  = new \Think\Page($count,$this->limit);
            $items = $orders->where($map)
                        ->field('rd_shops_orders.*,poj.name as poj_name,poj.id as poj_id')
                        ->order('add_time desc,id desc')
                        ->join('rd_project poj ON rd_shops_orders.project_id =  poj.id ')
                        ->limit($Page->firstRow,$Page->listRows)
                        ->select();
            ///dump($items);
            $this->assign('items', $items);
            $this->assign('page', $this->bootstrap_page_style($Page->show()));// 赋值分页输出
            $this->display();
        }else{
            $this->redirect('Shop/guide','', 0);
        }
    }
    //添加萝卜项目
    public function add_project(){
        self::__initialize();
        $keyword = I('keyword','','string');
        if($keyword != ''){
            $condition['name'] = array('like','%'.$keyword.'%');
        }else{
            $condition = null;
        }
        $projects = M('project')->where($condition)->order('create_time desc,status asc,id desc')->getField('id,name');
        $this->assign('projects', $projects);
        $this->display();
    }
    //储存项目
    public function set_project(){
        self::__initialize();
        $data = I('post.data');
        $order = M('shops_orders');
        $map['id_code'] = $data['id_code'];
        $map['project_id'] = $data['project_id'];
        $project_id = $order->where($map)->find();
        if($project_id){
            $result = ['status' => 'fail', 'msg' => '用户信息已存在'];
            $this->ajaxReturn($result);
        }
        $project_info = M('project')->where('id = '. $map['project_id'])->find();
        $data['shop_id'] = $this->shop_info['id'];
        $data['income'] = $project_info['subsidy'];

//        dump($data);
//        dump(123);
//        exit();
        $result = $order->data($data)->add();
        if($result){
            $result = ['status' => 'success', 'msg' => ''];
            $this->ajaxReturn($result);
        }else{
            $result = ['status' => 'fail', 'msg' => ''];
            $this->ajaxReturn($result);
            dump($data);
        }

    }

    //储存项目
    public function set_report(){
        self::__initialize();
        $data['name'] = I('post.name',null,'htmlspecialchars');
        $data['income'] = I('post.income',null,'int');
        $data['cat_id'] = I('post.cat_id',null,'int');
        $data['content'] = I('post.content',null,'htmlspecialchars');
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->user_id;
        $data['check'] = 0;

        if($data['name'] == null || $data['cat_id'] == null || $data['income'] == null){
            $this->error('项目名，分类，金额都是必填项','/Shop/report');
            exit();
        }

        $report = M('shops_report');

//        dump($data);
//        dump(123);
//        exit();
        $result = $report->data($data)->add();
        if($result){
            $this->redirect('Shop/report', array('message' => '成功添加'), 0);
        }else{
            $this->error($result,'/Shop/report');
            exit();

        }

    }
    //撤回业绩
    public function remove_report(){
        self::__initialize();
        $id = I('get.report_id');
        $map['id'] = $id;
        $map['user_id'] = $this->user_id;
        $map['check'] = 0;

        $report = M('shops_report');
        $result = $report->where($map)->find();
        if($result != null){
            //撤回
            $data['check'] = 2;
            $report->where('id = '.$result['id'])->save($data);
            $this->redirect('Shop/report');
        }else{
            $this->error('没找到数据','/Shop/report');
        }

    }
    //其他业绩
    public function report(){
        self::__initialize();

        $categories = M('shops_report_cats')->getField('id,name,status');
        $type = I('get.type',null,'int');
        $report = M('shops_report');
        if($type != null){
            $map['check'] = $type;
        }

        $map['user_id'] = $this->user_id;

        $count  = $report->where($map)->count();
        $Page   = new \Think\Page($count,$this->limit);

        $items  = $report->where($map)
                    ->limit($Page->firstRow,$Page->listRows)
                    ->select();
        //dump($items);
        $this->assign('items', $items);
        $this->assign('categories',$categories);
        $this->assign('type',$type);
        $this->assign('page', $this->bootstrap_page_style($Page->show()));// 赋值分页输出
        $this->assign('title', '其他业绩');
        $this->display();
    }

    //我的下线
    public function follower(){
        self::__initialize();
        $user = M('user');
        $map['rd_user.invite_uid'] = $this->user_id;

        $count  = $user->where($map)->count();
        $Page   = new \Think\Page($count,$this->limit);

        $items  = $user->field('rd_user.*')
                ->field('shop.*,rd_user.mobile,rd_user.create_time')
                ->join('rd_shops shop ON rd_user.id = shop.user_id ')
                ->where($map)
                ->limit($Page->firstRow,$Page->listRows)
                ->select();
        //dump($items);
        $this->assign('title', '我的粉丝');
        $this->assign('items', $items);
        $this->assign('page', $this->bootstrap_page_style($Page->show()));// 赋值分页输出
        $this->display();
    }
    //我的粉丝
    public function my_user(){
        self::__initialize();
        $user = M('user');
        $map['rd_user.invite_uid'] = $this->user_id;

        $count  = $user->where($map)->count();
        $Page   = new \Think\Page($count,$this->limit);

        $items  = $user->field('rd_user.*')
            ->field('info.*,rd_user.mobile,rd_user.create_time')
            ->join('rd_user_info info ON rd_user.id = info.user_id ')
            ->where($map)
            ->limit($Page->firstRow,$Page->listRows)
            ->select();
        //dump($items);
        $this->assign('title', '我的下线');
        $this->assign('items', $items);
        $this->assign('page', $this->bootstrap_page_style($Page->show()));// 赋值分页输出
        $this->display('Shop/my_shops');
    }
    //获取粉丝
    public function get_my_user(){
        self::__initialize();
        $mobile = I('post.mobile','','string');
        if($mobile != ''){
            $condition['rd_user.mobile'] = array('like','%'.$mobile.'%');
        }
        $condition['rd_user.invite_uid'] = $this->user_id;
        $users = M('user')
                    ->where($condition)
                    ->join('rd_user_info info ON rd_user.id = info.user_id')
                    ->field('rd_user.id,rd_user.mobile,info.real_name')->select();
//        dump(M('user')->getLastSql());
//        exit();
        if(empty($users)){
            $result = ['status' => 'success', 'users' => ''];
        }else{
            $result = ['status' => 'success', 'users' => $users];
        }
        $this->ajaxReturn($result);
    }
    //统计人数
    private function user_count(){
       $condition['rd_user.invite_uid'] = $this->user_id;
       //个人用户
       $count['user_count']          = M('user')->where($condition)->join('rd_user_info info ON rd_user.id = info.user_id')->count();
       //总用户
       $count['user_c']              = M('user')->where($condition)->count();
       //代理
       $count['follower_count']      = M('user')->join('rd_shops shop ON rd_user.id = shop.user_id ')->where($condition)->count();
       //新用户
       $join = array('left join rd_user_info u_i ON u_i.user_id = u.id', 'left join rd_company_info c_i ON c_i.user_id = u.id', 'left join rd_shops s ON s.user_id = u.id');
       $count['new']             = M('user')->alias('u')->join($join)->where(array('u.invite_uid' => $this->user_id, '_string' => 's.id is null AND c_i.id is null AND u_i.id is null'))->count('u.id');


       return $count;
    }
    //计算收益
    private function my_income(){
        $order = M('shops_orders');
        $other_order = M('shops_report');
        $condition['invite_uid'] = $this->user_id;
        //萝卜项目
        $result['predict'] = $order->where($condition)->sum('income');
        //其他业绩
        $result['predict'] += $other_order->where('user_id ='.$this->user_id)->sum('income');


        $condition['check_up'] = 1;
        $result['confirm'] = $order->where($condition)->sum('income');
        $map['check'] = 1;
        $map['user_id'] = $this->user_id;
        $result['confirm'] += $other_order->where($map)->sum('income');

        return $result;
    }
    //推广二维码
    public function qrcode(){

        $url = urlencode("http://www.luobowang.cn/Auth/register/uid/".$this->user_id);
        $logo_url = urlencode('http://www.luobowang.cn/Public/Home/images/cp_def.png');
        $url = 'http://qr.topscan.com/api.php?text='.$url.'&logo='.$logo_url;
        return $url;
//        $level=3;
//        $size=4;
//        Vendor('phpqrcode.phpqrcode');
//        $errorCorrectionLevel =intval($level) ;//容错级别
//        $matrixPointSize = intval($size);//生成图片大小
//        //生成二维码图片
//        $object = new \QRcode();
//        $logo = '__IMAGE_PATH__/cp_def.png';
//
//        ob_start();
//        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
//        $imageString = base64_encode(ob_get_contents());
//        if ($logo !== FALSE) {
//            $QR = imagecreatefromstring ( $imageString );
//            $logo = imagecreatefromstring ( file_get_contents ( $logo ) );
//            $QR_width = imagesx ( $QR );
//            $QR_height = imagesy ( $QR );
//            $logo_width = imagesx ( $logo );
//            $logo_height = imagesy ( $logo );
//            $logo_qr_width = $QR_width / 5;
//            $scale = $logo_width / $logo_qr_width;
//            $logo_qr_height = $logo_height / $scale;
//            $from_width = ($QR_width - $logo_qr_width) / 2;
//            imagecopyresampled ( $QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height );
//        }
//
//        ob_end_clean();
//        return "<img src='data:image/png;base64,{$imageString}'  />";

    }



}