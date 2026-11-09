<header class="account-bar" aria-label="Compte utilisateur">
    {% if not guest %}
    {% set nameParts = session.user_name|trim|split(' ') %}
    {% set initials = nameParts|first|slice(0, 1) %}

    {% if nameParts|length > 1 %}
    {% set initials = initials ~ (nameParts|last|slice(0, 1)) %}
    {% endif %}

    <div class="account-bar__identity">
        <span class="account-bar__avatar" aria-hidden="true">
            {{ initials|upper }}
        </span>

        <div class="account-bar__details">
            <span class="account-bar__name">
                {{ session.user_name }}
            </span>

            <span class="account-bar__role">
                {% if session.privilege_id == 1 %}
                Administrateur
                {% elseif session.privilege_id == 2 %}
                Client
                {% endif %}
            </span>
        </div>
    </div>

    <form action="{{ base }}/logout" method="post">
        <input type="hidden" name="csrf_token" value="{{ session.csrf_token }}">

        <button class="button button--secondary" type="submit">
            Se déconnecter
        </button>
    </form>
    {% else %}
    <a class="button button--primary" href="{{ base }}/login">
        Se connecter
    </a>
    {% endif %}
</header>