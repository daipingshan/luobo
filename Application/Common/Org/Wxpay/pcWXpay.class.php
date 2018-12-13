<?php



date_default_timezone_set('Asia/Shanghai');
error_reporting(E_ERROR);

require_once __DIR__ . "/lib/WxPay.Api.php";
require_once __DIR__ . "/unit/WxPay.NativePay.php";
require_once __DIR__ . "/unit/WxPay.JsApiPay.php";
require_once __DIR__ . "/unit/WxPay.MchPay.php";

class PcWXpay {

    private $wx_pay_unified_order = null;
    private $wx_pay_native = null;
    private $wx_pay_js = null;

    const WX_QR_CODE_URL = 'http://paysdk.weixin.qq.com/example/qrcode.php';
    const WX_ORDER_REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    const WX_ORDER_Requl_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    public function __construct() {
        $this->wx_pay_unified_order = new \WxPayUnifiedOrder();
        $this->wx_pay_native = new \NativePay();
        $this->wx_pay_js = new \JsApiPay();
        $this->wx_mchpay = new \WxMchPay();
    }
    public function rebate($data)
    {
        if(!isset($data['openid']) || !$data['openid']){
            return array('code'=>-1,'error'=>'缺少openid参数');
        }
        if(!isset($data['id']) || !$data['id']){
            return array('code'=>-1,'error'=>'缺少id参数');
        }
        $where = array('id'=>$data['id']);
        $cash = M('fanli_cash')->where($where)->find();
        $money = $cash['money']*100;
        $this->wx_mchpay->setParameter('openid', $data['openid']);
        $this->wx_mchpay->setParameter('partner_trade_no', $cash['order']);
        $this->wx_mchpay->setParameter('check_name', 'NO_CHECK');
        $this->wx_mchpay->setParameter('amount', $money);
        $this->wx_mchpay->setParameter('desc', '提现');
        $this->wx_mchpay->setParameter('spbill_create_ip', get_client_ip());
        $postXml = $this->wx_mchpay->createXml();
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $responseXml = $this->wx_mchpay->postXmlSSLCurl($postXml,$url);

        if(!empty($responseXml)) {
            $_data = simplexml_load_string($responseXml, null, LIBXML_NOCDATA);
            $arraydata = (array)$_data;
            if($arraydata['result_code'] != 'FAIL'){
                $_data = array('pay_time'=>time(),'status'=>'Y','vid'=>$arraydata['payment_no']);
                $result = M('fanli_cash')->where($where)->save($_data);
                $data = '提现成功';
                return  json_encode($data);
            }
        }else{
            return false;
        }

    }

    public function doPay($dataa , $option) {
        $money = ($dataa['money']*100);
        $this->wx_pay_unified_order->SetTotal_fee($money);
        $this->wx_pay_unified_order->SetBody($dataa['title']);
        $this->wx_pay_unified_order->SetOut_trade_no($dataa['order_no']);
        $this->wx_pay_unified_order->SetTime_start(date("YmdHis"));
        $this->wx_pay_unified_order->SetTime_expire(date("YmdHis", time() + 600));
        $this->wx_pay_unified_order->SetNotify_url('http://hotel.doufenger.com/index.php/index/index/notify');
        $this->wx_pay_unified_order->SetTrade_type("JSAPI");
        if (!empty($dataa['openid'])) {
            $openId = $dataa['openid'];
        } else {
            $openId = $this->wx_pay_js->GetOpenid($dataa['order_no']);
        }
        $this->wx_pay_unified_order->SetOpenid($openId);

        $order = WxPayApi::unifiedOrder($this->wx_pay_unified_order);

        $jsApiParameters = $this->wx_pay_js->GetJsApiParameters($order);

        $data =  array(
            'pay_data' => json_decode($jsApiParameters,true),
            'wx_data' => $this->wx_pay_js->data,
            'js_pay_config'=>$this->wx_pay_js->js_pay_config,
            'openid'=>$openId,
        );
        return $data;
    }

