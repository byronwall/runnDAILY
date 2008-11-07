<?php

class database extends mysqli
{
	//create a singleton instance
	private static $instance = NULL;

	//private constructor to prevent direct access
	private function __construct()
	{
		parent::__construct("localhost", "php", "j6Hmj544vUETZedU", "running");
		if(mysqli_connect_errno())
		{
			throw new Exception(mysqli_connect_error(), mysqli_connect_errno());
		}
	}
	//public create instance function to create singleton
	public function getDB()
	{
		if(self::$instance === null)
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
	public function prepare($query)
	{
		$stmt = new stmt_Extended($this, $query);

		return $stmt;
	}
	//prevent clone
	public function __clone()
	{
		throw new Exception("Cannot clone ".__CLASS__." class");
	}

	public function __destruct()
	{
		$this->close();
	}
}


class mysqli_Extended extends mysqli
{
	protected $selfReference;

	public function __construct($dbHost, $dbUsername, $dbPassword, $dbDatabase)
	{
		parent::__construct($dbHost, $dbUsername, $dbPassword, $dbDatabase);

	}

	public function prepare($query)
	{
		$stmt = new stmt_Extended($this, $query);

		return $stmt;
	}
}

class stmt_Extended extends mysqli_stmt
{
	protected $varsBound = false;
	protected $results;

	public function __construct($link, $query)
	{
		parent::__construct($link, $query);
	}

	public function fetch_assoc()
	{
		// checks to see if the variables have been bound, this is so that when
		//  using a while ($row = $this->stmt->fetch_assoc()) loop the following
		// code is only executed the first time
		if (!$this->varsBound) {
			$meta = $this->result_metadata();
			while ($column = $meta->fetch_field()) {
				// this is to stop a syntax error if a column name has a space in
				// e.g. "This Column". 'Typer85 at gmail dot com' pointed this out
				$columnName = str_replace(' ', '_', $column->name);
				$bindVarArray[] = &$this->results[$columnName];
			}
			call_user_func_array(array($this, 'bind_result'), $bindVarArray);
			$this->varsBound = true;
		}

		if ($this->fetch() != null) {
			// this is a hack. The problem is that the array $this->results is full
			// of references not actual data, therefore when doing the following:
			// while ($row = $this->stmt->fetch_assoc()) {
			// $results[] = $row;
			// }
			// $results[0], $results[1], etc, were all references and pointed to
			// the last dataset
			foreach ($this->results as $k => $v) {
				$results[$k] = $v;
			}
			return $results;
		} else {
			return null;
		}
	}
}
?>