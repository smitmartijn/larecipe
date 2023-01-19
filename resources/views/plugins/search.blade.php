@if(config('larecipe.search.enabled'))
    @if(config('larecipe.search.default') == 'algolia')
        <algolia-search-box
            v-if="searchBox"
            @close="searchBox = false"
            algolia-key="{{ config('larecipe.search.engines.algolia.key') }}"
            algolia-index="{{ config('larecipe.search.engines.algolia.index') }}"
        ></algolia-search-box>
    @elseif(config('larecipe.search.default') == 'internal')
        <internal-search-box
            v-if="searchBox"
            @close="searchBox = false"
            version-url="{{ route('larecipe.show') }}"
            search-url="{{ route('larecipe.search') }}"
        ></internal-search-box>
    @endif
@endif