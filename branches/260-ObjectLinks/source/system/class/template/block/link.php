<?php

class Template_Block_Link extends Template_Function {
	protected $_block = "link";
	protected $_function = "link";
	protected $_isBlock = true;
	
	function runtime($params, $smarty) {
		$obj = $params["href"];
		if (is_object ( $obj )) {
			if (is_a ( $obj, "Route" )) {
				$link = "/routes/view/{$obj->id}/{$obj->name}";
			} elseif (is_a ( $obj, "User" )) {
				$link = "/community/view_user/{$obj->uid}/{$obj->username}";
			}
		}
		
		$fields = "";
		foreach($params as $key=>$value){
			if($key == "href") continue;
			
			$fields .= " {$key}='{$value}' ";
		}
		
		return "<a href='{$link}' {$fields}>";
	}
	function handleEndBlock($data) {
		return "</a>";
	}

}
