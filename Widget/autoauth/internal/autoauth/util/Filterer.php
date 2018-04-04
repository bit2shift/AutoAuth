<?php
namespace autoauth\util;

abstract class Filterer extends \php_user_filter
{
	const BLOCK_SIZE = 1 << 10;

	/**
	 * Forwarded parameters from filter().
	 * @var resource $in
	 * @var resource $out
	 * @var &int $consumed
	 */
	private $in, $out, $consumed;

	/**
	 * Derived classes implement this.
	 * @param bool $eof
	 * @return bool
	 */
	protected abstract function filterer($eof);

	/**
	 * Reads $count bytes from the input brigade.
	 * @param int $count
	 * @return string
	 */
	protected final function read($count = self::BLOCK_SIZE)
	{
		static $input;
		while((strlen($input) < $count) && ($bucket = stream_bucket_make_writeable($this->in)))
		{
			$this->consumed += $bucket->datalen;
			$input .= $bucket->data;
		}

		list($data, $input) = Strings::slice($input, $count);
		return $data;
	}

	/**
	 * Writes $data to the output brigade.
	 * @param string $data
	 */
	protected final function write($data = '')
	{
		static $output;
		if($data)
		{
			list($data, $output) = Strings::slice($output . $data, self::BLOCK_SIZE);
			if($output)
				stream_bucket_append($this->out, stream_bucket_new($this->stream, $data));
			else
				$output = $data;
		}
		else
		{
			stream_bucket_append($this->out, stream_bucket_new($this->stream, $output));
			$output = '';
		}
	}

	function filter($in, $out, &$consumed, $closing)
	{
		$this->in = $in;
		$this->out = $out;
		$this->consumed = &$consumed;

		if(!$this->filterer($closing))
			return PSFS_ERR_FATAL;

		if($closing)
			$this->write();

		return PSFS_PASS_ON;
	}

	function onCreate()
	{
		return true;
	}
}
