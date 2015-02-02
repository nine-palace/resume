<?php
namespace Common\Service;
use Common\Service\ModuleVersionsService;
use Common\Model\Penglife\ModuleVersionsModel;
use Common\Model\Pengwifi\LogModel;
use Common\Component\ConvertComponent;
/**
 * 公共服务类
 * @author dengjingma
 * @time Aug 4, 2014
 */
class CommonService{
    /**
     * 指定自动转换字段
     * @var boolean
     */
    const AUTO_PARSE_FIELD_YES = TRUE;
    /**
     * 指定不自动转换字段
     * @var boolean
     */
    const AUTO_PARSE_FIELD_NO = FALSE;
    /**
     * 指定返回结果中带总数
     * @var boolean
     */
    const COUNT_YES = TRUE;
    /**
     * 指定返回结果中不带总数
     * @var boolean
     */
    const COUNT_NO = FALSE;
    /**
     * 指定是否返回纪录总数 根据配置而定
     * @var number
     */
    const COUNT_CONF = -1;
    /**
     * 指定返回结果中带是否有下一页的信息
     * @var boolean
     */
    const NEXT_YES = TRUE;
    /**
     * 指定返回结果中不带是否有下一页的信息
     * @var boolean
     */
    const NEXT_NO = FALSE;
    /**
     * 指定是否返回 下一页标识  根据配置而定
     * @var number
     */
    const NEXT_CONF = -1;
    /**
     * 指定返回结果不分页,读取全部信息
     * @var number
     */
    const LIMIT_ALL = 0;
    /**
     * 指定返回结果数量根据配置而定
     * @var boolean
     */
    const LIMIT_CONF = FALSE;
    /**
     * 指定返回结果页数根据配置而定
     * @var boolean
     */
    const PAGE_CONF = FALSE;
    /**
     * 指定强制删除
     * @var boolean
     */
    const DELETE_FORCE_YES = true;
    /**
     * 指定软删除
     * @var boolean
     */
    const DELETE_FORCE_NO = false;
    /**
     * 模型类实例
     * @var object
     */
	public $model = null;
	/**
	 * 每页读取数量 
	 * 读取列表类信息时可用
	 * @var integer
	 */
	protected static $limit = 10;
	/**
	 * 读取记录页码
	 * 读取列表类信息时可用
	 * @var integer
	 */
	protected static $page = 1;
	/**
	 * 返回数据中是否返回纪录总数(列表类数据中有效)
	 * @var boolean
	 */
	protected static $is_count = false;
	/**
	 * 返回数据总是否返回 是否有下一页的标识(列表类数据中有效)
	 * @var boolean
	 */
	protected static $is_next = true;
	/**
	 * 错误信息
	 * @var array
	 */
	protected $error = array('code' => '', 'msg' => '');
	/**
	 * 版本号模块名
	 * @var string
	 */
	protected $version_module = '';
	/**
	 * 读取列表信息时是否判断版本号
	 * @var boolean
	 */
	protected $auto_check_version = false;
	/**
	 * 是否使用缓存
	 * @var boolean
	 */
	protected $use_cache = true;
	/**
	 * 默认的读取单个记录时的字段
	 * @var mixed
	 */
	protected $_detail_fields = array();
	/**
	 * 是否自动对字段进行转换
	 * @var boolean
	 */
	protected $_auto_parse_field = self::AUTO_PARSE_FIELD_YES;
	/**
	 * 要进行转换的数据字段对应关系
	 * @var array
	 */
	protected $_parse_fields = array();
	/**
	 * 图片服务器地址
	 * @var string
	 */
	protected $_domain_image = '';
	/**
	 * 默认图片服务器地址
	 * @var string
	 */
	protected $_default_domain_image = '';
	
	protected $_factory_service = null;
	
