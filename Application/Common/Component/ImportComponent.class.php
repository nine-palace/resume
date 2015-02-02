<?php
namespace Common\Component;
/**
 * 自定义文件加载助手
 * @author dengjingma
 * @time Sep 29, 2014
 */
class ImportComponent{
	/**
	 * 加载用户自定义语言包<br />
	 * 如果文件名为空,lang变量作为文件名<br />
	 * 如果文件名不为空,lang变量作为文件夹名<br />
	 * 
	 * 如果lang为false,忽略lang变量<br />
	 * 如果lang为空,使用配置向中的DEFAULT_LANG项<br />
	 * 
	 * 首先尝试加载common目录中的文件<br />
	 * 其后如果制定了module变量,则加载module对应的语言文件<br />
	 * 否则加载当前module的语言变量
	 * @param stirng $lang
	 * @param string $filename
	 */
	public static function lang( $module = '', $filename = '', $lang = ''){
		$lang = empty($lang) && $lang !== false ? C('DEFAULT_LANG') : $lang;
		$flag = self::_lang($lang, $filename, $module);
	}
	private static function _lang($lang, $filename, $module){
		$dir = APP_PATH.$module.DS.'Lang'.DS;
		$dir .= empty($filename) ? $lang.'.php' : $lang.DS.$filename.'.php';
		if(file_exists($dir) && is_file($dir)){
			$tmp = include $dir;
			$tmp = is_array($tmp) ? $tmp : array();
			L($tmp);
			return true;
		}else{
			return false;
		}
	}
}