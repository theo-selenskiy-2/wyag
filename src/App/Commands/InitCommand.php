<?php

namespace Console\App\Commands;

require_once __DIR__ . '/../utils/helpers.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Console\App\utils\is_dir_empty;
use function Console\App\utils\repo_create;
use function Console\App\utils\repo_path;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('initialises a git repo')
            ->setHelp('(Re)Initialise a git repo')
            ->addArgument('path', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //repo_create($input->getArgument('path'));
        repo_create($input->getArgument('path') ?? '.');
        $output->writeln('done');
        return Command::SUCCESS;
    }
}
