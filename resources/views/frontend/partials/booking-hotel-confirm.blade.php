<div class="ma-bw__hotel-actions d-flex flex-wrap gap-2">
    @if ($whatsappAvailable)
        <button type="submit" class="theme-btn ma-bw__hotel-btn" data-bw-hotel-submit="whatsapp">
            <i class="fab fa-whatsapp me-1" aria-hidden="true"></i> Submit through WhatsApp <i class="far fa-angle-right ms-2" aria-hidden="true"></i>
        </button>
    @endif
    @if ($emailAvailable)
        <button type="submit" class="theme-btn ma-bw__hotel-btn style-three" data-bw-hotel-submit="email">
            <i class="far fa-envelope me-1" aria-hidden="true"></i> Submit through email <i class="far fa-angle-right ms-2" aria-hidden="true"></i>
        </button>
    @endif
</div>
