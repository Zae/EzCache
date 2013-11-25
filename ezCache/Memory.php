<?php

class ezCache_Memory extends ezCache {

	protected $_cache = array();
	protected $_stats = array('hit' => 0, 'miss' => 0);

	public function dump() {
		echo '<pre>';
		print_r($this->_stats);
		echo '</pre>';

		echo '<pre>';
		print_r($this->_cache);
		echo '</pre>';
	}

	public function __construct($config = NULL) {
		$this->_config = $config;

		$this->init();
	}

	public function __destruct() {
		$this->close();
	}

	public function init() {
		return $this->reset();
	}

	public function close() {
		return $this->reset();
	}

	public function get($key, $group = 'default', $force = false, &$found = null) {
		global $blog_id;

		$found = $this->exists($key, $group);

		if ($found) {
			$this->_stats['hit'] ++;
			return $this->_cache[$blog_id][$group][$key]['data'];
		}

		$this->_stats['miss'] ++;
		return;
	}

	public function set($key, $data, $group = 'default', $expire = 0) {
		global $blog_id;

		$this->_cache[$blog_id][$group][$key] = array(
			'data' => $data,
			'expire' => (empty($expire)) ? 0 : (time() + $expire)
		);

		return true;
	}

	public function add($key, $data, $group = 'default', $expire = 0) {
		return $this->set($key, $data, $group, $expire);
	}

	public function replace($key, $data, $group = 'default', $expire = 0) {
		return $this->set($key, $data, $group, $expire);
	}

	public function delete($key, $group = 'default') {
		global $blog_id;

		unset($this->_cache[$blog_id][$group][$key]);
		return true;
	}

	public function exists($key, $group = 'default') {
		global $blog_id;

		if (
				isset($this->_cache[$blog_id][$group][$key]) &&
				isset($this->_cache[$blog_id][$group][$key]['expire']) &&
				(
				$this->_cache[$blog_id][$group][$key]['expire'] === 0 ||
				$this->_cache[$blog_id][$group][$key]['expire'] > time()
				)
		) {
			return true;
		}

		return false;
	}

	public function incr($key, $offset = 1, $group = 'default') {
		return false;
	}

	public function decr($key, $offset = 1, $group = 'default') {
		return false;
	}

	public function flush() {
		return $this->reset();
	}

	public function reset() {
		$this->_cache = array();
		return true;
	}

}
