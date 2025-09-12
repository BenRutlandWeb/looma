<?php

namespace Looma\Foundation\Events;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceRepository;

final class RegisterServiceProviders
{
    public function __construct(private Application $app)
    {
        //
    }

    public function __invoke(): void
    {
        $serviceProviders = $this->app->get(ServiceRepository::class)->get('service-providers');

        $providers = [];

        foreach ($serviceProviders as $serviceProvider) {
            $providers[] = $this->app->register($serviceProvider);
        }

        foreach ($providers as $provider) {
            $provider->boot($this->app);
        }
    }
}
