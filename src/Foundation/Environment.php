<?php

namespace Looma\Foundation;

enum Environment: string
{
    case LOCAL = 'local';
    case DEVELOPMENT = 'development';
    case STAGING = 'staging';
    case PRODUCTION = 'production';

    public static function capture(): self
    {
        return self::tryFrom(wp_get_environment_type()) ?? self::PRODUCTION;
    }

    public static function toArray(): array
    {
        return array_map(fn ($env) => $env->value, self::cases());
    }

    public function isLocal(): bool
    {
        return $this === self::LOCAL;
    }

    public function isDevelopment(): bool
    {
        return $this === self::DEVELOPMENT;
    }

    public function isStaging(): bool
    {
        return $this === self::STAGING;
    }

    public function isProduction(): bool
    {
        return $this === self::PRODUCTION;
    }
}
