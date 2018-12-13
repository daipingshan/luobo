<?php
require_once dirname(__DIR__)."/lib/WxPay.Api.php";
require_once("CommonUtil.php");
class WxMchPay  
{
    public $ch = NULL;
    /**
     * API 参数
     * @var array
     * 'mch_appid'         # 公众号APPID
     * 'mchid'             # 商户号
     * 'device_info'       # 设备号
     * 'nonce_str'         # 随机字符串
     * 'partner_trade_no'  # 商户订单号
     * 'openid'            # 收款用户openid
     * 'check_name'        # 校验用户姓名选项 针对实名认证的用户
     * 're_user_name'      # 收款用户姓名
     * 'amount'            # 付款金额
     * 'desc'              # 企业付款描述信息
     * 'spbill_create_ip'  # Ip地址
     * 'sign'              # 签名
     */
    public $parameters = [];

    public $appid;
    public $mchid;
    public $key;
    public $appsecret;

    public function __construct()
    {
        $this->appid     = 'wx2e2f2a3416df4e7a';
        $this->mchid     = '1514239221';
        $this->key       = 'skdl12009sucnjkn12n129xNNNjds9qi';
        $this->appsecret = '7c12948cd37f1adbe39601609910e36a';
        $this->curl_timeout = 30;
    } 

    public function setParameter($parameter, $parameterValue) {
        $this->parameters[CommonUtil::trimString($parameter)] = CommonUtil::trimString($parameterValue);
    }

    /**
     * 生成请求xml数据
     * @return string
     */
    public function createXml($retcode = 0, $reterrmsg = "ok")
    {
        $this->parameters['mch_appid'] = $this->appid;
        $this->parameters['mchid']     = $this->mchid ;
        $this->parameters['nonce_str'] = $this->createNoncestr();
        $this->parameters['sign']      = $this->getSign($this->parameters);
        return $this->arrayToXml($this->parameters);
    }    

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public function createNoncestr( $length = 32 ) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        }  
        return $str;
    }

    /**
     *  作用：生成签名
     */
    public function getSign($Obj)
    {
        foreach ($Obj as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$this->key;
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) 
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
     *  作用：array转xml
     */
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
             if (is_numeric($val))
             {
                $xml.="<".$key.">".$val."</".$key.">"; 

             }
             else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }
    
    /**
     *  作用：将xml转为array
     */
    public function xmlToArray($xml)
    {       
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     *  作用：以post方式提交xml到对应的接口url
     */
    public function postXmlCurl($xml,$url,$second=60)
    {
        //初始化curl        
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOP_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        file_put_contents('/tmp/ti.log',var_export($data, true).'%%',FILE_APPEND);        
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }else{ 
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";            
            curl_close($ch);
            return false;
        }
    }

    /**
     *     作用：使用证书，以post方式提交xml到对应的接口url
     */
    function postXmlSSLCurl($xml,$url,$second=30)
    {
        //file_put_contents('/tmp/ti.log',var_export($xml, true).'$$',FILE_APPEND); 
        file_put_contents('/tmp/ti.log',var_export($url, true).'@@',FILE_APPEND); 
        //1.初始化，创建一个新cURL资源
        $ch = curl_init();
        //2.设置URL和相应的选项
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
       
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        curl_setopt($ch,CURLOPT_CAINFO,dirname((dirname(__FILE__))).'/cert/rootca.pem');
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
         
        curl_setopt($ch,CURLOPT_SSLCERT, dirname((dirname(__FILE__))).'/cert/apiclient_cert.pem');
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, dirname((dirname(__FILE__))).'/cert/apiclient_key.pem');

        //post提交方式
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        //file_put_contents('/tmp/ti.log',var_export($ch, true).'$$',FILE_APPEND); 
        //3.抓取URL并把它传递给浏览器
        $data = curl_exec($ch);   
        //file_put_contents('/tmp/ti.log',var_export($data, true).'$$',FILE_APPEND);  
        //返回结果
        if($data){
            //4.关闭cURL资源，并且释放系统资源
            curl_close($ch);
            return $data;
        }
        else {
            //4.关闭cURL资源，并且释放系统资源
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."";
            echo "错误原因查询";
            curl_close($ch);
            return false;
        }
    }

}
