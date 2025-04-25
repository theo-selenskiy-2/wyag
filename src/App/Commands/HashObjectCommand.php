<?php

namespace Console\App\Commands;

require_once __DIR__ . '/../utils/helpers.php';

use Console\App\Git\GitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Console\App\utils\is_dir_empty;
use function Console\App\utils\object_hash;
use function Console\App\utils\object_read;
use function Console\App\utils\repo_create;
use function Console\App\utils\repo_path;

class HashObjectCommand extends Command
{
    protected function configure()
    {
        $this->setName('hash-object')
            ->setDescription('compute object ID and optionally creates a blob from a file')
            ->setHelp('compute object ID and optionally creates a blob from a file')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'specify the type', 'blob')
            ->addOption('write', 'w', InputOption::VALUE_NONE, 'write the object into the database')
            ->addArgument('path', InputArgument::REQUIRED, 'read object from <file>');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getOption('type');
        $write = $input->getOption('write');

        $path = $input->getArgument('path');
        $sha = object_hash($path, $type, $write ? new GitRepository(".") : null);

        $output->writeln($sha);
        
        return Command::SUCCESS;
    }
}
