<section class="gallery-page-area ma-gallery-page pt-70 rpt-60 pb-100 rpb-70">
    <div class="container">
        @if ($categories->isNotEmpty())
            <ul class="gallery-filter filter-btns-one justify-content-center pb-40 mb-0" role="tablist" aria-label="Gallery categories">
                <li data-filter="*" class="current" role="tab" aria-selected="true">All</li>
                @foreach ($categories as $category)
                    <li data-filter=".{{ Str::slug($category) }}" role="tab" aria-selected="false">{{ ucfirst($category) }}</li>
                @endforeach
            </ul>
        @endif

        @if ($images->isEmpty())
            <p class="text-center text-muted mb-0">No gallery images yet.</p>
        @else
            <div class="ma-gallery-grid" id="ma-gallery-grid">
                @foreach ($images as $image)
                    @php
                        $src = asset('storage/images/gallery/'.$image->image);
                        $slug = Str::slug($image->category);
                    @endphp
                    <article class="ma-gallery-item item {{ $slug }}" data-category="{{ $slug }}">
                        <button type="button"
                            class="ma-gallery-card"
                            data-gallery-src="{{ $src }}"
                            data-gallery-caption="{{ $image->caption ?? ucfirst($image->category) }}"
                            aria-label="View {{ $image->caption ?? $image->category }}">
                            <img src="{{ $src }}"
                                 alt="{{ $image->caption ?? $image->category }}"
                                 loading="{{ $loop->index < 6 ? 'eager' : 'lazy' }}"
                                 decoding="async"
                                 @if ($loop->index < 3) fetchpriority="high" @endif
                                 width="640"
                                 height="480">
                            <span class="ma-gallery-card__overlay" aria-hidden="true">
                                <i class="far fa-search-plus"></i>
                            </span>
                        </button>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<div class="modal fade ma-gallery-lightbox" id="ma-gallery-lightbox" tabindex="-1" aria-labelledby="ma-gallery-lightbox-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <p class="ma-gallery-lightbox__caption mb-0" id="ma-gallery-lightbox-label"></p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <button type="button" class="ma-gallery-lightbox__nav ma-gallery-lightbox__nav--prev" id="ma-gallery-prev" aria-label="Previous image">
                    <i class="far fa-angle-left" aria-hidden="true"></i>
                </button>
                <figure class="ma-gallery-lightbox__figure">
                    <img src="" alt="" id="ma-gallery-lightbox-img" class="ma-gallery-lightbox__img">
                </figure>
                <button type="button" class="ma-gallery-lightbox__nav ma-gallery-lightbox__nav--next" id="ma-gallery-next" aria-label="Next image">
                    <i class="far fa-angle-right" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-footer">
                <span id="ma-gallery-counter" class="ma-gallery-lightbox__counter"></span>
            </div>
        </div>
    </div>
</div>