	public function __construct(){
		$this->use_cache = C('USE_CACHE') ? true : false;
		if(empty($this->_domain_image)){
		  $this->_domain_image = C('DOMAIN_IMAGE') ? C('DOMAIN_IMAGE') : $this->_default_domain_image;
		}
		$this->_initialize();
	}
	/**
	 * 自动初始化(类创建后自动执行)<br />
	 * 
	 * @author dengjingma
	 * @time Jan 30, 2015
	 */
	protected function _initialize(){
	    
	}
	/**
	 * 手动初始化(手动调用该方法)<br />
	 * 
	 * @author dengjingma
	 * @time Jan 30, 2015
	 */
	protected function _init_service(){
	}
	/**
	 * 增加需要获取的字段
	 * @param mixed $_fields
	 * @author dengjingma
	 * @time Jan 6, 2015
	 */
	public function addDetailFields($_fields){
	    if(!empty($_fields)){
	        $_fields = is_array($_fields) ? $_fields : explode(GLUE_FIELD, $_fields);
	        $this->_detail_fields = is_array($this->_detail_fields) ? $this->_detail_fields : explode(GLUE_FIELD, $this->_detail_fields);
	        $this->_detail_fields = array_merge($this->_detail_fields, $_fields);
	    }
	}
	/**
	 * 减少需要获取的字段
	 * @param mixed $_fields
	 * @author dengjingma
	 * @time Jan 8, 2015
	 */
	public function deleteDetailFields($_fields){
	    if(!empty($_fields)){
	        $_fields = is_array($_fields) ? $_fields : explode(GLUE_FIELD, $_fields);
	        $this->_detail_fields = is_array($this->_detail_fields) ? $this->_detail_fields : explode(GLUE_FIELD, $this->_detail_fields);
	        $this->_detail_fields = array_diff($this->_detail_fields, $_fields);
	    }
	}
	/**
	 * 增加一个字段对应关系
	 * @param string $key 将要显示的字段
	 * @param string $value 数据库中的字段
	 * @param string $_force 如果已存在  是否覆盖
	 * @author dengjingma
	 * @time Jan 9, 2015
	 */
	public function addParseFields($key = '', $value = '', $_force = true){
	    if(!empty($key) && !empty($value)){
	        if(true === $_force){
	            $this->_parse_fields[$key] = $value;
	        }elseif(empty($this->_parse_fields[$key])){
	            $this->_parse_fields[$key] = $value;
	        }
	    }
	}
	/**
	 * 删除一个字段转换关系
	 * @param string $key 将要删除的key
	 * @param string $value 将要删除的value
	 * @author dengjingma
	 * @time Jan 9, 2015
	 */
	public function deleteParseFields($key = '', $value = ''){
	    if(!empty($key)){
	        unset($this->_parse_fields[$key]);
	    }
	    if(!empty($value) && in_array($value, $this->_parse_fields)){
	        $this->_parse_fields = array_unset($value, $this->_parse_fields);
	    }
	}
	/**
	 * 
	 * @param unknown $_method
	 * @param unknown $args
	 * @return mixed|NULL
	 * @author dengjingma
	 * @time Dec 25, 2014
	 */
	public function __call($_method, $args){
	    if(method_exists($this->model, $_method)){
	        return call_user_method_array($_method, $this->model, $args);
	    }else{
	        E(__CLASS__.':'.$_method.L('_METHOD_NOT_EXIST_'));
	        return;
	    }
	}
	/**
	 * 设置分页条数和页码
	 * @param number $limit
	 * @param number $page
	 */
	public static function setLimit($limit = 10, $page = 1){
		if(false !== $limit){
			self::$limit = intval($limit);

		}
		if(false !== $page){
			$page = intval($page);
			self::$page = $page < 1 ? 1 : $page;
		}
	}
	/**
	 * 设置 是否需要返回 是否有下一页标识
	 * @param boolean $is_next
	 * @author dengjingma
	 * @time Jan 12, 2015
	 */
	public static function setIsNext($is_next){
	    if(self::NEXT_NO === $is_next || self::NEXT_YES === $is_next){
	        self::$is_next = $is_next;
	    }
	}
	/**
	 * 设置是否需要返回 纪录总数
	 * @param boolena $is_count
	 * @author dengjingma
	 * @time Jan 12, 2015
	 */
	public static function setIsCount($is_count){
	    if(self::COUNT_NO === $is_count || self::COUNT_YES === $is_count){
	        self::$is_count = $is_count;
	    }
	}
	
