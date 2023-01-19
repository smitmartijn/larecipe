<?php

namespace BinaryTorch\LaRecipe\Http\Controllers;

use BinaryTorch\LaRecipe\DocumentationRepository;

class SearchController extends Controller
{
    /**
     * @var DocumentationRepository
     */
    protected $documentationRepository;

    /**
     * SearchController constructor.
     * @param DocumentationRepository $documentationRepository
     */
    public function __construct(DocumentationRepository $documentationRepository)
    {
        $this->documentationRepository = $documentationRepository;

        if (config('larecipe.settings.auth')) {
            $this->middleware(['auth']);
        }else{
            if(config('larecipe.settings.middleware')){
                $this->middleware(config('larecipe.settings.middleware'));
            }
        }

    }

    /**
     * Get the index
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        $this->authorizeAccessSearch();

        return response()->json(
            $this->documentationRepository->search()
        );
    }

    /**
     */
    protected function authorizeAccessSearch()
    {
        abort_if(
            config('larecipe.search.default') != 'internal'
            ||
            ! config('larecipe.search.enabled')
        , 403);
    }
}
