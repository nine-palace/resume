<?php
namespace Common\Behaviors;
use Think\Behavior;
/**
 * js开始行为
 * @author dengjingma
 * @time Aug 29, 2014
 */
class JSstartBehavior extends Behavior{
	
	public function run(&$param){
		ob_start();
	}
}