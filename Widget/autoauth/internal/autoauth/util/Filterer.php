<?php
namespace autoauth\util;

abstract class Filterer extends \php_user_filter
{
	/**
	 * Registers this filter.
	 * Derived classes must defined 'FILTER_NAME'.
	 */
	static final function register()
	{
		stream_filter_register(static::FILTER_NAME, static::class);
	}

	/**
	 * Forwarded parameters from filter().
	 * @var resource $in
	 * @var resource $out
	 * @var &int $consumed
	 */
	private $in, $out, $consumed;

	/**
	 * Fetch bucket from the input brigade.
	 * @return string
	 */
	private function read_bucket()
	{
		if($bucket = stream_bucket_make_writeable($this->in))
		{
			$this->consumed += $bucket->datalen;
			return $bucket->data;
		}
		else
			return '';
	}

	/**
	 * Push bucket to the output brigade.
	 * @param string $buffer
	 */
	private function write_bucket($buffer)
	{
		$bucket = stream_bucket_new($this->stream, $buffer);
		stream_bucket_append($this->out, $bucket);
	}

	/**
	 * Reads $count bytes from the input brigade.
	 * @param int $count
	 * @return string
	 */
	protected final function read($count = 0)
	{
		static $buffer;

		if($count < 1)
			$count = Streams::ioChunkSize();

		while((strlen($buffer) < $count) && ($data = $this->read_bucket()))
			$buffer .= $data;

		list($data, $buffer) = Strings::slice($buffer, $count);
		return $data;
	}

	/**
	 * Writes $data to the output brigade.
	 * @param string $data
	 */
	protected final function write($data = '')
	{
		static $buffer;

		if(!$data)
		{
			$this->write_bucket($buffer);
			$buffer = '';
		}
		elseif(strlen($data) < Streams::ioChunkSize())
		{
			list($data, $buffer) = Strings::slice($buffer . $data, Streams::ioChunkSize());
			if($buffer)
				$this->write_bucket($data);
			else
				$buffer = $data;
		}
		else
		{
			list($buffer, $data) = Strings::slice($buffer . $data, -Streams::ioChunkSize());
			$this->write_bucket($buffer);
			$this->write_bucket($data);
			$buffer = '';
		}
	}

	/**
	 * Derived classes implement this.
	 * @param bool $eof
	 * @return bool
	 */
	protected abstract function filterer($eof);

	final function filter($in, $out, &$consumed, $closing)
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

	/**
	 * Derived classes should override onCreate() and onClose()
	 * instead of implementing the constructor and the destructor.
	 * {@inheritDoc}
	 * @see \php_user_filter::onCreate()
	 */
	function onCreate()
	{
		return true;
	}
}
