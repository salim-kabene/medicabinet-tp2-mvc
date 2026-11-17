<!DOCTYPE html>
<html lang="{{ locale }}">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'patients'
        }) }}

        <div class="app-layout__content">
            {{ include('layouts/account.php') }}
            <main class="patients">
                <header class="patients__header">
                    <div>
                        <h1>{{ trans['nav.patients'] }}</h1>

                        <p class="patients__description">
                            {{ trans['patient.description'] }}
                        </p>
                    </div>

                    <span class="count-badge">
                        <span class="icon icon--users" aria-hidden="true"></span>
                        <span>
                            {{ patientCount }}
                            {{ patientCount == 1 ? 'patient' : 'patients' }}
                        </span>
                    </span>
                </header>

                <div class="table-wrapper" role="region" aria-label="{{ trans['patient.list'] }}" tabindex="0">
                    <table class="data-table">
                        <caption class="sr-only">
                            {{ trans['patient.contacts'] }}
                        </caption>

                        <thead>
                            <tr>
                                <th scope="col">{{ trans['common.patient'] }}</th>
                                <th scope="col">{{ trans['patient.birth_date'] }}</th>
                                <th scope="col">{{ trans['patient.phone'] }}</th>
                                <th scope="col">{{ trans['patient.email'] }}</th>
                                <th scope="col">{{ trans['common.action'] }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            {% for patient in patients %}
                            <tr>
                                <th scope="row">
                                    {{ patient.first_name }}
                                    {{ patient.last_name }}
                                </th>

                                <td>
                                    {{ patient.birth_date|date('d/m/Y') }}
                                </td>

                                <td>{{ patient.phone }}</td>

                                <td>
                                    {{ patient.email|default(trans['common.not_provided']) }}
                                </td>
                                <td>
                                    <a class="data-table__action" href="{{ base }}/patient/show?id={{ patient.id }}"
                                        aria-label="{{ trans['patient.view_for'] }} {{ patient.first_name }} {{ patient.last_name }}">
                                        {{ trans['patient.view'] }}
                                    </a>
                                </td>
                            </tr>
                            {% else %}
                            <tr>
                                <td colspan="5">
                                    {{ trans['patient.empty'] }}
                                </td>
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