    public function doPaynew($dataa , $option) {
        $money = ($dataa['money']*100);
        $this->wx_pay_unified_order->SetTotal_fee($money);
        $this->wx_pay_unified_order->SetBody($dataa['product_name']);
        $this->wx_pay_unified_order->SetOut_trade_no($dataa['out_trade_no']);
        $this->wx_pay_unified_order->SetTime_start(date("YmdHis"));
        $this->wx_pay_unified_order->SetTime_expire(date("YmdHis", time() + 600));
        $this->wx_pay_unified_order->SetNotify_url('http://api.maidanfan.la/Wechat/notify');
        $this->wx_pay_unified_order->SetTrade_type("JSAPI");
        $openId = $this->wx_pay_js->GetOpenid($dataa['order_no']);
        //$this->wx_pay_unified_order->SetOpenid($openId);
        $this->wx_pay_unified_order->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($this->wx_pay_unified_order);
        $jsApiParameters = $this->wx_pay_js->GetJsApiParameters($order);
        $data =  array(
            'pay_data' => json_decode($jsApiParameters,true),
            'wx_data' => $this->wx_pay_js->data,
            'js_pay_config'=>$this->wx_pay_js->js_pay_config,
            'openid'=>$openId,

        );
        return $data;
    }

    public function orderQuery($orderId) {
        $input = new \WxPayOrderQuery();
        $input->SetOut_trade_no($orderId);        $res = WxPayApi::orderQuery($input);
        if(isset($res['err_code'])){
            return false;
        }
        return $res;
    }

    public function doWxPayApp($data) {
        $input=new WxPayUnifiedOrder();
        $input->SetAppid(WxPayConfig::APPID);
        $input->SetMch_id(WxPayConfig::MCHID);
        $input->SetNonce_str(WxPayApi::getNonceStr());
        $input->SetBody($data['body']);
        $input->SetOut_trade_no($data['out_trade_no']);
        $input->SetTotal_fee($data['total_fee']);
        $input->SetSpbill_create_ip($data['spbill_create_ip']);
        $input->SetTrade_type($data['trade_type']);
        $input->SetNotify_url($data['notify_url']);
        $md5_key = '';
        if(isset($data['md5_key']) && trim($data['md5_key'])){
            $md5_key = $data['md5_key'];
        }
        $input->SetSign($md5_key);
        $xml = $input->ToXml();
        $response = WxPayApi::postXmlCurl($xml, self::WX_ORDER_Requl_URL, true, 6);
        $result = WxPayResults::Init($response);

        return $this->kappSigin($result);
    }

    public function kappSigin($result)
    {
        $data['appid']=WxPayConfig::APPID;
        $data['partnerid']=WxPayConfig::MCHID;
        $data['prepayid']=$result['prepay_id'];
        $data['package']='Sign=WXPay';
        $data['noncestr']=WxPayApi::getNonceStr();
        $data['timestamp']=time();
        ksort($data);
        $signString = '';
        foreach ($data as $key => $val) {
            $signString.="$key=$val&";
        }
        $signString.="key=" .WxPayConfig::KEY;
        $sign = strtoupper(md5($signString));
        $data['sign']=$sign;
        $data['package_value']='Sign=WXPay';
        return $data;
    }

    public function wxShare($res){
        $wxShareConfig = $this->wx_pay_js->getWxPayConfig();
        //file_put_contents('/tmp/pay.log',var_export($wxShareConfig, true).'>>',FILE_APPEND);
        $data =  array(
            'js_pay_config'=>$this->wx_pay_js->js_pay_config,
            'share_content'=>array(
                'title'=>$res['title'],
                'describe'=>$res['describe'],
                'link'=>$res['link'],
                'img'=>$res['img'],
            ),
        );
        return $data;
    }

    public function scanQRCode(){
        $scanQRCodeConfig =$this->wx_pay_js->getWxPayConfig();
        $data =array(
             'js_pay_config'=>$this->wx_pay_js->js_pay_config,
            );
        return $data;
    }



}
