<?php 

namespace Console\App\Git;

use Exception;
use Console\App\utils\repo_path;

use function Console\App\utils\repo_file;
use function Console\App\utils\repo_path;

class GitRepository 
{
    private string $worktree;
    private string $gitdir;
    private array $conf;

    public function __construct(string $path, bool $force = false)
    {
        $this->worktree = $path;
        $this->gitdir = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.git';

        if(!$force && !realpath($this->gitdir)) {
            throw new Exception(sprintf('Not a Git repository %s', $path));
        }

        $cf = repo_file($this, false, "config");

        if($cf !== null && realpath($cf)) {
            $conf = parse_ini_file($cf);
            if(!$conf) {
                throw new Exception("failed to parse config file");
            }
            $this->conf = $conf;
        } elseif(!$force) {
            throw new Exception("configuration file missing");
        }

        if(!$force) {
            $vers = intval($this->conf['repositoryformatversion'] ?? -1);
            if($vers !== 0) {
                throw new Exception(sprintf('unsupported repositoryformatversion: %s', $vers));
            }
        }
    }

    public function getGitDir(): string
    {
        return $this->gitdir;
    }

    public function getWorktree(): string
    {
        return $this->worktree;
    }
}