	public static function log($user_id, $msg, $status, $module = '', $action = ''){
		$model = new LogModel();
		$log_data=array();
		$log_data['log_info'] = $msg;
		$log_data['log_time'] = time();
		$log_data['log_admin'] = $user_id;
		$log_data['log_ip']	= get_client_ip();
		$log_data['log_status'] = $status;
		$log_data['module']	=	empty($module) ? MODULE_NAME : $module;
		$log_data['action'] = 	empty($action) ? ACTION_NAME : $action;
		if($model->create($log_data)){
			$model->add($log_data);
		}
	}
	/**
	 * 获取错误信息
	 * @param string $type
	 * @return string|number
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	public function getError($type = 'code'){
		return $type == 'msg' ? $this->error['msg'] : $this->error['code'];
	}
	/**
	 * 设置错误信息
	 * @param string|number $code
	 * @param string $msg
	 * @author dengjingma
	 * @time Aug 6, 2014
	 */
	public function setError($code, $msg = ''){
		if(empty($msg)){
			$tmp = L($code);
			if(!empty($tmp) && $tmp != $code){
				$msg = $tmp;
			}
		}
		$this->error = array('code' => $code, 'msg' => $msg);
	}
	
	/**
	 * 数据更新,添加｜修改
	 * @param array $data 要更新的数据
	 * @param string $id 数据记录id,为空表示添加
	 * @param string $version_module 对应的模块版本名
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	public function update($data, $id = '', $version_module = ''){
	    if(empty($this->model)){
	        $this->setError('500_ERROR_EMPTY_MODEL', L('ERROR_EMPTY_DATA_MODEL'));
	        return false;
	    }
	    $this->_init_service();
		$_parse_force = true;
		$data = ConvertComponent::parseValues($data, $this->_parse_fields, ConvertComponent::PARSE_KEY_TO_VALUE, ConvertComponent::PARSE_DELETE_PARSED, $_parse_force);
		if(false !== $id){
		    $_pk = $this->model->getPk();
		    $id = empty($id) ? (isset($data[$_pk]) ? $data[$_pk] : '') : $id;
		}
		$_action = empty($id) ? 'INSERT' : 'UPDATE';
		$data = $this->_filter($data, $id);
		if(false === $data){
		    return false;
		}
		if(empty($data)){
		    $this->setError('400_ERROR_EMPTY_'.$_action.'_DATA', L('ERROR_EMPTY_DATA'));return false;
		}
		if($this->model){
		    $_model = $this->model;
		    $_type = empty($id) ? $_model::MODEL_INSERT : $_model::MODEL_UPDATE;
			if($this->model->create($data, $_type)){
				if(empty($id)){
					$res = $this->model->add();
				}else{
				    if(is_numeric($id)){
				        $_pk = $this->model->getPk();
				        $_cons = array($_pk => $id);
				    }else{
				        $_cons = $id;
				    }
					$res = $this->model->where($_cons)->save();
				}
				if(false === $res){
					$this->setError('500_ERROR_DATA_'.$_action.'_FAILED', $this->model->getDbError());return false;
				}
				if(!empty($version_module)){
					$version_service = new ModuleVersionsService();
					$_res = $version_service->update($version_module);
					if(false === $_res){
						$this->setError($version_service->getError('code'), $version_service->getError('msg'));
						return false;
					}
				}
				if($_model::MODEL_INSERT == $_type){
				    $this->_after_insert_success($data, $res);
				}else{
				    $this->_after_update_success($data, $id);
				}
				return $res;
			}
			$msg = '';
			$errors = $this->model->getError();
			if(is_array($errors)){
			     foreach ($errors as $v){
			         $msg = $v;
			         break;
			     }
			}else{
			    $msg = $errors;
			}
			$this->setError('500_ERROR_DATA_'.$_action.'_VALIDATTE_FAILED', $msg);return false;
		}
		
	}
	protected function _after_update_success($data, $id){
	    
	}
	protected function _after_insert_success($data, $id){
	    
	}
	/**
	 * 删除数据
	 * @param mixed $_conditions 条件
	 * @param string $_force 是否强制删除
	 * @param string $_is_delete_field 标记是否删除的字段名,当$_force为false时有效
	 * @return boolean
	 * @author dengjingma
	 * @time Jan 19, 2015
	 */
	public function delete($_conditions = array(), $_force = self::DELETE_FORCE_YES, $_is_delete_field = 'is_delete'){
	    if(empty($this->model)){
	        $this->setError(L('ERROR_EMPTY_DATA_MODEL'), '500_ERROR_EMPTY_MODEL');
	        return false;
	    }
	    $this->_init_service();
	    $_cons = $this->_filter_conditions($_conditions);
	    if(!$_cons){
	        $this->setError('400_INVALID_CONDITIONS_AT_DELETE', L('ERROR_INVALID_CONDITIONS'));
	        return false;
	    }
	    if(self::DELETE_FORCE_YES === $_force){
	        $res = $this->model->where($_cons)->delete();
	    }else{
	        $_model = $this->model;
	        if($this->model->create(array($_is_delete_field => $_model::DELETE_YES), $_model::MODEL_UPDATE)){
	            $res = $this->model->where($_cons)->save();
	        }else{
	            $this->setError('400_DELETE_DATA_FAILED_AT_CHECK', $this->model->getError());
	            return false;
	        }
	    }
	    if(false === $res){
	        $this->setError('500_DELETE_DATA_FAILED', $this->model->getDbError());
	        return false;
	    }
	    return true;
	}
	protected function _filter_conditions($_conditions){
	    return $_conditions;
	}
	protected function _after_delete_success($_conditions){
	    
	}
	protected function _after_delete_failed($_conditions){
	    
	}
	/**
	 * 检测数据是否有新版本
	 * @param number $version  当前数据版本
	 * @param string $module 模块名
	 * @return 有新版本返回新的版本号,否则返回false
	 * @author dengjingma
	 * @time Aug 12, 2014
	 */
	public function checkVersion($version, $module = ''){
		$module = empty($module) ? $this->version_module : $module;
		if(empty($module)){
			$module = get_class($this);
		}
		$model = new ModuleVersionsModel();
		$info = $model->where(array('module' => $module))->field('version')->find();
		if(empty($info)){
			return false;
		}
		if($info['version'] > $version){
			return $info['version'];
		}else{
			return false;
		}
	}
	
