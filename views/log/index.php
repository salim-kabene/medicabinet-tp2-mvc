<!DOCTYPE html>
<html lang="{{ locale }}">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'logs'
        }) }}

        <div class="app-layout__content">
            {{ include('layouts/account.php') }}
            <main class="home">
                <header>
                    <h1 class="home__title">{{ trans['nav.logs'] }}</h1>
                    <p>{{ trans['log.description'] }}</p>
                </header>

                {% if logs is empty %}
                <section class="logs-empty" aria-label="{{ trans['log.empty_label'] }}">
                    <h2>{{ trans['log.empty'] }}</h2>
                    <p>{{ trans['log.empty_help'] }}</p>
                </section>
                {% else %}
                <div class="logs-table-wrapper">
                    <table class="logs-table">
                        <caption>
                            {{ trans['log.visits'] }} {{ firstEntry }} {{ trans['log.to'] }} {{ lastEntry }}
                            {{ trans['log.of'] }} {{ total }}
                        </caption>

                        <thead>
                            <tr>
                                <th scope="col">{{ trans['appointment.datetime'] }}</th>
                                <th scope="col">{{ trans['log.user'] }}</th>
                                <th scope="col">{{ trans['log.ip'] }}</th>
                                <th scope="col">{{ trans['log.page_visited'] }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            {% for log in logs %}
                            <tr>
                                <td>
                                    {{ log.visited_at|date('d/m/Y H:i:s') }}
                                </td>
                                <td>{{ log.username == 'Visiteur' ? trans['log.visitor'] : log.username }}</td>
                                <td>{{ log.ip_address|escape }}</td>
                                <td>{{ log.page|escape }}</td>
                            </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                </div>

                {% if totalPages > 1 %}
                <nav class="logs-pagination" aria-label="{{ trans['log.pagination'] }}">

                    {% if currentPage > 1 %}
                    <a class="button button--secondary" href="{{ base }}/logs?page={{ currentPage - 1 }}">
                        {{ trans['log.previous'] }}
                    </a>
                    {% endif %}

                    <span class="logs-pagination__info">
                        {{ trans['log.page'] }} {{ currentPage }} {{ trans['log.of'] }} {{ totalPages }}
                    </span>

                    {% if currentPage < totalPages %}
                    <a class="button button--secondary" href="{{ base }}/logs?page={{ currentPage + 1 }}">
                        {{ trans['log.next'] }}
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
