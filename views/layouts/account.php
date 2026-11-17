<header class="account-bar" aria-label="{{ trans['account.label'] }}">
    {% include 'layouts/language.php' %}
    {% if not guest %}
    {% set nameParts = session.user_name|trim|split(' ') %}
    {% set initials = nameParts|first|slice(0, 1) %}

    {% if nameParts|length > 1 %}
    {% set initials = initials ~ (nameParts|last|slice(0, 1)) %}
    {% endif %}

    <div class="account-bar__identity">
        <a class="account-bar__avatar" href="{{ base }}/profile" aria-label="{{ trans['account.edit_photo'] }}">
            {% if session.user_photo|default('') %}
            <img src="{{ base }}/profile/photo" alt="" width="44" height="44">
            {% else %}
            {{ initials|upper }}
            {% endif %}
        </a>

        <div class="account-bar__details">
            <span class="account-bar__name">
                {{ session.user_name }}
            </span>

            <span class="account-bar__role">
                {% if session.privilege_id == 1 %}
                {{ trans['account.admin'] }}
                {% elseif session.privilege_id == 2 %}
                {{ trans['account.client'] }}
                {% endif %}
            </span>
        </div>
    </div>

    <form action="{{ base }}/logout" method="post">
        <input type="hidden" name="csrf_token" value="{{ session.csrf_token }}">

        <button class="button button--secondary" type="submit">
            {{ trans['account.logout'] }}
        </button>
    </form>
    {% else %}
    <a class="button button--primary" href="{{ base }}/login">
        {{ trans['account.login'] }}
    </a>
    {% endif %}
</header>
