<form action="{{ base }}/language" method="post" class="language-switch" aria-label="{{ trans['language.label'] }}">

    <input type="hidden" name="csrf_token" value="{{ session.csrf_token }}">

    <input type="hidden" name="return_to" value="{{ currentUrl }}">

    <button class="language-switch__button" type="submit" name="lang" value="fr" lang="fr"
        aria-pressed="{{ locale == 'fr' ? 'true' : 'false' }}" {% if locale == 'fr' %}disabled{% endif %}>
        Français
    </button>

    <button class="language-switch__button" type="submit" name="lang" value="en" lang="en"
        aria-pressed="{{ locale == 'en' ? 'true' : 'false' }}" {% if locale == 'en' %}disabled{% endif %}>
        English
    </button>
</form>