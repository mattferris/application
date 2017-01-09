Application
===========

A PHP application bootstrapping framework.

The framework is built on the concept of components. Each component is
responsible for configuring itself, registering services, subscribing to events,
etc. This means you just add the components you want to your application and
run it! The only thing that is required to configure an application is a dependency
injection container that implements `MattFerris\Di\ContainerInterface` (which in
turn extends `Interop\Container\ContainerInterface`)

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

When extending `MattFerris\Application\Component` you get a component that will
automatically load providers from within the same namespace. So given the
above component, you can just create providers that live within the `My\Project`
namespace.

```php
namespace My\Project;

use MattFerris\Provider\ProviderInterface;

class EventsProvider implements ProviderInterface
{
    public function provides($consumer)
    {
        // register events, etc...
    }
}
```

This `EventsProvider` will now be automatically loaded at application boot.

Providers
---------

A components only real job is to plug providers into the framework. Providers
are where the heavy-lifting happens, and are responsible for registering
services with service containers, registering event listeners with event
dispatchers, supplying routing information to HTTP request dispatchers, etc. A
provider must implement `MattFerris\Provider\ProviderInterface` which requires a
single method be present called `provides()`.

Be default, there are no provider types. Components must register provider types
during initialization.

```php
// provided by mattferris/bridge-components
use MattFerris\Bridge\Components\Di\DiComponent;
use MattFerris\Di\Di;
use MattFerris\Application\Application;
use My\Project\MyProjectComponent;

$app = new Application(new Di(), [
    DiComponent::class,
    MyProjectComponent::class
]);
```

`DiComponent` registers a `Services` provider type. You could now defined a
`ServicesProvider` in order to register services.

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
service container. Likewise, `EventsProvider`s will be passed an instance of the
event dispatcher, and so on.

Provider Types
--------------

So far, we've talked about *global* provider types. *Global* provider types can
be registered by components in two way. For components extending
`MattFerris\Application\Components`, it's as easy as defining the `$provides`
property in the component's class definition.

```php
namespace My\Project;

use MattFerris\Application\Component;

class MyProjectComponent extends Component
{
    protected $providers = [
        [
            'MyProjectType' => [
                'consumer' => MyProjectConsumer::class,
                'scope' => 'global'
            ]
        ]
    ];

    // ...
}
```

The *consumer* is the type of instance that will be passed to a providers
`provides()` method. In this case, it will be an instance of
`My\Project\MyProjectConsumer`. Because scope is set to `global`, the component
will automatically register the provider.

For standalone components (those not extending `MattFerris\Application\Component`),
registration of provider types is done by having the component class implement
`MattFerris\Provider\ProviderInterface`, and using `provides()` to manually
register the types.

```php
namespace My\Project;

use MattFerris\Component\ComponentInterface;
use MattFerris\Provider\ProviderInterface;

class MyProjectComponent implements ComponentInterface, ProviderInterface
{
    public function provides($consumer)
    {
        // $consumer will contain an instance of MattFerris\Application\Application
        $consumer->addProvider('MyProjectType', MyProjectConsumer::class);
    }

    // ...
}
```

For components extending `MattFerris\Application\Component`, you can also define
*local* providers for use within the scope of the component. This gives you some
flexibility in isolating intialization of your component as required.

```php
namespace My\Project;

use MattFerris\Application\Component;

class MyProjectComponent extends Component
{
    protected $providers = [
        [
            'MyLocalType' => [
                'consumer' => MyLocalProjectConsumer::class,
                'scope' => 'local'
            ]
        ]
    ];

    // ...
}
```

Advanced Component Initialization
---------------------------------

In some cases, components may have dependencies on each other. It's possible to
defined different intialization passes so that components can be initialized in
such a way that these dependencies can be satisfied.

```php
use MattFerris\Application\Application;

$app = new Application($di, [
    [ 'Component\Satisfying\DependenciesComponent' ], // pass 1
    [ 'Another\Component\DependingOnFirstComponent' ] // pass 2
]);
```

By simply passing an array of arrays, you can break down component intializtion
into as many passes as is required to successfully bootstrap your application.