	/**
	 * 读取单个信息
	 * @param number｜array $conditions 查询条件,如果为数字,则表示id号
	 * @param string $field 需要读取的字段,默认全部读取
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	public function getOne($conditions, $field = '', $sort = ''){
	    if(empty($conditions)){
	        $this->setError('400_ERROR_EMPTY_CONDITION_AT_GET_DETAIL', L('ERROR_INVALID_PARAMS'));
	        return false;
	    }
	    
	    $this->_init_service();
	    $field = empty($field) ? $this->_detail_fields : $field;
		$field = empty($field) ? '*' : $field;
		if(self::AUTO_PARSE_FIELD_YES === $this->_auto_parse_field){
		  $field = ConvertComponent::parseFields($field, $this->_parse_fields, ConvertComponent::PARSE_KEY_TO_VALUE, ConvertComponent::PARSE_DELETE_PARSED);
		}
		$field = $this->_filter_fields($field);
		if(is_numeric($conditions)){
			$pk = $this->model->getPk();
			$conditions = empty($pk) ? array('id' => $conditions) : array($pk => $conditions);
		}
		$res = $this->model->where($conditions)->field($field)->order($sort)->find();
		if(!$res){
			$this->setError('404_OBJECT_NOT_EXISTED', L('ERROR_NOT_EXISTED_OBJECT'));
			return false;
		}
		$this->_after_get_one($res);
		if(self::AUTO_PARSE_FIELD_YES === $this->_auto_parse_field){
		  $res = ConvertComponent::parseValues($res, $this->_parse_fields, ConvertComponent::PARSE_VALUE_TO_KEY, ConvertComponent::PARSE_DELETE_PARSED);
		}
		
		return $res;
	}
	/**
	 * 单个查询之后统一处理
	 * @param array $data 数据记录
	 * @author dengjingma
	 * @time Dec 26, 2014
	 */
	protected function _after_get_one(&$data){
	    
	}
	
