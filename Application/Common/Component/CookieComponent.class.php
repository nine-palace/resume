<?php
namespace Common\Component;
/**
 * Cookie管理类
 * @author dengjingma
 * @time Aug 4, 2014
 */
class CookieComponent{
	/**
	 * 判断Cookie是否存在
	 * @param string $name cookie名称
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	static function is_set($name) {
		return isset($_COOKIE[C('COOKIE_PREFIX').$name]);
	}
	
	/**
	 * 获取某个Cookie值
	 * @param string $name cookie名称
	 * @return mixed cookie值
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	static function get($name) {
		$value   = $_COOKIE[C('COOKIE_PREFIX').$name];
		return self::decodeCookie($value);
	}
	
	/**
	 * 设置某个Cookie值
	 * @param string $name  cookie名称
	 * @param mixed $value	cookie值
	 * @param string $expire 有效时长
	 * @param string $path 
	 * @param string $domain 有效域名
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	static function set($name,$value,$expire='',$path='',$domain='') {
		if($expire=='') {
			$expire =   C('COOKIE_EXPIRE');
		}
		if(empty($path)) {
			$path = C('COOKIE_PATH');
		}
		if(empty($domain)) {
			$domain =   C('COOKIE_DOMAIN');
		}
		$expire =   !empty($expire)?    time()+$expire   :  0;
		setcookie(C('COOKIE_PREFIX').$name, self::encodeCookie($value),$expire,$path,$domain);
		$_COOKIE[C('COOKIE_PREFIX').$name]  =   $value;
	}
	
	/**
	 * 删除某个Cookie值
	 * @param string $name cookie名称
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	static function delete($name) {
		self::set($name,'',-3600);
		unset($_COOKIE[C('COOKIE_PREFIX').$name]);
	}
	
	/**
	 * 清空Cookie值
	 * 
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	static function clear() {
		unset($_COOKIE);
	}
	/**
	 * cookie加密
	 * @param mixed $value
	 * @return string
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static private function encodeCookie($value){
		return base64_encode(serialize($value));
	}
	/**
	 * cookie解密
	 * @param string $value
	 * @return mixed
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static private function decodeCookie($value){
		return unserialize(base64_decode($value));
	}
}