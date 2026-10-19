<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <main class="home">
        <h1 class="home__title">{{ message }}</h1>

        <p class="home__description">
            Gérez les patients et les rendez-vous de votre cabinet médical.
        </p>
        <p>
            <a href="{{ base }}/patients">Consulter les patients</a>
        </p>
        <p>
            <a href="{{ base }}/appointments">
                Consulter les rendez-vous
            </a>
        </p>
    </main>
</body>

</html>