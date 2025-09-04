# Looma

Looma is a micro-framework to help with full site editor themes (and plugins).

## Advanced Custom Fields Pro

Looma has built in support for ACF Pro, nameply the ability to create ACF [blocks](#makeblock) with the CLI.

## Events and listeners

WordPress is run on actions and filters. Looma takes this concept and runs with it with "events" and "listeners". An event is an action or filter hook, and a listener is the callback that is run.

Looma's event dispatcher will automatically determine the number of parameters, and listener classes have the optional `Priority` attribute to change the listener priority from the default of 10.

You can generate listener classes with the [make:listener](#makelistener) command.

Listeners are registered in `app/Events/events.php`.

### CLI

Looma hooks into the WP CLI to register commands to make development faster.

### clear-compiled

Looma creates a manifest file containing classes and paths to autoload. For example, blocks are autoloaded once created via the CLI. The manifest is rebuilt automatically however cccasionally you may need to clear the manifest.

```bash
wp looma clear-compiled
```

### make:block

Creating blocks is made simple with this command. It creates the block.json, style.css and template.php files and autoloads them.

```bash
wp looma make:block
```

### make:command

Create your own commands and instantly call them from the CLI. All commands are prefixed e.g. `wp looma custom-command`.

```bash
wp looma make:command
```

### make:listener

This command generates a listener class that can be called by an [event](#events-and-listeners).

```bash
wp looma make:listener
```
