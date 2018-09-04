<?php
//Declare, what kind of requirements the items must meet
const CRITERIA = array(
	'email' => array(
		'required' => true,
		'name'     => 'Email',
		'length'   => 48,
		'function' => 'email', 
	),

	'username' => array(
		'required' => true,
		'name'     => 'Username',
		'length'   => [4, 24],
		'pattern'  => ['/^[-\w]+$/', "alphanumeric characters, underscores and hyphens"],
	),

	'password' => array(
		'required' => true,
		'name'     => 'Password',
		'length'   => [6, 100],
		'pattern'  => ['/^[\S]+$/', "non-whitespace characters"],
	),
);

//Supply names of items you want to validate
$signup = new FormProcess('username', 'password');

//Start validating them against the criteria
$signup->start(CRITERIA);

//Print errors if found
if($signup->errors) $signup->printErrors();
?>