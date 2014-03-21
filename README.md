CakePHP Google API Plugin
=========================

Requirements
------------
[CakePHP v2.x](https://github.com/cakephp/cakephp)   
[Opauth](https://github.com/LubosRemplik/cakephp-opauth)

Installlation
-------------
1.	Use [composer](https://getcomposer.org/doc/00-intro.md). 
	Add following to the `composer.json` file:

		"require": {
			"lubos/cakephp-google": "~1.0"
		}

	And run `php composer.phar update`

2.  Install required plugins with all dependcies and configuration (done via composer)

3.  Connect google's account with your application http://example.org/auth/google

4.  Include needed model in your controller or anywhere you want to

	```php
	$uses = array('Google.GoogleDriveFiles');
	...
	$data = $this->GoogleDriveFiles->listItems();
	debug ($data);
	```

	```php
	$data = ClassRegistry::init('Google.GoogleDriveFiles')->listItems();
	debug ($data);
	```

Sample
------
1.  Install [CakePHP Google API Plugin sample](https://github.com/LubosRemplik/CakePHP-Google-API-Plugin-sample)

	```bash
	git clone --recursive https://github.com/LubosRemplik/CakePHP-Google-API-Plugin-sample.git google-sample-app
	```

2.  Create database & run bake, schema scripts

	```bash
	# basic cakephp installation
	cd google-sample-app/app
	chmod -R 777 tmp
	Console/cake bake db_config

	# schema
	Console/cake schema create -p Opauth
	```

3.  Configure - set google's credentials  
	Copy bootstrap.php.default to bootstrap.php and add your client_id, client_secret. 
	You can get these details at https://code.google.com/apis/console/

	```bash
	cp Config/bootstrap.php.default Config/bootstrap.php
	vim Config/bootstrap.php
	```

**Note** You have to configure [Opauth](https://github.com/LubosRemplik/cakephp-opauth) correctly
