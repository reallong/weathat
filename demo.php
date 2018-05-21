<?php
//preg_match("/^(http:\/\/)?([^\/]+)/i","http://blog.snsgou.com/index.php", $matches);
//$host = $matches[2];
//echo $host;
////print_r($matches);
////// 从主机名中取得后面两段
//preg_match("/[^\.\/]+\.[^\.\/]+$/",$host, $matches);
//print_r($matches);
//$a1=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");
//$a2=array("a"=>"purple","b"=>"orange");
//array_splice($a1,0,2);
//print_r($a1);
//function console_log( $data ){
//    echo '<script>';
//    echo 'console.log('. json_encode( $data ) .')';
//    echo '</script>';
//}
//$myvar = array(1,2,3);
//console_log( $myvar );
/**
 *                             _ooOoo_
 *                            o8888888o
 *                            88" . "88
 *                            (| -_- |)
 *                            O\  =  /O
 *                         ____/`---'\____
 *                       .'  \\|     |//  `.
 *                      /  \\|||  :  |||//  \
 *                     /  _||||| -:- |||||-  \
 *                     |   | \\\  -  /// |   |
 *                     | \_|  ''\---/''  |   |
 *                     \  .-\__  `-`  ___/-. /
 *                   ___`. .'  /--.--\  `. . __
 *                ."" '<  `.___\_<|>_/___.'  >'"".
 *               | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *               \  \ `-.   \_ __\ /__ _/   .-` /  /
 *          ======`-.____`-.___\_____/___.-`____.-'======
 *                             `=---='
 *          ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *                     佛祖保佑        永无BUG
 *            佛曰:
 *                   写字楼里写字间，写字间里程序员；
 *                   程序人员写程序，又拿程序换酒钱。
 *                   酒醒只在网上坐，酒醉还来网下眠；
 *                   酒醉酒醒日复日，网上网下年复年。
 *                   但愿老死电脑间，不愿鞠躬老板前；
 *                   奔驰宝马贵者趣，公交自行程序员。
 *                   别人笑我忒疯癫，我笑自己命太贱；
 *                   不见满街漂亮妹，哪个归得程序员？
 */

$out_trade_no = '2018051717515773848715';
$paystatus = D('coursebag')->getpaystatus($out_trade_no);//查询订单状态
$user = D('user')->getUid($paystatus['uid']);//查询课时
$re['mch_id']           = '商户号';//商户号
$re['total_fee']        = 19999/100;//支付金额，出来的金额要除以100
$re['transaction_id']   = '454554520185275744';//微信内部的订单流水号
$re['openid']           = 'openid';//微信加密的用户身份识别
$re['bank_type']        = '银行类型';//银行类型
$re['cash_fee']         = 100;//现金支付金额
$re['fee_type']         = '货币类型';//货币类型
$re['time_end']         = '20180518102036';//订单支付时间
$re['trade_state']      = '交易状态';//交易状态
$re['trade_state_desc']      = '交易状态描述';//交易状态描述
$re['pay_flag']      = 1;//交易状态描述

