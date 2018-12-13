<?php
namespace Fanli\Controller;

use Common\Controller\CommonBusinessController;

require_once __DIR__ . "/KinaAlipay/pcAlipay.class.php";


class MdfalipayController extends CommonController{

    /*
    *   返现比例调整2017.2.15
    */
    const CONSU_RATIO = 0.25;     //  消费者返现比例
    const MERCH_RATIO = 0.15;     //  商户返现比例
    const AGENT_RATIO = 0.25;     //  代理返现比例
    const COMPA_RATIO = 0.10;     //  公司返现比例
    const FANSF_RATIO = 0.20;     //  上级返现比例
    const FANSS_RATIO = 0.05;     //  上上级返现比例

    //  用户返利比例
    //  最大支付金额
    const MAX = 10000;  
    const ZERO = 0;
    const ONE = 0.1;
    const TWEN = 20;
    //  返5块
    const FIVE = 5;
    //  10块
    const TEN = 10;
    //  返2块
    const TWO = 2;
    
    /**
     * 构造方法
     */
    public function __construct() {
        parent:: __construct();
        $this->users = M('fanli_wxuser');
        $this->orders = M('fanli_order');        
        $this->dis = M('fanli_dis');
        $this->agent = M('fanli_agent');
        $this->label = M('fanli_label');
        $this->cate = M('fanli_category');
        $this->money = M('fanli_money');
        $this->comment = M('fanli_comment');        
        $this->partner = M('fanli_partner');
        $this->tmp = M('fanli_tmp');
        $this->token = M('fanli_token');
        $this->customer = M('fanli_customer');
        $this->intergral = M('fanli_integral');   //  积分规则表
        $this->jforder = M('fanli_integral_order');     //  积分产品兑换记录
        $this->addjf = M('fanli_add_integral');     //  积分增加记录
        $this->sendsms = M('sendsms');     //  积分增加记录
    }     

   
    /*
    *   支付宝授权回调  1st start
    *   2017.3.4  by mpp
    */
    public function getqrcode(){ 

        $mid = I('get.mid','','intval');
        $uid = I('get.uid','','intval');        
        $ratio = $this->dis->where(array('partner_id'=>$mid))->getField('ratio');
        $partner = $this->partner->where(array('id'=>$mid))->find();
        $dis = (1-$ratio)*C('consu')*100;
        $yue = $this->users->where(array('id'=>$uid))->getField('yue');
        //file_put_contents('/tmp/payres.log',var_export($yue, true).'##'.var_export($uid,true).'@@',FILE_APPEND);
        // 设置积分规则sesion
        $rule = $this->intergral->where(array('id'=>'1'))->find();
        //每消费一元返10个积分，
        session('consume',$rule['consume']);
        //每评论一次返10个积分,
        session('comment',$rule['comment']);
        //每分享一次返5个积分，
        session('share',$rule['share']);
        //成功邀请一个粉丝返20个积分, 
        session('invite',$rule['share']);
        //新用户注册送100积分
        session('register',$rule['register']);
        $data = array(
            'dis'=>$dis,                    //返现折扣
            'mid'=>$mid,                    //商户id
            'title'=>$partner['title'],        //商户名称
            'discount'=>$partner['discount'],
            'yue'=>$yue,
            'uid'=>$uid,
        );
        $this->assign('data',$data);
        $this->display();
    }
    // 1st end

