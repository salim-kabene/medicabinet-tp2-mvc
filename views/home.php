<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'home'
        }) }}

        <div class="app-layout__content">
            <main class="home">
                <header class="home__header">
                    <h1 class="home__title">
                        Bienvenue dans MédiCabinet
                    </h1>

                    <p class="home__description">
                        Consultez les dossiers patients et organisez
                        les rendez-vous du cabinet.
                    </p>
                </header>

                <section class="home__grid" aria-label="Accès aux services du cabinet">
                    <article class="home__card">
                        <span class="home__card-icon" aria-hidden="true">
                            <span class="icon icon--calendar"></span>
                        </span>

                        <h2 class="home__card-title">
                            Rendez-vous
                        </h2>

                        <p class="home__card-description">
                            Consultez le planning, filtrez les consultations
                            et gérez les rendez-vous avec leurs services associés.
                        </p>

                        <a class="button button--primary" href="{{ base }}/appointments">
                            Consulter les rendez-vous
                        </a>
                    </article>

                    <article class="home__card">
                        <span class="home__card-icon" aria-hidden="true">
                            <span class="icon icon--users"></span>
                        </span>

                        <h2 class="home__card-title">
                            Patients
                        </h2>

                        <p class="home__card-description">
                            Retrouvez les coordonnées de chaque patient
                            et consultez l’historique de ses rendez-vous.
                        </p>

                        <a class="button button--secondary" href="{{ base }}/patients">
                            Consulter les patients
                        </a>
                    </article>
                </section>
            </main>
        </div>
    </div>
</body>

</html>