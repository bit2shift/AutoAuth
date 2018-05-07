<?php
namespace autoauth\util;

abstract class Filterer extends \php_user_filter
{
	/**
	 * Registers this filter.
	 * Derived classes must defined 'FILTER_NAME'.
	 */
	static final function register() : void
	{
		while(!stream_filter_register(static::FILTER_NAME, static::class))
		{
			if(!stream_filter_remove(static::FILTER_NAME))
				die('Filter "' . static::FILTER_NAME . '" cannot be removed. :(');
		}
	}

	/**
	 * Forwarded parameters from filter().
	 * @var resource $in
	 * @var resource $out
	 * @var &int $consumed
	 */
	private $in, $out, $consumed;

	/**
	 * Reads $count bytes from the input brigade.
	 * Undefined behaviour if outside [1, Streams::CHUNK_SIZE].
	 * @param int $count
	 * @return string
	 */
	protected final function read(int $count = Streams::CHUNK_SIZE) : string
	{
		static $buffer;

		while((strlen($buffer) < $count) && ($bucket = stream_bucket_make_writeable($this->in)))
		{
			$this->consumed += $bucket->datalen;
			$buffer .= $bucket->data;
		}

		list($data, $buffer) = Strings::slice($buffer, $count);
		return $data;
	}

	/**
	 * Writes $data to the output brigade.
	 * @param string $data
	 */
	protected final function write(string $data = null) : void
	{
		static $buffer;

		if($data === null)
		{
			stream_bucket_append($this->out, stream_bucket_new($this->stream, $buffer));
			$buffer = '';
		}
		else
		{
			$size = strlen($buffer .= $data) & Streams::CHUNK_SIZE_MASK;
			for($i = 0; $i < $size; $i += Streams::CHUNK_SIZE)
				stream_bucket_append($this->out, stream_bucket_new($this->stream, substr($buffer, $i, Streams::CHUNK_SIZE)));
			$buffer = substr($buffer, $size);
		}
	}

	/**
	 * Derived classes implement this.
	 * @param bool $eof
	 * @return bool
	 */
	protected abstract function filterer(bool $eof) : bool;

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
	function onCreate() : bool
	{
		return true;
	}
}
