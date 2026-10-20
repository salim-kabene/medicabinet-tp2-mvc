<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <main class="error-page">
        <section class="error-page__card" aria-labelledby="error-title">
            <span class="error-page__icon" aria-hidden="true">
                <span class="icon icon--info"></span>
            </span>

            <p class="error-page__code">
                <span class="sr-only">Erreur </span>
                {{ httpStatus|default(500) }}
            </p>

            <h1 class="error-page__title" id="error-title">
                {{ pageTitle|replace({' — MédiCabinet': ''}) }}
            </h1>

            <p class="error-page__message">
                {{ message }}
            </p>

            <div class="error-page__actions">
                <a class="button button--primary" href="{{ base }}/">
                    Retour à l’accueil
                </a>

                <a class="button button--secondary" href="{{ base }}/appointments">
                    Voir les rendez-vous
                </a>
            </div>
        </section>
    </main>
</body>

</html>