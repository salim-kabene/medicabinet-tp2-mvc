<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <main>
        <h1>{{ pageTitle }}</h1>

        <p>{{ message }}</p>

        <a class="back-link" href="{{ base }}/patients">
            Retour aux patients
        </a>
    </main>
</body>

</html>