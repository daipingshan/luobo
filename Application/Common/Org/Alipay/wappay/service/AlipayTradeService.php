<?php
/* *
 * 功能：支付宝手机网站alipay.trade.close (统一收单交易关闭接口)业务参数封装
 * 版本：2.0
 * 修改日期：2016-11-01
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */


require dirname ( __FILE__ ) . "/AlipayTradeWapPayRequest.php";
require dirname ( __FILE__ ) . "/AopClient.php";
require dirname ( __FILE__ ).'/config.php';

class AlipayTradeService {

	//支付宝网关地址
	public $gateway_url = "https://openapi.alipay.com/gateway.do";

	//支付宝公钥
	public $alipay_public_key;

	//商户私钥
	public $private_key;

	//应用id
	public $appid;

	//编码格式
	public $charset = "UTF-8";

	public $token = NULL;

	//返回数据格式
	public $format = "json";

	//签名方式
	public $signtype = "RSA";

	function __construct($alipay_config){

		$this->gateway_url = "https://openapi.alipay.com/gateway.do";
		$this->appid = "2018062760492108";

		$this->private_key = "MIIEpAIBAAKCAQEAmGdIj06o5G6WMVM3ScnlWLTWYb9MwPlkeEEjSHB62H+p5WLhqwtQopvZRl53EDELWv15DudY8T1rkQXs9aiXgve1BsP8Bx8fCjUh+LxSWzpmesf1URTy0YLuuy/ZAgeFRzmjBZ+Nwf9RaFd50j/Hr048W+JGc3AtORQ3H5fkYpLUHdRwd83+beVoa2gVCiTsIAIM6TW2InMG1t0Fj290IDUm5gaWPZZzoHwVx1+8BIN/K8TanyqGJew+R52nvsi30BzIHevABLvnmqj8d9//xJ6YBROiLtjz/LDqcgwolAvEcuFwEDykg0cZortk9tq9xxIwSWJ9o/5fy5WogQoAVwIDAQABAoIBAQCP3BbTJ7YngvgFcX7kCU+Tz4f6VuC12/Rpy1rp2zsquD+AhzlsAs9H34NWeH89an5EkrW+SsVbWtX53DUUouL6PqbDzyZM9qSPNx5tNB8tfFAWIEBCHFslR8ngKkdXMhAt7osjGLxwXnjS3Jfizk/I/DnfphVIjKAznBB4oyFzpZ7cxQNjKkGQzZjY+shntbxYCMbAdmQ5oAbbfiOZZgXHTQM/wy4wGaoSvc6bAMKmGruEx7lPCayAVaOk2OUpitYD9twNNSkrUNSmqcigyzrqgKPisiEgLJAU0J0yUUUnZEyPpSODZXVJXdxqJV5Lte8NC+K6QZZXr2xxN7Y0h9JRAoGBAMj51MHfz9BSccD+OwcV/ua87MvI8+OukHbIuAuRhGBR7B5jJ6I1Q5HN/i91UkOAQuoTd9hKTGZ63oJxFGSrh5XxelD05mQgkC/en5QFtwgexv+Fz1PDKPnnJsbbDl8DUxgULw13LzrpnePb8NN6Y9Sg4AdCQggg5dLFIzjo5lxpAoGBAMIhELfd2/RRPkXiFLyra2OaEyL0HVZKb51WLow5efnkJew0/zb1RKAZGQ0HoVEb/DaIBbU8/62NxXXou9BeBvJ+FU3Ook5WOAqVORY8CqLABrg6Lx2PWOzXJTvSwMJ9jS1ZTDigUsLpPMyNCz8FMWpKEv4Rn8FQZ3pQV3+vyN6/AoGAMnha750PCRfLLVYq6KqjarqDFOIQLVtHOC1L2sveXHn2O+NWrquFnnYGoVBrKjxpkXL2I8D00r+EZWmUX3ub/xG3T+FQglCTJRJZLMkKn+Vqv/yQhk56Wdesqz+TqlShZ2iaMF1/5OGKHxC6t8EClQEZgXkoL/ZjAL01DTOfthECgYEAhROhoEmIRL2E0OcgjA1+unKed7GcgtDYHqU7l4i3IyTRElFqOsR73LwXviTi4vUqOj+YmhtMsi6jlCaMyVQLsyPXetUR0l2sYSTWU3WpXNQCRzwrnDnuHb2GmrHozeq/Yrz6UT8mPNMiiJ6PfQ4UR+ariluOhjiWFvUIZlGcg4UCgYAqfWAP/mwp8y5MqArYnI1FZ7LKOwFRKrn4HtlEjuPTmiotA44976PM5eCYDh3iYqYZpG9A56HgDjGz5WJ96YNvo1BSFZLlo+jfk83lkro9GWPUID8odZzMZ2jqquXZPIz0T6mgUBE8XE0LynvFJUZMvUV/6Ld2lyOjmSxbA4XoXQ==";

		$this->alipay_public_key = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmGdIj06o5G6WMVM3ScnlWLTWYb9MwPlkeEEjSHB62H+p5WLhqwtQopvZRl53EDELWv15DudY8T1rkQXs9aiXgve1BsP8Bx8fCjUh+LxSWzpmesf1URTy0YLuuy/ZAgeFRzmjBZ+Nwf9RaFd50j/Hr048W+JGc3AtORQ3H5fkYpLUHdRwd83+beVoa2gVCiTsIAIM6TW2InMG1t0Fj290IDUm5gaWPZZzoHwVx1+8BIN/K8TanyqGJew+R52nvsi30BzIHevABLvnmqj8d9//xJ6YBROiLtjz/LDqcgwolAvEcuFwEDykg0cZortk9tq9xxIwSWJ9o/5fy5WogQoAVwIDAQAB";
		$this->charset = "UTF-8";
		$this->signtype="RSA2";

		if(empty($this->appid)||trim($this->appid)==""){
			throw new Exception("appid should not be NULL!");
		}
		if(empty($this->private_key)||trim($this->private_key)==""){
			throw new Exception("private_key should not be NULL!");
		}
		if(empty($this->alipay_public_key)||trim($this->alipay_public_key)==""){
			throw new Exception("alipay_public_key should not be NULL!");
		}
		if(empty($this->charset)||trim($this->charset)==""){
			throw new Exception("charset should not be NULL!");
		}
		if(empty($this->gateway_url)||trim($this->gateway_url)==""){
			throw new Exception("gateway_url should not be NULL!");
		}

	}
	function AlipayWapPayService($alipay_config) {
		$this->__construct($alipay_config);
	}

