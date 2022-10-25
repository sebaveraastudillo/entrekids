<?php

function openCon()
{
	$dbhost = "<host>";
	$dbuser = "<username>";
	$dbpass = "<password>";
	$db = "<database>";


	$conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);


	return $conn;
}
 
function closeCon($conn)
{
	$conn -> close();
}

?>