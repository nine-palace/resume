<?php
namespace Common\Controller;
use Think\Controller\RestController;
use Common\Service\ApisService;
use Common\Service\ApplicationsService;
use Common\Service\CommonService;
use Common\Component\FilterComponent;
use Common\Component\DateComponent;
use Common\Component\UtilsComponent;
use Common\Component\MemcacheComponent;
use Common\Component\ArrayComponent;
use Common\Service\TokensService;
use Common\Model\Penglife\ApplicationsModel;
use Common\Model\Penglife\ApplicationPermissionsModel;
/**
 * 多模块公共控制器类
 * @author dengjingma
 * @time Sep 29, 2014
 */
abstract class BasicController extends RestController{
	/**
	 * 应用标识
	 * @var string
	 */
	protected $app_key = null;
	/**
	 * 应用密钥
	 * @var string
	 */
	protected $secret_key = null;
	/**
	 * 随机数
	 * @var string
	 */
	protected $nonce = null;
	/**
	 * 时间戳
	 * @var integer
	 */
	protected $timestamp = null;
	/**
	 * 访问令牌
	 * @var string
	 */
	protected $token = null;
	/**
	 * 签名
	 * @var string
	 */
	protected $signature = null;
	/**
	 * 签名参数数组
	 * @var array
	 */
	protected $signature_arr = array();
	/**
	 * 参数数组
	 * @var array
	 */
	protected $params = array();
	/**
	 * 当前操作的用户id
	 * @var number
	 */
	protected $user_id = '';
	/**
	 * 当前操作的用户名
	 * @var string
	 */
	protected $user_name = '';
	/**
	 * 默认的分页数量
	 * @var integer
	 */
	protected $limit = 0;
	/**
	 * 默认的页码
	 * @var integer
	 */
	protected $page = 1;
	/**
	 * 查询的数据状态
	 * @var number
	 */
	protected $select_status = null;
	/**
	 * 是否自动解析status参数
	 * @var boolean
	 */
	protected $auto_parse_status = false;
	/**
	 * 当前访问模式,api or web
	 * @var string
	 */
	protected $mode = 'api';
	/**
	 * http访问方法前缀
	 * @var string
	 */
	protected $method_prefix = '_';
	
