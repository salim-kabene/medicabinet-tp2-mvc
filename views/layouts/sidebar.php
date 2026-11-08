<aside class="sidebar">
    <a class="sidebar__brand" href="{{ base }}/">
        <img class="sidebar__logo" src="{{ asset }}/icons/logo.svg" width="36" height="36" alt="">

        <span>MédiCabinet</span>
    </a>

    <nav class="sidebar__nav" aria-label="Navigation principale">
        <ul class="sidebar__list">
            <li class="sidebar__item">
                <a class="sidebar__link {% if activePage == 'home' %}sidebar__link--active{% endif %}"
                    href="{{ base }}/" {% if activePage == 'home' %}aria-current="page" {% endif %}>
                    <span class="icon icon--list" aria-hidden="true"></span>
                    Accueil
                </a>
            </li>

            <li class="sidebar__item">
                <a class="sidebar__link {% if activePage == 'appointments' %}sidebar__link--active{% endif %}"
                    href="{{ base }}/appointments" {% if activePage == 'appointments' %}aria-current="page" {% endif %}>
                    <span class="icon icon--calendar" aria-hidden="true"></span>
                    Rendez-vous
                </a>
            </li>

            <li class="sidebar__item">
                <a class="sidebar__link {% if activePage == 'patients' %}sidebar__link--active{% endif %}"
                    href="{{ base }}/patients" {% if activePage == 'patients' %}aria-current="page" {% endif %}>
                    <span class="icon icon--users" aria-hidden="true"></span>
                    Patients
                </a>
            </li>
            {% if session.user_id is defined %}
            <li class="sidebar__item">
                <form action="{{ base }}/logout" method="post">
                    <input type="hidden" name="csrf_token" value="{{ session.csrf_token }}">

                    <button class="button button--secondary" type="submit">
                        Se déconnecter
                    </button>
                </form>
            </li>
            {% else %}
            <li class="sidebar__item">
                <a class="sidebar__link" href="{{ base }}/login">
                    Se connecter
                </a>
            </li>
            {% endif %}
        </ul>
    </nav>
</aside>