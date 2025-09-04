<?php

namespace Looma\Foundation;

interface ServiceProviderInterface
{
    public function register(Application $app): void;

    public function boot(Application $app): void;
}
