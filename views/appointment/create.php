<!DOCTYPE html>
<html lang="{{ locale }}">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'appointments'
        }) }}

        <div class="app-layout__content">
            {{ include('layouts/account.php') }}
            <p class="language-switch__notice">{{ trans['language.reload_notice'] }}</p>
            <main class="appointment-editor">
                <header class="appointment-editor__header">
                    <a class="back-link" href="{{ base }}/appointments">
                        <span class="icon icon--arrow-left" aria-hidden="true"></span>
                        {{ trans['appointment.back'] }}
                    </a>
                    <h1 class="appointment-editor__title"> {{ heading }} </h1>
                </header>

                {{ include('appointment/form.php') }}
            </main>
        </div>
    </div>
</body>

</html>
