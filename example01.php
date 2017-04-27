<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'WSDLGen.php';

$wsdl = new WSDLGen();

function sum($a, $b)
{
	return $a + $b;
}

$wsdl->operation("sum", array (
		'a' => 'xsd:string',
		'b' => 'xsd:string' 
), array (
		'return' => 'xsd:string' 
), 'encoded', "Sum of two values");

$wsdl->dump();

?>