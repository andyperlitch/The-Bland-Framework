# The Bland Framework: A light-weight PHP Framework
* * *

Based on the kohana framework without as many bells and whistles, as well as less globals/statics. I am not a PHP ninja. Pull at your own risk.

## General Goals for the project:
1. **Loose Coupling**
	* limited or no singletons, globals, static methods, etc.
	* use dependency injection
	* single responsibility principle for classes
2. **High Testability**
	* test suites for every class
	* avoid instantiation outside of factory classes
	* use PHPUnit for Test-driven Development
		* default phpunit cmd: `/Applications/MAMP/bin/php5.3/bin/php ~/Sites/BlandFramework/The-Bland-Framework/tests/AppTests.php`
3. **Well-Documented**
	* DocBlocks for every class, property, method, and file
	* Follow conventions of PHPDocumentor
		* default phpdoc cmd: `phpdoc -d /Users/andyperlitch/Sites/BlandFramework/The-Bland-Framework/ -t /Users/andyperlitch/Sites/BlandFramework/docs/ -ti 'The Bland Framework' -dn 'bland' -pp on`
	
## Stuff to change when creating new project
1. **Error Logging**
	1. /system/classes/error.php
		* *ERROR_NOTIFIER_NAME* - name to appear from email sent upon urgent error
		* *ERR_FROM_MAIL* - email address that error msgs sent FROM
		* *ERR_TO_MAIL* - email address that error msgs sent TO
		* uncomment `$e->SendMail(false);` on line 48.
	* 
2. **Testing**
	* adjust the include path for bootstrap.php file in AppTests.php
	
3. **Header Information**
	1. change "Host" header to appropriate URL: system/classes/response.php, `Response::__construct`
	
4. **Config**
	1. in the `/app/config/application.php` config file, change 'domain' keys to match your environment
	
5. **Absolute Path Constant**
	1. For local development environment, change "localhost:8888" if not using MAMP/WAMP (or using them with another port)