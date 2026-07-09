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

        if ($this->callSilently("dotenv salts regenerate")) {
            $this->success("Generated application keys and salts.")->terminate();
        }

        $this->error('Failed to generate the application keys and salts.');
    }
}
