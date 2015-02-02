<?php
namespace Common\Behaviors;
use Think\Behavior;
/**
 * js结束行为
 * @author dengjingma
 * @time Aug 29, 2014
 */
class JSendBehavior extends Behavior{
	
	public function run(&$params){
		$js = ob_get_clean();
		$GLOBALS['content_for_js'] = isset($GLOBALS['content_for_js']) ? $GLOBALS['content_for_js'].$js : $js;
	}
}