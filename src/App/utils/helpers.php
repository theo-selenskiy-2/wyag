<?php

namespace Console\App\utils;

use Console\App\Git\GitRepository;
use Exception;

/**
 * Compute path under repo's gitdir.
 * @param GitRepository $repo 
 * @return string
 */
function repo_path(GitRepository $repo, ...$path)
{
    return array_reduce($path, function ($carry, $item) {
        return rtrim($carry, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $item;
    }, $repo->getGitDir());
}

/**
 * Compute path under repo's gitdir. Create dir if absent.
 * 
 * @param GitRepository $repo
 * @param bool $mkdir
 * @return string|null
 */
function repo_file(GitRepository $repo, bool $mkdir, ...$path)
{
    if(repo_dir($repo, $mkdir, ...array_slice($path, 0, -1))) {
        return repo_path($repo, ...$path);
    }
}

/**
 * Compute path under repo's gitdir. Create dir if absent and $mkdir=true.
 * 
 * @param GitRepository $repo
 * @param bool $mkdir
 * @return string|null
 */
function repo_dir(GitRepository $repo, bool $mkdir, ...$path)
{
    $dir = repo_path($repo, ...$path);

    if(realpath($dir)) {
        if(is_dir($dir)) {
            return $dir;
        }
        throw new Exception(sprintf('Not a directory %s', $dir));
    }

    if($mkdir) {
        mkdir($dir, 0777, true);
        return $dir;
    }
    return null;
}

function repo_create(string $worktree) 
{
    $repo = new GitRepository($worktree, True);

    if(is_readable($worktree)) {
        if(!is_dir($worktree)) {
            throw new Exception(sprintf('%s is not a directory', $worktree));
        }
        if(is_dir($repo->getGitDir()) && !is_dir_empty($repo->getGitDir())) {
            throw new Exception(sprintf('%s is not empty', $repo->getGitDir()));
        }
    } else {
        mkdir($worktree);
    }

    assert(repo_dir($repo, true, "branches"));
    assert(repo_dir($repo, true, "objects"));
    assert(repo_dir($repo, true, "refs", "tags"));
    assert(repo_dir($repo, true, "refs", "heads"));

    $description = fopen(repo_file($repo, false, "description"), "w") or die("Unable to open file!");
    fwrite($description, "Unnamed repository; edit this file 'description' to name the repository.\n");

    $head = fopen(repo_file($repo, false, "HEAD"), "w") or die("Unable to open file!");
    fwrite($head, "ref: refs/heads/master\n");

    repo_default_config(repo_file($repo, false, "config"));
}

function is_dir_empty($dir) {
    if (!is_readable($dir)) return NULL; 
    return (count(scandir($dir)) == 2);
}

function repo_default_config($configPath){
    $default_config = [
        "core" => [
            "repositoryformatversion" => 0,
            "filemode" => false,
            "bare" => false
        ]
    ];

    return write_to_ini($default_config, $configPath);
}


function write_to_ini($config, $file) {
    $content = '';
    
    foreach($config as $key => $pairs) {
        $content .= "[$key]\n";
        foreach($pairs as $v1 => $v2) {
            $content .= "        $v1 = $v2\n";
        }
    }
    return file_put_contents($file, $content) !== false;
}
