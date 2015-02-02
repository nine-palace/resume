<?php
namespace Common\Model;
use Think\Model\RelationModel;
use Common\Model\Pengwifi\UserModel;
/**
 * 公共模型
 * @author dengjingma
 * @time Oct 20, 2014
 */
class CommonModel extends RelationModel{
	/**
	 * 标记未删除状态
	 * @var number
	 */
	const DELETE_NO = 0;
	/**
	 * 标记已删除状态(程序中允许恢复该数据)
	 * @var number
	 */
	const DELETE_YES = 1;
	/**
	 * 标记硬删除状态(程序中不允许恢复该数据)
	 * @var number
	 */
	const DELETE_HARD = 2;
	/**
	 * 标记无效状态
	 * @var number
	 */
	const EFFECT_NO = 0;
	/**
	 * 标记有效状态
	 * @var number
	 */
	const EFFECT_YES = 1;
	
	/**
	 * 平台:windows客户端
	 * @var number
	 */
	const PLATFORM_PC_WINDOWS = 101;
	/**
	 * 平台:linux客户端
	 * @var number
	 */
	const PLATFORM_PC_LINUX = 102;
	/**
	 * 平台:mac客户端
	 * @var number
	 */
	const PLATFORM_PC_MAC = 103;
	/**
	 * 平台:windows phone app
	 * @var number
	 */
	const PLATFORM_MOBILE_WINDOWS = 201;
	/**
	 * 平台:android app
	 * @var number
	 */
	const PLATFORM_MOBILE_ANDROID = 202;
	/**
	 * 平台:ios app
	 * @var number
	 */
	const PLATFORM_MOBILE_IOS = 203;
	/**
	 * 平台:ipad app
	 * @var number
	 */
	const PLATFORM_MOBILE_IPAD = 204;
	/**
	 * 平台:firefox浏览器
	 * @var number
	 */
	const PLATFORM_BROWSER_FIREFOX = 301;
	/**
	 * 平台:chrome浏览器
	 * @var number
	 */
	const PLATFORM_BROWSER_CHROME = 302;
	/**
	 * 平台:safari浏览器
	 * @var number
	 */
	const PLATFORM_BROWSER_SAFARI = 303;
	/**
	 * 平台:ie浏览器
	 * @var number
	 */
	const PLATFORM_BROWSER_IE = 304;
	/**
	 * 平台:qq浏览器
	 * @var number
	 */
	const PLATFORM_BROWSER_QQ = 305; 
	/**
	 * 状态:创建中
	 * @var number
	 */
	const STATUS_CREATING = 0;
	/**
	 * 状态:已创建
	 * @var number
	 */
	const STATUS_CREATED = 1;
	/**
	 * 状态:审核中
	 * @var number
	 */
	const STATUS_VERIFING = 2;
	/**
	 * 状态:审核通过
	 * @var number
	 */
	const STATUS_SUCCESSED = 3;
	/**
	 * 状态:审核未通过
	 * @var number
	 */
	const STATUS_FAILED = 4;
	/**
	 * 状态:请等待
	 * @var number
	 */
	const STATUS_WAITING = 5;
	/**
	 * 状态:被举报
	 * @var number
	 */
	const STATUS_REPORTED = 6;
	/**
	 * 状态:不可用
	 * @var number
	 */
	const STATUS_UNEFFECT = 7;
	/**
	 * 状态:被删除
	 * @var number
	 */
	const STATUS_DELETED = 8;
	/**
	 * 日志纪录状态:失败
	 * @var number
	 */
	const LOG_STATUS_FAILED = 0;
	/**
	 * 日志纪录状态:成功
	 * @var number
	 */
	const LOG_STATUS_SUCCESSED = 1;
	/**
	 * 当前类中定义的所有常量的数组
	 * @var array
	 */
	protected $_constants = array();
	/**
	 * 数据库连接实例编号
	 * @var number
	 */
	protected static $_db_link_number = null;
	/**
	 * 解析模型名时的文件夹名边界值<br />
	 * 用以支持不同库文件夹下继续对模型类分文件夹排布
	 * @var string
	 */
	protected $_model_name_boundary = '';
	/**
	 * 重写父类方法,实现对不同的库连接不同的数据库实例
	 * @see \Think\Model::db()
	 */
	public function db($linkNum='',$config='',$force=false) {
	    $linkNum = is_null(self::$_db_link_number) ? $linkNum : self::$_db_link_number;
	    return parent::db($linkNum, $config, $force);
	}
	/**
	 * 获取当前类定义的常量
	 * @param string $prefix 常量前缀
	 * @return array
	 * @author dengjingma
	 * @time Oct 20, 2014
	 */
	public function getConstants($prefix = ''){
	    if(empty($this->_constants)){
	        $ref_class = new \ReflectionClass($this);
	        $this->_constants = $ref_class->getConstants();
	    }
	    if(empty($prefix)){
	        $res = $this->_constants;
	    }else{
	        $res = array();
	        $len = strlen($prefix);
	        foreach ($this->_constants as $key => $value){
	            if(substr($key, 0, $len) == $prefix){
	                $res[$key] = $value;
	            }
	        }
	    }
	    return $res;
	}
	/**
	 * 重写获取模型名方法,以支持文件夹分类形式
	 * @see \Think\Model::getModelName()
	 */
	public function getModelName() {
	    if(empty($this->name)){
	        $name = substr(get_class($this),0,-strlen(C('DEFAULT_M_LAYER')));
	        $pos = strrpos($name, '\\');
	        if ( $pos > 0 ) {//有命名空间
	            $name_arr = explode('\\', $name);
	            $_boundary = empty($this->_model_name_boundary) ? C('DEFAULT_M_LAYER') : $this->_model_name_boundary;
	            $set = array_search($_boundary, $name_arr);
	            array_splice($name_arr, 0, $set + 1, array());
	            $this->name = implode('', $name_arr);
	        }else{
	            $this->name = $name;
	        }
	    }
	    return $this->name;
	}
	/**
	 * 验证用户是否存在
	 * @param number $user_id 用户id
	 * @return boolean
	 * @author dengjingma
	 * @time Dec 26, 2014
	 */
	protected function isExistedUserId($user_id){
	    $_model = new UserModel();
	    $_user = $_model->where(array('id' => $user_id))->field('id')->find();
	    if(empty($_user)){
	        return false;
	    }else{
	        return true;
	    }
	}
	/**
	 * 验证用户是否存在
	 * @param string $user_name
	 * @return boolean
	 * @author dengjingma
	 * @time Dec 26, 2014
	 */
	protected function isExistedUserName($user_name){
	    $_model = new UserModel();
	    $_user = $_model->where(array('user_name' => $user_name))->field('id')->find();
	    if(empty($_user)){
	        return false;
	    }else{
	        return true;
	    }
	}
	/**
	 * 验证评论内容<br />
	 * 子类可重写
	 * @param string $content 帖子内容
	 * @return boolean
	 * @author dengjingma
	 * @time Oct 14, 2014
	 */
	protected function validateContent($content){
		if(empty($content)){
			return false;
		}
		return true;
	}
	/**
	 * 验证是否是一个删除状态值<br />
	 * 子类可重写
	 * @param number $is_delete 1或0标记是否删除
	 * @return boolean
	 * @author dengjingma
	 * @time Oct 20, 2014
	 */
	protected function validateIsDelete($is_delete){
		$deletes = $this->getConstants('DELETE_');
		if(in_array($is_delete, $deletes)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 验证电话号码
	 * @param string $phone 电话号码
	 * @return boolean
	 * @author dengjingma
	 * @time Jan 8, 2015
	 */
	protected function validateTelephone($phone){
	    return true;
	}
	
	/**
	 * 验证给定平台是否是一个合法的平台<br />
	 * 子类可重写
	 * @param string $platform
	 * @return boolean
	 * @author dengjingma
	 * @time Oct 20, 2014
	 */
	protected function validatePublishPlatform($platform){
	    return true;
		$platforms = $this->getConstants('PLATFORM_');
		return in_array($platform, $platforms);
	}
	/**
	 * 验证给定状态是否是一个合法状态值<br />
	 * 子类可重写
	 * @param number $status 状态值
	 * @return boolean
	 * @author dengjingma
	 * @time Oct 20, 2014
	 */
	protected function validateStatus($status){
		$statuses = $this->getConstants('STATUS_');
		if(in_array($status, $statuses)){
			return true;
		}else{
			return false;
		}
	}
	
	protected function validateTerminalId($terminal_id){
	    return is_numeric($terminal_id);
	}
	
	/**
	 * 加载额外数据库连接信息
	 * @param string $_key 配置名
	 * @author dengjingma
	 * @time Dec 23, 2014
	 */
	protected function _loadExtendConfig($_key){
	    $_configs = array(
	        'dbms'      =>  'DB_TYPE',
	        'username'  =>  'DB_USER',
	        'password'  =>  'DB_PWD',
	        'hostname'  =>  'DB_HOST',
	        'hostport'  =>  'DB_PORT',
	        'database'  =>  'DB_NAME',
	        'dsn'       =>  'DB_DSN',
	        'params'    =>  'DB_PARAMS',
	        'charset'   =>  'DB_CHARSET',
	    );
	    
	    $_extends = C('DB_EXTENDS');
	    $_config = isset($_extends[$_key]) && is_array($_extends[$_key]) ? $_extends[$_key] : array();
	    foreach ($_configs as $k => $v){
	        $this->connection[$v] = isset($_config[$v]) ? $_config[$v] : C($v);
	    }
	    if(isset($_config['DB_PREFIX'])){
	        $this->tablePrefix = $_config['DB_PREFIX'];
	    }
	}
}