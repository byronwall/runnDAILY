<?php
class SqlLikeCondition extends SqlCondition{

	function setData($data){
		$this->sql = "{$this->field} LIKE ?";
		$this->codes = "s";
		$this->params = array("%{$data}%");

		$this->active = true;
		return true;
	}
}
?>