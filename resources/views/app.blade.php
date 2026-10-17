@php
  $siteName    = "TvIt";
  $title       = "TvIt | TV italiana in diretta e guida programmi";
  $description = "Guarda i canali TV italiani in diretta streaming e consulta la guida programmi (EPG) di Rai, Mediaset, La7 e altri canali. Liste IPTV M3U ed EPG XMLTV gratuite.";
  $pageUrl     = url()->current();
  $ogImage     = url("/og-image.png");
@endphp
<!doctype html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
{{--  <meta name="viewport" content="viewport-fit=cover" />--}}

  {{-- Defaults for crawlers; the `inertia` attribute lets pages override them client-side --}}
  <title inertia>{{ $title }}</title>
  <meta name="description" content="{{ $description }}" inertia="description">
  <meta name="keywords" content="tv italiana, diretta tv, streaming tv, guida tv, programmi tv, epg, iptv, m3u, rai, mediaset, la7">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="{{ $pageUrl }}">

  {{-- Icons --}}
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta name="theme-color" content="#1f1e2c">
  <meta name="application-name" content="{{ $siteName }}">
  <meta name="apple-mobile-web-app-title" content="{{ $siteName }}">

  {{-- Open Graph --}}
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="{{ $siteName }}">
  <meta property="og:locale" content="it_IT">
  <meta property="og:title" content="{{ $title }}">
  <meta property="og:description" content="{{ $description }}">
  <meta property="og:url" content="{{ $pageUrl }}">
  <meta property="og:image" content="{{ $ogImage }}">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="TvIt, TV italiana in diretta e guida programmi">

  {{-- Twitter / X --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $title }}">
  <meta name="twitter:description" content="{{ $description }}">
  <meta name="twitter:image" content="{{ $ogImage }}">

  {{-- Structured data --}}
  <script type="application/ld+json">
    {!! json_encode([
      "@context"    => "https://schema.org",
      "@type"       => "WebSite",
      "name"        => $siteName,
      "url"         => url("/"),
      "description" => $description,
      "inLanguage"  => "it-IT",
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>

  @inertiaHead

  @vite(['resources/js/app.js'])
</head>
<body>
@inertia
</body>
</html>
