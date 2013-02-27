<?php

class SuperStackEngine extends CacheEngine {

	public function init($settings = array()) {
		parent::init(array_merge(array(
			'engine'=> 'SuperStack',
			'prefix' => Inflector::slug(APP_DIR) . '_',
			), $settings)
		);

		foreach ($settings['stack'] as $key => $stack) {
			Cache::config($key, $stack);
		}

		return true;
	}

	public function write($key, $value, $duration) {
		$setStack = false;
		foreach ($this->settings['stack'] as $engine => $stack) {
			$cacheWritten = Cache::write($key, $value, $engine);
			if ($setStack === false && $cacheWritten === true) {
				$setStack = true;
			}
		}
		return $setStack;
	}

	public function read($key) {
		$emptyEngines = array();
		foreach ($this->settings['stack'] as $engine => $stack) {
			$data = Cache::read($key, $engine);
			if (!empty($data)) {
				break;
			} else {
				$emptyEngines[] = $engine;
			}
		}

		if (!empty($data) && !empty($emptyEngines)) {
			foreach ($emptyEngines as $engine) {
				Cache::write($key, $data, $engine);
			}
		}
		return $data;
	}

	public function increment($key, $offset = 1) {
		return parent::increment($key, $offset);
	}

	public function decrement($key, $offset = 1) {
		return parent::increment($key, $offset);
	}

	public function gc($expires = null) {
		return $this->clear(true);
	}

	public function clear($checkExpiry) {
		foreach ($this->settings['stack'] as $engine => $stack) {
			Cache::clear($checkExpiry, $engine);
		}
		return true;
	}

	public function delete($key) {
		foreach ($this->settings['stack'] as $engine => $stack) {
			Cache::delete($key, $engine);
		}
		return true;
	}

	public function key($key) {
		return $key;
	}

}