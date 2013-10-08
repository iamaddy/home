<?php
/**
 * 注意
 * 添加函数时候请写明函数的用途
 * 复杂函数写函数的使用方法
 * 添加函数时请注明函数作者
 * public函数必须static
 */

class Util {

    /**
     * 取得客户端IP
     * @author  jinhaidong
     */
    public static function get_client_ip(){
        if (@$_SERVER['HTTP_CLIENT_IP'] && $_SERVER['HTTP_CLIENT_IP']!='unknown') {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (@$_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR']!='unknown') {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = @$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    
    /**
     * 对输出进行格式化
     */
    public static function format_int($val, $len = 0) {
    	return number_format((float) $val, $len);
    }
    /**
     * 对输出进行格式化
     */
    public static function format_float($val, $len = 2) {
    	return number_format((float) $val, $len);
    }
    
    /**
     * 如果字符串value不为空 且!='' 返回true
     * @param unknown_type $value
     * @return boolean
     * @author xubin
     */
    public static function trueStr($value) {
    	if(isset($value) && !empty($value) && $value != '') return true;
    	else return false;
    }
    /**
     * 截取字符串
     * @param unknown $value
     * @param number $len
     * @return string|unknown
     */
    public static function subString($value, $len = 30){
    	if(Util::trueStr($value)){
    		if(strlen($value) > $len){
    			return Util::utf_substr($value, $len) . "...";
    		} else{
    			return $value;
    		}
    	} else{
    		return "";
    	}
    }
    public static function utf_substr($str, $len){
    	if(strlen($str) > $len){
    		for($i = 0; $i < $len; $i++) {
    			$temp_str = substr($str, 0, 1);
    			if(ord($temp_str) > 127){
    				$i++;
    				if($i < $len){
    					$new_str[] = substr($str, 0, 3);
    					$str = substr($str, 3);
    				}
    			} else {
    				$new_str[] = substr($str, 0, 1);
    				$str = substr($str, 1);
    			}
    		}
    		return join($new_str);
    	} else{
    		return $str;
    	}
    	
    }
    

}