	/**
	 * 读取数据列表
	 * @param mixed $where 查询条件
	 * @param mixed $field 查询字段
	 * @param mixed $sort 排序
	 * @param boolean $is_count 返回结果中是否带总数
	 * @param boolean $is_next 返回结果中是否标示是否有后续数据
	 * @param boolean|number $limit 读取数量,0表示全部,false表示忽略,使用配置中的该数值
	 * @param boolean|number $page 页码,false表示忽略,使用配置中的该数值,小于1的取1
	 * @param string $having having子句表达式
	 * @return 
	 * array(<br />
	 * 	'list' => array(), 表示结果数组,<br />
	 * 	'limit' => number, 本次读取条数(原始期望值),<br />
	 * 	'page'	=>	number, 本次读取页码(原始期望值),<br />
	 * 	'count' =>	number, 符合条件的总记录数,is_count参数为真时返回<br />
	 * 	'next' => boolean, 是否还有后续数据,true是,false否;当is_next参数为真时返回<br />
	 * )
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	public function getList($where = '', $field = '', $sort = '', $is_count = self::COUNT_CONF, $is_next = self::NEXT_CONF, $limit = self::LIMIT_CONF, $page = self::PAGE_CONF, $having = ''){
	    $this->_init_service();
		if(empty($where)){ $where = '1=1';}
		$return = array('list' => array());
		$limit = self::LIMIT_CONF === $limit ? self::$limit : $limit;
		$page = self::PAGE_CONF === $page ? self::$page : $page;
		$return['limit'] = $limit;
		$return['page'] = $page;
		if(self::COUNT_CONF === $is_count){
		    $is_count = self::$is_count;
		}
		if(self::NEXT_CONF === $is_next){
		    $is_next = self::$is_next;
		}
		if(self::COUNT_YES === $is_count){
			$return['count'] = 0;
		}
		if(self::NEXT_YES === $is_next){
			$return['next'] = false;
		}
		$field = empty($field) ? $this->_detail_fields : $field;
		$field = empty($field) ? '*' : $field;
		if(self::AUTO_PARSE_FIELD_YES === $this->_auto_parse_field){
		  $field = ConvertComponent::parseFields($field, $this->_parse_fields, ConvertComponent::PARSE_KEY_TO_VALUE, ConvertComponent::PARSE_DELETE_PARSED);
		}
		$field = $this->_filter_fields($field);
		if($this->model){
			if(!empty($limit)){
				$start = ($page - 1) * $limit;
				if($is_next){
					$limit++;
					$list = $this->model->where($where)->order($sort)->field($field)->having($having)->limit($start, $limit)->select();
					if(!is_array($list)){
						$list = array();
					}else{
						if(count($list) >= $limit){
							$return['next'] = true;
							array_pop($list);
						}
					}
				}else{
					$list = $this->model->where($where)->order($sort)->field($field)->having($having)->limit($start, $limit)->select();
					if(!is_array($list)){ $list = array(); }
				}
			}else{
				$list = $this->model->where($where)->order($sort)->field($field)->having($having)->select();
				if(!is_array($list)){ $list = array(); }
			}
			$return['list'] = $list;
			if($is_count){
				$return['count'] = !empty($limit) ? $this->getCount($where, $having) : count($return['list']);
			}
			$this->_after_get_list($return['list']);
			if(self::AUTO_PARSE_FIELD_YES === $this->_auto_parse_field){
			    $return['list'] = ConvertComponent::parseValuesList($return['list'], $this->_parse_fields, ConvertComponent::PARSE_VALUE_TO_KEY, ConvertComponent::PARSE_DELETE_PARSED);
			}
		}
		if(isset($return['count'])){
		    $return['count'] = (int)($return['count']);
		}
		return $return;
	}
	/**
	 * 获取一个空的列表结果集
	 * @param boolean $is_count 返回结果中是否带总数
	 * @param boolean $is_next 返回结果中是否标示是否有后续数据
	 * @param boolean|number $limit 读取数量,0表示全部,false表示忽略,使用配置中的该数值
	 * @param boolean|number $page 页码,false表示忽略,使用配置中的该数值,小于1的取1
	 * @return
	 * array(<br />
	 * 	'list' => array(), 表示结果数组,<br />
	 * 	'limit' => number, 本次读取条数(原始期望值),<br />
	 * 	'page'	=>	number, 本次读取页码(原始期望值),<br />
	 * 	'count' =>	number, 符合条件的总记录数,is_count参数为真时返回<br />
	 * 	'next' => boolean, 是否还有后续数据,true是,false否;当is_next参数为真时返回<br />
	 * )
	 * @author dengjingma
	 * @time Aug 4, 2014
	 */
	public function getEmptyList($is_count = self::COUNT_CONF, $is_next = self::NEXT_CONF, $limit = self::LIMIT_CONF, $page = self::PAGE_CONF){
	    $return = array('list' => array());
	    $limit = self::LIMIT_CONF === $limit ? self::$limit : $limit;
	    $page = self::PAGE_CONF === $page ? self::$page : $page;
	    $return['limit'] = $limit;
	    $return['page'] = $page;
	    if(self::COUNT_CONF === $is_count){
	        $is_count = self::$is_count;
	    }
	    if(self::NEXT_CONF === $is_next){
	        $is_next = self::$is_next;
	    }
	    if(self::COUNT_YES === $is_count){
	        $return['count'] = 0;
	    }
	    if(self::NEXT_YES === $is_next){
	        $return['next'] = false;
	    }
	    return $return;
	}
	/**
	 * 查询数据集,结果以id为key的形式返回
	 * @param mixed $where 查询条件
	 * @param mixed $field 查询字段
	 * @param string $_key_field 作为key的字段名,默认为id
	 * @return  array
	 * @author dengjingma
	 * @time Dec 29, 2014
	 */
	public function getKeyList($where = '', $field = '', $_key_field = ''){
	    $return = $this->getList($where, $field, '', self::COUNT_NO, self::NEXT_NO, self::LIMIT_ALL);
	    $_list = array();
	    $_key_field = empty($_key_field) ? 'id' : $_key_field;
	    foreach ($return['list'] as $v){
	        if(!empty($v[$_key_field])){
	            $_list[$v[$_key_field]] = $v;
	        }
	    }
	    return empty($_list) ? $return['list'] : $_list;
	}
	
