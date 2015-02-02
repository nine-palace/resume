<?php
namespace Common\Component;
class MathComponent{
	static private $pi = 3.14159265358979323;
	static private $r = 6371229;
	/**
	 * 计算A B两点的距离
	 * @param float $lat_a A点纬度
	 * @param float $lng_a A点经度
	 * @param float $lat_b B点纬度
	 * @param float $lng_b B点经度
	 * @author dengjingma
	 * @time Aug 13, 2014
	 */
	static public function getDistanceFromAtoB($lat_a, $lng_a, $lat_b,$lng_b, $int = true){
		$pi = pi();
		$pk = 180 / self::$pi;
		$a1 = $lat_a / $pk;
		$a2 = $lng_a / $pk;
		$b1 = $lat_b / $pk;
		$b2 = $lng_b / $pk;
		$t1 = cos($a1) * cos($a2) * cos($b1) * cos($b2);
		$t2 = cos($a1) * sin($a2) * cos($b1) * sin($b2);
		$t3 = sin($a1) * sin($b1);
		$tt = acos($t1 + $t2 + $t3);
		$res = self::$r * $tt;
		return (int)$res;
	}
	
	/**
	 * 根据给定经纬度和半径,计算目标经纬度范围 
	 * @param float $lat 纬度
	 * @param float $lon 经度
	 * @param float $raidus 半径,单位米
	 * @author dengjingma
	 * @time Aug 14, 2014
	 */
	static public function getAround($lat,$lon,$raidus){
		$latitude = $lat;
		$longitude = $lon;
		$degree = (24901 * 1609) / 360.0;
		$raidusMile = $raidus;
		$dpmLat = 1 / $degree;
		$radiusLat = $dpmLat * $raidusMile;
		$minLat = $latitude - $radiusLat;
		$maxLat = $latitude + $radiusLat;
	
		$mpdLng = $degree*cos($latitude * (self::$pi / 180));
		$dpmLng = 1 / $mpdLng;
		$radiusLng = $dpmLng * $raidusMile;
		$minLng = $longitude - $radiusLng;
		$maxLng = $longitude + $radiusLng;
		
		return array('min_lat' => $minLat, 'max_lat' => $maxLat, 'min_lng' => $minLng, 'max_lng' => $maxLng);
	}
}