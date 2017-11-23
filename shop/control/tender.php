<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/10/26
 * Time: 上午10:20
 * 6D5DF11F-CAB0-4F06-8C06-F640C8AB3AE7
 */
class tenderControl extends HomeControl {

    //获取招标列表请求地址
    private $tenderListShow = '/impac/restapi/tender/tenderListShow';

    //获取标书文件请求地址
    private $tenderFile = '/impac/restapi/tender/tenderFile';

    //投标请求地址
    private $tenderInsert = '/impac/restapi/tender/tenderInsert';

    //我要投标请求地址
    private $tenderRegister = '/impac/restapi/tender/tenderRegister';


    public function __construct(){
        parent::__construct();
    }


    /**
     * 招标首页
     */
    public function indexOp(){
        Language::read('home_article_index');
        $lang	= Language::getLangContent();
        Tpl::setLayout('home_layout');
        Tpl::output('index_sign','tender');
        $nav_link = array(
            array('title'=>$lang['homepage'], 'link'=>SHOP_SITE_URL),
            array('title'=>'招标信息',)
        );
        Model('seo')->type('article')->param(array('article_class'=>'招标信息'))->show();
        $tender_name = empty($_GET['tender_name']) ? '':$_GET['tender_name'];
        //获取供应商的相关数据信息
        $model = Model();
        $member_info = $model->table('member')->where("member_id = '".$_SESSION['member_id']."'")->find();
        if($member_info['role_id'] == '03' || $member_info['role_id'] == '02'){//校验是否属于认证供应商
            Tpl::output('nav_link_list',$nav_link);
            Tpl::showpage('parentIframe.tender');
        }
    }


    public function getTenderListOp(){
        $page = empty($_GET['page']) ? '1':$_GET['page'];
        $tender_name = empty($_GET['tender_name']) ? '':$_GET['tender_name'];
        //获取供应商的相关数据信息
        $model = Model();
        $member_info = $model->table('member')->where("member_id = '".$_SESSION['member_id']."'")->find();
        if($member_info['role_id'] == '03' || $member_info['role_id'] == '02'){//校验是否属于认证供应商
            $supplier_data = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->find();
            //处理供应商类型为空
            if(!empty($supplier_data['type_json']) && is_array(json_decode($supplier_data['type_json'],true))) {
                //获取城市中心数据
                $city_code = array();
                if (!empty($supplier_data['city_center_list']) && is_array(explode(',', $supplier_data['city_center_list']))) {
                    foreach (explode(',', $supplier_data['city_center_list']) as $val) {
                        $cityinfo = $model->table("city_centre")->field('zt_city_code')->where("id = '" . $val . "'")->find();
                        if (!empty($cityinfo['zt_city_code'])) {
                            $city_code[] = "'" . $cityinfo['zt_city_code'] . "'";
                        }
                    }

                }
                $type_data = array();
                if (!empty($supplier_data['type_json']) && is_array(json_decode($supplier_data['type_json'], true))) {
                    foreach (json_decode($supplier_data['type_json'], true) as $vl) {
                        foreach ($vl as $tp) {
                            $type_data[] = $tp;
                        }
                    }
                }
                $send_data = array(
                    'page' => $page,
                    'rows' => '10',
                    'supplier_code' => $supplier_data['business_licence_number'],
                    'company_code' => implode(',', $city_code),
                    'tender_category' => implode('|', $type_data),
                    'tender_name' => $tender_name,
                );
                $url = $this->getSendUrl() . $this->tenderListShow;
                $return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
                $rest_data = json_decode($return_json, true);
                $type_data = array(
                    'C122TENDERAPPROVEDISCARD-VS' => '审批拒绝',
                    'C122TENDERAPPROVEING-VS' => '审批中',
                    'C122TENDERCANCEL-VS' => '招标取消',
                    'C122TENDERFINISH-VS' => '定标完成',
                    'C122TENDERREVIEW-VS' => '投标中',
                    'C122WAITTENDERAPPROVE-VS' => '待审批',
                    'C122WAITTENDERING-VS' => '待投标',
                    'C122WAITTENDERREGISTER-VS' => '待报名',
                    'C122WAITTENDERRELEASE-VS' => '待发布',
                    'C122WAITTOBECANDIDATE-VS' => '待入围'
                );
                if (!empty($rest_data) && is_array($rest_data['data'])) {
                    $new_data = array();
                    foreach ($rest_data['data'] as $va) {
                        //获取城市公司名称
                        $city_info = $model->table('city_centre')->where("zt_city_code = '" . $va['partyCode'] . "'")->find();
                        $va['city_name'] = $city_info['city_name'];
                        $va['type_name'] = $type_data[$va['statusId']];
                        $va['tenderName'] = htmlspecialchars($va['tenderName']);
                        $new_data[] = $va;
                    }
                    $rest_data['data'] = $new_data;
                }
                echo json_encode($rest_data);
            }else {
                $new_data = array(
                    'code' => '-1',
                    'msg' => '供应商类型不能为空，请联系管理员进行添加补充',
                    'count' => '0',
                    'data' => '',
                );
                echo json_encode($new_data);
            }
        }
    }


