<?php

namespace Looma\Foundation;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceRepository;

final class RegisterManifestServiceProviders
{
    public function __construct(private Application $app, private ServiceRepository $manifest)
    {
        //
    }

    public function register()
    {
        $serviceProviders = $this->manifest->get('service-providers');

        foreach ($serviceProviders as $serviceProvider) {
            $this->app->register($serviceProvider);
        }
    }
}
