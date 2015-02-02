<?php
namespace Common\Component;
class UtilsComponent{
	/**
	 * 获取类型(不包含命名空间)
	 * @param object $obj 目标对象
	 * @param string $suffix
	 * @return Ambigous <multitype:, unknown, mixed>
	 * @author dengjingma
	 * @time Sep 24, 2014
	 */
	static public function getClassName($obj, $suffix = ''){
		$name = get_class($obj);
		$name = explode("\\", $name);
		$key = count($name) - 1;
		if(isset($name[$key])){
			$name = $name[$key];
			if(!empty($suffix)){
				$name = str_replace($suffix, '', $name);
			}
		}
		return $name;
	}
	/**
	 * 发送邮件
	 * @param strig $title
	 * @param string $content
	 * @param array $address
	 * @author dengjingma
	 * @time Aug 13, 2014
	 */
	static public function sendEmail($title,$content, $address = array()){
		try {
			//邮件提醒
			require_once APP_PATH."system/utils/es_mail.php";
			$mail = new mail_sender();
			$address = is_array($address) ? $address : array($address);
			foreach ($address as $v){
				$mail->AddAddress($v);
			}
			$mail->IsHTML(false); 	// 设置邮件格式为 HTML
			$mail->Subject = $title;   // 标题
			$mail->Body = $content;  // 内容
			$mail->Send();
		} catch (\Exception $e) {
			
		}
	}
	
	/**
	 * 获得当前格林威治时间的时间戳
	 * @return  integer
	 */
	static public function gmtTime($data=""){
		if($data){
			$nowData = strtotime($data);
		}else{
			$nowData = time();
		}
		return ($nowData - date('Z'));
	}
	
	/**
	 * 创建目录
	 * @param string $dir 要创建的目录
	 * @param number $mode 目录权限
	 * @return boolean
	 * @author dengjingma
	 * @time Aug 14, 2014
	 */
	static public function mkdir($dir, $mode = 0777){
		if (is_dir($dir) || @mkdir($dir,$mode)) return true;
		if (!self::mkdir(dirname($dir),$mode)) return false;
		return @mkdir($dir,$mode);
	}
	
	/**
	 * 生成唯一字符串
	 * @param number $length 字符串长度
	 * @author dengjingma
	 * @time Oct 9, 2014
	 */
	static public function unique($length = 32){
		$length = (int)$length;
		if($length < 1){
			return '';
		}
		$times = (int)(ceil($length / 32));
		$str = '';
		for($i = 0; $i < $times; $i++){
			$chars = md5(uniqid(mt_rand(), true));
			$t = md5($chars.time());
			$str .= $t;
		}
		return substr($str, 0, $length);
	}
	
	/**
	 * 获取验证码
	 * @param number $type 类型,1为纯数字,2为纯字母,3为字母数字混合
	 * @param number $length 验证码长度,默认为6
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getVerify($type = 1, $length = 6){
	    switch ($type){
	    }
	}
	/**
	 * 获取一个指定长度的随机数
	 * @param number $length 长度
	 * @return number
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getNumberVerify($length = 6){
	    if($length < 1){ $length = 6;}
	    $tmp = $length - 1;
	    $start = pow(10, $tmp);
	    $end = pow(10, $length) - 1;
	    return mt_rand($start, $end);
	}
	
	static public function getStringVerify($length = 6){
	
	}
}