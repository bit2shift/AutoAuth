<?php
namespace hooks;

abstract class AUser
{
	/**
	 * Name of this user.
	 * @return string
	 */
	abstract function name();

	/**
	 * One of ['admin', 'user', 'guest']
	 * @return string
	 */
	abstract function role();

	/**
	 * Adapt property access into method invocation.
	 * @param string $name
	 * @return mixed
	 */
	function __get($name)
	{
		return $this->$name();
	}
}
