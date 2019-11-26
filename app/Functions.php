<?php

namespace App;

class Functions {

	public static function cleanGroupsString ($groupsString, $limit = NULL){

		$groups = explode(',', $groupsString);

		if($limit && count($groups)){
			$temp = $groups[0];
			unset($groups);
			$groups[] = $temp;
		}

		foreach($groups as $key => $group){
			$temp = trim($group);
			
			if(mb_strlen($temp) < 3){
				unset($groups[$key]);
			} else {
				$groups[$key] = $temp;
			}
		}

		return $groups;
	}

	public static function codeGen($length){
		$arr = [
			'a','b','c','d','e','f',  
			'g','h','i','j','k','l',  
			'm','n','o','p','r','s',  
			't','u','v','x','y','z',  
			'A','B','C','D','E','F',  
			'G','H','I','J','K','L',  
			'M','N','O','P','R','S',  
			'T','U','V','X','Y','Z',  
			'1','2','3','4','5','6',  
			'7','8','9','0','.',',',  
			'(',')','[',']','!','?',  
			'&','^','%','@','*','$',  
			'<','>','/','|','+','-',  
			'{','}','`','~'
		];

		$arr_length = count($arr) - 1;

		$result = [];

		for($i = 0; $i < $length; $i++){
			$result[] = $arr[rand(0, $arr_length)];
		}

		return implode($result);
	}

}