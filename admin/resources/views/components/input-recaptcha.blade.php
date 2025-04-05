@pushOnce('head')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endPushOnce

<div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITEKEY') }}"></div>