    /*
    *   支付宝支付  2nd  start
    *   @param：获取get到的信息
    *   实现功能：支付宝支付成功（用户没有登陆的情况下）
    */
    public function alipay(){

        

        //  获取get到的信息
        $_data = array(            
            'mid'=>I('get.mid', '', 'intval'),                      //  商户id
            'all'=>round(I('get.origin','','doubleval'),2),         //  支付的总额
            'noJoin'=>round(I('get.bucan','','doubleval'),2),       //  不参与打折的金额            
            'uid'=>I('get.uid', '', 'intval'),                      //  用户id
        );        

        //  支付金额不能大于2000  并且不能小于0
        if($_data['all'] > self::MAX || $_data['all'] <= self::ZERO || $_data['all'] == ''){
            $word = '订单异常!您的支付金额不正确，请输入正确的支付金额(大于0小于2000)';
            $this->assign('word', $word);
            $this->display('error');
            die;
        }
        //file_put_contents('/tmp/payres.log',var_export($_data, true).'>>',FILE_APPEND);
        //  如果实际支付的金额大于不参与打折的金额
        if($_data['noJoin'] > $_data['all']){ 
            $word = '支付异常！请输入正确的支付金额！(不参与打折的金额不能大于支付金额！)';
            $this->assign('word', $word);
            $this->display('error');
            die;
        }        
        
        if($_data['noJoin'] > self::ZERO){
            $origin = $_data['all'] - $_data['noJoin'];    //商家的利润
        }else{
            $origin = $_data['all'];
        }        
                
        $merchant = $this->partner->where(array('id'=>$_data['mid']))->find();  
        $title = $merchant['title'];
        $city_id = $merchant['city_id'];
        $cate = $merchant['group_id'];

        $ratio = $this->dis->where(array('partner_id'=>$_data['mid']))->getField('ratio');
        $benjin = $origin * $ratio;        
        $fenchenge = $origin - $benjin;        
        $type = '3';
       
        $time = time();
        $trade_no = $all*100;
        $rand = rand(100,999);
        $order_no = date('mdHis',$time).$type.$mid.$trade_no.$rand;              

        $data = array();
        $pay_id = $order_no.'_'.$_data['all'];

        // 获取用户的余额
        if($_data['uid']){
            $yue = $this->users->where(array('id'=>$_data['uid']))->getField('yue');
        }
        
        //添加用户订单到返利订单
        $order_data = array(                
            'mid'=>$_data['mid'],                  //用户的id            
            'order_no'=>$order_no,        //订单号
            'goods_name'=>$title,         //商家的名称
            'prices'=>$_data['all'],               //用户支付总额
            'nojoin_cash'=>$_data['noJoin'],       //不参与打折的金额
            'rebate_money'=>$fenchenge,   //利润额
            'capital'=>$benjin,           //商家的本金
            'pay_status'=>'unpay',        //支付状态                            
            'create_time'=>time(),        //下单时间
            'read'=>'N',                  //是否已读
            'pay_type'=>'alipay',     //  支付宝支付
            'city'=>$city_id,                  //城市id
            'category'=>$cate,              //分类id            
        );
        if ($_data['uid']) {
            $order_data['uid'] = $_data['uid'];        
        }
        $order = $this->orders->add($order_data);
        $data = array(
            'trade_no'=>$order_no,
            'name'=>$title,
            'prices'=>$_data['all'],
            'desc'=>$title,
        );
        $alipay = new \pcAlipay();
        if(!empty($yue) && $yue >= $_data['all']){
            $res = array(
                'pay_type'=>'credit',
                'pay_time'=>time(),
                'pay_status'=>'pay',
            );
            $new_order = $this->orders->where(array('id'=>$order))->save($res);
            $this->users->where(array('id'=>$_data['uid']))->setDec('yue',$_data['all']);
            $back_data = $this->payBack($order_no);            
            $this->success($order_no);
            die;
        }elseif($yue < $_data['all']){
            $res = array('pay_type'=>'ali_cre','cash'=>$yue);
            $new_order = $this->orders->where(array('id'=>$order))->save($res);
            $data['prices'] = round(($_data['all'] - $yue),2);
            //file_put_contents('/tmp/payres.log',var_export($data, true).'$$',FILE_APPEND);                    
        }
        $return =  $alipay->doPay($data);
        if($return){
            echo '支付成功！';
            die ;            
        }else{
            echo '支付失败';
            die;
        }            
    }
    //  2st   end

