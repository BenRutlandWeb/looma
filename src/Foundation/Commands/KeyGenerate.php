<?php

namespace Looma\Foundation\Commands;

use Looma\Console\CommandInterface;
use Looma\Console\Concerns\GeneratesFiles;
use Looma\Console\Concerns\HasOutput;

final class KeyGenerate implements CommandInterface
{
    use GeneratesFiles;
    use HasOutput;

    public string $name = 'key:generate';

    /**
     * Generate keys for the application..
     */
    public function __invoke(array $arguments = [], array $options = []): void
    {
        $this->header('Looma', 'Generate keys and salts for the application.');

        $keys = [
            'AUTH_KEY',
            'SECURE_AUTH_KEY',
            'LOGGED_IN_KEY',
            'NONCE_KEY',
            'AUTH_SALT',
            'SECURE_AUTH_SALT',
            'LOGGED_IN_SALT',
            'NONCE_SALT',
            'WP_CACHE_KEY_SALT',
        ];

        $filename = dirname(dirname(ABSPATH)) . '/.env';

        if (!$this->exists($filename)) {
            $this->error('.env file doesn\'t exist.');
        }

        $env = $this->getContents($filename);

        foreach ($keys as $key) {
            $value = wp_generate_password(64, true, true);

            $pattern = "/^($key=.*)$/m";

            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, "$key=\"$value\"", $env);
            } else {
                $env .= "\n$key=\"$value\"";
            }

            $this->line("<info>$key</info> generated.");
            usleep(100000);
        }

        $this->putContents($filename, $env);

        $this->newLine()->success('All keys generated.', true);
    }
}
