<?php
namespace Common\Component;
class MobileComponent{
    static public $_detect = null;

    
    
    private function _init(){
        if(empty(self::$_detect)){
            require COMMON_PATH.'Plugins/MobileDetect/Mobile_Detect.php';
            self::$_detect = new \Mobile_Detect();
        }
    }
}