	public function getFromObj($obj_id, $obj_type, $field = ''){
	    $this->_init_service();
	    $_factory = $this->_factory_service;
	    $service = $_factory::createObj($obj_type);
	    $_result = false;
	    if($service){
	        $_result = $service->getOne($obj_id, $field);
	    }
	    return $_result;
	}
	/**
	 * 获取数据列表后统一处理
	 * @param array $list
	 * @author dengjingma
	 * @time Dec 26, 2014
	 */
	protected function _after_get_list(&$list){
	    foreach ($list as $k => $v){
	        $this->_after_get_one($list[$k]);
	    }
	}
	
	/**
	 * 根据条件查询结果集总数
	 * @param mixed $where 查询条件
	 * @param string $having having子句表达式
	 * @return number
	 * @author dengjingma
	 * @time Aug 15, 2014
	 */
	protected function getCount($where, $having = ''){
		$res = 0;
		if($this->model){
			$res = $this->model->where($where)->field('count(*)')->having($having)->select();
		}
		return is_array($res) && isset($res[0], $res[0]['count(*)']) ? $res[0]['count(*)'] : 0;
	}
	/**
	 * 字段特殊处理
	 * @param array $_fields
	 * @return array
	 * @author dengjingma
	 * @time Dec 30, 2014
	 */
	protected function _filter_fields($_fields){
	    return $_fields;
	}
	/**
	 * 添加|更新 数据过滤
	 * @param array $data
	 * @author dengjingma
	 * @time Jan 7, 2015
	 */
	protected function _filter($data, $id = ''){
	    return $data;
	}
}