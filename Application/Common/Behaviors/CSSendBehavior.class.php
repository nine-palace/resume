<?php
namespace Common\Behaviors;
use Think\Behavior;
/**
 * css结束行为
 * @author dengjingma
 * @time Aug 29, 2014
 */
class CSSendBehavior extends Behavior{
	
	public function run(&$param){
		$css = ob_get_clean();
		$GLOBALS['content_for_css'] = isset($GLOBALS['content_for_css']) ? $GLOBALS['content_for_css'].$css : $css;
	}
}