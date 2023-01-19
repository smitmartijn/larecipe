<?php

namespace BinaryTorch\LaRecipe;

use Symfony\Component\DomCrawler\Crawler;
use BinaryTorch\LaRecipe\Models\Documentation;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use BinaryTorch\LaRecipe\Traits\HasDocumentationAttributes;

class DocumentationRepository
{
    use HasAttributes, HasDocumentationAttributes;

    /**
     * The documentation model.
     *
     * @var Documentation
     */
    private $documentation;

    /**
     * DocumentationController constructor.
     *
     * @param Documentation $documentation
     */
    public function __construct(Documentation $documentation)
    {
        $this->documentation = $documentation;

        $this->docsRoute = route('larecipe.index');
        $this->defaultVersionUrl = route('larecipe.show');
    }

    /**
     * @param null $page
     * @param array $data
     * @return $this|DocumentationRepository
     */
    public function get($page = null, $data = [])
    {
        $this->sectionPage = $page ?: config('larecipe.docs.landing');
        $this->index = $this->documentation->getIndex();

        $this->content = $this->documentation->get($this->sectionPage, $data);

        if (is_null($this->content)) {
            return $this->prepareNotFound();
        }

        $this->prepareTitle()
            ->prepareCanonical()
            ->prepareSection($page);

        return $this;
    }

    /**
     * If the docs content is empty then show 404 page.
     *
     * @return $this
     */
    protected function prepareNotFound()
    {
        $this->title = 'Page not found';
        $this->content = view('larecipe::partials.404');
        $this->currentSection = '';
        $this->canonical = '';
        $this->statusCode = 404;

        return $this;
    }

    /**
     * Prepare the page title from the first h1 found.
     *
     * @return $this
     */
    protected function prepareTitle()
    {
        $this->title = (new Crawler($this->content))->filterXPath('//h1');
        $this->title = count($this->title) ? $this->title->text() : null;

        return $this;
    }

    /**
     * Prepare the current section page.
     *
     * @param $page
     * @return $this
     */
    protected function prepareSection($page)
    {
        if ($this->documentation->sectionExists($page)) {
            $this->currentSection = $page;
        }

        return $this;
    }

    /**
     * Prepare the canonical link.
     *
     * @return $this
     */
    protected function prepareCanonical()
    {
        if ($this->documentation->sectionExists($this->sectionPage)) {
            $this->canonical = route('larecipe.show', [
                'page' => $this->sectionPage
            ]);
        }

        return $this;
    }

    /**
     *
     * @return $this
     */
    public function search()
    {
        return $this->documentation->index();
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }
}
