<?php
namespace Common\Component;
use Common\Service\PageService;
/**
 * 分页类
 * @author dengjingma
 * @time Aug 4, 2014
 */
class PageComponent{
	/**
	 * 默认分页样式
	 * @var integer
	 */
	const STYLE_DEFAULT = 1;
	/**
	 * 数字分页样式
	 * @var integer
	 */
	const STYLE_NUMBER = 2;
	/**
	 * 可选每页显示数量范围
	 * @var array
	 */
	static public $page_all_limits = array(15, 30, 50, 100);
	static public $page_config = array(
			'header'	=>	'PAGE_RECORD',
			'prev'		=>	'PAGE_PREV',
			'next'		=>	'PAGE_NEXT',
			'first'		=>	'PAGE_FIRST',
			'last'		=>	'PAGE_LAST',
			'theme'		=>	'PAGE_THEME',
			'number_length'	=>	'PAGE_NUMBER_LENGTH',
			'number_first'	=>	'PAGE_NUMBER_FIRST',
			'number_last'	=>	'PAGE_NUMBER_LAST',
			'number_prev'	=>	'PAGE_NUMBER_PREV',
			'number_next'	=>	'PAGE_NUMBER_NEXT',
			'number_glue'	=>	'PAGE_NUMBER_GLUE',
			'title_first'	=>	'PAGE_TITLE_FIRST',
			'title_last'	=>	'PAGE_TITLE_LAST',
			'title_prev'	=>	'PAGE_TITLE_PREV',
			'title_next'	=>	'PAGE_TITLE_NEXT'
	);
	
	static private $url = '';
	/**
	 * 分页方法
	 * @param number $total 总记录数
	 * @param number $limit 每页记录数
	 * @param boolean $isRange 是否可选每页数量
	 * @return string
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	static public function page($total, $limit = 10, $isRange = false, $style = self::STYLE_NUMBER){
		$p = new PageService($total, $limit);
		$p->rollPage = C('PAGE_ROLLPAGE');
		foreach (self::$page_config as $k => $c){
			if(C($c)){
				$p->setConfig($k, C($c));
			}
		}
		$p->lastSuffix = false;
		$page = $style == self::STYLE_NUMBER ? $p->numberRange() : $p->show();
		if($isRange){
			$onPageNum[$limit] = "class='fousepageNum'";
			$page .= '<span>显示记录数量: ';
			$param = $_GET;
			$var_limit = C('VAR_LIMIT') ? C('VAR_LIMIT') : 'limit';
			foreach (self::$page_all_limits as $v){
				$param[$var_limit] = $v;
				$page .= "<a href='".U('', $param)."' ".$onPageNum[$v].">{$v}</a> ";
			}
			$page .= '</span>';
		}
		return $page;
	}
	
	static private function numberRange($total, $limit = 10, $current = 1){
		$page = '';
		
	}
}