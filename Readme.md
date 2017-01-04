Application
===========

A PHP application bootstrapping framework.

The framework is built on the concept of components. Each component is
responsible for configuring itself, registering services, subscribing to events,
etc. This means you just add the components you want to your application and
run it! The only thing that is required to configure an application is a dependency
injection container that implements `MattFerris\Di\ContainerInterface`.

A simple HTTP application might look something like:

```php
use MattFerris\Application\Application;
use MattFerris\Di\Di;

// setup your application by adding components to it
$app = new Application(new Di(), [
    '\MattFerris\Events\EventsComponent', // event handling
    '\MattFerris\Http\Routing\HttpRoutingComponent' // HTTP request handling
]);

// then run it by passing a startup callable to run()
$app->run(['\MattFerris\Http\Routing\HttpRoutingComponent', 'run']);
```

The `HttpRoutingComponent` has a static method called `run()` that provides some
additional bootstrapping to start the request handling process. You could also
supply your own `callable` to `$app->run()` if you wanted.

Components
----------

A component might be anything from an individual function to an entire library.
In Application terms, a project (i.e. the code) only becomes a component when
the projects's configuration and initialization are wrapped into a neat package,
represented as an class implementing `MattFerris\Component\ComponentInterface`.
Typically, this component class would live in the top-most namespace of your
project.

For simplicity, your component class can just extend
`MattFerris\Application\Component`.

```php
namespace My\Project;

use MattFerris\Application\Component;

class MyProjectComponent extends Component
{
}
```

That's it! When loaded, the component will attempt to locate
`My\Project\ServicesProvider` which allows you to configure services with the
service container.

Providers
---------

A components only real job is to plug providers into the framework. Providers
are where the heavy-lifting happens, and are responsible for registering
services with service containers, registering event listeners with event
dispatchers, supplying routing information to HTTP request dispatchers, etc. A
provider must implement `MattFerris\Provider\ProviderInterface` which requires a
single method be present called `provides()`.

By default, components extending `MattFerris\Application\Component` will only
look for one provider, `ServicesProvider`.

```php
namespace My\Project;

use MattFerris\Provider\ProviderInterface;

class ServicesProvider implements ProviderInterface
{
    public function provides($consumer)
    {
        // $consumer will contain an instance of the service container
        $container = $consumer;

        // register a service
        $container->set('MyProjectService', new MyProjectService());
    }
}
```

`ServicesProvider::provides()` is always passed an instance of the configured
service container. Likewise, `EventsProvider`s will be passed an instance of the event
dispatcher, and so on.
