<?php
if(isset($_GET['ssi_function']))
{
	unset($_GET['ssi_function']);
	return header('Location: ?' . http_build_query($_GET));
}

require('../SSI.php');
header('Content-Type: application/xhtml+xml; charset=utf-8');

$doc = new DOMDocument();
$doc->resolveExternals = true;
$doc->preserveWhiteSpace = false;
$doc->load('xhtml/layout.xml');
if(!$doc->validate())
	die('Invalid XML layout.');

$admin = &$user_info['is_admin'];
$guest = &$user_info['is_guest'];

$id = ($admin ? 'admin' : ($guest ? 'guest' : 'user'));

$html = $doc->implementation->createDocument
(
	null,
	'html',
	$doc->implementation->createDocumentType
	(
		'html',
		'-//W3C//DTD XHTML 1.0 Transitional//EN',
		'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'
	)
);

$xp = new DOMXPath($doc);
$html->documentElement->appendChild($html->importNode($xp->query('//head')->item(0), true));
$html->documentElement->appendChild($html->importNode($xp->query("//body[@id='$id']")->item(0), true));

$html->encoding = 'utf-8';
$html->formatOutput = true;

if(!$guest && ($_SERVER['REQUEST_METHOD'] === 'POST'))
{
	$out = $xp->query('.//input[@readonly]', $html)->item(0);

	$name = ($admin ? $_POST['name'] : $user_info['username']);
	if(!strlen($name))
		$out->setAttribute('value', 'Username cannot be empty!');
	else
	{
		/*$sock = socket_create(AF_INET, SOCK_STREAM, 0);
		if(socket_connect($sock, 'localhost', 666))
		{
			java_write($sock, 'UUID');
			java_write($sock, $name);
			java_write($sock, $context['session_id']);

			$uuid = java_read($sock);
			socket_close($sock);
		}
		else
			$uuid = 'Error generating new UUID';

		$out->setAttribute('value', $uuid);*/

		$out->setAttribute('value', 'Just kidding, no UUID for you!');
	}
}

echo preg_replace_callback
(
	array_map
	(
		function($str)
		{
			return '/' . preg_quote($str, '/') . '/';
		},
		glob('media/*')
	),
	function($match)
	{
		$mime = mime_content_type($match[0]);
		$data = base64_encode(file_get_contents($match[0]));
		return "data:$mime;base64,$data";
	},
	$html->saveXML()
);
?>
