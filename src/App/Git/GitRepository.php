<?php 

namespace Console\App\Git;

use Exception;

class GitRepository 
{
    private string $worktree;
    private string $gitdir;
    private string $conf;

    public function __construct(string $path, bool $force = false)
    {
        $this->worktree = $path;
        $this->gitdir = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.git';

        if(!$force && !realpath($this->gitdir)) {
            throw new Exception(sprintf('Not a Git repository %s', $path));
        }

        
    }

    public function getGitDir()
    {
        return $this->gitdir;
    }
}