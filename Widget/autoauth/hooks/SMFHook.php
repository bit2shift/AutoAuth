<?php
namespace hooks;

class SMFHook implements IHook
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
			return header('Location: ?' . http_build_query($_GET));
		}

		require("$boarddir/SSI.php");
	}

	function name()
	{
		global $user_info;
		return $user_info['username'];
	}

	function role()
	{
		global $user_info;
		return ($user_info['is_admin'] ? 'admin' : ($user_info['is_guest'] ? 'guest' : 'user'));
	}
}
