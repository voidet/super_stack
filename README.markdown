A simple yet possibly effective way of dealing with multiple layers of caching.
More documentation coming after some tests have been written (tutorial etc.)

#Super Stack

The idea behind Super Stack is a way of seamlessly handling multiple cache engines under a single cache key. The benefit of this is to have a failover system, whereby the primary cache engine may of lost its data, however instead of resorting to the app level for regeneration of the cached objects, a 2nd, 3rd nth level of cache can be reached.

#How It Works

Super Stack will traverse through your defined cache configs/engines until it finds data returned. As it reads/steps down the ladder of caches, if any caches are missing Super Stack will go back and refill them with data found from a lower level.

##Installation

Firstly clone the plugin into your plugins directory via:

cd myapp/app/plugins/
git clone http://github.com/voidet/super_stack.git super_stack

In app/config/bootstrap.php define your cache settings/stacks for example:

	CakePlugin::load('SuperStack');
	Cache::config('teststack', array(
		'engine' => 'SuperStack.SuperStack',
		'stack' => array(
			'memcachestack' => array(
				'engine' => 'Memcache',
				'servers' => array(
					'127.0.0.1:11211'
				),
				'compress' => false,
				'duration' => 0,
			),
			'filestack' => array(
				'engine' => 'File',
				'duration'=> 3600,
			)
		),
	));

Please note, you **must** place this call in bootstrap.php and not core.php, otherwise Super Stack won't be loaded.

From there you will be able to use your cache stack just like any other CakePHP cache object:

Cache::read('mykey', 'teststack');