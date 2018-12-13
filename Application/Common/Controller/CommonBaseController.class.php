<?php

/**
 * Created by PhpStorm.
 * User: daipingshan
 * Date: 2018/5/2
 * Time: 11:15
 */

namespace Common\Controller;

use Think\Controller;
use Think\Page;
use Common\Org\AliYunOss as OSS;
use Think\Upload;
use Think\Image;

/**
 * Class CommonController
 *
 * @package Common\Controller
 */
class CommonBaseController extends Controller {

    /**
     * @var int
     */
    protected $limit = 20;

    /**
     * CommonBaseController constructor.
     */
    public function __construct() {
        parent::__construct();
        header("Content-type: text/html; charset=utf-8");
    }

    /**
     * 上传项目图片
     */
    public function uploadImgCompany($width, $height) {


        $width  = I('width', 300, 'int');
        $height = I('height', 300, 'int');

        $image            = new Image();
        $upload           = new Upload();// 实例化上传类
        $upload->maxSize  = 3145728;
        $upload->rootPath = './Uploads/';
        $upload->savePath = '';
        $upload->saveName = array('uniqid', '');
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $upload->autoSub  = true;
        $upload->subName  = array('date', 'Ymd');


        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $res = array(
                "code" => -1,
                "data" => array(),
                "msg"  => $upload->getError(),
            );
            //            $this->error();
        } else {// 上传成功 获取上传文件信息
            $img_data = array();
            foreach ($info as $file) {
                $img_data[] = '/Uploads/' . $file['savepath'] . $file['savename'];
                $image->open('./Uploads/' . $file['savepath'] . $file['savename']);
                $image->thumb($width, $height)->save('./Uploads/' . $file['savepath'] . $file['savename']);
            }

            $res = array(
                "code" => 0,
                "data" => $img_data,
                "msg"  => '上传成功',
            );
        }
        return $res;
    }

    /**
     * 上传项目图片
     */
    public function uploadImg() {
        $upload           = new Upload();// 实例化上传类
        $upload->maxSize  = 3145728;
        $upload->rootPath = './Uploads/';
        $upload->savePath = '';
        $upload->saveName = array('uniqid', '');
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $upload->autoSub  = true;
        $upload->subName  = array('date', 'Ymd');


        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $res = array(
                "code" => 0,
                "data" => array(),
                "msg"  => $upload->getError(),
            );
            $this->error();
        } else {// 上传成功 获取上传文件信息
            $img_data = array();
            foreach ($info as $file) {
                $img_data[] = '/Uploads/' . $file['savepath'] . $file['savename'];
            }

            $res = array(
                "code" => 1,
                "data" => $img_data,
                "msg"  => '上传成功',
            );
        }
        ob_clean();
        die(json_encode($res));
    }

    /**
     * 上传项目图片
     */
    public function uploadEditImg() {
        $upload           = new Upload();// 实例化上传类
        $upload->maxSize  = 3145728;
        $upload->rootPath = './Uploads/';
        $upload->savePath = '';
        $upload->saveName = array('uniqid', '');
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $upload->autoSub  = true;
        $upload->subName  = array('date', 'Ymd');
        $info             = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $res = array(
                "code" => -1,
                "data" => array(),
                "msg"  => $upload->getError(),
            );
            $this->error();
        } else {// 上传成功 获取上传文件信息
            $img_data = null;
            foreach ($info as $file) {
                $img_data = '/Uploads/' . $file['savepath'] . $file['savename'];
            }
            $res = array(
                "code" => 0,
                "data" => array('src' => $img_data),
                "msg"  => '上传成功',
            );
        }
        ob_clean();
        die(json_encode($res));
    }


    /**
     * @param string $msg
     * @param string $code
     * @param array $data
     */
    protected function output($msg = '请求错误', $code = 'fail', $data = array()) {
        $json_data = array('code' => $code, 'msg' => $msg, 'data' => $data);
        ob_clean();
        die(json_encode($json_data));
    }


    /**
     * @param $totalRows
     * @param $listRows
     * @param array $map
     * @param int $rollPage
     * @return Page
     */
    protected function pages($totalRows, $listRows, $map = array(), $rollPage = 5) {
        $Page = new Page($totalRows, $listRows, '', MODULE_NAME . '/' . ACTION_NAME);
        if ($map && IS_POST) {
            foreach ($map as $key => $val) {
                $val             = urlencode($val);
                $Page->parameter .= "$key=" . urlencode($val) . '&';
            }
        }
        if ($rollPage > 0) {
            $Page->rollPage = $rollPage;
        }
        $Page->setConfig('header', '条信息');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '末页');
        $Page->setConfig(
            'theme', '<div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-1"><span>当前页' . $listRows . '条数据 总%TOTAL_ROW% %HEADER%</span>%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE%</div>'
        );
        return $Page;
    }

    /**
     * @param $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _get($url, $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        if (!empty($params)) {
            if (strpos($url, '?') !== false) {
                $url .= "&" . http_build_query($params);
            } else {
                $url .= "?" . http_build_query($params);
            }
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 5);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _post($url = '', $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'Content-Type:application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 5);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);

        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * @param string $url
     * @param array $params
     * @param $cookie
     * @return bool|mixed
     */
    protected function _file($url = '', $params = array(), $cookie = '') {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        $header = array(
            'Accept:application/json, text/javascript, */*; q=0.01',
            'X-Requested-With:XMLHttpRequest',
        );
        if ($cookie) {
            $header[] = "cookie:{$cookie}";
        }
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($oCurl, CURLOPT_TIMEOUT, 5);
        $sContent = curl_exec($oCurl);
        $aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * 二维数组排序
     *
     * @param $data
     * @param $sort_order_field
     * @return mixed
     */
    protected function _arraySort($data, $sort_order_field) {
        if (!$data) {
            return array();
        }
        foreach ($data as $val) {
            $key_arrays[] = $val[$sort_order_field];
        }
        array_multisort($key_arrays, SORT_ASC, SORT_NUMERIC, $data);
        return $data;
    }


    /**
     * @param $filename
     * @param $data
     */
    protected function _addLogs($filename, $data) {
        $path = "/logs/" . date('Y-m-d');
        if (!is_dir($path)) {
            @mkdir($path);
        }
        $path = $path . "/" . $filename;
        if (is_array($data)) {
            file_put_contents($path, var_export($data, true) . "\r\n", FILE_APPEND);
        } else {
            file_put_contents($path, $data . "\r\n", FILE_APPEND);
        }
    }


    /**
     * @param $user_id
     * @param $represent
     * @param $admin_name
     * @param $user_amount
     * @param $amount
     * @param int $admin_id
     */
    protected function _addUserFlow($user_id, $represent, $admin_name, $user_amount, $amount, $admin_id = 9999) {
        $in_or_de = $amount > 0 ? 1 : 2;
        $data     = array(
            'user_id'       => $user_id,
            'represent'     => $represent,
            'admin_id'      => $admin_id,
            'admin_name'    => $admin_name,
            'op_bf_amount'  => $user_amount,
            'op_amount'     => $amount,
            'op_aft_amount' => $user_amount + $amount,
            'in_or_de'      => $in_or_de,
            'create_time'   => date('Y-m-d H:i:s'),
        );
        M('user_flow')->add($data);
    }

    /**
     * 获取城市信息
     */
    protected function _getCityCache() {
        $data = S('city');
        if (empty($data)) {
            $data = M('city')->index('id')->select();
            S('city', $data);
        }
        return $data;
    }

    /**
     * 获取职位分类信息
     */
    protected function _getPositionCache() {
        $data = S('position');
        if (empty($data)) {
            $data = M('position_type')->index('id')->select();
            S('position', $data);
        }
        return $data;
    }

    /**
     * 获取企业标签信息
     */
    protected function _getCompanyLabelCache() {
        $data = S('company_label');
        if (empty($data)) {
            $data = M('company_label')->index('id')->select();
            S('company_label', $data);
        }
        return $data;
    }

    /**
     * 获取企业标签信息
     */
    protected function _getShopReportCateCache() {
        $data = S('shop_report_cate');
        if (empty($data)) {
            $data = M('shops_report_cats')->index('id')->select();
            S('shop_report_cate', $data);
        }
        return $data;
    }

    /**
     * 获取萝卜项目分类信息
     */
    protected function _getProjectTypeCache() {
        $data = S('project_type');
        if (empty($data)) {
            $data = M('project_type')->index('id')->select();
            S('project_type', $data);
        }
        return $data;
    }

}