    //报名
    public function signTenderOp(){
        $model = Model();
        $member_info = $model->table('member')->where("member_id = '".$_SESSION['member_id']."'")->find();
        if(($member_info['role_id'] == '03' || $member_info['role_id'] == '02') && !empty($_POST['tender_id'])){//校验是否属于认证供应商
            $supplier_data = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->find();
            $send_data = array(
                'supplier_code'=>$supplier_data['business_licence_number'],
                'tender_id'=>$_POST['tender_id'],
            );
            $url = $this->getSendUrl().$this->tenderRegister;
            $return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
            $rest_data = json_decode($return_json,true);
            echo $rest_data['code'];
        }else{
            echo "非供应商账户";
        }
    }



    //标书列表
    public function tenderMaterialOp(){
        if(!empty($_GET['tender_id'])){
            Tpl::output('title',$_GET['title']);
            Tpl::output('end_time',$_GET['time']);
            Tpl::output('tender_id',$_GET['tender_id']);
            Tpl::output('city',$_GET['city']);
            Tpl::showpage('parentIframe.tender.material');
        }
    }

    //获取标书文件列表
    public function getTenderMaterialOp(){
        if(!empty($_GET['tender_id'])) {
            $page = empty($_GET['page']) ? '1':$_GET['page'];
            $send_data = array(
                'tender_id' => $_GET['tender_id'],
            );
            $url = $this->getSendUrl().$this->tenderFile;
            $return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
            $rest_data = json_decode($return_json, true);
            if($rest_data['code'] == '0' && !empty($rest_data['data'])){
                $list_data = $this->pageArrayList(5,$page,$rest_data['data']);
                $new_data = array(
                    'code'  => $rest_data['code'],
                    'msg'   => $rest_data['msg'],
                    'count' => $rest_data['count'],
                    'data'  => $list_data,
                );
                echo json_encode($new_data);
            }else{
                echo $return_json;
            }
        }
    }


    //投标
    public function bidTenderOp(){
        $model = Model();
        $member_info = $model->table('member')->where("member_id = '".$_SESSION['member_id']."'")->find();
        if( ($member_info['role_id'] == '03' || $member_info['role_id'] == '02') && !empty($_POST['tender_id'])){//校验是否属于认证供应商
            $supplier_data = $model->table('supplier')->where("member_id = '".$_SESSION['member_id']."'")->find();
            $send_data = array(
                'supplier_code'=>$supplier_data['business_licence_number'],
                'tender_id'=>$_POST['tender_id'],
            );
            $url = $this->getSendUrl().$this->tenderInsert;
            $return_json = WebServiceUtil::getDataByCurl($url, json_encode($send_data), 1);
            $rest_data = json_decode($return_json,true);
            echo $rest_data['code'];
        }else{
            echo "非供应商账户";
        }
    }



    private function pageArrayList($count,$page,$array,$order='0'){
        $page=(empty($page)) ? '1':$page; #判断当前页面是否为空 如果为空就表示为第一页面
        $start=($page-1)*$count; #计算每次分页的开始位置
        if($order==1){
            $array=array_reverse($array);
        }
        $totals=count($array);
        //$countpage=ceil($totals/$count); #计算总页面数
        $pagedata=array();
        $pagedata=array_slice($array,$start,$count);
        return $pagedata;  #返回查询数据
    }

}