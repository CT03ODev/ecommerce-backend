<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
<meta name="author" content="{{ config('app.name') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', config('app.title'))</title>

<!-- Favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
<link rel="manifest" href="/favicon/site.webmanifest">
<link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
<link rel="shortcut icon" href="/favicon.ico">
<meta name="msapplication-TileColor" content="#2d89ef">
<meta name="msapplication-config" content="/favicon/browserconfig.xml">
<meta name="theme-color" content="#ffffff">

<meta name="robots" content="index,follow">
<link rel="canonical" href="{{ url()->full() }}">
<meta name="description" content="@yield('description', config('app.description'))" />

<!-- open Graph -->
<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="@yield('title', config('app.title'))" />
<meta name="twitter:description" content="@yield('description', config('app.description'))" />

<meta property="og:locale" content="vi-VN" />
<meta property="og:site_name" content="{{ config('app.name') }}" />
<meta property="og:url" content="{{ url()->full() }}" />
<meta property="og:type" content="website" />
<meta property="og:title" content="@yield('title', config('app.title'))" />
<meta property="og:description" content="@yield('description', config('app.description'))" />
<meta property="og:image" content="@yield('image', config('app.logo'))" />
