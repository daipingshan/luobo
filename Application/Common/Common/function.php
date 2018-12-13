<?php
/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 17:04
 */

/**
 * @param $str
 * @return string
 */
function encrypt_pwd($str) {
    return md5(trim($str) . C('PWD_ENCRYPT_STR'));
}

/**
 * 获取图片路径
 *
 * @param $path
 * @return string
 */
function get_img_url($path) {
    if (!trim($path)) {
        return '';
    }
    if (strpos($path, 'http') !== false) {
        return $path;
    }
    return C('IMG_PREFIX') . $path;
}

/**
 * @param $mobile
 * @return bool
 */
function is_mobile($mobile) {
//    preg_match('/^1\d{10}$/', $mobile) ? true : false;
    return preg_match("/^1[3456789]{1}\d{9}$/", $mobile) ? true : false;
}

/**
 * @param $email
 * @return bool
 */
function is_email($email) {
    return preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email) ? true : false;
}


/**
 * @param $html
 * @param $star
 * @param $end
 * @return mixed
 */
function get_word($html, $star, $end) {
    $wd  = '';
    $pat = '/' . $star . '(.*?)' . $end . '/s';
    if (!preg_match_all($pat, $html, $mat)) {
    } else {
        $wd = $mat[1][0];
    }
    return $wd;
}

/**
 * @param $data
 * @param $keynames
 * @param string $name
 */
function download_xls($data, $keynames, $name = 'dataxls') {

    $xls[] = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><head><head><meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\" /><style id=\"Classeur1_16681_Styles\"></style></head><body><div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\"><table x:str border=1 cellpadding=0 cellspacing=0 width=100% style=\"border-collapse: collapse\">";

    $xls[] = "<tr><td>" . implode("</td><td>", array_values($keynames)) . '</td></tr>';

    foreach ($data As $o) {
        $line = array();
        foreach ($keynames AS $k => $v) {

            $line[] = $o[$k];
        }

        $xls[] = '<tr class="xl2216681 nowrap"><td>' . implode("</td><td>", $line) . '</td></tr>';
    }

    $xls[] = '<table></div></body></html>';

    $xls = join("\r\n", $xls);
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header('Content-Disposition: attachment; filename="' . $name . '.xls"');
    header("Content-Transfer-Encoding: binary");

    die(mb_convert_encoding($xls, 'UTF-8', 'UTF-8'));
}

/**
 * 邮件发送函数
 */
function send_mail($to, $title, $content) {

    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD') ; //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to,"尊敬的客户");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
    $mail->Subject =$title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    return($mail->Send());
}

/**
 * 阿里大鱼发送短信
 * @param  $mobile 收短信手机号
 * @param  $code   验证码
 * @return boolean
 */
function send_sms($mobile, $code, $type = 0)
{
    date_default_timezone_set('Asia/Shanghai');

    Vendor('dayuSms.sendSms','','.class.php');
    // 线上服务器这里走不通
    $dayu_sms = new dayuSms();

    $sms_code = C('LOGIN_CODE');
    if ($type == 1){
        $sms_code = C('FORGET_PASSWORD_CODE');
    }

    $response = $dayu_sms->sendSms(
        C('ACCESS_KEY_ID'),
        C('ACCESS_KEY_SECRET'),
        C('SIGN_NAME'),
        $sms_code,
        $mobile,
        Array("code"=>$code)
    );

    return $response;
}

/**
 *  判断是否是移动端
 * @return bool
 */
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}
