<?php

class ezCache_APC extends ezCache {

	protected $_stats = array('hit' => 0, 'miss' => 0);

	public function dump() {
		echo '<pre>';
		print_r($this->_stats);
		echo '</pre>';

		$this->_memory->dump();
	}

	public function __construct($config) {
		$this->_config = $config;

		$this->init();
	}

	public function __destruct() {
		$this->close();
	}

	public function init() {
		return true;
	}

	public function close() {
		return true;
	}

	public function delete($key, $group = 'default') {
		global $blog_id;

		return apc_delete($this->key($blog_id, $key, $group));
	}

	public function exists($key, $group = 'default') {
		global $blog_id;

		return apc_exists($this->key($blog_id, $key, $group));
	}

	public function flush() {
		return apc_clear_cache('user');
	}

	public function get($key, $group = 'default', $force = false, &$found = null) {
		global $blog_id;

		$data =  apc_fetch($this->key($blog_id, $key, $group), $found);

		if ($found) {
			$this->_stats['hit']++;
		} else {
			$this->_stats['miss']++;
		}

		return $data;
	}

	public function set($key, $data, $group = 'default', $expire = 0) {
		global $blog_id;

		if ($expire === 0) {
			$expire = 86400;
		}

		return apc_store($this->key($blog_id, $key, $group), $data, $expire);
	}

	public function add($key, $data, $group = 'default', $expire = 0) {
		global $blog_id;

		if ($expire === 0) {
			$expire = 86400;
		}

		return apc_add($this->key($blog_id, $key, $group), $data, $expire);
	}

	public function replace($key, $data, $group = 'default', $expire = 0) {
		return $this->set($key, $data, $group, $expire);
	}

	public function incr($key, $offset = 1, $group = 'default') {
		global $blog_id;

		return apc_inc($this->key($blog_id, $key, $group), $offset);
	}

	public function decr($key, $offset = 1, $group = 'default') {
		global $blog_id;

		return apc_dec($this->key($blog_id, $key, $group), $offset);
	}

	public function reset() {
		return $this->flush();
	}

	protected function key($blog_id, $key, $group = 'default') {
		return sprintf('%s:%s:%s', $blog_id, $group, $key);
	}

}
