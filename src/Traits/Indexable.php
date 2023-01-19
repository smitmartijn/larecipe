<?php

namespace BinaryTorch\LaRecipe\Traits;

use Symfony\Component\DomCrawler\Crawler;

trait Indexable
{
    /**
     * @return mixed
     */
    public function index()
    {
        return $this->cache->remember(function () {
            $pages = $this->getPages();

            $result = [];
            foreach($pages as $page) {
                $pageContent = $this->get($page);

                if(! $pageContent)
                    continue;

                $indexableNodes = implode(',', config('larecipe.search.engines.internal.index'));

                $nodes = (new Crawler($pageContent))
                        ->filter($indexableNodes)
                        ->each(function (Crawler $node, $i) {
                            return $node->text();
                        });

                $title = (new Crawler($pageContent))
                        ->filter('h1')
                        ->each(function (Crawler $node, $i) {
                            return $node->text();
                        });

                $result[] = [
                    'path'     => $page,
                    'title'    => $title ? $title[0] : '',
                    'headings' => $nodes
                ];
            }

            return $result;
        }, 'larecipe.docs.search');
    }

    /**
     * @return mixed
     */
    protected function getPages()
    {
        $path = base_path(config('larecipe.docs.path').'/index.md');

        // match all markdown urls => [title](url)
        preg_match_all('/\[.+\]\((.+)\)/', $this->files->get($path), $matches);

        return $matches[1];
    }
}
