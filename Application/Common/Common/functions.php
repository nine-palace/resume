<?php
use Common\Component\StringComponent;
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	return StringComponent::substr($str, $length, $charset, $suffix);
}
/**
 * 检查字符串是否是UTF8编码
 * @param string $string 字符串
 * @return Boolean
 */
function is_utf8($string) {
	return StringComponent::isUtf8($string);
}
/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len=6,$type='',$addChars='') {
	return StringComponent::rand($len, $type, $addChars);
}
/**
 * 从数组中删除指定value值
 * @param string $value 要删除的值
 * @param array $array 原数组
 * @return array 删除数据后的数组
 * @author dengjingma
 * @time Jan 6, 2015
 */
function array_unset($value, $array){
    $_k = array_search($value, $array);
    array_splice($array, $_k, 1);
    return $array;
}