<?php

namespace Console\App\Git\Object;

class GitTreeLeaf
{
    private string $mode;
    private string $path;
    private string $sha;

    public function __construct(string $mode, string $path, string $sha)
    {
        $this->mode = $mode;
        $this->path = $path;
        $this->sha = $sha;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getSha()
    {
        return $this->sha;
    }
}
