<?php

namespace Common\Org;

require_once __DIR__ . "/wappay/buildermodel/AliConterBuilder.php";
require_once __DIR__ . "/wappay/service/AlipayTradeService.php";
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/wappay/service/AopClient.php";
require_once __DIR__ . "/aop/request/AlipayOpenPublicMessageSingleSendRequest.php";
require_once __DIR__ . "/aop/request/AlipayFundTransToaccountTransferRequest.php";

class pcAlipay {

    private $ali_conter = null;
    private $ali_pay_trade = null;    
    private $aop_client = null;    
    private $single_msg = null;    
    
    //   同步跳转
    const RETURN_URL = 'http://hotel.doufenger.com/Alipay/notify_url';    
    //  异步通知地址
    const NOTIFY_URL = 'http://hotel.doufenger.com/Alipay/return_url';

    /**
     * 构造函数
     */
    public function __construct() {
        $this->ali_conter = new \AliConterBuilder();
        $this->ali_pay_trade = new \AlipayTradeService();        
        $this->aop_client = new \AopClient();        
        $this->single_msg = new \AlipayOpenPublicMessageSingleSendRequest();        
        $this->fund_trans = new \AlipayFundTransToaccountTransferRequest();        
    }

    /**
     * 支付宝支付
     * @param $data
     * @param $option
     * @return array
     * @throws WxPayException
     */
    public function doPay($data) {

        $money = $data['money'];
        $this->ali_conter->setBody($data['title']);
        $this->ali_conter->setSubject($data['title']);
        $this->ali_conter->setOutTradeNo($data['trade_no']);
        $this->ali_conter->setTotalAmount($money);
        $this->ali_conter->setTimeExpress("1m");

        $result=$this->ali_pay_trade->wapPay($this->ali_conter,self::NOTIFY_URL,self::RETURN_URL);

        if($result){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 企业付款测试
     */
    public function rebate($data){        
        $this->aop_client->gatewayUrl = C('GATEWAY_URL');
        $this->aop_client->appId = C('ALIPAYID');
        $this->aop_client->rsaPrivateKey = C('RSAKEY');
        $this->aop_client->alipayrsaPublicKey= C('ALI_KEY');
        $this->aop_client->apiVersion = '1.0';
        $this->aop_client->signType = 'RSA2';
        $this->aop_client->postCharset='UTF-8';
        $this->aop_client->format='json';        
        $sendData = array(
            'out_biz_no'=>$data['order'],           //  我方生成的提现订单号
            'payee_type'=>'ALIPAY_USERID',         //  收款方账户类型   ALIPAY_USERID：支付宝账号对应的支付宝唯一用户号
            'payee_account'=>$data['unionid'],      //  收款方的unionid
            'amount'=>$data['jine'],                //  转账金额单位元，支持2位小数
            'payer_real_name'=>'',                  //  付款方真实姓名（可选）
            'payer_show_name'=>'',                  //  付款方显示姓名（可选）
            'payee_real_name'=>'',                  //  收款方真实姓名（可选）
            'remark'=>'转账备注',
            'ext_param'=>array(
                'order_title'=>'支付宝用户提现',
            ),
        );        
        $json_data = urldecode(json_encode($sendData));
        $this->fund_trans->setBizContent($json_data);
        $result = $this->aop_client->execute ( $this->fund_trans);         
        $responseNode = str_replace(".", "_", $this->fund_trans->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;        
        if(!empty($resultCode)&&$resultCode == 10000){            
            //  更改提现订单状态  order_id
            $_data = array('pay_time'=>time(),'status'=>'Y','vid'=>$result->$responseNode->order_id);                
            $result = M('fanli_cash')->where(array('order'=>$data['order']))->save($_data);   
            return true;
        } else {            
            return false;
        }
    }

    /**
    *   支付宝推送消息
     *  @param $data 消息内容
     *  @param $act 方法
    */
    public function sendMsg($data,$act){      
        $this->aop_client->gatewayUrl = C('GATEWAY_URL');
        $this->aop_client->appId = C('ALIPAYID');
        $this->aop_client->rsaPrivateKey = C('RSAKEY');
        $this->aop_client->alipayrsaPublicKey= C('ALI_KEY');
        $this->aop_client->apiVersion = '1.0';
        $this->aop_client->signType = 'RSA2';
        $this->aop_client->postCharset='UTF-8';
        $this->aop_client->format='json';
                
        if($act == 'rebate'){
            // 返现到账通知
            $result = $this->sendUserData($data);
            return true;          
        }elseif($act == 'paied'){            
            // 支付成功通知
            $result = $this->sendPartnData($data);
            return true;
        }elseif($act == 'register'){
            // 新用户注册通知
            $result = $this->sendNewData($data);
            return true;
        }elseif ($act == 'invite') {
            // 邀请注册成功通知
            $result = $this->sendMsgNotice($data,$act);
            return true;
        }elseif($act == 'refund'){
            // 退款通知
            $result = $this->sendMsgNotice($data,$act);
            return true;
        }elseif($act == 'arrival'){
            // 提现到账通知
            $result = $this->sendMsgNotice($data,$act);
            return true;
        }
        
        $responseNode = str_replace(".", "_", $this->single_msg->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;        
        if(!empty($resultCode)&&$resultCode == 10000){
            return true;
        } else {            
            return false;
        }
    }

    /**
    *   通知消息   邀请注册成功通知
    *   @param $data 消息内容
    */
    public function sendMsgNotice($data,$act){
        if($act == 'invite'){
            $content = '有人通过您的邀请，并成功注册.';
            $remark = '感谢您的使用，谢谢。';                
            $details = '用户：'.$data['user'].'，通过您的邀请，成功注册买单返！';
        }elseif($act == 'refund'){
            if($data['type'] == 'alipay'){
                $type = '支付宝';
            }elseif($data['type'] == 'wechatpay'){
                $type = '微信';
            }
            // 退款通知
            $content = '您好，你的提现审核未通过，已退款。';
            $remark = '如有疑问，请致电客服！';                
            $details = '尊敬的用户:'.$data['user'].'你好，你的审核未通过，现已完成退款，退款金额为:'.$data['jine'].'元。';
        }elseif($act == 'arrival'){
            // 提现到账通知
            $content = '你好，你的提现审核已经通过，请在您的余额进行查看。';
            $remark = '感谢您的使用，谢谢。';                
            $details = '尊敬的用户：'.$data['user'].'，您好，你的提现审核已经通过，提现金额为：'.$data['jine'].'请及时查看您的余额。';
        }
        
        $sendData = array(
            'to_user_id'=>$data['uid'],
            'template'=>array(
                'template_id'=>'cd1223207af74ef6ab24a38a75499208',
                'context'=>array(
                    'head_color'=>'#e60827',
                    //'url'=>$url,
                    //'action_name'=>'点击查看',
                    // 时间
                    'keyword1'=>array(
                        'value'=>$data['time'],
                    ),
                    // 内容
                    'keyword2'=>array(
                        'value'=>$details,
                    ),
                    'first'=>array(
                        'value'=>$content,
                    ),
                    'remark'=>array(
                        'value'=>$remark,
                    ),
                ),
            ),
        );
        $json_data = urldecode(json_encode($sendData));
        $this->single_msg->setBizContent($json_data);
        return $result = $this->aop_client->execute ( $this->single_msg);        
    }

    /**
    *   返现到账通知
    *   @param $data 消息内容
    */
    public function sendUserData($data){
        $content = '尊敬的用户您好，您的消费返利已到账。';
        $remark = '感谢您的使用，谢谢。';        
        $details = '您在'.$data['title'].'商户消费'.$data['prices'].'元，获得返利'.$data['fanli'].'元。';
        $url = 'http://api.maidanfan.la/public/getHongBao/order_id/'.$data['order_no'].'.html';
        $sendData = array(
            'to_user_id'=>$data['uid'],
            'template'=>array(
                'template_id'=>'6581721b800b4b1588cbf15e132ed75f',
                'context'=>array(
                    'head_color'=>'#e60827',
                    'url'=>$url,
                    'action_name'=>'点击查看',
                    // 返现金额
                    'keyword1'=>array(                        
                        'value'=>$data['fanli'],
                    ),
                    // 返现说明
                    'keyword2'=>array(                        
                        'value'=>$details,
                    ),
                    'first'=>array(                        
                        'value'=>$content,
                    ),
                    'remark'=>array(                        
                        'value'=>$remark,
                    ),
                ),
            ),
        );
        $json_data = urldecode(json_encode($sendData));
        $this->single_msg->setBizContent($json_data);
        return $result = $this->aop_client->execute ( $this->single_msg);        
    }

    /**
    *   支付成功通知
    *   @param $data 消息内容
    */
    public function sendPartnData($data){
        $content = '您好，您的订单'.$data['order_no'].'支付成功。';        
        $remark = '请及时前往查看!';
        $sendData = array(
            'to_user_id'=>$data['muid'],
            'template'=>array(
                'template_id'=>'b3ccbbe330dd4805a630860bc320ce8a',
                'context'=>array(
                    'head_color'=>'#e60827',
                    //'url'=>'http://m.baidu.com',
                    //'action_name'=>'点击查看',
                    // 消费店铺                    
                    'keyword1'=>array(                        
                        'value'=>$data['title'],
                    ),
                    // 订单金额                    
                    'keyword2'=>array(                        
                        'value'=>$data['prices'],
                    ),
                    // 订单编号                    
                    'keyword3'=>array(                        
                        'value'=>$data['order_no'],
                    ),
                    // 支付时间
                    'keyword4'=>array(                        
                        'value'=>$data['time'],
                    ),
                    'first'=>array(                        
                        'value'=>$content,
                    ),
                    'remark'=>array(                        
                        'value'=>$remark,
                    ),
                ),
            ),
        );
        $json_data = urldecode(json_encode($sendData));
        $this->single_msg->setBizContent($json_data);                
        return $result = $this->aop_client->execute ($this->single_msg);        
    }

    /**
    *   新用户注册通知
    *   @param $data 消息内容
    */
    public function sendNewData($data){
        $content = '您好，您已成为【买单返】会员';        
        $remark = '感谢您的信任，我们将为您提供更好的服务';
        $sendData = array(
            'to_user_id'=>$data['uid'],
            'template'=>array(
                'template_id'=>'ea468b2af73a4d34b5066abe5d3c7208',
                'context'=>array(
                    'head_color'=>'#e60827',                    
                    //  用户名
                    'keyword1'=>array(                        
                        'value'=>$data['user'],
                    ),
                    //  注册时间
                    'keyword2'=>array(                        
                        'value'=>$data['time'],
                    ),
                    'first'=>array(                        
                        'value'=>$content,
                    ),
                    'remark'=>array(                        
                        'value'=>$remark,
                    ),
                ),
            ),
        );
        $json_data = urldecode(json_encode($sendData));
        $this->single_msg->setBizContent($json_data);
        return $result = $this->aop_client->execute($this->single_msg);
    }

    

}
