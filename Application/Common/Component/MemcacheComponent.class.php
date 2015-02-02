<?php
namespace Common\Component;
/**
 * 缓存助手类<br />
 * 依赖:<br />
 * Think\Cache\Driver\Memcache  ThinkPHP内置memcache类<br />
 * Think\Cache\Driver\Memcached ThinkPHP内置memcached类
 * @author dengjingma
 * @time Aug 5, 2014
 */
class MemcacheComponent{
	static private $cache = null;
	
	/**
	 * 初始化缓存
	 * @return boolean
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static public function init(){
		if(empty(self::$cache)){
			if(extension_loaded('memcache') && class_exists('\Think\Cache\Driver\Memcache')){
				self::$cache = new \Think\Cache\Driver\Memcache();
			}elseif(extension_loaded('memcached') && class_exists('\Think\Cache\Driver\Memcached')){
				self::$cache = new \Think\Cache\Driver\Memcached();
			}else{
				return false;
			}
		}
		return true;
	}
	/**
	 * 缓存数据
	 * @param string $name 键名
	 * @param mixed $value 缓存值
	 * @param string $expired 缓存时间,为false时,取默认时间
	 * @return boolean
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static public function set($name, $value, $expired = false){
		self::init();
		if(false === $expired || is_null($expired)){
			$expired = C('DEFAULT_CACHE_EXPIRED') ? C('DEFAULT_CACHE_EXPIRED') : 86400;
		}
		return self::$cache->set($name, $value, $expired);
	}
	/**
	 * 读取缓存数据
	 * @param string $name 键名
	 * @return mixed
	 * @author dengjingma
	 * @time Aug 5, 2014
	 */
	static public function get($name){
		self::init();
		return self::$cache->get($name);
	}
	/**
	 * 删除缓存数据
	 * @param string $name
	 * @author dengjingma
	 * @time Aug 8, 2014
	 */
	static public function delete($name){
		self::init();
		return self::$cache->delete($name);
	}
}