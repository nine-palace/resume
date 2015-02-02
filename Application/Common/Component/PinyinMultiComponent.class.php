<?php
namespace Common\Component;
use Common\Component\PinyinTableComponent;
use Common\Component\PinyinTableWithTuneComponent;
/**
 * 汉字转拼音助手类<br />
 * 依赖:<br />
 * Common\Component\PinyinTableComponent  无声调拼音表助手类<br />
 * Common\Component\PinyinTableWithTuneComponent  带声调拼音表助手类<br />
 * @author dengjingma
 * @time Aug 7, 2014
 */
class PinyinMultiComponent{
	private static $tables = array();
	private static $filter = array(
		214 => array(216 => array('chong'))
	);
	/**
	 * 是否首字母大写
	 * @var boolean
	 */
	public static $isFirstUpper = true;
	/**
	 * 前缀
	 * @var string
	 */
	public static $prefix = '';
	/**
	 * 后缀
	 * @var string
	 */
	public static $suffix = '';
	/**
	 * 需要几位拼音,为0表示全部
	 * @var number
	 */
	public static $length = 1;
	/**
	 * 是否需要声调
	 * @var boolean
	 */
	public static $tune = false;
	/**
	 * 是否以数组形式返回结果
	 * @var boolean
	 */
	public static $returnArray = false;
	/**
	 * 每个字拼音分隔符
	 * @var string
	 */
	public static $returnDS = '';
	
	public static function getPinyin($string, $code = 'utf-8'){
		if(self::$tune === true){
			self::$tables = PinyinTableWithTuneComponent::getPinyinTable();
		}else{
			self::$tables = PinyinTableComponent::getPinyinTable();
		}
		$flow = array();
		if($code != 'gb2312'){
			$string = self::Utf8($string);
		}
		for ($i=0;$i<strlen($string);$i++){
			if (ord($string[$i]) >= 0x81 and ord($string[$i]) <= 0xfe){
				$h = ord($string[$i]);
				if (isset($string[$i+1])){
					$i++;
					$l = ord($string[$i]);
					if(isset(self::$filter[$h][$l])){
						array_push($flow, self::$filter[$h][$l]);
					}elseif (isset(self::$tables[$h][$l])){
						array_push($flow,self::$tables[$h][$l]);
					}else{
						array_push($flow,$h);
						array_push($flow,$l);
					}
				}else{
					array_push($flow,ord($string[$i]));
				}
			}else{
				array_push($flow,ord($string[$i]));
			}
		}
		$res = self::_pinyin($flow);
		return self::$returnArray === true ? $res : implode(self::$returnDS, $res);
	}
	private static function _pinyin($flow){
		$pinyin = array();
		$pinyin[0] = '';
		for ($i=0;$i<sizeof($flow);$i++){
			if (is_array($flow[$i])){
				if (sizeof($flow[$i]) == 1){
					foreach ($pinyin as $key => $value){
						$t = self::$length > 0 ? substr($flow[$i][0], 0, self::$length) : $flow[$i][0];
						$t = self::$isFirstUpper === true ? strtoupper(substr($t, 0, 1)).substr($t, 1) : $t;
						$pinyin[$key] .= self::$prefix.$t.self::$suffix;
					}
				}
				if (sizeof($flow[$i]) > 1){
					$tmp1 = $pinyin;
					foreach ($pinyin as $key => $value){
						$t = self::$length > 0 ? substr($flow[$i][0], 0, self::$length) : $flow[$i][0];
						$t = self::$isFirstUpper === true ? strtoupper(substr($t, 0, 1)).substr($t, 1) : $t;
						$pinyin[$key] .= self::$prefix.$t.self::$suffix;
					}
					for ($j=1;$j<sizeof($flow[$i]);$j++){
						$tmp2 = $tmp1;
						for ($k=0;$k<sizeof($tmp2);$k++){
							$t = self::$length > 0 ? substr($flow[$i][$j], 0, self::$length) : $flow[$i][$j];
							$t = self::$isFirstUpper === true ? strtoupper(substr($t, 0, 1)).substr($t, 1) : $t;
							$tmp2[$k] .= self::$prefix.$t.self::$suffix;
						}
						array_splice($pinyin,sizeof($pinyin),0,$tmp2);
					}
				}
			}else{
				foreach ($pinyin as $key => $value)
				{
					$pinyin[$key] .= chr($flow[$i]);
				}
			}
		}
		return $pinyin;
	}
	
	private static function Utf8($_C){
		$_String = '';
		if($_C < 0x80){
			$_String .= $_C;
		}elseif($_C < 0x800)	{
			$_String .= chr(0xC0 | $_C>>6);
			$_String .= chr(0x80 | $_C & 0x3F);
		}elseif($_C < 0x10000){
			$_String .= chr(0xE0 | $_C>>12);
			$_String .= chr(0x80 | $_C>>6 & 0x3F);
			$_String .= chr(0x80 | $_C & 0x3F);
		} elseif($_C < 0x200000) {
			$_String .= chr(0xF0 | $_C>>18);
			$_String .= chr(0x80 | $_C>>12 & 0x3F);
			$_String .= chr(0x80 | $_C>>6 & 0x3F);
			$_String .= chr(0x80 | $_C & 0x3F);
		}
		return iconv('UTF-8', 'GB2312', $_String);
	}
}