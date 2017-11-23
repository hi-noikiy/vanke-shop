<?php
/**
 * 记录日志 
 *
 *
 */

class Log{

    const SQL       = 'SQL';
    const ERR       = 'ERR';
    const IN_ERR       = 'IN_ERR';
    const INFO       = 'INFO';
    const IN_INFO       = 'IN_INFO';
    const MOBILE_MESSAGE = 'MOBILE_MESSAGE';
    private static $log =   array();

    public static function record($message,$level=self::ERR) {
        $now = @date('Y-m-d H:i:s',time());
        switch ($level) {
            case self::SQL:
               self::$log[] = "[{$now}] {$level}: {$message}\r\n";
               break;
            case self::ERR:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'err.log';
                $url = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
                $url .= " ( act={$_GET['act']}&op={$_GET['op']} ) ";
                $content = "[{$now}] {$url}\r\n{$level}: {$message}\r\n";
                try{
                    file_put_contents($log_file,$content, FILE_APPEND);
                }catch(Exception $e) {
                }
                break;
        }
    }
    
    public static function record4inter($message,$level=self::ERR) {
        $now = @date('Y-m-d H:i:s',time());
        switch ($level) {
            case self::SQL:
               self::$log[] = "[{$now}] {$level}: {$message}\r\n";
               break;
            case self::ERR:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'out_inter_error.log';
                break;
            case self::INFO:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'out_inter_info.log';
                break;
            case self::IN_ERR:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'in_inter_error.log';
                break;
            case self::IN_INFO:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'in_inter_info.log';
                break;
            case self::MOBILE_MESSAGE:
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'mobile_msg_info.log';
                break;
            default :
                $log_file = BASE_DATA_PATH.'/log/'.date('Ymd',TIMESTAMP).'info.log';
                break;
         
        }
        $url = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
        $url .= " ( act={$_GET['act']}&op={$_GET['op']} ) ";
        $content = "[{$now}] {$url}\r\n{$level}: {$message}\r\n";
        try{
            file_put_contents($log_file,$content, FILE_APPEND);
        }catch(Exception $e) {
            self::record(json_encode($e,true),self::ERR);
        }
    }

    public static function read(){
    	return self::$log;
    }
}