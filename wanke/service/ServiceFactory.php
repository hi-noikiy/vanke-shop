<?php
/**
 * User: win7
 * Date: 2016/6/29
 * Time: 17:04
 */
class ServiceFactory
{
    /**
     * @param  $service
     * @param $base_path
     * @return Model
     */
    public function Service ($service = null, $base_path = null)
    {
        static $_cache = array();
        $cache_key = $service . '.' . $base_path;
        if (!is_null($service) && isset($_cache[$cache_key]))
            return $_cache[$cache_key];
        $base_path = $base_path == null ? BASE_WEBSERVICE_PATH : $base_path;
        $file_name = $base_path . '/service/' . $service . '.service.php';
        $class_name = $service . 'Service';
        if (file_exists($file_name)) {
            require_once($file_name);
            if (!class_exists($class_name)) {
                $error = 'Service Error:  Class ' . $class_name . ' is not exists!';
                throw_exception($error);
            } else {
                return $_cache[$cache_key] = new $class_name();
            }
        }
    }

}