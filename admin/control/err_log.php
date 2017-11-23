<?php
/**
 * Created by PhpStorm.
 * User: zhengguiyun
 * Date: 2017/10/16
 * Time: 下午3:06
 */

class err_logControl extends SystemControl{

    private $path = BASE_DATA_PATH . DS . "log" ;

    public function __construct(){
        parent::__construct();
    }



    public function indexOp(){
        Tpl::showpage('err_log_list');
    }


    public function getLogListOp(){
        $page = empty($_GET['page']) ? '1':$_GET['page'];
        $list = array(
            'code'  => 0,
            'msg'   => '',
            'count' => $this->getFileCounts(),
            'data'  => $this->pageArrayList(10,$page,$this->listDir($this->path)),
        );
        echo json_encode($list);
    }

    public function readlogOp(){
        $file_path=$this->path.DS.$_GET['name'].'.log';
        if(empty($_GET['name']) && is_dir($file_path)){
            echo "错误";
        }else{
            $fp=fopen($file_path,'a+');
            $contents=fread($fp,filesize($file_path));
            $contents=str_replace("\r\n","<br />",$contents);
            echo $contents;
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


    private function getFileCounts(){
        $handle = opendir($this->path);
        $i = 0;
        while(false !== $file=(readdir($handle))){
            if($file !== '.' && $file != '..') {
                list($filesname,$kzm) = explode(".",$file);
                if($kzm == 'log') {
                    $i++;
                }
            }
        }
        closedir($handle);
        return $i;
    }


    private function listDir($dir){
        $data = array();
        if(is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if((is_dir($dir."/".$file)) && $file!="." && $file!="..") {
                        echo "<b><font color='red'>文件名：</font></b>",$file,"<br><hr>";
                        listDir($dir."/".$file."/");
                    }else{
                        if($file!="." && $file!="..") {
                            list($filesname,$kzm) = explode(".",$file);
                            if($kzm == 'log') {
                                $data[] = array(
                                    'ky'    =>substr($filesname,0,8),
                                    'name'=>$filesname,
                                    'time'=>substr($filesname,0,4).'/'.substr($filesname,4,2).'/'.substr($filesname,6,2),
                                );
                                //$string = "<a href='/admin/index.php?act=err_log&op=readlog&nm=".$file."'";
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
        if(!empty($data)){
            $flag=array();
            foreach($data as $arr2){
                $flag[]=$arr2["ky"];
            }
            array_multisort($flag, SORT_DESC, $data);
        }
        return $data;
    }

}