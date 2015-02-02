<?php
namespace Common\Component;
/**
 * 数据转换助手类
 * 
 * @author dengjingma
 * @time Dec 29, 2014
 */
class ConvertComponent{
    /**
     * 转换时,将key对应的字段转换成value对应的字段
     * @var boolean
     */
    const PARSE_KEY_TO_VALUE = true;
    /**
     * 转换时,将value对应的字段转换成key对应的字段
     * @var boolean
     */
    const PARSE_VALUE_TO_KEY = false;
    /**
     * 转换时,删除原字段(不管是否转换过该字段)
     * @var number
     */
    const PARSE_DELETE_YES = 1;
    /**
     * 转换时,不删除原字段(不管是否转换过该字段)
     * @var number
     */
    const PARSE_DELETE_NO = 2;
    /**
     * 转换时,当转换过字段后,删除原字段
     * @var number
     */
    const PARSE_DELETE_PARSED = 3;
    
    static private $_parsed_fields = array();
    /**
     * 字段值换
     * @param array $params 原始数据
     * @param array $_fields 需要转换的字段对应关系
     * @param boolean $_from_key true标识用$_fields中的key转换成value,false 反之
     * @param boolean $_is_delete 是否将原字段删除
     * @author dengjingma
     * @time Dec 26, 2014
     */
    static public function parseValues($params, $_fields = array(), $_from_key = self::PARSE_KEY_TO_VALUE, $_is_delete = self::PARSE_DELETE_NO, $_parse_force = false){
        $params = self::_parseValues($params, $_fields, $_from_key, $_is_delete, $_parse_force);
        $_parse_key = serialize($_fields);
        unset(self::$_parsed_fields[$_parse_key]);
        return $params;
    }
    static private function _parseValues($params, $_fields = array(), $_from_key = self::PARSE_KEY_TO_VALUE, $_is_delete = self::PARSE_DELETE_NO, $_parse_force = false){
//         if(is_array($_fields)){
//             foreach ($_fields as $k => $v){
//                 if($k != $v){
//                     $_from = self::PARSE_KEY_TO_VALUE === $_from_key ? $k : $v;
//                     $_to = self::PARSE_KEY_TO_VALUE === $_from_key ? $v : $k;
//                     if(!isset($params[$_to]) && isset($_from)){
//                         $parmas[$_to] = $params[$_from];
//                         if(self::PARSE_DELETE_PARSED === $_is_delete){
//                             unset($params[$_from]);
//                         }
//                     }
//                     if(self::PARSE_DELETE_YES === $_is_delete){
//                         unset($params[$_from]);
//                     }
//                 }
//             }
//         }
        $_result = array();
        $_parse_key = serialize($_fields);
        $_parse_fields = isset(self::$_parsed_fields[$_parse_key]) ? self::$_parsed_fields[$_parse_key] : array();
        if(is_array($params)){
            foreach ($params as $k => $v){
                if(self::PARSE_KEY_TO_VALUE === $_from_key && isset($_fields[$k]) && (true === $_parse_force || in_array($_fields[$k], $_parse_fields))){
                    $_result[$_fields[$k]] = $v;
                    if(self::PARSE_DELETE_NO === $_is_delete){
                        $_result[$k] = $v;
                    }
                }elseif(self::PARSE_VALUE_TO_KEY === $_from_key && in_array($k, $_fields)){
                    $_tmp_fields = array_flip($_fields);
                    if(true === $_parse_force || in_array($_tmp_fields[$k], $_parse_fields)){
                        $_result[$_tmp_fields[$k]] = $v;
                        if(self::PARSE_DELETE_NO === $_is_delete){
                            $_result[$k] = $v;
                        }
                    }else{
                        $_result[$k] = $v;
                    }
                }elseif(self::PARSE_DELETE_YES !== $_is_delete){
                    $_result[$k] = $v;
                }
            }
        }
        return $_result;
    } 
    /**
     * 对列表数据进行字段转换
     * @param array $list 列表数据
     * @param array $_fields 需要转换的字段对应关系
     * @param boolean $_from_key true标识用$_fields中的key转换成value,false 反之
     * @param boolean $_is_delete 是否将原字段删除
     * @author dengjingma
     * @time Dec 26, 2014
     */
    static public function parseValuesList($list, $_fields = array(), $_from_key = self::PARSE_KEY_TO_VALUE, $_is_delete = self::PARSE_DELETE_NO, $_parse_force = false){
        foreach ($list as $k => $v){
            $list[$k] = self::_parseValues($v, $_fields, $_from_key, $_is_delete, $_parse_force);
        }
        $_parse_key = serialize($_fields);
        unset(self::$_parsed_fields[$_parse_key]);
        return $list;
    }
    /**
     * 字段转换
     * @param array|string $params 原始字段
     * @param array $_fields 原始字段与新字段的对应关系
     * @param boolean $_from_key true标识用$_fields中的key转换成value,false 反之
     * @param boolean $_is_delete 是否将原字段删除
     * @return array|string
     * @author dengjingma
     * @time Dec 27, 2014
     */
    static public function parseFields($params, $_fields = array(), $_from_key = self::PARSE_KEY_TO_VALUE, $_is_delete = self::PARSE_DELETE_PARSED){
        $_is_array = true;
        if(!is_array($params)){
            $params = explode(GLUE_FIELD, $params);
            $_is_array = false;
        }
        $_result = array();
        $_parse_key = serialize($_fields);
        self::$_parsed_fields[$_parse_key] = array();
        if(is_array($_fields)){
            foreach ($params as $v){
                if(self::PARSE_KEY_TO_VALUE === $_from_key && isset($_fields[$v])){
                    $_result[$_fields[$v]] = 1;
                    self::$_parsed_fields[$_parse_key][] = $v;
                    if(self::PARSE_DELETE_NO === $_is_delete){
                        $_result[$v] = 1;
                    }
                }elseif(self::PARSE_VALUE_TO_KEY === $_from_key && in_array($v, $_fields)){
                    $_tmp_fields = array_flip($_fields);
                    $_result[$_tmp_fields[$v]] = 1;
                    self::$_parsed_fields[$_parse_key][] = $v;
                    if(self::PARSE_DELETE_NO === $_is_delete){
                        $_result[$v] = 1;
                    }
                }elseif(self::PARSE_DELETE_YES !== $_is_delete){
                    $_result[$v] = 1;
                }
            }
//             foreach ($_fields as $k => $v){
//                 if($v != $k){
//                     $_from = self::PARSE_KEY_TO_VALUE === $_from_key ? $k : $v;
//                     $_to = self::PARSE_KEY_TO_VALUE === $_from_key ? $v : $k;
//                     if(in_array($_from, $params)){
//                         $_result[$_to] = 1;
//                         self::$_parsed_fields[] = $_from;
//                         if(self::PARSE_DELETE_NO === $_is_delete){
//                             $_result[$_from] = 1;
//                         }
//                     }
//                 }
//             }
        }
        return $_is_array ? array_keys($_result) : implode(GLUE_FIELD, array_keys($_result));
    }
    /**
     * 获取指定的已转换的字段序列
     * @param array $_fields
     * @return multitype:
     * @author dengjingma
     * @time Jan 9, 2015
     */
    static public function getParsedFields($_fields = array()){
        $key = self::getKey($_fields);
        return isset(self::$_parsed_fields[$key]) ? self::$_parsed_fields[$key] : array();
    }
    /**
     * 设置指定的转换字段序列
     * @param array $_fields
     * @param array $value
     * @author dengjingma
     * @time Jan 9, 2015
     */
    static public function setParsedFields($_fields = array(), $value = array()){
        $key = self::getKey($_fields);
        if(!empty($key)){
            self::$_parsed_fields[$key] = $value;
        }
    }
    static private function getKey($_fields = array()){
        return serialize($_fields);
    }
}