	/**
	 * 图片服务器地址
	 * @var string
	 */
	protected $_domain_image = '';
	protected $_service = null;
	/**
	 * 标识是否是由于请求来源自动创建的控制器
	 * @var boolean
	 */
	private $_is_auto = true;
	/**
	 * 构造函数
	 * @param boolean $_is_auto 标识是否是自动创建的该类<br />
	 * true 是  false 否<br />
	 * 如果是自动创建的,则程序会进行对应的验证<br />
	 * 若在程序中希望使用控制器中的某些功能,在实例化控制器类时,应将该值置为false
	 * @author dengjingma
	 * @time Dec 26, 2014
	 */
	public function __construct($_is_auto = true){
	    $this->_is_auto = $_is_auto;
	    if(empty($this->_domain_image)){
	        $this->_domain_image = C('DOMAIN_IMAGE') ? C('DOMAIN_IMAGE') : '';
	    }
	    parent::__construct();
	}
	/**
	 * GET请求时执行的方法<br />
	 * 通常执行数据查询类业务<br />
	 * @access protected 限制直接访问
	 * @see \Think\Controller::get()
	 */
	protected function _get(){
		$this->response(L('ERROR_INVALID_REQUEST_METHOD'), '405_ERROR_REQUEST_METHOD_GET');
	}
	/**
	 * POST请求时执行的方法<br />
	 * 通常执行数据新增类业务<br />
	 * @access protected 限制直接访问
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	protected function _post(){
		$this->response(L('ERROR_INVALID_REQUEST_METHOD'), '405_ERROR_REQUEST_METHOD_POST');
	}
	/**
	 * PUT请求时执行的方法<br />
	 * 通常执行数据更新类业务<br />
	 * @access protected 限制直接访问
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	protected function _put(){
		$this->response(L('ERROR_INVALID_REQUEST_METHOD'), '405_ERROR_REQUEST_METHOD_PUT');
	}
	/**
	 * DELETE请求时执行的方法<br />
	 * 通常执行数据删除类业务<br />
	 * @access protected 限制直接访问
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	protected function _delete(){
		$this->response(L('ERROR_INVALID_REQUEST_METHOD'), '405_ERROR_REQUEST_METHOD_DELETE');
	}
	/**
	 * 魔术方法,当执行不存在或不可访问的类方法时执行<br />
	 * HTTP不同请求方式映射到不同的方法上主要就是此方法实现
	 * @see \Think\Controller\RestController::__call()
	 */
	public function __call($method, $args){
		$_method = $this->method_prefix.$this->_method;
		if(method_exists($this, $_method)){
		    return $this->$_method();
		}else{
			$this->response('', '404_ERROR_REQUEST_NOT_FOUND');
		}
	}
	protected function _initialize(){
	    //如果是发生请求而自动创建的该类,则进行对应的验证
	    //如果是程序运行中手动创建的,则不进行验证
	    if(true === $this->_is_auto){
    		$this->authRequestMethod();
    		$this->loadCommonData();
    		$this->afterInit();
	    }
	    $this->_parseStatus();
	}
	/**
	 * 初始化之后自动调用,子类可重写<br />
	 * 在action之前执行
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	protected function afterInit(){
	
	}
	/**
	 * 验证请求来源是否有效
	 * @author dengjingma
	 * @time Sep 12, 2014
	 */
	protected function authApp(){
		$app_key = $this->app_key;
		$secret_key = $this->secret_key;
		$app_service = new ApplicationsService();
		$this->_terminal_id = $app_service->checkApp($app_key, $secret_key, $this->current_platform);
		if(false === $this->_terminal_id){
			$this->response($app_service->getError('msg'), $app_service->getError('code'));
		}
		return true;
	}
	/**
	 * 验证随机数<br />
	 * 验证内容包括:<br />
	 * 随机数位数,随机数是否重复
	 * @author dengjingma
	 * @time Sep 12, 2014
	 */
	protected function authNonce(){
		$nonce = $this->nonce;
		if(empty($nonce)){
			$this->response(L('ERROR_EMPTY_NONCE'), '400_ERROR_EMPTY_NONCE');
		}
		$prefix = C('PREFIX_NONCE') ? C('PREFIX_NONCE') : '';
		$_key = $prefix.$nonce;
		$res = MemcacheComponent::get($prefix.$nonce);
		if($res){
			$this->response(L('ERROR_INVALID_NONCE'), '400_ERROR_INVALID_NONCE');
		}
		$expired = C('API_EXPIRED') ? C('API_EXPIRED') : 300;
		MemcacheComponent::set($_key, 1, $expired);
		return true;
	}
	/**
	 * 验证访问权限
	 * @param string $app_key 应用标识
	 * @param string $api_name 接口名称(如果为空, 取当前访问的接口名称)
	 * @param string $platform 平台名称(如果为空,取当前访问的平台名称)
	 * @param mixed $perm  要验证的权限,以数字或数组形式给定(如果为空,取当前访问的请求方式对应的权限)
	 * @author dengjingma
	 * @time Sep 25, 2014
	 */
	protected function authPermission($app_key = '', $api_name = '', $platform = '', $perm = array()){
		$app_key = $this->app_key;
		$service = new ApplicationsService();
		$api_service = new ApisService();
		$api_name = empty($api_name) ? UtilsComponent::getClassName($this, 'Controller') : $api_name;
		$platform = empty($platform) ? $this->current_platform : $platform;
		if(empty($perm)){
			switch (strtolower($this->_method)){
				case 'get':
				case 'header': $perm = ApplicationPermissionsModel::PERM_GET;break;
				case 'post': $perm = ApplicationPermissionsModel::PERM_POST;break;
				case 'put':
				case 'patch': $perm = ApplicationPermissionsModel::PERM_PUT;break;
				case 'delete': $perm = ApplicationPermissionsModel::PERM_DELETE;break;
				default : $perm = 0;
			}
		}
		if(false == $service->checkPermByPlatform($app_key, $platform, $api_name, $perm)){
			$this->response(L('ERROR_NEED_PERMISSION'), '403_ERROR_NO_PERMISSION_TO_REQUEST');
		}
	}
	/**
	 * 验证接口访问频次
	 * @param string $app_key 应用标识
	 * @param string $action 接口名称(为空标识验证总频次,否则验证某个接口的频次)
	 * @return boolean
	 * @author dengjingma
	 * @time Sep 16, 2014
	 */
	protected function authRate($app_key, $action = ''){
	
		return true;
	}
	/**
	 * 验证签名
	 * @author dengjingma
	 * @time Sep 24, 2014
	 */
	protected function authSignature(){
		$arr = $this->signature_arr;
		$signature = $this->signature;
		ksort($arr);
		$str = ArrayComponent::implode($arr, '&', '=');
		$new_signature = sha1($str);
		if($new_signature !== $signature){
			$this->response(L('ERROR_INVALID_SIGNATURE'), '400_ERROR_INVALID_SIGNATURE');
		}
	}
	/**
	 * 验证时间戳
	 * @author dengjingma
	 * @time Sep 12, 2014
	 */
	protected function authTimestamp(){
		$timestamp = $this->timestamp;
		if(empty($timestamp)){
			$this->response(L('ERROR_EMPTY_TIMESTAMP'), '400_ERROR_EMPTY_TIMESTAMP');
		}
		$now = DateComponent::getMicroTime();
		$expired = C('API_EXPIRED') ? C('API_EXPIRED') : 300;
		$expired = DateComponent::getMicroTime($expired);
		if(($timestamp + $expired) < $now){
			$this->response(L('ERROR_INVALID_TIMESTAMP'), '400_ERROR_INVALID_TIMESTAMP');
		}
		return false;
	}
	/**
	 * 验证token<br />
	 * 子类可重写
	 * @author dengjingma
	 * @time Aug 14, 2014
	 */
	protected function authToken($auto_return = true){
		$service = new TokensService();
		$user = $service->checkGeneralToken($this->token, $this->_terminal_id);
		if(false == $user){
		    if($auto_return){
			     $this->response($service->getError('msg'), $service->getError('code'));
		    }else{
		        return false;
		    }
		}else{
			$this->user_id = $user['id'];
			$this->user_name = $user['user_name'];
		}
	}
	/**
	 * 验证请求方式
	 *
	 * @author dengjingma
	 * @time Aug 24, 2014
	 */
	private function authRequestMethod(){
		//验证请求方法
		if(!in_array($this->_method, array('get', 'post'))){
			//如果是非GET,POST请求,则要特殊处理传递的参数
			parse_str(file_get_contents('php://input'), $this->params);
			$this->params = array_merge($_GET, $this->params);
		}else{
			$this->params = $_REQUEST;
		}
		$this->allowMethod = C('ALLOW_REQUEST_METHODS') ? C('ALLOW_REQUEST_METHODS') : $this->allowMethod;
		//HTTP请求方法兼容
		$flag = C('COMPATIBLE_REQUEST_METHOD') ? C('COMPATIBLE_REQUEST_METHOD') : true;
		if(false === $flag){
			//不使用兼容模式的请求方式,则使用HTTP原生的请求方式
			$_method = strtolower($_SERVER['REQUEST_METHOD']);
		}else{
			$_method = isset($this->params['_method']) ? FilterComponent::get($this->params['_method']) : '';
			//使用兼容模式的请求方式,则优先使用_method参数的值
			if(empty($_method)){
				$_method = strtolower($_SERVER['REQUEST_METHOD']);
			}else{
				$_method = strtolower($_method);
				if(!in_array($_method, $this->allowMethod)){
					$_method = strtolower($_SERVER['REQUEST_METHOD']);
				}
			}
		}
		if(!in_array($_method, $this->allowMethod)){
			$this->response(L('ERROR_INVALID_REQUEST_METHOD'), '400_NOT_ALLOW_REQUEST_METHOD');
		}
		$this->_method = $_method;
	}
	/**
	 * 验证用户是否已登录
	 * @return boolean
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	protected function isLogin(){
		return false;
	}
	/**
	 * 加载一些公共值
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	protected function loadCommonData(){
		$var_app_key = C('VAR_APP_KEY') ? C('VAR_APP_KEY') : 'app_key';
		$var_secret_key = C('VAR_SECRET_KEY') ? C('VAR_SECRET_KEY') : 'secret_key';
		$var_nonce = C('VAR_NONCE') ? C('VAR_NONCE') : 'nonce';
		$var_timestamp = C('VAR_TIMESTAMP') ? C('VAR_TIMESTAMP') : 'timestamp';
		$var_token = C('VAR_TOKEN') ? C('VAR_TOKEN') : 'token';
		$var_signature = C('VAR_SIGNATURE') ? C('VAR_SIGNATURE') : 'signature';
		$var_platform = C('VAR_PLATFORM') ? C('VAR_PLATFORM') : 'platform';
		$this->app_key = isset($this->params[$var_app_key]) ? FilterComponent::get($this->params[$var_app_key]) : null;
		$this->nonce = isset($this->params[$var_nonce]) ? FilterComponent::getString($this->params[$var_nonce]) : null;
		$this->timestamp = isset($this->params[$var_timestamp]) ? FilterComponent::get($this->params[$var_timestamp], 'int') : null;
		$this->token = isset($this->params[$var_token]) ? FilterComponent::get($this->params[$var_token]) : null;
		$this->signature = isset($this->params[$var_signature]) ? FilterComponent::get($this->params[$var_signature]) : null;
		//$this->push_platfrom = isset($this->params[$var_platform]) ? FilterComponent::get($this->params[$var_platform]) : null;
		$service = new ApplicationsService();
		$t = $service->getOne(array('app_key' => $this->app_key), 'secret_key');
		$this->secret_key = empty($t) ? '' : $t['secret_key'];
		$this->signature_arr = array(
				$var_nonce 		=> $this->nonce,
				$var_app_key 	=> $this->app_key,
				$var_secret_key => $this->secret_key,
				$var_timestamp 	=> $this->timestamp,
				$var_token 		=> $this->token
		);
		
		$var_page = C('VAR_PAGE') ? C('VAR_PAGE') : 'page';
		$var_limit = C('VAR_LIMIT') ? C('VAR_LIMIT') : 'limit';
		$var_count = C('VAR_COUNT') ? C('VAR_COUNT') : 'count';
		$var_next = C('VAR_NEXT') ? C('VAR_NEXT') : 'next';
		$this->limit = isset($this->params[$var_limit]) ? $this->params[$var_limit] : (C('DEFAULT_LIMIT') >= 0 ? C('DEFAULT_LIMIT') : 10);
		$this->page = isset($this->params[$var_page]) ? $this->params[$var_page] : (C('DEFAULT_PAGE') ? C('DEFAULT_PAGE') : 1);
		$is_count = isset($this->params[$var_count]) ? FilterComponent::getInt($this->params[$var_count]) : null;
		$is_next = isset($this->params[$var_next]) ? FilterComponent::getInt($this->params[$var_next]) : (C('DEFAULT_IS_NEXT') ? C('DEFAULT_IS_NEXT') : $service::NEXT_YES);
		if(!is_numeric($this->limit)){
			$this->limit = C('DEFAULT_LIMIT') >= 0 ? C('DEFAUL_LIMIT') : 0;
		}
		if(!is_numeric($this->page)){
			$this->page = C('DEFAULT_PAGE') ? C('DEFAULT_PAGE') : 1;
		}
		CommonService::setLimit($this->limit, $this->page);
		if(1 === $is_count){
		    CommonService::setIsCount($service::COUNT_YES);
		}elseif(0 === $is_count){
		    CommonService::setIsNext($service::COUNT_YES);
		}else{
		    $is_count = C('DEFAULT_IS_COUNT');
		    if($service::COUNT_YES === $is_count || $service::COUNT_NO === $is_count){
		        CommonService::setIsCount($is_count);
		    }
		}
		if(1 === $is_next){
		    CommonService::setIsNext($service::NEXT_YES);
		}elseif(0 === $is_next){
		    CommonService::setIsNext($service::NEXT_NO);
		}else{
		    $is_next = C('DEFAULT_IS_NEXT');
		    if($service::NEXT_YES === $is_next || $service::NEXT_NO === $is_next){
		        CommonService::setIsNext($is_next);
		    }
		}
	}
	/**
	 * 输出数据
	 * @param mixed $data  原数据
	 * @param number $status 结果状态,默认为200
	 * @param string $mode 输出形式,当该值为web且当前不是ajax访问时,输出结果将会以web页面形式呈现
	 * @param string $type 输出格式,默认为json
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	protected function response($data = null, $status = 200,  $mode = null, $type = null){
		$mode = empty($mode) ? $this->mode : $mode;
		$_debug_msg = '';
		if(!is_numeric($status)){
		    $_tmp = explode('_', $status);
		    $status = isset($_tmp[0]) ? $_tmp[0] : 400;
		    $status = (int)($status);
		    array_shift($_tmp);
		    $_debug_msg = implode('_', $_tmp);
		}
		if($mode === 'web' && false === IS_AJAX){
			if($status == 200){
				$this->success($data);
			}else{
				$this->error($data);
			}
			exit;
		}else{
			$response = array('status' => $status, 'message' => '', 'data' => array());
			if((defined('APP_DEBUG') && TRUE === APP_DEBUG && !empty($_debug_msg)) || (isset($this->params['_debug']) && (true == $this->params['_debug']))){
			    $response['debug'] = $_debug_msg;
			}
			if($data === '' || $data === null){
				$status = (string)$status;
				$response['message'] = L('HTTP_'.$status);
			}elseif (is_string($data)){
				$response['message'] = $data;
			}else{
				$response['data'] = $data;
			}
			$type = $this->getOutputType($type);
			parent::response($response, $type);
		}
	}
	/**
	 * 获取输出数据类型<br />
	 * 当前只输出json格式
	 * @return string
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	protected function getOutputType($type){
		$type = !empty($type) && isset($this->allowOutputType[$type]) ? $type : $this->_type;
		$type = isset($this->allowOutputType[$type]) ? $type : $this->defaultType;
		return 'json';
	}
	/**
	 * 解析status参数
	 * 
	 * @author dengjingma
	 * @time Jan 8, 2015
	 */
	protected function _parseStatus(){
	    //自动解析status参数
	    if(true === $this->auto_parse_status){
	        $_default_status = null;
	        $_statuses = array();
	        if(!empty($this->_service)){
	            $_model = $this->_service->model;
	            $_default_status = $_model::STATUS_SUCCESSED;
	            $_statuses = $_model->getConstants('STATUS_');
	        }
	        $_status = isset($this->params['status']) ? FilterComponent::getInt($this->params['status'], $_default_status) : $_default_status;
	        if(is_array($_statuses) && in_array($_status, $_statuses)){
	            $this->select_status = $_status;
	        }else{
	            $this->select_status = $_default_status;
	        }
	    }
	}
}