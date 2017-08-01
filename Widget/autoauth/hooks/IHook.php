<?php
namespace hooks;

interface IHook
{
	/**
	 * Name of this user.
	 * @return string
	 */
	function name();

	/**
	 * Either 'admin', 'user' or 'guest'.
	 * @return string
	 */
	function role();
}
