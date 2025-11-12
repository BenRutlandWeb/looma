# Command arguments and options

Commands can specify arguments and options in the same doc block as the command description.

There is a specific syntax that is required for wp-cli to pick up the arguments and options correctly. Arguments need to be written in this format:

```php
<?php
/**
 * argument
 * : argument description
 */
```

See [example](#example) below for more information.

## Arguments

An argument is a value passed to a command in a specific position. Arguments are used to provide the command with the information it needs to operate.

Arguments can be required: `<argument>` or optional `[<argument>]`. They can also be multiples `<argument>...` or `[<argument>...]`.

## Options

An option is a named argument passed to a command, prefixed with --, that modifies the command's behavior. Options can be boolean flags `[--option]` or key-value pairs `[--option=<val>]`.

## Example

```php
<?php

/**
 * Command description.
 *
 * <required-positional>
 * : One required positional argument.
 *
 * [<optional-positional>]
 * : One optional positional argument.
 *
 * <multiple-required-positional>...
 * : Multiple required positional arguments (accepts one or more values).
 *
 * [<multiple-positional>...]
 * : Multiple optional positional arguments (accepts one or more values).
 *
 * [--optional-flag]
 * : Optional boolean flag.
 *
 * [--opt=<val>]
 * : Optional named argument with a value.
 */
public function __invoke(array $arguments = [], array $options = []): void
{
    //
}
```
