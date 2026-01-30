<?php

namespace Looma\Foundation;

use Composer\Script\Event;

final class ComposerScripts
{
    public static function postCreateProject(Event $event): void
    {
        //$io = $event->getIO();

        //$io->write("<info>Welcome to the WP Custom Installer!</info>");

        // 1. Ask a question
        //$installTheme = $io->askConfirmation("Do you want to install a starter theme? (y/n) ", false);

        //if ($installTheme) {
        //$themeName = $io->ask("Which theme slug? (e.g., twentytwentyfive): ", "twentytwentyfive");

        // 2. Execute a sub-command
        // We use 'composer require' to add it to the new project
        //passthru("composer require wpackagist-theme/$themeName");
        //}

        //$io->write("<comment>Installation complete!</comment>");
    }
}
