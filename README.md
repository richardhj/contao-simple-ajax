# SimpleAjax for Contao Open Source CMS

SimpleAjax provides an endpoint for Ajax endpoints.

While it has been very convenient for Contao 3, this extension is not required for Contao 4 anymore. You should only use this extension in case your extension needs to be compatible with Contao 3 and Contao 4. Find the upgrade instructions below.

## Changelog

v1.1.0 introduces the `SimpleAjax\Event\SimpleAjax`. You are able to register an event listener. If you're not using
composer, you might want to use v1.0 instead and use the legacy hooks.

v1.2.0 lets you set an `Response` instance (from the symfony/http package). This was introduced to prepare a smooth
upgrade process to Contao 4.

v1.3.0 is the Contao 4 release with equal features. The extension will not include any features as Contao 3 is approaching EOL.

## Usage

### Per Event

At first: Listen on the event `SimpleAjax\Event\SimpleAjax` using one [method described here](https://github.com/contao-community-alliance/event-dispatcher#event-listener-per-configuration). 

#### Either: Set a `Response` (recommended)

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


#### Or: Handwritten response with termination (legacy)
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

## Upgrade to Contao 4

This extension has been very convenient for Contao 3. While upgrading to Contao 4, you are advised to use the routing features coming with Contao 4.

In case you want to build an AppBundle (that's most probably true if you are no extension developer): [Create an AppBundle](https://community.contao.org/de/showthread.php?69448-GEL%C3%96ST-AppBundle-lokale-Extension-Managed-Edition&p=462159&viewfull=1#post462159)

Implement the `RoutingPluginInterface` in your Contao Manager Plugin and load the routing.yml with the `getRouteCollection()`.

```diff
-class Plugin implements BundlePluginInterface
+class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        // …
    }
+
+    /**
+     * Returns a collection of routes for this bundle.
+     *
+     * @param LoaderResolverInterface $resolver
+     * @param KernelInterface         $kernel
+     *
+     * @return RouteCollection|null
+     *
+     * @throws \Exception
+     */
+    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
+    {
+        return $resolver
+            ->resolve(__DIR__.'/../Resources/config/routing.yml')
+            ->load(__DIR__.'/../Resources/config/routing.yml');
+    }
}
```

Create a `routing.yml`, e.g. in the directory `src/AppBundle/Resources/config/routing.yml`

Put the following content:

```yml
app.ajax_tags:
   path: /ajax_tags/{param1}
   defaults:
       _scope: frontend
       _token_check: true
       _controller: 'AppBundle\Controller\AjaxTagsController'
```

Create the `AppBundle\Controller\AjaxTagsController.php`.

You can use https://github.com/richardhj/isotope-klarna-checkout/blob/v1.0/src/Controller/Push.php as a boilerplate.

The `__invoke()` method contains all the parameters that are mentioned in the path of the routing definition (`routing.yml`).
