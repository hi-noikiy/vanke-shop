<?php
/**
 * 手机端首页控制
 */




class indexControl extends mobileHomeControl{

    public function __construct() {
        parent::__construct();
    } 

    /**
     * 首页
     */
    public function indexOp() {
        $model_mb_special = Model('mb_special');
        $data = $model_mb_special->getMbSpecialIndex();
        $this->_output_special($data, $_GET['type']);
    }

    /**
     * 专题
     */
    public function specialOp() {
        $model_mb_special = Model('mb_special');
        $data = $model_mb_special->getMbSpecialItemUsableListByID($_GET['special_id']);

        //fix
        $special_info = $model_mb_special->getMbSpecialInfo($_GET['special_id'],'*');
        $outdata = array(
            'special_id' => $_GET['special_id'],
            'special_desc' => $special_info['special_desc'],
            'list' => $data
        );
        $this->_output_special($outdata, $_GET['type'], $_GET['special_id']);
//        $this->_output_special($data, $_GET['type'], $_GET['special_id']);
    }

    /**
     * 输出专题
     */
    private function _output_special($data, $type = 'json', $special_id = 0) {
        $model_special = Model('mb_special');
        if($_GET['type'] == 'html') {
            $html_path = $model_special->getMbSpecialHtmlPath($special_id);
            if(!is_file($html_path)) {
                ob_start();
                Tpl::output('list', $data);
                Tpl::showpage('mb_special');
                file_put_contents($html_path, ob_get_clean());
            }
            header('Location: ' . $model_special->getMbSpecialHtmlUrl($special_id));
            die;
        } else {
            output_data($data);
        }
    }
    /**
     * 默认搜索词列表
     */
    public function search_key_listOp() {
		//热门搜索
        $list = @explode(',',C('hot_search'));
        if (!$list || !is_array($list)) { 
            $list = array();
        }
                
        //历史搜索
        if (cookie('his_sh') != '') {
            $his_search_list = explode('~', cookie('his_sh'));
        }

        $data['list'] = $list;
		$data['his_list'] = is_array($his_search_list) ? $his_search_list : array();
		output_data($data);
    }
	
    /**
     * 热门搜索列表
     */
    public function search_hot_infoOp() {
				//热门搜索
        if (C('rec_search') != '') {
            $rec_search_list = @unserialize(C('rec_search'));
			$rec_value = array();
			foreach($rec_search_list as $v){
				$rec_value[] = $v['value'];
			}
			
        }
        output_data(array('hot_info'=>$result ? $rec_value : array()));
    }

    /**
     * 高级搜索
     */
    public function search_advOp() {
        $area_list = Model('area')->getAreaList(array('area_deep'=>1),'area_id,area_name');
        if (C('contract_allow') == 1) {
            $contract_list = Model('contract')->getContractItemByCache();
            $_tmp = array();$i = 0;
            foreach ($contract_list as $k => $v) {
                $_tmp[$i]['id'] = $v['cti_id'];
                $_tmp[$i]['name'] = $v['cti_name'];
                $i++;
            }
        }
        output_data(array('area_list'=>$area_list ? $area_list : array(),'contract_list'=>$_tmp));
    }

    /**
     * android客户端版本号
     */
    public function apk_versionOp() {
        $version = C('mobile_apk_version');
        $url = C('mobile_apk');
        if(empty($version)) {
           $version = '';
        }
        if(empty($url)) {
            $url = '';
        }

        output_data(array('version' => $version, 'url' => $url));
    }
}
