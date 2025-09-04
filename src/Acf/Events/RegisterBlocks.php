<?php

namespace Looma\Acf\Events;

use Looma\Foundation\Application;
use Looma\Foundation\ServiceRepository;

final class RegisterBlocks
{
    public function __construct(private Application $app)
    {
        //
    }

    public function __invoke(): void
    {
        $blocks = $this->app->get(ServiceRepository::class)->get('blocks');

        foreach ($blocks as $block) {
            register_block_type($block, [
                'render_callback' => [$this, 'renderCallback'],
            ]);
        }
    }

    public function renderCallback(array $block, string $content = '', bool $preview = false, int $postId = 0)
    {
        if (!$preview) {
            printf('<div %s>', get_block_wrapper_attributes());
        }

        if (file_exists($template = $block['path'] . '/template.php')) {
            include $template;
        }

        if (!$preview) {
            printf('</div>');
        }
    }
}
