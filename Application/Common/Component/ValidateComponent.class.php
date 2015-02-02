<?php
namespace Common\Component;
/**
 * 验证助手类
 * @author dengjingma
 * @time Aug 13, 2014
 */
class ValidateComponent{
	static private $phone_reg = '/^(\+?86)?1[3-8]\\d{9}$/i';
	static private $fixed_phone_reg = '/^(\\d{2,4}-?)?\\d{7,8}$/i';
	
	/**
	 * 验证邮箱地址
	 * @param string $email 邮箱地址
	 * @param string $reg 自定义验证格式
	 * @return boolean|string 验证通过返回原数据,否则返回false
	 * @author dengjingma
	 * @time Sep 26, 2014
	 */
	static public function email($email, $reg = ''){
		if(empty($reg)){
			$res = filter_var($email, FILTER_VALIDATE_EMAIL);
		}else{
			$res = preg_match($reg, $email) ? $email : false;
		}
		return $res;
	}
	/**
	 * 验证电话号码(可以为手机号码或固定电话)
	 * @param string $phone 电话号码
	 * @param string $reg 验证表达式,如果为空,则使用默认表达式
	 * @param string $filter 返回结果是否过滤国家前缀
	 * @return boolean|mixed 若输入电话不是一个合法的电话号码,返回false<br />
	 * 否则返回过滤后的电话号码
	 * @author dengjingma
	 * @time Aug 13, 2014
	 */
	static public function phone($phone, $reg = '', $filter = true){
		$res = self::telephone($phone, $reg, $filter);
		if(false === $res){
			$res = self::fixphone($phone, $reg, $filter);
		}
		return $res;
	}
	/**
	 * 验证手机号码
	 * @param string $phone 手机号码
	 * @param string $reg 验证表达式,如果为空,则使用默认表达式
	 * @param string $filter 返回结果是否过滤国家前缀
	 * @return boolean|string 若输入手机号不是一个合法的手机号码,返回false<br />
	 * 否则返回过滤后的手机号码 
	 * @author dengjingma
	 * @time Sep 26, 2014
	 */
	static public function telephone($phone, $reg = '', $filter = true){
		$phone = (string)$phone;
		$reg = empty($reg) ? self::$phone_reg : $reg;
		if(!preg_match($reg, $phone)){
			return false;
		}elseif($filter){
			$phone = self::filterCountryPrefix($phone);
		}
		return $phone;
	}
	/**
	 * 验证固定电话
	 * @param string $phone 固定电话号码
	 * @param string $reg 验证表达式,如果为空,则使用默认表达式
	 * @param string $filter 返回结果是否过滤国家前缀
	 * @return boolean|string 若输入的固定电话不是一个合法的固定电话号码,返回false<br />
	 * 否则返回过滤后的固定电话号码
	 * @author dengjingma
	 * @time Sep 26, 2014
	 */
	static public function fixphone($phone, $reg = '', $filter = true){
		$phone = (string)$phone;
		$reg = empty($reg) ? self::$fixed_phone_reg : $reg;
		if(!preg_match($reg, $phone)){
			return false;
		}elseif($filter){
			$phone = self::filterCountryPrefix($phone);
		}
		return $phone;
	}
	/**
	 * 过滤国家前缀
	 * @param string $phone 电话号码
	 * @return mixed
	 * @author dengjingma
	 * @time Sep 26, 2014
	 */
	static public function filterCountryPrefix($phone){
		return preg_replace('/^\+?86/i', '', $phone);
	}
	/**
	 * 验证url地址
	 * @param string $url url地址
	 * @param string $reg 自定义验证规则
	 * @return boolean|string 验证通过返回原数据,否则返回false
	 * @author dengjingma
	 * @time Sep 26, 2014
	 */
	static public function url($url, $reg = ''){
		if(empty($reg)){
			$res = filter_var($url, FILTER_VALIDATE_URL);
		}else{
			$res = preg_match($reg, $url) ? $url : false;
		}
		return $res;
	}
	/**
	 * 验证一段文字内是否含有敏感词
	 * @param string $text 将要验证的文字内容
	 * @return boolean 有返回true 否则false
	 * @author dengjingma
	 * @time Jan 16, 2015
	 */
	static public function hasSensitiveWords($text){
	    return false;
	}
}