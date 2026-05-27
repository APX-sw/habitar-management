@if ($paginator->hasPages())
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; margin-top: 1rem; gap: 0.5rem;">
        
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="Anterior">
                    <span class="page-link" aria-hidden="true">&lsaquo; Ant</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">&lsaquo; Ant</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente">Sig &rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Siguiente">
                    <span class="page-link" aria-hidden="true">Sig &rsaquo;</span>
                </li>
            @endif
        </ul>

        <div class="pagination-info" style="color: #718096; font-size: 0.85rem; text-align: center;">
            Mostrando resultados del <span style="font-weight: 600; color: var(--primary-color);">{{ $paginator->firstItem() }}</span> al <span style="font-weight: 600; color: var(--primary-color);">{{ $paginator->lastItem() }}</span> de un total de <span style="font-weight: 600; color: var(--primary-color);">{{ $paginator->total() }}</span>
        </div>
    </div>
@endif
