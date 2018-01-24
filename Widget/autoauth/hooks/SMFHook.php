<?php
namespace autoauth\hooks;

final class SMFHook implements IHook
{
	/**
	 * Create a new SMFHook.
	 * @param string $boarddir path to the forum's folder.
	 */
	function __construct($boarddir)
	{
		if(isset($_GET['ssi_function']))
		{
			unset($_GET['ssi_function']);
			header('Location: ?' . http_build_query($_GET));
			exit;
		}

		require("$boarddir/SSI.php");
	}

	function userName()
	{
		global $user_info;
		return $user_info['username'];
	}

	function userRole()
	{
		global $user_info;

		if($user_info['is_guest'])
			return 'guest';
		elseif($user_info['is_admin'])
			return 'admin';
		else
			return 'user';
	}
}
