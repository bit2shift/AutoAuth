<?php
namespace autoauth\hook;

interface IHook
{
	const ADMIN = 'admin';
	const USER  = 'user';
	const GUEST = 'guest';

	/**
	 * Name of the logged-in user.
	 * @return string
	 */
	function userName() : string;

	/**
	 * Either ADMIN, USER or GUEST.
	 * @return string
	 */
	function userRole() : string;
}
