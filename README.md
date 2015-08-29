# netphp

Using .Net code from PHP needs not to be a nightmare any more! Built upon the com_dotnet extension
this library allows you to easily integrate your .Net code into any PHP application.

* Use any .Net binaries (even without COM Visibility)
* Write code in PHP that consumes any of the .Net Framework libraries out of the box.
* Automatically wrap any .Net type inside PHP for seamless integration
* Iterate over .Net collections directly from PHP
* You can wrap the .Net types in PHP interfaces, so that you can interact with them as if they were PHP objects, unleashing alll the power of your IDE
* Automatic propagation of .Net errors into native PHP exceptions that can be properly handled
* Acces native enums and static methods
* Use class constructors with parameters
* Debug .Net and PHP code at the same time as if it was a single application.
* Works with libraries compiled for any version of the .Net framework (including 4.0 and above)

Some usage examples here:

* http://www.drupalonwindows.com/en/blog/how-use-netphp
* http://www.drupalonwindows.com/en/blog/calling-net-framework-and-net-assemblies-php
* http://www.drupalonwindows.com/en/blog/using-linq-language-integrated-queries-drupal-or-how-write-queries-x5-faster-0
* http://www.drupalonwindows.com/en/blog/pdf-generation-php

This code in C#:

```c#
string javascript = "";
Microsoft.Ajax.Utilities.Minifier m = new Microsoft.Ajax.Utilities.Minifier();
Microsoft.Ajax.Utilities.CodeSettings settings = new Microsoft.Ajax.Utilities.CodeSettings();
settings.OutputMode = Microsoft.Ajax.Utilities.OutputMode.SingleLine;
settings.PreserveFunctionNames = false;
string minified = m.MinifyJavaScript(javascript, settings);
```

Can be writen like this in PHP:

```php
$manager = new NetManager();
static::LoadAssemblyAndAliases($manager);

$javascript = "";
$minifier = $manager->Create('Ajaxmin', 'Minifier')->Instantiate();
$settings = $manager->Create('Ajaxmin', 'CodeSettings')->Instantiate();
$settings->OutputMode = $manager->Create('Ajaxmin', 'OutputMode')->Enum('SingleLine');
$minified = $minifier->MinifyJavaScript($javascript, $settings)->Val();
```