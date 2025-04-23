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
