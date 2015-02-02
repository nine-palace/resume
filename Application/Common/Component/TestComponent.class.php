<?php
namespace Common\Component;
class TestComponent{
	/**
	 * 获取公司
	 * 
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getComponay(){
		return array(
			array('name' => '北京银达物业管理有限公司', 'contact' => '', 'contact_num' => ''),
			array('name' => '北京祥洪永佳物业管理有限公司', 'contact' => '', 'contact_num' => ''),
		);
	}
	/**
	 * 获取小区
	 * 
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getCommunity(){
		return array(
			array('name' => '雍和家园', 'company_id' => '1', 'pro_id' => '', 'city_id' => '110100', 'area_id' => '', 'address' => '安定门东滨河路3号院', 'xpoint' => '116.426944', 'ypoint' => '39.956497'),
			array('name' => '中轴国际', 'company_id' => '2', 'pro_id' => '', 'city_id' => '110100', 'area_id' => '', 'address' => '安定门外西滨河路19号', 'xpoint' => '116.407031', 'ypoint' => '39.95637'),
		);
	}
	/**
	 * 获取管理员账号
	 * 
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getManager(){
		return array(
			array('account' => 'admin', 'password' => '123456', 'company_id' => '1', 'communities' => '1')
		);
	}
	/**
	 * 获取商家信息
	 * @return multitype:multitype:string  
	 * @author dengjingma
	 * @time Aug 21, 2014
	 */
	static public function getSuppliers(){
		return array(
			array('name' => '')
		);
	}
}