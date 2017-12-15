<?php
header("Content-type: text/html; charset=utf-8");
//set_time_limit(0);
class zgy_testControl extends Control {
    private $times;

    private $end_time;

    private $stat_time;

    private $model;

    public function __construct(){
        $this->model = Model();
        $this->times = time();
        $this->stat_time = strtotime(date('Y-m-d',time())) - 86400;
        $this->end_time = strtotime(date('Y-m-d',time())) - 1;
    }

    public function indexOp(){
        echo "123";
    }

    public function suppliereAddOp(){
        $time_a = new Timer();
        $time_a->start();
        $sql = "select * from `sc_daddress`";
        $list = Model()->query($sql);
        echo "开始时间：".date('Y-m-d H:i:s',time())."</br>";
        $num = $num_y =$num_n = 0;
        foreach ($list as $val){
            if(!empty($val['area_info'])){
                $city_old = preg_replace("/(\s+)/",' ',$val['area_info']);
                list($p,$y,$c) = explode(" ",$city_old);
                $p_data = Model()->table("linkage_province")->where("city_name like '%".$p."%'")->find();
                if(!empty($p_data)){
                    $y_data = Model()->table("linkage_city")->where("belong_province_code = '".$p_data['code']."' and city_name like '%".$y."%'")->find();
                    if(!empty($y_data)){
                        $c_data = Model()->table("linkage_county")->where("belong_city_code = '".$y_data['code']."' and city_name like '%".$c."%'")->find();
                    }
                }
                $new_city = array($p_data['city_name'],$y_data['city_name'],$c_data['city_name']);
                if(!empty($new_city) && is_array($new_city)){
                    $city_str = implode(" ",$new_city);
                }else {
                    $city_str = '';
                }
                if(!empty($c_data['code']) && !empty($city_str)){
                    $up_data = array(
                        'area_info' => $city_str,
                        'city_id'   => empty($c_data['code']) ? "":$c_data['code'],
                        'type'=>2
                    );
                    $rest = Model()->table('daddress')->where("address_id = '".$val['address_id']."'")->update($up_data);
                    if($rest){
                        $num_y++;
                    }else{
                        $num_n++;
                    }
                }else{
                    $num_n++;
                }
            }
            $num++;
        }
        $time_a->stop();
        echo "共计：".$num."（Y:".$num_y."，N:".$num_n."）</br>";
        echo "结束时间：".date('Y-m-d H:i:s',time())."</br>";
        echo "耗时：".$time_a->spent()."</br>";
    }


    public function memberAddOp(){
        $time_a = new Timer();
        $time_a->start();
        $sql = "select * from `sc_address`";
        $list = Model()->query($sql);
        echo "开始时间：".date('Y-m-d H:i:s',time())."</br>";
        $num = $num_y =$num_n = 0;
        foreach ($list as $val){
            if(!empty($val['area_info'])){
                $city_old = preg_replace("/(\s+)/",' ',$val['area_info']);
                list($p,$y,$c) = explode(" ",$city_old);
                $p_data = Model()->table("linkage_province")->where("city_name like '%".$p."%'")->find();
                if(!empty($p_data)){
                    $y_data = Model()->table("linkage_city")->where("belong_province_code = '".$p_data['code']."' and city_name like '%".$y."%'")->find();
                    if(!empty($y_data)){
                        $c_data = Model()->table("linkage_county")->where("belong_city_code = '".$y_data['code']."' and city_name like '%".$c."%'")->find();
                    }
                }
                $new_city = array($p_data['city_name'],$y_data['city_name'],$c_data['city_name']);
                if(!empty($new_city) && is_array($new_city)){
                    $city_str = implode(" ",$new_city);
                }else {
                    $city_str = '';
                }
                if(!empty($c_data['code']) && !empty($city_str)){
                    $up_data = array(
                        'area_info' => $city_str,
                        'city_id'   => empty($c_data['code']) ? "":$c_data['code'],
                        'type'=>2
                    );
                    $rest = Model()->table('address')->where("address_id = '".$val['address_id']."'")->update($up_data);
                    if($rest){
                        $num_y++;
                    }else{
                        $num_n++;
                    }
                }else{
                    $num_n++;
                }
            }
            $num++;
        }
        $time_a->stop();
        echo "共计：".$num."（Y:".$num_y."，N:".$num_n."）</br>";
        echo "结束时间：".date('Y-m-d H:i:s',time())."</br>";
        echo "耗时：".$time_a->spent()."</br>";
    }