    /*
    *   异步通知地址     3th  start
    *
    */
    public function notify_url(){
        // $order = I('get.order_no', '', 'trim');
        // if($order){
        //     $res = $this->orders->where(array('order_no'=>$_POST['out_trade_no']))->find();
        // }else{
            $res = $this->orders->where(array('order_no'=>$_POST['out_trade_no']))->find();
        //}
        
        //file_put_contents('/tmp/payres.log',var_export($res, true).'^^',FILE_APPEND);
        //  判断是否支付成功
        if($_POST['trade_status'] = 'TRADE_SUCCESS'){
            // 获取用户id
            $user = $this->users->where(array('unionid'=>$_POST['buyer_id']))->find();
            if($user){
                $uid = $user['id'];
            }
            //  支付成功，修改本地订单的状态
            $ali_data = array(
                'uid'=>$uid,
                'vid'=>$_POST['trade_no'],
                'pay_time'=>time(),
                'pay_status'=>'pay',
            );
            if($res['pay_type'] = 'ali_cre'){                
                $this->users->where(array('id'=>$res['uid']))->setField('yue','0');
            }
            $order_data = $this->orders->where(array('order_no'=>$_POST['out_trade_no']))->save($ali_data);                        

            if($order_data){
                //开启事物
                $model = M();
                $model->startTrans();
                if($res['pay_type'] == 'alipay'){
                    if(!$res){
                        $model->rollback();                
                        return false;
                    }
                }    
                $back_data = $this->payBack($_POST['out_trade_no']);
                if($back_data == false){
                    $model->rollback();                
                    return false;
                }                 
                $model->commit();    
            }
            
        }        
    }
    //  3th  end

    /*
    *   同步跳转地址  4th start
    *
    */
    public function return_url(){
        //商户订单号
        $out_trade_no = htmlspecialchars($_GET['out_trade_no']);    
        
        
        //支付宝交易号
        $trade_no = htmlspecialchars($_GET['trade_no']);        
        //  获取订单信息           
        $order_data = $this->orders->where(array('order_no'=>$out_trade_no))->find();
        
        //  获取用户信息
        $wxuser = $this->users->where(array('id'=>$order_data['uid']))->find();
        $merchant = $this->partner->where(array('id'=>$order_data['mid']))->find();
        $fanli = substr(sprintf("%.3f", $order_data['rebate_money']*C('consu')),0,-1);
        if($wxuser['mobile'] == ''){
            $mobile = '未绑定';
        }else{
            $mobile = substr($wxuser['mobile'], 0, 3) . '****' . substr($wxuser['mobile'], -4, 4);            
        }
        $return_data=array(
            'uid'=>$order_data['uid'],
            'unionid'=>$wxuser['unionid'],
            'pay_type'=>$order_data['pay_type'],            
            'order'=>$order_data['order_no'],            
            'mobile'=>$mobile,
            'merchant'=>$merchant['title'],
            'mid'=>$merchant['id'],
            'no_prices'=>$order_data['nojoin_cash'],
            'prices'=>$order_data['prices'],
            'rebate'=>$fanli,
            'time'=>$order_data['create_time'],
        );
        //file_put_contents('/tmp/payres.log',var_export($order_data, true).'$$',FILE_APPEND);
        $this->assign('return_data',$return_data);
        $this->display('return_url');        
    }
    // 4th end

