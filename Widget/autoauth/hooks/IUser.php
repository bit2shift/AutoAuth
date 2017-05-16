<?php
namespace hooks;

interface IUser
{
	/**
	 * Name of this user.
	 * @return string
	 */
	function name();

	/**
	 * One of ['admin', 'user', 'guest']
	 * @return string
	 */
	function role();
}
