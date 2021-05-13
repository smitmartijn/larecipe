<?php

namespace BinaryTorch\LaRecipe\Models;

use BinaryTorch\LaRecipe\Traits\HasMarkdownParser;

class Document extends Model
{
    use HasMarkdownParser;

    /**
     * @var string[]
     */
    protected $fillable = ['seo', 'path', 'title', 'content', 'canonical'];

    /**
     * @return bool
     */
    public function hasContent(): bool
    {
        return false;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'seo' => $this->seo,
            'index' => '',
            'path' => $this->path,
            'currentVersion' => '1.0',
            'versions' => ['1.2', '23s'],
            'title' => $this->title,
            'content' => $this->parse($this->content),
            'canonical' => $this->canonical,
            'currentSection' => '',
            'canonical'      => '',
        ];
    }
}
