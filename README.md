# netphp

Using .Net code from PHP needs not to be a nightmare any more. Built upon the com_dotnet extension
this library allows to easily integrate your .Net code into any PHP application.

* Use any .Net binaries (even without COM Visibility)
* Automatically wrap any .Net type inside PHP for seamless integration
* Iterate over .Net collections directly from PHP
* You can wrap the .Net types in PHP interfaces, so that you can interact with them as if they were PHP objects, unleashing alll the power of your IDE
* Automatic propagation of .Net errors into native PHP exceptions that can be properly handled
* Acces native enums and static methods
* Use class constructors with parameters
* Debug .Net and PHP code at the same time as if it was a single application.
* Work libraries compiled for any version of the .Net framework (including 4.0 and above)

Some usage examples here:

* http://www.drupalonwindows.com/en/blog/calling-net-framework-and-net-assemblies-php
* http://www.drupalonwindows.com/en/blog/using-linq-language-integrated-queries-drupal-or-how-write-queries-x5-faster-0