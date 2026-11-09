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
            {{ include('layouts/account.php') }}

            <main class="home">
                <header class="home__header">
                    <h1 class="home__title">
                        Bienvenue dans MédiCabinet
                    </h1>

                    <p class="home__description">
                        Votre espace en ligne pour consulter les rendez-vous
                        enregistrés auprès de votre cabinet.
                    </p>
                </header>

                <section aria-labelledby="access-title">
                    <h2 id="access-title">Accéder à votre espace</h2>

                    <p class="home__description">
                        Connectez-vous avec les identifiants fournis par le
                        cabinet pour retrouver vos rendez-vous, les médecins
                        associés et le statut de vos consultations.
                    </p>

                    <p class="home__description">
                        Vous ne disposez pas encore d’un compte ?
                        Adressez-vous au cabinet pour obtenir votre accès.
                    </p>
                </section>
            </main>
        </div>
    </div>
</body>

</html>