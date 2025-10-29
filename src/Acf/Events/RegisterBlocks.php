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
            printf('<div %s>', get_block_wrapper_attributes([
                // block anchor is not supported in dynamic blocks
                'id' => isset($block['anchor']) ? $block['anchor'] : $block['id'],
            ]));
        }

        if (file_exists($template = $this->getPath($block))) {
            $block['post']   = get_post($postId);
            $block['fields'] = get_fields();

            (static function (string $__file, array $block) {
                return include $__file;
            })($template, $block);
        }

        if (!$preview) {
            printf('</div>');
        }
    }

    public function getPath(array $block): string
    {
        if (isset($block['path']) && file_exists($block['path'] . '/' . $block['render_template'])) {
            return $block['path'] . '/' . $block['render_template'];
        } elseif (file_exists($block['render_template'])) {
            return $block['render_template'];
        }

        return locate_template($block['render_template']);
    }
}
