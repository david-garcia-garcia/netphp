# NetPhp

Using .Net code from PHP needs not to be a nightmare any more! Built upon the com_dotnet extension
this library allows you to easily integrate your .Net code into any PHP application.

* Use any .Net binaries (even without COM Visibility).
* Write code in PHP that consumes any of the .Net Framework libraries out of the box.
* Automatically generated PHP class dumps for IDE integration.
* Iterate over .Net collections directly from PHP.
* Propagation of .Net errors into native PHP exceptions that can be properly handled and examined.
* Acces native enums and static methods.
* Use class constructors with parameters.
* Debug .Net and PHP code at the same time as if it was a single application.
* Works with libraries compiled for any version of the .Net framework (including 4.0 and above)

# See it in action

![Sample](/example0.gif?raw=true "Sample")

Download the examples project from GitHub

[NetPhp Sample](https://github.com/david-garcia-garcia/netphp-sample)

[Check out the NetPhp User Guide] (http://www.drupalonwindows.com/en/blog/netphp-user-guide)

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
$minifier = netMinifier::Minifier_Constructor();
$settings = netCodeSettings::CodeSettings_Constructor();
$csssettings = \ms\Microsoft\Ajax\Utilities\netCssSettings::CssSettings_Constructor();
$settings->OutputMode(\ms\Microsoft\Ajax\Utilities\netOutputMode::SingleLine());
$settings->PreserveFunctionNames(FALSE);
$settings->QuoteObjectLiteralProperties(TRUE);
$result = $minifier->MinifyStyleSheet($css, $csssettings, $settings)->Val();
```