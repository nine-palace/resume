<?php
namespace Common\Component;
/**
 * 加密解密助手类
 * @author dengjingma
 * @time Aug 12, 2014
 */
class EncryptComponent{
	
	static public function baiduPushEncrypt($arr){
		return json_encode($arr);
	}
	
	static public function baiduPushDecrypt($str){
		return json_decode($str, true);
	}
	
	static public function ajaxEncrypt($data){
		return json_encode($data);
	}
	
	static public function ajaxDecrypt($data){
		return json_decode($data, true);
	}
}