    /*
    *   支付成功跳转地址  4th start
    *
    */
    public function success($order_no){            
            
        //  获取订单信息           
        $order_data = $this->orders->where(array('order_no'=>$order_no))->find();
        
        //  获取用户信息
        $wxuser = $this->users->where(array('id'=>$order_data['uid']))->find();
        $merchant = $this->partner->where(array('id'=>$order_data['mid']))->find();
        $fanli = substr(sprintf("%.3f", $order_data['rebate_money']*C('consu')),0,-1);
        if($wxuser['mobile'] == ''){
            $mobile = '未绑定';
        }else{
            $mobile = substr($wxuser['mobile'], 0, 3) . '****' . substr($wxuser['mobile'], -4, 4);            
        }
        $return_data=array(
            'uid'=>$order_data['uid'],
            'unionid'=>$wxuser['unionid'],
            'pay_type'=>$order_data['pay_type'],            
            'order'=>$order_data['order_no'],            
            'mobile'=>$mobile,
            'merchant'=>$merchant['title'],
            'mid'=>$merchant['id'],
            'no_prices'=>$order_data['nojoin_cash'],
            'prices'=>$order_data['prices'],
            'rebate'=>$fanli,
            'time'=>$order_data['create_time'],
        );
        
        $this->assign('return_data',$return_data);
        $this->display('return_url');        
    }
    // 4th end

