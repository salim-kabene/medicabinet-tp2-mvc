<!DOCTYPE html>
<html lang="{{ locale }}">

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
                        {{ trans['home.title'] }}
                    </h1>

                    <p class="home__description">
                        {{ trans['home.description'] }}
                    </p>
                </header>

                <section aria-labelledby="access-title">
                    <h2 id="access-title">
                        {{ trans['home.access_title'] }}
                    </h2>

                    <p class="home__description">
                        {{ trans['home.access_description'] }}
                    </p>

                    <p class="home__description">
                        {{ trans['home.no_account'] }}
                    </p>
                </section>
            </main>
        </div>
    </div>
</body>

</html>