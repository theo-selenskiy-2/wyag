<?php

namespace Console\App\Commands;

require_once __DIR__ . '/../utils/helpers.php';

use Console\App\Git\GitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Console\App\utils\is_dir_empty;
use function Console\App\utils\object_find;
use function Console\App\utils\object_read;
use function Console\App\utils\repo_create;
use function Console\App\utils\repo_path;

class CatFileCommand extends Command
{
    protected function configure()
    {
        $this->setName('cat-file')
            ->setDescription('provide content of repository objects')
            ->setHelp('prodive content of repository objects')
            ->addArgument('format', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = new GitRepository('.');
        $format = $input->getArgument('format');
        $valid_types = ['blob', 'commit', 'tree', 'tag'];
        if (!in_array($format, $valid_types, true)) {
            $output->writeln(sprintf("Invalid type: %s", $format));
            return Command::FAILURE;
        }

        $name = $input->getArgument('name');
        $object = object_read($repo, object_find($repo, $name, $format));
        if(!$object) {
            $output->writeln(sprintf("failed to read object with name: %s", $name));
            return Command::FAILURE;
        }
        $output->writeln($object->serialize());
        return Command::SUCCESS;
    }
}
