<?php
namespace Common\Component;
/**
 * 日期助手类
 * @author dengjingma
 * @time Aug 21, 2014
 */
class DateComponent{
	/**
	 * 取毫秒时间
	 * @param 秒级时间戳
	 * @return integer 
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getMicroTime($time = ''){
		return empty($time) ? (int)(microtime(true) * 1000) : $time * 1000;
	}
	/**
	 * 通过日期取毫秒时间
	 * @param string $date 日期
	 * @return number
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getMicroTimeFromDate($date = ''){
		$now = empty($date) ? time() : strtotime($date);
		return self::getMicroTime($now);
	}
	/**
	 * 通过毫秒级时间戳获取秒级时间戳
	 * @param string $microtime 毫秒时间戳
	 * @return number 秒时间戳
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getTimeFromMicro($microtime = ''){
		return empty($microtime) ? time() : (int)($microtime / 1000);
	}
	/**
	 * 通过毫秒级时间戳获取日期
	 * @param number $microtime 时间戳
	 * @param string $format 日期格式
	 * @return string
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getDateFromMicro($microtime = 0, $format = 'Y-m-d'){
		$now = empty($microtime) ? time() : (int)($microtime / 1000);
		return date($format, $now);
	}
}