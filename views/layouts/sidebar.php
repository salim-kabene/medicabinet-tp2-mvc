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
        </ul>
    </nav>
</aside>