<?php
namespace Common\Component;
/**
 * session助手类
 * @author dengjingma
 * @time Aug 5, 2014
 */
class SessionComponent{
	static private $sessionId = null;
	static private $memcache_config = array();
	static private $cache = null;
	static private $expired = null;
	static private $default_expired = 900;
	static private $cookie_name = 'GlobSessionCookie';
	static private $cookie_url = 'http://x.pengwifi.com/system/utils/es_session_globeCookie.php?';
	/**
	 * 是否优先使用缓存存储
	 * @var boolean
	 */
	static public $use_cache = false;
	
	static public function start(){
		self::getMaxLifeTime();
		@session_start();
		self::$sessionId = self::getId();
		self::upSMTime();
	}
	/**
	 * 清空session
	 * 
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static public function clear(){
		unset($_SESSION);
		session_destroy();
	}
	/**
	 * 关闭session读写
	 * 
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static public function close(){
		@session_write_close();
	}
	/**
	 * 删除session
	 * @param string $name
	 * @param boolean $useCache 是否优先操作cache
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function delete($name, $useCache = null){
		$flag = is_null($useCache) ? self::$use_cache : $useCache;
		if($flag){
			$flag = MemcacheComponent::delete(self::$sessionId);
			if($flag){
				self::delMemcacheSessionKey($name);
			}
		}
		if(!$flag){
			unset($_SESSION[C('AUTH_KEY').$name]);
		}
	}
	/**
	 * 判断某个session值是否存在
	 * @param unknown $name
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function isExisted($name){
		
	}
	/**
	 * 是否过期
	 * @return boolean
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function isExpired(){
		$key = md5(C('AUTH_KEY').'expire');
		if(isset($_SESSION[$key]) && $_SESSION[$key] < time()){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 设置session值
	 * @param unknown $name
	 * @param unknown $value
	 * @param string $useCache
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function set($name, $value, $useCache = null){
		$flag = is_null($useCache) ? self::$use_cache : $useCache;
		if($flag){
			$flag = self::setMemcacheSessionKey($name);
			if($flag){
				if(is_array($value)){ $value['login_time'] = time();}
				$flag = MemcacheComponent::set(self::$sessionId, $value);
			}
		}
		if(!$flag){
			//var_dump(C('auth_key').$name);EXIT;d41d8cd98f00b204e9800998ecf8427e
			$_SESSION[C('AUTH_KEY').$name] = $value;
		}
	}
	/**
	 * 设置session过期时间
	 * @param unknown $num
	 * @param string $unit
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static public function setExpired($num, $unit = 'm'){
		$lsecond = time();
		switch ($unit){
			case 'w':
			case 'week':
			case 'weeks':
				self::$expired = $lsecond + ($num * 7 * 86000);break;
			case 'd':
			case 'day':
			case 'days':
				self::$expired = $lsecond + ($num * 86000);break;
			case 'h':
			case 'hour':
			case 'hours':
				self::$expired = $lsecond + ($num * 36000);break;
			case 'm':
			case 'minute':
			case 'minutes':
				self::$expired = $lsecond + ($num * 60);break;
			case 's':
			case 'second':
			case 'seconds':
				self::$expired = $lsecond + $num;break;
		}
	}
	/**
	 * 获取session值
	 * @param stirng $name
	 * @param boolean $useCache 是否优先使用缓存
	 * @return mixed
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function get($name, $useCache = null){
		$flag = is_null($useCache) ? self::$use_cache : $useCache;
		if($flag){
			$flag = MemcacheComponent::get($name);
		}
		if(!$flag){
			$flag = isset($_SESSION[C('AUTH_KEY').$name]) ? $_SESSION[C('AUTH_KEY').$name] : null;
		}
		return $flag;
	}
	/**
	 * 获取cache中的session
	 * @param string $name
	 * @return array
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function getMemcacheSessions($name){
		$indexs = MemcacheComponent::get($name);
		$indexs ? '' : array();
		$newIndexs = array();
		$sessions = array();
		foreach ($indexs as $key => $value){
			if($data = MemcacheComponent::get($key)){
				$newIndexs[$key] = 'sessionID';
				$sessions[] = $data;
			}
		}
		MemcacheComponent::set($name, $newIndexs, 0);
		return $sessions;
	}
	/**
	 * 获取远程地址
	 * @param string $type
	 * @return string
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function getRemoteURL($type){
		$url = self::$cookie_url.'a='.$type.'&cn='.self::$cookie_name;
		$url .= $type == 'set' ? '&si='.self::$sessionId.'&st='.self::$expired : '';
		$url .= '&callbak=?';
		return $url;
	}
	/**
	 * 获取远程访问的js脚本
	 * @param string $type
	 * @return string
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function getScript($type){
		$script = "$.getJSON('".self::getRemoteURL($type)."',function(){});";
		return $script;
	}
	
	/**
	 * 获取session有效期
	 * 
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static private function getMaxLifeTime(){
		$lsecond = time();
		$sys_second = ini_get('session.gc_maxlifetime');
		if($sys_second <= 0){
			self::$expired = $lsecond + self::$default_expired;
		}else{
			self::$expired = $lsecond + $sys_second;
		}
	}
	/**
	 * 生成一个session id
	 * 
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static private function getId(){
		return session_id();
	}
	/**
	 * 删除memcache中session键值
	 * @param string $name
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static private function delMemcacheSessionKey($name){
		$indexs = MemcacheComponent::get($name);
		$indexs ? '' : array();
		if(isset($indexs[self::$sessionId]) && $indexs[self::$sessionId] == 'sessionID'){
			unset($indexs[self::$sessionId]);
			MemcacheComponent::set($name, $indexs, 0);
		}
	}
	/**
	 * 获取memcache中session键值
	 * @param string $name
	 * @return array
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static private function getMemcacheSessionKey($name){
		$indexs = MemcacheComponent::get($name);
		$indexs ? '' : array();
		$newIndexs = array();
		foreach ($indexs as $key => $value){
			if($data = MemcacheComponent::get($key)){
				$newIndexs[$key] = 'sessionID';
			}		
		}
		MemcacheComponent::set($name, $newIndexs, 0);
		return $newIndexs;
	}
	/**
	 * 设置memcache中session键值
	 * @param string $name
	 * @return boolean
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static private function setMemcacheSessionKey($name){
		$indexs = MemcacheComponent::get($name);
		$indexs ? '' : $indexs = array();
		$indexs[self::$sessionId] = 'sessionID';
		return MemcacheComponent::set($name, $indexs, 0);
	}
	/**
	 * 更新session时间
	 * 
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static private function updateSessionTime(){
		if(self::$cache){
			$value = self::$cache->get(self::$sessionId);
			if($value){
				self::MemcacheSet(self::$sessionId, $value);
			}
		}
	}
}