<?php
class Object{
	public function __construct($arr = null, $arr_pre = ""){
		if (is_array($arr))
		{
			foreach(array_keys(get_class_vars(get_class($this))) as $k){
				if (isset($arr[$arr_pre.$k]))
				{
					$this->$k = $arr[$arr_pre.$k];
				}
			}
		}
	}
}
?>