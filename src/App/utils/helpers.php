<?php

namespace Console\App\utils;

use Console\App\Git\GitRepository;
use Console\App\Git\Object\GitBlob;
use Console\App\Git\Object\GitCommit;
use Console\App\Git\Object\GitObject;
use Console\App\Git\Object\GitTag;
use Console\App\Git\Object\GitTree;
use Exception;

file_put_contents('/tmp/helper_debug.log', "Helpers loaded\n", FILE_APPEND);

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

/**
 * Create a git repo in the passed worktree
 * 
 * @param string $worktree
 * @return string|null
 */
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

/**
 * Find git repo in current directory or its parents
 * 
 * @param string $path
 * @param bool $required
 * @return GitRepository|null
 */
function repo_find(string $path, bool $required = true)
{
    if(!is_readable($path)) {
        throw new Exception(sprintf("This file or folder doesn't exist: %s", $path));
    }

    $absolute_path = realpath($path);
    $git_dir = $absolute_path . DIRECTORY_SEPARATOR . '.git';
    if(is_dir($git_dir)) {
        return new GitRepository($absolute_path);
    }

    $parent = dirname($absolute_path);
    if($parent === $absolute_path) {
        if($required) {
            throw new Exception("No git directory");
        } else {
            return null;
        }
    }

    return repo_find($parent, $required);
}

/**
 * Read object sha from Git repo. Return a GitObject of appropriate type.
 * 
 * @param GitRepository $repo
 * @param string $sha
 * @return GitObject|null
 */
function object_read(GitRepository $repo, string $sha): GitObject|null
{
    $path = repo_file($repo, false, "objects", substr($sha, 0, 2), substr($sha, 2));

    if(!is_file($path)) {
        throw new Exception(sprintf("Is not a file: %s", $path));
    }

    $contents = file_get_contents($path);
    $raw = zlib_decode($contents);
    if(!$raw) {
        throw new Exception(sprintf("Failed to decompress object %s", $sha));
    }

    $space_pos = strpos($raw, ' ');
    $format = substr($raw, 0, $space_pos);

    $null_pos = strpos($raw, "\x00");
    $size = intval(substr($raw, $space_pos+1, $null_pos-$space_pos-1));
    $actual_size = strlen($raw) - $null_pos - 1;

    if($size !== $actual_size) {
        throw new Exception(sprintf("Malformed object %s: bad length", $sha));
    }

    $data = substr($raw, $null_pos + 1);

    switch ($format) {
        // case "commit":
        //     $class = GitCommit::class;
        //     break;
        // case "tree":
        //     $class = GitTree::class;
        //     break;
        // case "tag":
        //     $class = GitTag::class;
        //     break;
        case "blob":
            $class = GitBlob::class;
            break;
        default:
            throw new Exception(sprintf("Unknown type %s", $format));
            break;
    }

    return new $class($data);
}

/**
 * Write an object 
 * 
 * @param GitObject $object
 * @param GitRepository $repo
 * @return string
 */
function object_write(GitObject $object, ?GitRepository $repo = null): string
{
    $data = $object->serialize();

    $result = $object->getFormat() . ' ' . strlen($data) . "\x00" . $data;
    $sha = sha1($result);

    if($repo !== null) {
        $path = repo_file($repo, true, "objects", substr($sha, 0, 2), substr($sha, 2));

        if(!is_readable($path)) {
            file_put_contents($path, zlib_encode($result, ZLIB_ENCODING_DEFLATE));
        } else {
            throw new Exception(sprintf("Failed to write to %s as it already exists", $path));
        }
    }

    return $sha;
}

/**
 * Hash an object and optionally save it if repo is passed
 * @param string $path
 * @param string $format
 * @param GitRepository|null
 * @return string
 */
function object_hash(string $path, string $format, ?GitRepository $repo = null): string
{
    if(!is_file($path)) {
        throw new Exception(sprintf("Is not a file: %s", $path));
    }
    
    $valid_formats = ['blob', 'commit', 'tree', 'tag'];
    if(!in_array($format, $valid_formats, true)) {
        throw new Exception(sprintf("Format: %s has to be either blob, commit, tree or tag", $format));
    }

    $data = file_get_contents($path);
    switch ($format) {
        // case "commit":
        //     $class = GitCommit::class;
        //     break;
        // case "tree":
        //     $class = GitTree::class;
        //     break;
        // case "tag":
        //     $class = GitTag::class;
        //     break;
        case "blob":
            $class = GitBlob::class;
            break;
        default:
            throw new Exception(sprintf("Unknown type %s", $format));
            break;
    }

    $obj = new $class($data);
    return object_write($obj, $repo);
}

function kvlm_parse(string $raw, int $start = 0, array $dict = [])
{
    $space = strpos($raw, ' ', $start);
    $new_line = strpos($raw, "\n", $start);

    if ($space === false || $new_line < $space) {
        assert($new_line===$start);
        $dict[null] = substr($raw, $start);
        return $dict;
    }

    $key = substr($raw, $start, $space-$start);

    $end = $start;
    while(true) {
        $end = strpos($raw, "\n", $end+1);
        if($raw[$end+1] !== ' ') break;
    }

    $value = substr($raw, $space+1, $end-$space-2);
    $value = str_replace("\n ", "\n", $value);

    if(array_key_exists($key, $dict)) {
        if(is_array($dict[$key])) {
            $dict[$key][] = $value;
        } else {
            $dict[$key] = [$dict[$key], $value];
        }
    } else {
        $dict[$key] = $value;
    }

    return kvlm_parse($raw, $end+1, $dict);
}

function is_dir_empty($dir) {
    if (!is_readable($dir)) return NULL; 
    return (count(scandir($dir)) == 2);
}

function repo_default_config($configPath){
    $default_config = [
        "core" => [
            "repositoryformatversion" => "0",
            "filemode" => "false",
            "bare" => "false"
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
