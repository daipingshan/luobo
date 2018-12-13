<?php


namespace Common\Org;

date_default_timezone_set('Asia/Shanghai');
error_reporting(E_ERROR);

require_once __DIR__ . "/Wxpay/lib/WxPay.Api.php";
require_once __DIR__ . "/Wxpay/unit/WxPay.NativePay.php";
require_once __DIR__ . "/Wxpay/unit/WxPay.JsApiPay.php";
require_once __DIR__ . "/Wxpay/unit/WxPay.MchPay.php";

class pcWXpay{

    private $wx_pay_unified_order = null;
    private $wx_pay_native = null;
    private $wx_pay_js = null;

    const WX_QR_CODE_URL = 'http://qr.topscan.com/api.php?text=';
    const WX_ORDER_REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    const WX_ORDER_Requl_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    public function __construct() {
        $this->wx_pay_unified_order = new \WxPayUnifiedOrder();
        $this->wx_pay_native = new \NativePay();
        $this->wx_pay_js = new \JsApiPay();
        $this->wx_mchpay = new \WxMchPay();
        $this->wx_pay_api = new \WxPayApi();
    }

    /**
     * 微信公众号H5支付
     * @DateTime 2018-10-15
     * @param    [type]     $dataa [description]
     * @return   [type]            [description]
     */
    public function doPay($dataa) {

        $money = ($dataa['money']*100);
        $this->wx_pay_unified_order->SetTotal_fee($money);
        $this->wx_pay_unified_order->SetBody($dataa['title']);
        $this->wx_pay_unified_order->SetOut_trade_no($dataa['order']);
        $this->wx_pay_unified_order->SetTime_start(date("YmdHis"));
        $this->wx_pay_unified_order->SetTime_expire(date("YmdHis", time() + 3000));
        $this->wx_pay_unified_order->SetNotify_url('http://www.luobowang.cn/Pay/notify.html');
        $this->wx_pay_unified_order->SetTrade_type("JSAPI");
        $this->wx_pay_unified_order->SetOpenid($dataa['openid']);

        $order = $this->wx_pay_api->unifiedOrder($this->wx_pay_unified_order);
        $jsApiParameters = $this->wx_pay_js->GetJsApiParameters($order);

        $data =  array(
            'pay_data' => json_decode($jsApiParameters,true),
            'wx_data' => $this->wx_pay_js->data,
            'js_pay_config'=>$this->wx_pay_js->js_pay_config,
            'openid'=>$dataa['openid'],
        );
        return $data;
    }

    /**
     * 扫码支付
     */
    public function getWXpayCode($payId, $title, $product, $payFee, $plat) {
        // 过滤$product
        $product = str_replace("'", "", $product);
        $product = str_replace(" ", "", $product);
        $product = str_replace("+", "", $product);
        $now_time = time();

        $this->wx_pay_unified_order->SetBody($product);
        $this->wx_pay_unified_order->SetAttach($product);
        $this->wx_pay_unified_order->SetOut_trade_no($payId);
        $this->wx_pay_unified_order->SetTotal_fee($payFee);
        $this->wx_pay_unified_order->SetTime_start(date("YmdHis", $now_time));
        $this->wx_pay_unified_order->SetTime_expire(date("YmdHis", $now_time + 3600));
        $this->wx_pay_unified_order->SetGoods_tag(md5($title));
        $this->wx_pay_unified_order->SetNotify_url('http://www.luobowang.cn/Pay/notify.html');
        $this->wx_pay_unified_order->SetTrade_type("NATIVE");
        $this->wx_pay_unified_order->SetProduct_id($payId);

        $result = $this->wx_pay_native->GetPayUrl($this->wx_pay_unified_order);
        $result['GetPrePayUrl'] = $this->wx_pay_native->GetPrePayUrl($payId);
        $url = self::WX_QR_CODE_URL;
        if (isset($result["code_url"]) && trim($result["code_url"])) {
            $url = "{$url}{$result["code_url"]}";
        }

        return $url;
    }
}
