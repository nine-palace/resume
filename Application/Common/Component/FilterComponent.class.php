<?php
namespace Common\Component;
/**
 * 数据过滤助手类
 * @author dengjingma
 * @time Aug 6, 2014
 */
class FilterComponent{
	/**
	 * 数据过滤
	 * @param mixed $value 原数据
	 * @param string $type 需要的数据类型 默认为字符串形
	 * int|integer	整形
	 * float|double 浮点形
	 * string 字符串形
	 * html	网页代码
	 * @param mixed $default 默认值
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	static public function get($value, $type = 'string', $default = null){
		$res = null;
		switch ($type){
			case 'string' : $res = self::getString($value, $default);break;
			case 'int' :
			case 'integer' : $res = self::getInt($value, $default); break;
			case 'float' :
			case 'double' : $res = self::getFloat($value, $default);break;
			case 'html'	: $res = self::getHtml($value, $default);break;
			default : $res = self::getString($value, $default);
		}
		return $res;
	}
	/**
	 * 过滤整形
	 * @param number $value
	 * @return number
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	static public function getInt($value, $default = null){
		$tmp = filter_var($value, FILTER_VALIDATE_INT); 
		if(false === $tmp && !is_null($default)){
			return $default;
		}
		return $tmp;
	}
	/**
	 * 过滤浮点形
	 * @param float $value
	 * @return number
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	static public function getFloat($value, $default = null){
		$tmp = filter_var($value, FILTER_VALIDATE_FLOAT);
		if(false === $tmp && !is_null($default)){
			return $default;
		}
		return $tmp;
	}
	
	/**
	 * 过滤字符串
	 * @param string $value
	 * @return string
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	static public function getString($value){
		$strings = strip_tags($value);
		$strings = str_replace("'", '&#39;', $strings);
		$strings = str_replace("\"", '&quot;', $strings);
		$strings = str_replace("\\", '', $strings);
		$strings = str_replace("\/", '', $strings);
		$strings = trim($strings);
		return $strings;
	}
	/**
	 * 过滤html
	 * @param unknown $val
	 * @return mixed
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	static public function getHtml($val) {
		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
		$val = preg_replace('/([\x00-\x08|\x0b-\x0c|\x0e-\x19])/', '', $val);
	
		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=@avascript:alert('XSS')>
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++) {
			// ;? matches the ;, which is optional
			// 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
			// @ @ search for the hex values
			$val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
			// @ @ 0{0,7} matches '0' zero to seven times
			$val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
		}
	
		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', '<script', 'object', 'iframe', 'frame', 'frameset', 'ilayer'/* , 'layer' */, 'bgsound', 'base');
		$ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra = array_merge($ra1, $ra2);
	
		$found = true; // keep replacing as long as the previous round replaced something
		while ($found == true) {
			$val_before = $val;
			for ($i = 0; $i < sizeof($ra); $i++) {
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++) {
					if ($j > 0) {
						$pattern .= '(';
						$pattern .= '(&#[xX]0{0,8}([9ab]);)';
						$pattern .= '|';
						$pattern .= '|(&#0{0,8}([9|10|13]);)';
						$pattern .= ')*';
					}
					$pattern .= $ra[$i][$j];
				}
				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
				$val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
				if ($val_before == $val) {
					// no replacements were made, so exit the loop
					$found = false;
				}
			}
		}
		return $val;
	}
	/**
	 * 得到url地址
	 * @param unknown $value
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	static public function getUrl($value){
		return self::getString($value);
	}
}