$done = D('coursebag')->setpaystatus($out_trade_no,$re);//修改订单状态
$course = D('user')->addToken($user['uid'],array('course_number'=>$paystatus['course_number']+$user['course_number']));//修改课时
Jsondata($done);
Jsondata($course);
if($course && $done){
    echo 111;exit;
}else{
    echo 452;exit;
}





   function wxpay($num = 1,$openid,$order_number){//统一下单
        ini_set('date.timezone','Asia/Shanghai');
        error_reporting(E_ERROR);
        import("Vendor.Wxpay.lib.WxPayApi");
        import("Vendor.Wxpay.example.WxPayJsApiPaypro");
        import("Vendor.Wxpay.example.log");


//初始化日志
        $logHandler= new \CLogFileHandler("./wxpaylogs/".date('Y-m-d').'.log');

        $log = \Log::Init($logHandler, 15);


/*//打印输出数组信息
        function printf_info()
        {
            foreach($data as $key=>$value){
                $re = $key. ':'. $value;

                echo "<font color='#00ff55;'>$key</font> : $value <br/>";
            }
        }*/

//①、获取用户openid
        $tools = new \JsApiPay();
        $openId = $this->_user['openid'];
        $openId = 'oy4kt1ffYbTubCfnsJrjkRyDa6h4';

//②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("趣学外教-课包购买");//设置商品或支付单简要描述
        $input->SetAttach("趣学外教-课包购买");//设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        $input->SetOut_trade_no($order_number);//设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        $input->SetTotal_fee("1");//设置订单总金额，只能为整数，详见支付金额
        //$input->SetTotal_fee($num);//设置订单总金额，只能为整数，详见支付金额
        $input->SetTime_start(date("YmdHis"));//设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
        $input->SetTime_expire(date("YmdHis", time() + 600));//设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
        $input->SetGoods_tag("趣学外教-课包购买");//设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
        $input->SetNotify_url("http://book.quxueabc.com/login/wxcallback");//设置接收微信支付异步通知回调地址
        $input->SetTrade_type("JSAPI");//设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        $input->SetOpenid($openId);//设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。
        $order = \WxPayApi::unifiedOrder($input);

        $jsApiParameters = $tools->GetJsApiParameters($order);//前端apijs接口调用信息
        $arr['order']=$order;
        $arr['jsapi']=$jsApiParameters;
//        $editAddress = $tools->GetEditAddressParameters();//获取共享收货地址js函数参数
//        print_r($editAddress);exit;
        return $arr;


    }
//查询订单
 function wxpaystatus()//支付异步回调
{

     ini_set('date.timezone','Asia/Shanghai');
     error_reporting(E_ERROR);
     import("Vendor.Wxpay.lib.WxPayApi");
     import("Vendor.Wxpay.example.WxPayJsApiPay");
     import("Vendor.Wxpay.example.log");
     import("Vendor.Wxpay.lib.WxPayNotify");
     Paydata(file_get_contents('php://input', 'r'));
     $xml = file_get_contents('php://input', 'r');
     $paydata = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

     $input = new \WxPayOrderQuery();
     $input->SetTransaction_id($paydata['transaction_id']);
     $result = \WxPayApi::orderQuery($input);
     Paydata($result);
//        \Log::DEBUG("query:" . json_encode($result));日志
     $notify = new \WxPayNotify();
     if(array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
          $out_trade_no     = $result['out_trade_no'];//私有订单号，你就用这个订单号来进行你自己订单的各种更新吧
          $re['mch_id']           = $result['mch_id'];//商户号
          $re['total_fee']        = $result['total_fee']/100;//支付金额，出来的金额要除以100
          $re['transaction_id']   = $result['transaction_id'];//微信内部的订单流水号
          $re['openid']           = $result['openid'];//微信加密的用户身份识别

          $re['bank_type']        = $result['bank_type'];//银行类型
          $re['cash_fee']         = $result['cash_fee'];//现金支付金额
          $re['fee_type']         = $result['fee_type'];//货币类型
          $re['time_end']         = $result['time_end'];//订单支付时间
          $re['trade_state']      = $result['trade_state'];//交易状态
          $re['trade_state_desc']      = $result['trade_state_desc'];//交易状态描述
          $re['pay_flag']      = 1;//交易状态描述
          $paystatus = D('coursebag')->getpaystatus($out_trade_no);//查询订单状态
          if($paystatus['pay_flag'] == 1 ){//重复的通知直接返回
               $re['stutus'] = 'done';
               $re['payststustime'] = TIMESTR;
               Paydata($re);
               $notify ->Handle(true);
          }
          $user = D('user')->getUid($paystatus['uid']);//查询课时
          $done = D('coursebag')->setpaystatus($out_trade_no,$re);//修改订单状态
          $course = D('user')->addToken($user['uid'],array('course_number'=>$paystatus['course_number']+$user['course_number']));//修改课时
          if($course && $done){
               $re['stutus'] = 'done';
               $re['payststustime'] = TIMESTR;
               Paydata($re);
               $notify ->Handle(true);
          }else{
               $re['stutus'] = 'fail';
               $re['payststustime'] = TIMESTR;
               Paydata($re);
               $notify ->Handle(true);
          }
     }else{
          $notify ->Handle(false);
     }
     //return false;
}

?>
