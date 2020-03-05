<?php

/**
 * File:	upload.php
 * Path:	\server
 * Date:	2020-03-04 15:24:32
 * Desc:	no description.
 */


/**
 *
 */
class UploadServer
{
	public function __construct()
	{
		$this->test();

		$this->server = new Swoole\WebSocket\Server('0.0.0.0', 50000);
		$this->server->on('message', [$this, 'onMessage']);
		$this->server->start();
	}

	public function test()
	{
		$data = file_get_contents('gzdata');
		var_dump(md5($data) == 'f600aaf99d8c3ca1a0154039892d2394');
		if ($data = gzuncompress($data)) {
			var_dump(md5($data) == 'a3f4c0bc2ac78b4ed71307eed6ec28a8');
		}
	}

	public function onMessage(Swoole\WebSocket\Server $server, Swoole\WebSocket\Frame $frame)
	{
		$result = "uncompress success";
		if ($frame->data) {
			if (!@gzuncompress($frame->data)) {
				$result = "uncompress error md5:" . md5($frame->data) . " length:" . strlen($frame->data);
				var_dump($result);
			}
		} else {
			$result = "upload error";
		}

		$server->push($frame->fd, $result);
		$server->disconnect($frame->fd);
	}
}

new UploadServer;