	/**
	 * alipay.trade.wap.pay
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @param $return_url 同步跳转地址，公网可访问
	 * @param $notify_url 异步通知地址，公网可以访问
	 * @return $response 支付宝返回的信息
 	*/
	function wapPay($builder,$return_url,$notify_url) {
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog($biz_content);
		$request = new AlipayTradeWapPayRequest();
		$request->setNotifyUrl($notify_url);
		$request->setReturnUrl($return_url);
		$request->setBizContent ($biz_content);
		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request,true);
		// $response = $response->alipay_trade_wap_pay_response;
		return $response;
	}

	 function aopclientRequestExecute($request,$ispage=false) {
	 	// print_r($request);
	 	// die;
		$aop = new AopClient();
		$aop->gatewayUrl = $this->gateway_url;
		$aop->appId = $this->appid;
		$aop->rsaPrivateKey =  $this->private_key;
		$aop->alipayrsaPublicKey = $this->alipay_public_key;
		$aop->apiVersion ="1.0";
		$aop->postCharset = $this->charset;
		$aop->format= $this->format;
		$aop->signType=$this->signtype;
		// 开启页面信息输出
		$aop->debugInfo=true;
		if($ispage)
		{
			$result = $aop->pageExecute($request,"post");
			//echo $result;
			print_r($result);
		}
		else
		{
			$result = $aop->Execute($request);
			print_r($result);
		}

		//打开后，将报文写入log文件
		$this->writeLog("response: ".var_export($result,true));
		return $result;
	}

	/**
	 * alipay.trade.query (统一收单线下交易查询)
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
 	*/
	function Query($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog($biz_content);
		$request = new AlipayTradeQueryRequest();
		$request->setBizContent ( $biz_content );

		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_trade_query_response;
		var_dump($response);
		return $response;
	}

	/**
	 * alipay.trade.refund (统一收单交易退款接口)
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	function Refund($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog($biz_content);
		$request = new AlipayTradeRefundRequest();
		$request->setBizContent ( $biz_content );

		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_trade_refund_response;
		var_dump($response);
		return $response;
	}

	/**
	 * alipay.trade.close (统一收单交易关闭接口)
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	function Close($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog($biz_content);
		$request = new AlipayTradeCloseRequest();
		$request->setBizContent ( $biz_content );

		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_trade_close_response;
		var_dump($response);
		return $response;
	}

	/**
	 * 退款查询   alipay.trade.fastpay.refund.query (统一收单交易退款查询)
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	function refundQuery($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog($biz_content);
		$request = new AlipayTradeFastpayRefundQueryRequest();
		$request->setBizContent ( $biz_content );

		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		var_dump($response);
		return $response;
	}
	/**
	 * alipay.data.dataservice.bill.downloadurl.query (查询对账单下载地址)
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	function downloadurlQuery($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog($biz_content);
		$request = new alipaydatadataservicebilldownloadurlqueryRequest();
		$request->setBizContent ( $biz_content );

		// 首先调用支付api
		$response = $this->aopclientRequestExecute ($request);
		$response = $response->alipay_data_dataservice_bill_downloadurl_query_response;
		var_dump($response);
		return $response;
	}

	/**
	 * 验签方法
	 * @param $arr 验签支付宝返回的信息，使用支付宝公钥。
	 * @return boolean
	 */
	function check($arr){
		$aop = new AopClient();
		$aop->alipayrsaPublicKey = $this->alipay_public_key;
		$result = $aop->rsaCheckV1($arr, $this->alipay_public_key, $this->signtype);
		return $result;
	}

	//请确保项目文件有可写权限，不然打印不了日志。
	function writeLog($text) {
		// $text=iconv("GBK", "UTF-8//IGNORE", $text);
		//$text = characet ( $text );
		file_put_contents ( dirname ( __FILE__ ).DIRECTORY_SEPARATOR."./../../log.txt", date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
	}


	/** *利用google api生成二维码图片
	 * $content：二维码内容参数
	 * $size：生成二维码的尺寸，宽度和高度的值
	 * $lev：可选参数，纠错等级
	 * $margin：生成的二维码离边框的距离
	 */
	function create_erweima($content, $size = '200', $lev = 'L', $margin= '0') {
		$content = urlencode($content);
		$image = '<img src="http://chart.apis.google.com/chart?chs='.$size.'x'.$size.'&amp;cht=qr&chld='.$lev.'|'.$margin.'&amp;chl='.$content.'"  widht="'.$size.'" height="'.$size.'" />';
		return $image;
	}
}

?>