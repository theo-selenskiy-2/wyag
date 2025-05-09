<?php

namespace Console\App\Commands;

use Console\App\Git\GitRepository;
use Console\App\Git\Object\GitCommit;
use Console\App\Git\Object\GitTree;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Console\App\utils\is_dir_empty;
use function Console\App\utils\log_graphviz;
use function Console\App\utils\object_find;
use function Console\App\utils\object_read;
use function Console\App\utils\tree_checkout;

use Exception;

require_once __DIR__ . '/../utils/helpers.php';

class CheckoutCommand extends Command
{
    protected function configure()
    {
        $this->setName('checkout')
            ->setDescription('checkout a branch')
            ->setHelp('checkout a branch')
            ->addArgument('commit', InputArgument::REQUIRED, 'commit or tree to checkout')
            ->addArgument('path', InputArgument::REQUIRED, 'the empty directory to checkout');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = new GitRepository(".");
        $commit = $input->getArgument('commit');
        $path = $input->getArgument('path');
        $obj = object_read($repo, object_find($repo, $commit, null));

        if($obj->getFormat() === 'commit' && $obj instanceof GitCommit) {
            $tree_sha = $obj->getKvlm()['tree'];
            $obj = object_read($repo, $tree_sha);
            if(!($obj instanceof GitTree)) {
                throw new Exception(sprintf("object with sha: %s is not a tree", $tree_sha));
            }
        }

        if(realpath($path)) {
            if(!is_dir($path)) {
                throw new Exception(sprintf("is not a directory %s", $path));
            }
            if(!is_dir_empty($path)) {
                throw new Exception(sprintf("directory %s is not empty", $path));
            }
        } else {
            mkdir($path, 0777, true);
        }
        
        tree_checkout($repo, $obj, $path);
        return Command::SUCCESS;
    }
}