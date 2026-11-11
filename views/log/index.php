<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            logs: 'client'
        }) }}

        <div class="app-layout__content">
            {{ include('layouts/account.php') }}
            <main class="home">
                <header>
                    <h1 class="home__title">Journal de bord</h1>
                    <p>Consultez les visites et les accès aux pages du site.</p>
                </header>

                <div class="logs-table-wrapper">
                    <table class="logs-table">
                        <caption>Historique des accès, du plus récent au plus ancien</caption>

                        <thead>
                            <tr>
                                <th scope="col">Date et heure</th>
                                <th scope="col">Utilisateur</th>
                                <th scope="col">Adresse IP</th>
                                <th scope="col">Méthode</th>
                                <th scope="col">Page visitée</th>
                            </tr>
                        </thead>

                        <tbody>
                            {% for log in logs %}
                            <tr>
                                <td>
                                    <time datetime="{{ log.visited_at|date('Y-m-d\\TH:i:s') }}">
                                        {{ log.visited_at|date('d/m/Y H:i:s') }}
                                    </time>
                                </td>
                                <td>{{ log.username|escape }}</td>
                                <td>{{ log.ip_address|escape }}</td>
                                <td>{{ log.http_method|escape }}</td>
                                <td>{{ log.page|escape }}</td>
                            </tr>
                            {% else %}
                            <tr>
                                <td colspan="5">Aucune visite enregistrée.</td>
                            </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>

</html>