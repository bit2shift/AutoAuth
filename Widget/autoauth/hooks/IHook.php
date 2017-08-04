<?php
namespace hooks;

interface IHook
{
	/**
	 * Name of the logged-in user.
	 * @return string
	 */
	function userName();

	/**
	 * Either 'admin', 'user' or 'guest'.
	 * @return string
	 */
	function userRole();
}
