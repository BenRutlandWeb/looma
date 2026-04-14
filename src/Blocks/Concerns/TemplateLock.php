<?php

namespace Looma\Blocks\Concerns;

enum TemplateLock: string
{
    case ALL = 'all';
    case INSERT = 'insert';
    case CONTENT_ONLY = 'contentOnly';
    case NONE = 'false';
}
