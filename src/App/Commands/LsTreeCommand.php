<?php

namespace Console\App\Commands;

use Console\App\Git\GitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Console\App\utils\log_graphviz;
use function Console\App\utils\ls_tree;
use function Console\App\utils\object_find;

require_once __DIR__ . '/../utils/helpers.php';

class LsTreeCommand extends Command
{
    protected function configure()
    {
        $this->setName('ls-tree')
            ->setDescription('Pretty print a tree object')
            ->setHelp('Pretty print a tree object')
            ->addOption('recursive', 'r', InputOption::VALUE_NONE, 'recursive into subtrees')
            ->addArgument('tree', InputArgument::OPTIONAL, 'commit to start at', 'HEAD');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tree = $input->getArgument('tree');
        $recursive = $input->getOption('recursive');

        $repo = new GitRepository('.');
        ls_tree($repo, $tree, $recursive ? true : false);

        return Command::SUCCESS;
    }
}