    public function settlementOp(){
        $time_a = new Timer();
        $time_a->start();
        $sql = "select id,member_id,bank_account_name,bank_account_number,bank_name,bank_code,bank_address,bank_licence_electronic,is_settlement_account,";
        $sql.= "settlement_bank_account_name,settlement_bank_account_number,settlement_bank_name,settlement_bank_code,settlement_bank_address from `sc_supplier`";
        $suplier = Model()->query($sql);
        $num = $num_y =$num_n = 0;
        echo "开始时间：".date('Y-m-d H:i:s',time())."</br>";
        foreach ($suplier as $val){
            if($val['is_settlement_account'] == '1'){
                if(!empty($val['bank_account_name']) && !empty($val['bank_account_number'])){
                    $data = array(
                        'member_id'=>$val['member_id'],
                        'supplier_id'=>$val['id'],
                        'settlement_name'=>$val['bank_account_name'],
                        'settlement_number'=>$val['bank_account_number'],
                        'bank_name'=>'',
                        'bank_branch_name'=>$val['bank_name'],
                        'bank_branch_code'=>$val['bank_code'],
                        'bank_address'=>$val['bank_address'],
                    );
                    Model()->table("supplier_settlement_bank")->insert($data);
                    $num_y++;
                }else{
                    $num_n++;
                }
            }else{
                if(!empty($val['settlement_bank_account_name']) && !empty($val['settlement_bank_account_number'])){
                    $data = array(
                        'member_id'=>$val['member_id'],
                        'supplier_id'=>$val['id'],
                        'settlement_name'=>$val['settlement_bank_account_name'],
                        'settlement_number'=>$val['settlement_bank_account_number'],
                        'bank_name'=>'',
                        'bank_branch_name'=>$val['settlement_bank_name'],
                        'bank_branch_code'=>$val['settlement_bank_code'],
                        'bank_address'=>$val['settlement_bank_address'],
                    );
                    Model()->table("supplier_settlement_bank")->insert($data);
                    $num_y++;
                }else{
                    $num_n++;
                }
            }
            $num++;
        }
        $time_a->stop();
        echo "共计：".$num."（Y:".$num_y."，N:".$num_n."）</br>";
        echo "结束时间：".date('Y-m-d H:i:s',time())."</br>";
        echo "耗时：".$time_a->spent()."</br>";
    }

    public function accountOp(){
        $time_a = new Timer();
        $time_a->start();
        $suplier = Model()->query("select id,member_id,bank_account_name,bank_account_number,bank_name,bank_code,bank_address,bank_licence_electronic,is_settlement_account from `sc_supplier`");
        $num = $num_y =$num_n = 0;
        echo "开始时间：".date('Y-m-d H:i:s',time())."</br>";
        foreach ($suplier as $val){
            if(!empty($val['bank_account_name']) && !empty($val['bank_account_number'])){
                $data = array(
                    'member_id'=>$val['member_id'],
                    'supplier_id'=>$val['id'],
                    'account_name'=>$val['bank_account_name'],
                    'account_number'=>$val['bank_account_number'],
                    'bank_name'=>'',
                    'bank_branch_name'=>$val['bank_name'],
                    'bank_branch_code'=>$val['bank_code'],
                    'bank_address'=>$val['bank_address'],
                    'bank_licence_electronic'=>$val['bank_licence_electronic'],
                    'is_settlement'=>$val['is_settlement_account'],
                );
                Model()->table("supplier_account_bank")->insert($data);
                $num_y++;
            }else{
                $num_n++;
            }
            $num++;
        }
        $time_a->stop();
        echo "共计：".$num."（Y:".$num_y."，N:".$num_n."）</br>";
        echo "结束时间：".date('Y-m-d H:i:s',time())."</br>";
        echo "耗时：".$time_a->spent()."</br>";
    }

	
}


class Timer{
    private $startTime = 0; //保存脚本开始执行时的时间（以微秒的形式保存）
    private $stopTime = 0; //保存脚本结束执行时的时间（以微秒的形式保存）

    //在脚本开始处调用获取脚本开始时间的微秒值
    function start()
    {
        $this->startTime = microtime(true); //将获取的时间赋值给成员属性$startTime
    }

    //脚本结束处嗲用脚本结束的时间微秒值
    function stop()
    {
        $this->stopTime = microtime(true); //将获取的时间赋给成员属性$stopTime
    }

    //返回同一脚本中两次获取时间的差值
    function spent()
    {
        //计算后4舍5入保留4位返回
        return round(($this->stopTime - $this->startTime), 4);
    }


/*$timer= new Timer();
$timer->start();
usleep(1000);
$timer->stop();
echo "执行本次操作用时<b>".$timer->spent()."</b>秒";*/
}
?>