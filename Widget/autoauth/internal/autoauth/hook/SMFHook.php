<?php
namespace autoauth\hook;

final class SMFHook implements IHook
{
	/**
	 * Create a new SMFHook.
	 * @param string $boarddir path to the forum's folder.
	 */
	function __construct(string $boarddir)
	{
		if(isset($_GET['ssi_function']))
		{
			unset($_GET['ssi_function']);
			header('Location: ?' . http_build_query($_GET));
			die('Begone, ssi_function!');
		}

		require("$boarddir/SSI.php");
	}

	function userName() : string
	{
		global $user_info;
		return $user_info['username'];
	}

	function userRole() : string
	{
		global $user_info;

		if($user_info['is_guest'])
			return self::GUEST;
		elseif($user_info['is_admin'])
			return self::ADMIN;
		else
			return self::USER;
	}
}
