<?php
namespace Common\Component;
/**
 * 数组操作组件类
 * @author dengjingma
 * @time Sep 17, 2014
 */
class ArrayComponent{
	
	public static function dataToArray($dbData, $keyword) {
	    $retArray = array ();
	    if (is_array ( $dbData ) == false or empty ( $dbData )) {
	        return $retArray;
	    }
	    foreach ( $dbData as $oneData ) {
	        if (isset ( $oneData [$keyword] ) and empty ( $oneData [$keyword] ) == false) {
	            $retArray [] = $oneData [$keyword];
	        }
	    }
	    return $retArray;
	}
    static public function getKeyByValue(Array $arr, $value){
    	if(!in_array($value, $arr)) { return null;}
    	foreach ($arr as $k => $v){
    		if($v == $value){
    			return $k;
    		}
    	}
    	return null;
    }
    /**
     * 数组连接
     * @param array $arr 原数组
     * @param string $value_glue 数组元素的分隔符
     * @param mixed $key_glue 键与值之间的分隔符,若为true,则只连接元素值,若为false,则只连接键
     * @param boolean $deep 当元素为存在数组时,是否当元素继续连接
     * @return mixed 如果传入的是数组,则返回连接后的字符串,否则,返回参数本身
     * @author dengjingma
     * @time Sep 17, 2014
     */
    static public function implode($arr, $glue = '', $key_glue = false, $deep = true){
    	if(!is_array($arr)){
    		return $arr;
    	}else{
    		$str = '';
    		$glue = (string)$glue;
    		foreach ($arr as $key => $value){
    			if(true === $key_glue){
    				$str .= is_array($value) ? (true === $deep ? $glue.self::implode($value, $glue, $key_glue, $deep) : '') : $glue.$value;
    			}elseif(false === $key_glue){
    				$str .= $glue.$key;
    			}else{
    				$str .= $glue.$key.$key_glue.self::implode($value, $glue, $key_glue, $deep);
    			}
    		}
    		if(!empty($str)){
    			$len = mb_strlen($glue);
    			$str = mb_substr($str, $len);
    		}
    		return $str;
    	}
    }
}