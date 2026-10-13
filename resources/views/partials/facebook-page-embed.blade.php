@php
    $pageUrl = $pageUrl ?? 'https://www.facebook.com/profile.php?id=61586592647177';
    $height = $height ?? 700;
@endphp

<div class="w-full overflow-hidden rounded-lg border border-gray-200 bg-white">
    <iframe
        src="https://www.facebook.com/plugins/page.php?href={{ urlencode($pageUrl) }}&tabs=timeline&height={{ $height }}&small_header=true&adapt_container_width=true&hide_cover=false&show_facepile=true&appId"
        width="100%"
        height="{{ $height }}"
        style="border:none; overflow:hidden; max-width: 100%;"
        scrolling="no"
        frameborder="0"
        allowfullscreen="true"
        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
        loading="lazy"
        title="Sangguniang Bayan ng Allacapan Facebook Page"
    ></iframe>
</div>