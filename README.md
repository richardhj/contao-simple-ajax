# SimpleAjax for Contao Open Source CMS

## Description

v1.1.0 introduces the `SimpleAjax\Event\SimpleAjax`. You will be able to register an event listener. If you're not using
composer, you might want to use v1.0 instead and use the legacy hooks.

v1.2.0 lets you set an `Response` instance (from the symfony/http package). This was introduced to prepare a smooth
upgrade process to Contao 4.

You simply have to register your class/method and the extension will call your class if there is an incoming ajax
request. You simply have to decide if it's an ajax request for your module and return the data you want.

There are a few things you should know about the extension:
* YOU have the full control over the response. That also means that you have to set the correct header.
* When not setting a `Response`, YOU have to terminate the request after you have send the response. If the ajax request
is not for your method you simply have to return nothing.

## Usage

### Per Event

At first: Listen on the event `SimpleAjax\Event\SimpleAjax` using one [method described here](https://github.com/contao-community-alliance/event-dispatcher#event-listener-per-configuration). 

#### Either: Set a `Response`

```php
class MyAjaxListener
{
   public function myMethod(\SimpleAjax\Event\SimpleAjax $event)
   {
       if ('myrequest' !== \Input::get('acid'))
       {
           return;
       }
       
       $return = ['foo', 'bar', 'foobar'];
       $response = new \Symfony\Component\HttpFoundation\JsonResponse($return);
       $event->setResponse($response);
   }
}
```


#### Or: Handwritten response with termination
```php
class MyAjaxListener
{
   public function myMethod(\SimpleAjax\Event\SimpleAjax $event)
   {
       if ('myrequest' !== \Input::get('acid'))
       {
           return;
       }
       
       // Check whether the SimpleAjaxFrontend.php was requested
       if (false === $event->isIncludeFrontendExclusive())
       {
           return;
       }
       
       $return = ['foo', 'bar', 'foobar'];

       header('Content-Type: application/json');
       echo json_encode($return);
       exit;
   }
}
```

### Per Hook (legacy)

```php
// config.php
$GLOBALS['TL_HOOKS']['simpleAjax'][] = array('MyClass', 'myMethod');
$GLOBALS['TL_HOOKS']['simpleAjaxFrontend'][] = array('MyClass', 'myMethod'); // Use this hook for front end exclusive hooks

// MyClass.php
class MyClass
{
   public function myMethod()
   {
       if ('myrequest' === \Input::get('acid'))
       {
           $return = ['foo', 'bar', 'foobar'];

           header('Content-Type: application/json');
           echo json_encode($return);
           exit;
       }
   }
}
```