    /*
    *   给个级别的用户返现      5th start
    *   
    */
    public function payBack($order){
        $res = $this->orders->where(array('order_no'=>$order))->find();
        $user = $this->users->where(array('id'=>$res['uid']))->find();
        // 发送短信，发送模版消息
        $fanli = substr(sprintf("%.3f", $res['rebate_money']*self::CONSU_RATIO),0,-1);    //0.25消费者，代理返现比例
        $mobile = $this->partner->where(array('id'=>$res['mid']))->getField('mobile');        
        $pushData = array(
            'uid'=>$user['openid'],         //  用户id
            'title'=>$res['goods_name'],    //  商品名称
            'fanli'=>$fanli,                //  用户实际返利金额
            'prices'=>$res['prices'],       //  用户实际支付金额
            'time'=>$res['create_time'],    //  用户实际支付时间
            'order_no'=>$res['order_no'],   //  用户订单订单号
            'mobile'=>$mobile,              //  商家电话
            'mid'=>$res['mid'],             //  商户id
            'user'=>$user['cardid'],        //  用户的买单返id
            'name'=>$user['nickname'],      //  用户的昵称
        );
        
        $msgData = $this->paySuccessSendSms($pushData);
        $alipay = new \pcAlipay();
        //  支付成功后，
        //  给用户发送返利提醒        
        $userData = $alipay->sendMsg($pushData, 'user');
        //  给商家发短信，给商家发送模版消息        
        $parData = $alipay->sendMsg($pushData, 'partner');            
        //  新用户注册邀请通知
        $newData = $alipay->sendMsg($pushData, 'newuser');


        //  商家本金
        $capital = $res['capital'];
        //  返利总金额
        $fenchenge = $res['rebate_money'];
        //  商家id
        $mid = $res['mid'];
        //  用户id
        $uid = $res['uid'];
        //  订单号
        $order_no = $res['order_no'];

        //  购物送积分，买多钱送多少积分,添加积分记录        
        $order = 'mdb'.$uid.time();
        $reg_score = floor($res['prices'])*session('consume');
                
        $score = $this->users->where(array('id'=>$uid))->setInc('score',$reg_score);        
        $_score = array(
            'uid'=>$uid,
            'begin_point'=>$reg_score,
            'order'=>$order,
            'create_time'=>time(),
            'end_time'=>time()+60*60*24*365,
            'status'=>'Y',
            'type'=>'expend',
            'code'=>'wechat',
            'appid'=>$res['order_no'],
        );
        $jifen = $this->addjf->add($_score);

        //  商家所在地区的代理id
        $agent = $this->partner->where(array('id'=>$mid))->getField('agent_id');
        if(!$agent){
            $agent_id = $mid;
        }
        //  用户的信息
        $wxusers = $this->users->where(array('id'=>$uid))->find();

        //  给商家返还本金，给自己返0.5的利润额，给上一级返回0.2的利润额，给上上以及返还0.1的利润额，
        //  给代理返还0.1的利润额,给公司返还0.1的利润额

        //  2017.02.16 调整各个阶层的返现比例
        $consu = substr(sprintf("%.3f", $fenchenge*self::CONSU_RATIO),0,-1);    //0.25消费者，代理返现比例
        $fansf = substr(sprintf("%.3f", $fenchenge*self::FANSF_RATIO),0,-1);    //0.20上级返现比例
        $merch = substr(sprintf("%.3f", $fenchenge*self::MERCH_RATIO),0,-1);    //0.15商户返现比例
        $compa = substr(sprintf("%.3f", $fenchenge*self::COMPA_RATIO),0,-1);    //0.1代理返现比例
        $fanss = substr(sprintf("%.3f", $fenchenge*self::FANSS_RATIO),0,-1);    //0.05上上级返现比例
        //file_put_contents('/tmp/payres.log',var_export($res['order_no'], true).'!!',FILE_APPEND);
        $order_money = $this->money->where(array('fanli_order'=>$order_no))->find();
        
        if(!$order_money){
            //  如果该订单已经支付进行返利
            if($res['pay_status'] == 'pay'){
                //  1,先给商家，2,自己，3,公司，4,代理进行返利,这几个用户的钱是定额，是必须要返利的
                //  1,给商家返还本金,添加到返利金额表                
                $merchant_data = array(
                    'fanli_order'=>$order_no,
                    'mid'=>$mid,
                    'uid'=>$uid,
                    'fan_money'=>$capital,               
                    'create_time'=>time(),
                    'status'=>'N',            
                    'user_attr'=>'merchant',            
                );
                $merchant = $this->money->add($merchant_data);
                //  增加商家余额
                $merchant_credit = $this->partner->where(array('id'=>$mid))->setInc('yue',$capital);   
                $time = time();
                //if($uid == '9237'){
                    //  1.1给商家绑定的微信也就是店主返利10%(2017.2.16改为15%)                    
                    $xid = $this->partner->where(array('id'=>$mid))->getField('xid');
                    if($xid){
                        $fdid = $this->partner->where(array('id'=>$xid))->getField('id');
                    }else{
                        $fdid = $mid;
                    }
                    $umid = $this->users->where(array('mid'=>$fdid))->getField('id');
                    $merchantes_data = array(
                        'fanli_order'=>$order_no,
                        'mid'=>$mid,
                        'uid'=>$umid,
                        'fan_money'=>$merch,               
                        'create_time'=>time(),
                        'status'=>'N',            
                        'user_attr'=>'user',            
                    );
                    $merchantes = $this->money->add($merchantes_data);
                    //增加商家余额
                    $user_mid = $this->users->where(array('mid'=>$fdid))->setInc('yue',$merch);
                    //  添加临时数据
                    //  商家返利10%  merchant_15_per  
                    $contant = '商家返利'.$merch;                                        
                    $mer_data = array(
                        'type'=>'merchant_15_per',
                        'status'=>'Y',
                        'contant'=>$contant,
                        'create_time'=>time(),
                    );
                    $tmp = $this->tmp->add($mer_data);
                //}
                

                //  2，给自己返利，添加到返利金额表(2016.2.16调整返现比例为0.25)                
                $user_data = array(
                    'fanli_order'=>$order_no,
                    'mid'=>$mid,
                    'uid'=>$uid,
                    'fan_money'=>$consu,                                                    
                    'create_time'=>time(),
                    'status'=>'N',            
                    'user_attr'=>'user',            
                );
                //file_put_contents('/tmp/payres.log',var_export($user_data, true).'@@',FILE_APPEND);
                $wechatuser = $this->money->add($user_data);
                //file_put_contents('/tmp/payres.log',var_export($wechatuser, true).'##',FILE_APPEND);
                //  增加自己的余额     
                $wechatuser_credit = $this->users->where(array('id'=>$uid))->setInc('yue',$consu);
                //  添加临时数据
                //  用户返利25%  user_25_per
                $contant = '用户返利'.$consu;
                $user_per = array(
                    'type'=>'user_25_per',
                    'status'=>'Y',
                    'contant'=>$contant,
                    'create_time'=>time(),
                );
                $tmp = $this->tmp->add($user_per);       
                
                //if($uid == '9237'){
                // }else{
                //     $wechatuser_credit = $this->users->where(array('id'=>$uid))->setInc('yue',$user_ratio);    
                // }
                
                //  3,给公司返利，添加到返利金额表(2017.2.16调整返现比例为0.1)          
                $company_data = array(
                    'fanli_order'=>$order_no,
                    'mid'=>$mid,
                    'uid'=>'0',
                    'fan_money'=>$compa,              
                    'create_time'=>time(),
                    'status'=>'N', 
                    'user_attr'=>'company',          
                );
                $company = $this->money->add($company_data);                
                //  添加临时数据
                //  代理返利25%  company_10_per
                $contant = '公司返利'.$compa;
                $compan_data = array(
                    'type'=>'company_10_per',
                    'status'=>'Y',
                    'contant'=>$contant,
                    'create_time'=>time(),
                );
                $tmp = $this->tmp->add($compan_data);

                //  4,给代理返利，添加到返利金额表(2017.2.16调整返现比例0.25)                           
                $agent_data = array(
                    'fanli_order'=>$order_no,
                    'mid'=>$mid,
                    'uid'=>'0',
                    'fan_money'=>$consu,               
                    'create_time'=>time(),
                    'status'=>'N',
                    'user_attr'=>'agent',          
                );
                $agent = $this->money->add($agent_data);            
                //  增加代理的余额
                $agent_credit = $this->agent->where(array('id'=>$agent_id))->setInc('yue',$consu);
                //  添加临时数据
                //  代理返利25%  agent_25_per
                $contant = '代理返利'.$consu;
                $agent_per = array(
                    'type'=>'agent_25_per',
                    'status'=>'Y',
                    'contant'=>$contant,
                    'create_time'=>time(),
                );
                $tmp = $this->tmp->add($user_per);
                

                //  判断用户是否存在上级和上上级，如果有上级给上级返利20%，有上上级给返利10%
                if($wxusers['fid']){
                    //  给上级返利20%
                    //  如果有上级，没有上上级，给上级返利20%，给公司返利10%
                    $up_data = array(
                        'fanli_order'=>$order_no,
                        'mid'=>$mid,
                        'uid'=>$wxusers['fid'],
                        'fan_money'=>$fansf,               
                        'create_time'=>time(),
                        'status'=>'N',
                        'user_attr'=>'user',          
                    );
                    $up = $this->money->add($up_data);
                    //  给上级用户增加余额
                    //$ufid = $this->users->where(array('id'=>$wxusers['fid']))->getField('cardid');
                    $up_credit = $this->users->where(array('id'=>$wxusers['fid']))->setInc('yue',$fansf);
                    // if(!$ufid){
                    //     $ufid_credit = $this->users->where(array('id'=>'3'))->setInc('yue',$sup_ratio);
                    // }else{
                           
                    // }                    
                    if($wxusers['gfid']){
                        $upwx_data = array(
                            'fanli_order'=>$order_no,
                            'mid'=>$mid,
                            'uid'=>$wxusers['gfid'],
                            'fan_money'=>$fanss,               
                            'create_time'=>time(),
                            'status'=>'N',
                            'user_attr'=>'user',          
                        );
                        $upwx = $this->money->add($upwx_data); 
                        //  给上级用户增加余额
                        //$gfid = $this->users->where(array('id'=>$wxusers['gfid']))->getField('cardid');
                        $upwx_credit = $this->users->where(array('id'=>$wxusers['gfid']))->setInc('yue',$fanss); 
                        // if(!$gfid){
                        //     $ugfid_credit = $this->users->where(array('id'=>'3'))->setInc('yue',$com_ratio);
                        // }else{
                               
                        // }                        
                    }else{
                        $company_data = array(
                            'fanli_order'=>$order_no,
                            'mid'=>$mid,
                            'uid'=>'0',
                            'fan_money'=>$com_ratio,               
                            'create_time'=>time(),
                            'status'=>'N', 
                            'user_attr'=>'company',          
                        );
                        $company = $this->money->add($company_data);
                    }
                }else{
                    //  没有上级，没有上上级，公司返利30%
                    $all_ratio = $com_ratio + $sup_ratio;
                    $company_data = array(
                        'fanli_order'=>$order_no,
                        'mid'=>$mid,
                        'uid'=>'0',
                        'fan_money'=>$all_ratio,               
                        'create_time'=>time(),
                        'status'=>'N', 
                        'user_attr'=>'company',          
                    );
                    $company = $this->money->add($company_data);
                }

                //  做推送，发5块，发短信
                //  返5块
                //  给新用户返现5元  判断该用户在订单里面有没有产生订单并且支付成功
                
                $map =array(
                    'uid'=>$res['uid'],
                    'pay_status'=>'pay',
                    'pay_type'=>'wapwechatpay',
                );
                $sumOrder = $this->orders->where($map)->count('id');
                if($sumOrder == 1){
                    //用户的原来余额 + 红包的5块  2012.12.21 需求改动给商户返2块 mpp
                    //  新用户支付10块以上，给商家返2块
                    if($res['pay_type'] == 'wapwechatpay' && $res['pay_status'] == 'pay' && $res['prices'] >= self::TEN){
                        //$yueUser = $this->users->where(array('id'=>$res['uid']))->setInc('yue',self::FIVE);
                        //  给商户返2块
                        $xid = $this->partner->where(array('id'=>$res['mid']))->getField('xid');
                        if($xid){
                            $merchant = $this->users->where(array('mid'=>$xid))->find();                            
                            if($merchant){
                                $mid_yue = $this->users->where(array('mid'=>$xid))->setInc('yue',self::TWO);
                                //  给该订单增加标识
                                $order =array('neworder'=>'Y');
                                $new = $this->orders->where(array('order_no'=>$res['order_no']))->save($order);
                            }                            
                        }else{
                            $merchant = $this->users->where(array('mid'=>$res['mid']))->find();                            
                            if($merchant){
                                $mid_yue = $this->users->where(array('mid'=>$res['mid']))->setInc('yue',self::TWO);
                                //  给该订单增加标识
                                $order =array('neworder'=>'Y');
                                $new = $this->orders->where(array('order_no'=>$res['order_no']))->save($order);
                            }                            
                        }                        
                    }                    
                }
                //  构建推送data
                // 2017.2.16调整返现比例
                $time = time();           
                $fanli = substr(sprintf("%.3f", $res['rebate_money']*C('consu')),0,-1);     
                $nickname = $this->users->where(array('id'=>$res['uid']))->getField('nickname');
                $merchant = $this->partner->where(array('id'=>$res['mid']))->field('id,title,mobile,xid')->find();
                //判断是给总店发信息还是分店
                /*if($merchant['xid']){
                    $x_mobile = $this->partner->where(array('id'=>$merchant['xid']))->getField('mobile');
                    $mobile = $x_mobile;
                }else {
                    $mobile = $merchant['mobile'];
                }        */        
                $tsdata = array(
                    'uid'=>$res['uid'],
                    'mid'=>$merchant['id'],
                    'user'=>$nickname,
                    'order_id'=>$res['order_no'],
                    'merchant'=>$merchant['title'],
                    'mobile'=>$merchant['mobile'],
                    'nojoin_cash'=>$res['nojoin_cash'],
                    'cash'=>$res['cash'],
                    'prices'=>$res['prices'],
                    'capital'=>$res['capital'],
                    'fanli'=>$fanli,
                    'pay_time'=>date('Y-m-d H:i',$res['pay_time']),            
                    'time'=>date('Y-m-d H:i',$res['create_time']),
                );                
                //推送消息      
                //  给个人推送返利提醒
                $wedata = $this->_WeiXinSendMsg($tsdata, 'buy');   
                //file_put_contents('/tmp/res.log',var_export($wedata, true).'$$',FILE_APPEND);
                //  给商家发短信
                $dat = $this->paySuccessSendSms($tsdata);
                //file_put_contents('/tmp/res.log',var_export($dat, true).'%%',FILE_APPEND);
                //  给商家客服推送
                //  2017.1.4给客服推送消息改                
                $_where=array('kid'=>$res['mid']);
                $umerchant = $this->users->where($_where)->getField('id',true);                
                
                if($umerchant){                                  
                    foreach ($umerchant as  $v) {
                        $mdata = array(
                            'uid'=>$v,
                            'mid'=>$merchant['id'],
                            'nickname'=>$nickname,
                            'merchant'=>$merchant['title'],
                            'mobile'=>$merchant['mobile'],
                            'jine'=>$res['prices'],
                            'capital'=>$res['capital'],
                            'fanli'=>$fanli,
                            'order'=>$res['order_no'],
                            'pay_time'=>date('Y-m-d H:i',$res['pay_time']),            
                            'time'=>date('Y-m-d H:i',$res['create_time']),
                        );                
                        $data = $this->_WeiXinSendMsg($mdata, 'merchant');                                       
                        //file_put_contents('/tmp/res.log',var_export($data, true).'((',FILE_APPEND);
                    }
                }
                $__where=array('kid'=>$res['mid']);
                $ukeid = $this->customer->where($__where)->group('uid')->getField('uid',true);                
                if($ukeid){                                  
                    foreach ($ukeid as  $v) {
                        $ukedata = array(
                            'uid'=>$v,
                            'mid'=>$merchant['id'],
                            'nickname'=>$nickname,
                            'merchant'=>$merchant['title'],
                            'mobile'=>$merchant['mobile'],
                            'jine'=>$res['prices'],
                            'capital'=>$res['capital'],
                            'fanli'=>$fanli,
                            'order'=>$res['order_no'],
                            'pay_time'=>date('Y-m-d H:i',$res['pay_time']),
                            'time'=>date('Y-m-d H:i',$res['create_time']),
                        );
                        $ukdata = $this->_WeiXinSendMsg($ukedata, 'merchant');            
                        //file_put_contents('/tmp/res.log',var_export($ukdata, true).'||',FILE_APPEND);            
                    }
                }
                return  true;
            }else{
                return false;
            }  
        }
    }
    // 5th end

