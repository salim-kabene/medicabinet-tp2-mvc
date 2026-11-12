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

                {% if logs is empty %}
                <section class="logs-empty" aria-label="Journal vide">
                    <h2>Aucune visite enregistrée</h2>
                    <p>Les accès aux pages du site apparaîtront ici.</p>
                </section>
                {% else %}
                <div class="logs-table-wrapper">
                    <table class="logs-table">
                        <caption>
                            Visites {{ firstEntry }} à {{ lastEntry }}
                            sur {{ total }}
                        </caption>

                        <thead>
                            <tr>
                                <th scope="col">Date et heure</th>
                                <th scope="col">Utilisateur</th>
                                <th scope="col">Adresse IP</th>
                                <th scope="col">Page visitée</th>
                            </tr>
                        </thead>

                        <tbody>
                            {% for log in logs %}
                            <tr>
                                <td>
                                    {{ log.visited_at|date('d/m/Y H:i:s') }}
                                </td>
                                <td>{{ log.username|escape }}</td>
                                <td>{{ log.ip_address|escape }}</td>
                                <td>{{ log.page|escape }}</td>
                            </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                </div>

                {% if totalPages > 1 %}
                <nav class="logs-pagination" aria-label="Pagination du journal">

                    {% if currentPage > 1 %}
                    <a class="button button--secondary" href="{{ base }}/logs?page={{ currentPage - 1 }}">
                        Précédent
                    </a>
                    {% endif %}

                    <span class="logs-pagination__info">
                        Page {{ currentPage }} sur {{ totalPages }}
                    </span>

                    {% if currentPage < totalPages %}
                    <a class="button button--secondary" href="{{ base }}/logs?page={{ currentPage + 1 }}">
                        Suivant
                    </a>
                    {% endif %}
                </nav>
                {% endif %}
                {% endif %}
            </main>
        </div>
    </div>
</body>

</html>