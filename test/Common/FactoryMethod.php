<?php

class FactoryMethod
{
	function createPrintout($type) {}

	function create($type)
	{
		$obj = $this->createProduct($type);

		return $obj;
	}
}

?>