    /**
     * 支付成功后发送短信     6th start
     * @param type $orderRes
     * @param type $teamRes
     * @return type
     */
    public function paySuccessSendSms($orderRes) {
        date_default_timezone_set('Asia/Hong_Kong');
        $order = $this->sendsms->where(array('order'=>$orderRes['order_no']))->find();
        file_put_contents('/tmp/payres.log',var_export($orderRes, true).'@@',FILE_APPEND);
        if(!$order){
            $time =  date('Y-m-d H:i',$orderRes['time']);    
            $origin = round(($orderRes['prices']),2);  
            file_put_contents('/tmp/payres.log',var_export($origin, true).'##',FILE_APPEND);  
            $sendSms = new \Common\Org\sendSms();                
            //您的买单返刚刚完成一笔收款：2016-9-30 10:22:14  用户 XXXXX  金额 XXXXX
            $content = "您的买单返刚刚完成一笔收款：{$time}，用户:{$orderRes['name']}，金额：{$origin}";            
            $res = $sendSms->ClySendSms($orderRes['mobile'], $content);        
            $data = array(
                'mid'=>$orderRes['mid'],
                'mobile'=>$orderRes['mobile'],
                'order'=>$orderRes['order_no'],
                'time'=>$orderRes['time'],
            );
            $this->sendsms->add($data);
            file_put_contents('/tmp/payres.log',var_export($res, true).'$$',FILE_APPEND);        
            \Think\Log::write('短信提示结果: '.var_export($res,true), 'INFO');
            return $res;
        }                    
    }
    // 6th end
}