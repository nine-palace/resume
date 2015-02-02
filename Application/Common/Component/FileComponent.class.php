<?php
namespace Common\Component;
use Common\Component\UtilsComponent;
use Think\Upload;
/**
 * 文件相关助手类<br />
 * 依赖:<br />
 * UtilsComponent 工具助手类<br />
 * Think\Upload  ThinkPHP内置文件上传类
 * @author dengjingma
 * @time Aug 14, 2014
 */
class FileComponent{
	static public $error = '';
	/**
	 * 上传文件
	 * @param array $config 配置信息
	 * @param string $dir 上传目的文件夹
	 * @return boolean
	 * @author dengjingma
	 * @time Aug 14, 2014
	 */
	static public function upload($config=array(),$_files = '', $dir = 'images'){
		if(!$dir){ return false; }
		if(!is_array($config)){ return false;}
		if(!isset($config['maxSize']) || $config['maxSize'] < 1){
			$size = C('UPLOAD_MAX_SIZE') ? intval(C('UPLOAD_MAX_SIZE')) : 1024 * 1024;
			$config['maxSize'] = $size;
		}
		if(!isset($config['UPLOAD_ALLOW_EXTS'])){
			$exts = C('UPLOAD_ALLOW_EXTS') ? C('UPLOAD_ALLOW_EXTS') : array();
			$config['exts'] = $exts;
		}
		$save_path = "public/upload/" . $dir . "/" . date('Ym/d', UtilsComponent::gmtTime()) . "/"; 					//上传至服务器的相对路径
		$config['rootPath'] = APP_PATH.'../';
		$config['savePath'] = $save_path;
		$save_path = $config['rootPath'].$config['savePath'];
		if(!is_dir($save_path)){															//绝对路径
			$res = UtilsComponent::mkdir($save_path);
		}
		$config['autoSub'] = false;
		$upload = new Upload($config);
		$res = $upload->upload($_files);
		if(!$res) {	// 上传错误提示错误信息
			self::$error = $upload->getError();
			return false;
		}else{																				//上传成功 获取上传文件信息
			return $res;
		}
	}
}