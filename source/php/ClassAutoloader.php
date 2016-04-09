<?php

class ClassAutoloader
{
	public function register()
	{
		spl_autoload_register(array($this, 'loader'));
	}

	private function loader($className)
	{
		include __DIR__ . '/' . $className . '.php';
	}
}