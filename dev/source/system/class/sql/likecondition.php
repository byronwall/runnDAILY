<?php
class Sql_LikeCondition extends Sql_Condition{

	function setData($data){
		$this->sql = "{$this->field} LIKE ?";
		$this->codes = "s";
		$this->params = array("%{$data}%");

		$this->active = true;
		return true;
	}
}
?>