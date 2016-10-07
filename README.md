# SimpleAjax for Contao Open Source CMS

## Description

The usage of the SimpleAjax extensions is very easy. The extension provides the hook `$GLOBALS['TL_HOOKS']['simpleAjax']` for extension developers. You simply have to register your class/method and the extension will call your class if there is an incoming ajax request.

You simply have to decide if it's an ajax request for your module and return the data you want.

There are a few thinks you should know about the extension:
* YOU have the full controll over the response. That also means that you have to set the correct header.
* YOU have to terminate the request after you have send the response. If the ajax request is not for your method you simply have nothing to return.

## Usage

```php
// config.php
$GLOBALS['TL_HOOKS']['simpleAjax'][] = array('MyClass', 'myMethod');
$GLOBALS['TL_HOOKS']['simpleAjaxFronted'][] = array('MyClass', 'myMethod'); // Use this line for front end exclusive hooks

// MyClass.php
class MyClass extends System
{
   public function myMethod()
   {
       if ($this->Input->get('acid') == 'myrequest')
       {
           $arrReturn = array('foo', 'bar', 'foobar');

           header('Content-Type: application/json');
           echo json_encode($arrReturn);
           exit;
       }
   }
}
```