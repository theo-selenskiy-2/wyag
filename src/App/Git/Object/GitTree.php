<?php

namespace Console\App\Git\Object;

use Console\App\Git\GitRepository;

use function Console\App\utils\tree_parse;
use function Console\App\utils\tree_serialize;

class GitTree extends GitObject
{
    /**
     * @var GitTreeLeaf[]
     */
    private array $data;

    public function __construct(?string $data = null)
    {
        parent::__construct($data);
    }
    
    public function serialize(): string 
    {
        return tree_serialize($this);
    }

    public function deserialize(string $data): void
    {
        $this->data = tree_parse($data);
    }

    public function getFormat(): string
    {
        return "tree";
    }

    /**
     * @return GitTreeLeaf[]
     */
    public function getData(): array
    {
        return $this->data;
    }
}
