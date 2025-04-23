<?php

namespace Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('initialises a git repo')
            ->setHelp('(Re)Initialise a git repo');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('done');
        return Command::SUCCESS;
    }
}
