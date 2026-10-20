<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'patients'
        }) }}

        <div class="app-layout__content">
            <main class="appointment-editor">
                <header class="appointment-editor__header">
                    <a class="appointment-editor__back" href="{{ base }}/appointments">
                        Retour aux rendez-vous
                    </a>

                    <h1 class="appointment-editor__title">
                        {{ heading }}
                    </h1>
                </header>

                {{ include('appointment/form.php') }}
            </main>
        </div>
    </div>
</body>

</html>