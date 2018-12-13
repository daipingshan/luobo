<?php
/**
 * Created by PhpStorm.
 * User: Leborn
 * Date: 2018/9/25
 * Time: 14:42
 */

namespace Home\Controller;

class PayController extends CommonController
{

    protected $checkUser = false;

    public function zhifu()
    {
        $this->display();
    }

    /**
     * 支付成功
     */
    public function success()
    {
        $order = I('get.order', '' ,'trim');
        $order = M('order')->where(array('order' => $order))->find();
        $amount = M('user')->where(array('id' => $order['uid']))->getField('amount');

        $data = array(
            'pay_amount' => $order['amount'],
            'user_amount' => $amount,
        );

        $this->assign('data', $data);
        $this->display();
    }

    /**
     * 支付失败
     */
    public function fail()
    {
        $this->display();
    }

    /**
     * 用户支付页
     * @DateTime 2018-10-10
     * @return   [type]     [description]
     */
    public function wxPay()
    {
        $amount = I('post.amount', '', 'doubleval');

        parent::_checkUser();
        $uid = $this->user_id;

        $user = M('user')->where(array('id' => $uid))->find();
        $order = $uid . time();
        $title = '萝卜网用户('.$user['mobile'] .')'. '充值' . $amount;
        $data = array(
            'uid' => $uid,
            'title' => $title,
            'amount' => $amount,
            'order' => $order,
            'type' => 0,
            'create_time' => time(),
            'status' => 0,
        );

        $res = M('order')->add($data);

        $pay_data = array(
            'money' => $amount,
            'title' => $title,
            'order' => $order,
            'openid' => $user['openid'],
        );

        $wxpay = new \Common\Org\pcWXpay();

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false){

            $code_url =  $wxpay->getWXpayCode($order, $title, $title, $amount*100, 'pc');
            $result = array(
                'code_url' => $code_url,
                'order_no' => $order,
                'success_url' => U('Pay/success'),
                'money' => $amount,
            );

            $this->assign($result);
            $this->display('pcwx_qrcode');
        } else {
            $return =  $wxpay->doPay($pay_data);
            $result = array(
                'appId'     => $return['pay_data']['appId'],
                'nonceStr'  => $return['pay_data']['nonceStr'],
                'package'   => $return['pay_data']['package'],
                'signType'  => $return['pay_data']['signType'],
                'timeStamp' => $return['pay_data']['timeStamp'],
                'paySign'   => $return['pay_data']['paySign'],
                'openid'    => $return['openid'],
            );

            $this->assign('data', $result);
            $this->display();
        }
    }

    // 微信支付回调
    public function notify()
    {
        $xml    = file_get_contents('php://input');
        $result = $this->xmlToArray($xml);
        file_put_contents('/tmp/wxpay.log', '----' . var_export($result, true) . '----', FILE_APPEND);
        if (trim($result['result_code']) == 'SUCCESS') {
            $where = array('order' => $result['out_trade_no']);
            $orders = M('order')->where($where)->find();
            $bf_amount = M('user')->where(array('id' => $orders['uid']))->getField('amount');
            if ($orders) {
                $data = array(
                    'transaction_id' => $result['transaction_id'],
                    'pay_time' => time(),
                    'status' => 1,
                );
                $save_res = M('order')->where($where)->save($data);
                M('user')->where(array('id' => $orders['uid']))->setInc('amount', $orders['amount']);
                $this->_addUserFlow($orders['uid'], 'wechat pay', '微信充值', $bf_amount, $orders['amount']);
            }
            $str = '<xml>
                        <return_code><![CDATA[SUCCESS]]></return_code>
                        <return_msg><![CDATA[OK]]></return_msg>
                    </xml>';
            echo $str;
        }
    }

    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val       = json_decode(json_encode($xmlstring), true);
        return $val;
    }

    /**
     * 获取订单状态
     * @DateTime 2018-10-23
     * @return   [type]     [description]
     */
    public function getOrderStatus()
    {
        $order = I('post.order', '', 'trim');

        $where = array(
            'order' => $order,
            'status' => 1,
            'transaction_id' => array('neq', '')
        );

        $order_res = M('order')->where($where)->find();
        $res = array('code' => '-1', 'msg' => '正在支付中', 'data' => '', 'url' => '');
        if (!empty($order_res)) {
            $res = array('code' => 0, 'msg' => '支付成功', 'data' => '', 'url' => U('pay/success', array('order' => $order)));
        }
        $this->ajaxReturn($res);
    }

}