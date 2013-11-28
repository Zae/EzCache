<?php

class ezCache_Redis extends ezCache {

	protected $_redis;
	protected $_memory;
	protected $_stats = array('hit' => 0, 'miss' => 0);

	public function dump() {
		echo '<pre>';
		print_r($this->_stats);
		echo '</pre>';

		echo '<pre>';
		print_r($this->_non_persistent_groups);
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
		$this->_redis = new Redis();
		$this->_redis->connect('127.0.0.1', 6379);

		$this->_redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
		$this->_redis->setOption(Redis::OPT_PREFIX, 'myAppName:');

		$this->_memory = new ezCache_Memory();

		return !!($this->_redis && $this->_memory);
	}

	public function close() {
		$this->_memory->close();
		return $this->_redis->close();
	}

	public function delete($key, $group = 'default') {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		$this->_memory->delete($key, $group);
		return $this->_redis->delete($this->key($b_id, $key, $group));
	}

	public function exists($key, $group) {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		return ($this->_memory->exists($key, $group) || $this->_redis->exists($this->key($b_id, $key, $group)));
	}

	public function flush() {
		$this->_memory->flush();
		
		$keys = $this->_redis->keys('*');
		return $this->_redis->delete($keys);
	}

	public function get($key, $group = 'default', $force = false, &$found = null) {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		
		$found = $this->_memory->exists($key, $group);

		if ($found) {
			$data = $this->_memory->get($key, $group, $force, $found);
		} else {
			$b_id = $this->_sanitizeBlogId($blog_id, $group);
			
			$data = $this->_redis->get($this->key($b_id, $key, $group));
			$this->_memory->set($key, $data, $group, $this->_redis->ttl($this->key($b_id, $key, $group)));

			if ($data !== FALSE) {
				$this->_stats['hit'] ++;
			} else {
				$this->_stats['miss'] ++;
			}
		}

		$found = $data !== FALSE;
		return $data;
	}

	public function set($key, $data, $group = 'default', $expire = 0) {
		global $blog_id;

		if ($expire === 0) {
			$expire = 86400;
		}

		$group = self::_sanitizeGroup($group);

		$m = $this->_memory->set($key, $data, $group, $expire);
		if (!in_array($group, $this->_non_persistent_groups, TRUE)) {
			$b_id = $this->_sanitizeBlogId($blog_id, $group);
			return $this->_redis->setex($this->key($b_id, $key, $group), $expire, $data);
		}
		return $m;
	}

	public function add($key, $data, $group = 'default', $expire = 0) {
		return $this->set($key, $data, $group, $expire);
	}

	public function replace($key, $data, $group = 'default', $expire = 0) {
		return $this->set($key, $data, $group, $expire);
	}

	public function incr($key, $offset = 1, $group = 'default') {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		return $this->_redis->incrBy($this->key($b_id, $key, $group), $offset);
	}

	public function decr($key, $offset = 1, $group = 'default') {
		global $blog_id;

		$group = self::_sanitizeGroup($group);
		$b_id = $this->_sanitizeBlogId($blog_id, $group);

		return $this->_redis->decrBy($this->key($b_id, $key, $group), $offset);
	}

	public function reset() {
		return $this->_memory->reset();
	}

	public function add_global_groups($groups) {
		return (parent::add_global_groups($groups) && $this->_memory->add_global_groups($groups));
	}

	protected function key($blog_id, $key, $group = 'default') {
		return sprintf('%s:%s:%s', $blog_id, $group, $key);
	}

}
