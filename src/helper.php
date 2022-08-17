<?php
// +----------------------------------------------------------------------
// | 王磊 [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2022 08 12  http://www.wlphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: wl < 613154514@qq.com >
// +----------------------------------------------------------------------
use think\Db;

//  遇到/lizengbang_get_track/xxx 路由转换访问 Track控制器
\think\Route::any('lizengbang_get_track/[:id]', "\\lizengbang\\Track@get_track");



//接受到 礼赠帮推送后的业务逻辑处理 ,注意需要拷贝到 自己项目的helper.php里面。
//接受到 礼赠帮推送后的业务逻辑处理
function get_tuisong_do_data_copy($param_content = "", $content = "")
{
    //数据样例
    //$param_content='{"clientkeynum":"2429C9B29076DEBB2E3A155D13B7D096","com":"yunda","data":[{"context":"【揭阳市】已离开 广东揭阳分拨交付中心；发往 河北保定分拨交付中心","time":"2022-08-16 03:17:23"},{"context":"【揭阳市】已到达 广东揭阳分拨交付中心","time":"2022-08-16 03:15:40"},{"context":"【汕头市】已离开 广东汕头新两英公司；发往 河北保定分拨交付中心","time":"2022-08-15 22:18:29"},{"context":"【汕头市】广东汕头新两英公司-贝伟升（13145996200） 已揽收","time":"2022-08-15 22:13:12"}],"num":"462508923976475","state":"0","id":null}';  
    //$content='{"com":"yunda","data":[{"context":"【揭阳市】已离开 广东揭阳分拨交付中心；发往 河北保定分拨交付中心","time":"2022-08-16 03:17:23"},{"context":"【揭阳市】已到达 广东揭阳分拨交付中心","time":"2022-08-16 03:15:40"},{"context":"【汕头市】已离开 广东汕头新两英公司；发往 河北保定分拨交付中心","time":"2022-08-15 22:18:29"},{"context":"【汕头市】广东汕头新两英公司-贝伟升（13145996200） 已揽收","time":"2022-08-15 22:13:12"}],"num":"462508923976475","state":"0"}';
   

    $receive_param_arr = json_decode($param_content,1);;
    //快递100主体信息
    $state = $receive_param_arr['state'];
    //快递单当前状态，包括0在途，1揽收，2疑难，3签收，4退签，5派件，6退回，7转单，10待清关，11清关中，12已清关，13清关异常，14收件人拒签等13个状态
    $lastResult = $receive_param_arr['data'];
    //存入数据库里面保存
    $kuaidi_code = $receive_param_arr['com'];
    //快递代码
    $kuaidi_num = $receive_param_arr['num'];
    //快递单号

    $order_info = Db::table('client_order_info')->where("find_in_set( '$kuaidi_num', shipping_num )")->find();
    //如果获取不到订单这里直接拦截报错
     //返回成功 
     if(empty($order_info)){
        $rt['sta'] = "0";
        $rt['msg'] = '未找到订单信息！';
        return $rt;   
     }
    $clientkeynum = $order_info['clientkeynum'];

    //更新物流轨迹表,存在更新，不存在则新增，里面是快递以及快递轨迹，一个订单可能有多个快递做兼容
    $kuaidi_detail_info = Db::table('client_order_kuaidi_detail')->where("shipping_num='$kuaidi_num' and shipping_code='$kuaidi_code'")->find();
    if ($kuaidi_detail_info) {
        $kuaidi_up_data['lastresultdata'] = json_encode($lastResult, JSON_UNESCAPED_UNICODE);
        $kuaidi_up_data['state'] = $state;
        $kuaidi_up_data['mod_time'] = time();
        Db::table('client_order_kuaidi_detail')->where("shipping_num='$kuaidi_num' and shipping_code='$kuaidi_code'")->update($kuaidi_up_data);
    } else {
        $kuaidi_insert_data['order_sn'] = $order_info['order_sn'];
        $kuaidi_insert_data['clientkeynum'] =  $order_info['clientkeynum'];
        $kuaidi_insert_data['merchantkeynum'] =  $order_info['merchantkeynum'];
        $kuaidi_insert_data['shipping_name'] =  $kuaidi_code;
        $kuaidi_insert_data['shipping_code'] =  $kuaidi_code;
        $kuaidi_insert_data['shipping_time'] =  time();
        $kuaidi_insert_data['shipping_num'] =  $kuaidi_num;
        $kuaidi_insert_data['lastresultdata'] = json_encode($lastResult, JSON_UNESCAPED_UNICODE);
        $kuaidi_insert_data['state'] = $state;
        $kuaidi_insert_data['mod_time'] = time();
        Db::table('client_order_kuaidi_detail')->insert($kuaidi_insert_data);
    }

    //礼赠帮推送日志记录
    $tuisong_log['words'] = json_encode($lastResult, JSON_UNESCAPED_UNICODE);
    $tuisong_log['time'] = time();
    $tuisong_log['clientkeynum'] = $clientkeynum;
    $tuisong_log['api_return_content'] = json_encode($lastResult, JSON_UNESCAPED_UNICODE);
    $tuisong_log['state'] = $state;
    $tuisong_log['kuaidi_name'] = '';
    $tuisong_log['kuaidi_code'] = $kuaidi_code;
    $tuisong_log['kuaidi_num'] = $kuaidi_num;
    $tuisong_log['order_sn'] = $order_info['order_sn'];
    $tuisong_log['all_content'] = json_encode($param, JSON_UNESCAPED_UNICODE);
    $tuisong_log['all_content1'] = $content;
    Db::table('plat_lizengbang_kuaidi_tuisong_log')->insert($tuisong_log);

    //如果是签收则需要把订单改为签收状态 if里面报错了，上面仍然是可以执行成功的！
    $order_id = $order_info['order_id'];

    if ($state == '3') {
        //修改订单主表
        $up_arr['qianshou_time'] = time();
        $up_arr['order_status'] = 3;
        //这里要注意不要把已经签收之后状态的订单重新改回已签收装填，这里也可以根据有没有签收时间的逻辑。目前是只有已经发货状态的才能改成签收
        $up = Db::table('client_order_info')->where('order_id', $order_id)->where("order_status='2'")->update($up_arr);

        if ($up) {
            //添加订单变更日志
            $log['order_sn'] = $order_info['order_sn'];
            $log['clientkeynum'] = $order_info['clientkeynum'];
            $log['merchantkeynum'] = $order_info['merchantkeynum'];
            $log['action_user'] = '快递主动推送';
            $log['action_note'] = '快递推送签收状态';
            $log['add_time'] = time();
            $log_flag = Db::table('client_order_log')->insert($log);
        }
    }

     //更新订单表快递状态字段
      //快递单当前状态，包括0在途，1揽收，2疑难，3签收，4退签，5派件，6退回，7转单，10待清关，11清关中，12已清关，13清关异常，14收件人拒签等13个状态
     $up_kuaidi_state['kuaidi_status'] = $state;
     Db::table('client_order_info')->where('order_id', $order_id)->update($up_kuaidi_state);


    //返回成功 
    $rt['sta'] = 1;
    $rt['msg'] = '成功';
    return $rt;
}
