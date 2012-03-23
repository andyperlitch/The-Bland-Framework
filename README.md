# The Bland Framework: A light-weight PHP Framework
* * *

I am not a PHP ninja. Pull at your own (extremely high) risk.

## General Goals for the project:
1. **Loose Coupling**
	* limited or no singletons, globals, static methods, etc.
	* use dependency injection
	* single responsibility principle for classes
2. **High Testability**
	* test suites for every class
	* use PHPUnit for Test-driven Development
3. **Well-Documented**
	* DocBlocks for every class, property, method, and file
	* Follow conventions of PHPDocumentor
	
## Stuff to change when creating new project
1. **Error Logging**
	1. /system/classes/error.php
		* *ERROR_NOTIFIER_NAME* - name to appear from email sent upon urgent error
		* *ERR_FROM_MAIL* - email address that error msgs sent FROM
		* *ERR_TO_MAIL* - email address that error msgs sent TO
		* uncomment `$e->SendMail(false);`
	* 
2. **Testing**
	* adjust the include path for bootstrap.php file in AppTests.php
	