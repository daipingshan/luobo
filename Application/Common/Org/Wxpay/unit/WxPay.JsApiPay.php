<?php

class JsApiPay {
    public $data = null;

    public function GetOpenid($order_no) {
        //通过code获得openid
        if (!isset($_GET['code'])) {
            $park = explode('/',$_SERVER['PATH_INFO']);
            $baseUrl = 'http://www.luobowang.cn/Pay/pay/order/'.$order_no;
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            $code = $_GET['code'];
            //$openid = $this->getOpenidFromMp($code);
            $openid = $this->getOpenidFromMp($code);
            $this->getWxPayConfig();
            return $openid;
        }
    }

    public function GetJsApiLocation($UnifiedOrderResult) {
        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);     //  必填，公众号的唯一标识
        $timeStamp = time();
        $jsapi->SetTimeStamp($timeStamp);                   //  必填，生成签名的时间戳
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());       //  必填，生成签名的随机串
        $jsapi->SetPaySign($jsapi->MakeSign());             //  必填，签名
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

    public function GetJsApiParameters($UnifiedOrderResult) {
        if (!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == "") {
            throw new WxPayException("参数错误");
        }

        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp($timeStamp);
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

    public function GetOpenidFromMp($code) {
        $url = $this->__CreateOauthUrlForOpenid($code);

        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOP_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (WxPayConfig::CURL_PROXY_HOST != "0.0.0.0" && WxPayConfig::CURL_PROXY_PORT != 0) {
            curl_setopt($ch, CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->data = $data;
        $openid = $data['openid'];
        //$nickurl =  "https://api.weixin.qq.com/sns/userinfo?access_token=$data['access_token']&openid=$openid&lang=zh_CN";
        $nickurl =  'https://api.weixin.qq.com/sns/userinfo?access_token='.$data['access_token'].'&openid='.$openid.'&lang=zh_CN';
        $nickname =$this->GetOpenidNickname($nickurl);
        //file_put_contents('/tmp/wxloc.log','用户名称'.var_export($nickname, true).'&&',FILE_APPEND);
       /* $info =array(
        	'openid'=>$openid,
        	'nickname'=>$nickname,
        	);*/
        //return $info;
        return $openid;
    }

    private function ToUrlParams($urlObj) {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    public function GetEditAddressParameters() {
        $getData = $this->data;
        $data = array();
        $data["appid"] = WxPayConfig::APPID;
        $data["url"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $time = time();
        $data["timestamp"] = "$time";
        $data["noncestr"] = "1234568";
        $data["accesstoken"] = $getData["access_token"];
        ksort($data);
        $params = $this->ToUrlParams($data);
        $addrSign = sha1($params);

        $afterData = array(
            "addrSign" => $addrSign,
            "signType" => "sha1",
            "scope" => "jsapi_address",
            "appId" => WxPayConfig::APPID,
            "timeStamp" => $data["timestamp"],
            "nonceStr" => $data["noncestr"]
        );
        $parameters = json_encode($afterData);
        return $parameters;
    }

    private function __CreateOauthUrlForCode($redirectUrl) {
        $urlObj["appid"] = WxPayConfig::APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);

        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    private function __CreateOauthUrlForOpenid($code) {
        $urlObj["appid"] = WxPayConfig::APPID;
        $urlObj["secret"] = WxPayConfig::APPSECRET;
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }


   public function getWxPayConfig(){

        $wx_pay_key = 'jsapi_ticket';
        $jsapi_ticket = S($wx_pay_key);
        if(!$jsapi_ticket){

            $appid= WxPayConfig::APPID;
            $secret = WxPayConfig::APPSECRET;

            $token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
            $access_data = $this->get($token_url);
            $access_token = $access_data['access_token'];
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type=jsapi";
            $jsapi_ticket_res = $this->get($url);
            if(isset($jsapi_ticket_res['ticket']) && isset($jsapi_ticket_res['expires_in'])){
                S($wx_pay_key,$jsapi_ticket_res['ticket'],$jsapi_ticket_res['expires_in']);
                $jsapi_ticket = $jsapi_ticket_res['ticket'];
            }
        }

        $data = array(
            'noncestr'=>WxPayApi::getNonceStr(),
            'timestamp'=>time(),
            'url'=>"http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'jsapi_ticket'=>$jsapi_ticket
        );
        ksort($data);
        $params = $this->ToUrlParams($data);
        $sign = sha1($params);

        $this->js_pay_config = array(
            'appId'=>WxPayConfig::APPID,
            'noncestr'=>$data['noncestr'],
            'timestamp'=>$data['timestamp'],
            'signature'=>$sign,
        );

        return true;
    }


    public function get($url='') {

        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOP_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (WxPayConfig::CURL_PROXY_HOST != "0.0.0.0" && WxPayConfig::CURL_PROXY_PORT != 0) {
            curl_setopt($ch, CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        return $data;
    }

    /**
     * 获取用户姓名
     */
       public function GetOpenidNickname($url) {
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOP_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (WxPayConfig::CURL_PROXY_HOST != "0.0.0.0" && WxPayConfig::CURL_PROXY_PORT != 0) {
            curl_setopt($ch, CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        }
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        $this->data = $data;
        $nickname = $data['nickname'];
        return $nickname;
    }

}
