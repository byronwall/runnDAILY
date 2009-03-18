<?php
class Sql_EqualCondition extends Sql_Condition{

	function setData($data){
		$this->sql = "{$this->field} = ?";
		$this->codes = "s";
		$this->params = array($data);

		$this->active = true;
		return true;
	}
}
?>