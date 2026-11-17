<!DOCTYPE html>
<html lang="{{ locale }}">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'client'
        }) }}

        <div class="app-layout__content">
            {{ include('layouts/account.php') }}
            <main class="patient-profile">
                <header class="home__header">
                    <h1 class="home__title">
                        {{ trans['client.welcome'] }} {{ session.user_name }}
                    </h1>

                    <p class="home__description">
                        {{ trans['client.description'] }}
                    </p>
                </header>

                <section aria-labelledby="appointments-title">
                    <div class="appointments-heading">
                        <h2 id="appointments-title">
                            {{ trans['client.appointments'] }}
                        </h2>

                        <a class="button button--secondary" href="{{ base }}/pdf/appointments">
                            {{ trans['client.download_pdf'] }}
                        </a>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <caption class="sr-only">
                                {{ trans['client.appointments_for'] }}
                                {{ patient.first_name }} {{ patient.last_name }}
                            </caption>

                            <thead>
                                <tr>
                                    <th scope="col">{{ trans['appointment.datetime'] }}</th>
                                    <th scope="col">{{ trans['appointment.doctor'] }}</th>
                                    <th scope="col">{{ trans['appointment.services'] }}</th>
                                    <th scope="col">{{ trans['appointment.status'] }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                {% for appointment in appointments %}
                                <tr>
                                    <td>
                                        {{ appointment.starts_at|date('d/m/Y') }}
                                        {{ trans['common.at'] }}
                                        {{ appointment.starts_at|date('H:i') }}
                                    </td>

                                    <td>
                                        {{ appointment.doctor_first_name }}
                                        {{ appointment.doctor_last_name }}
                                    </td>

                                    <td>
                                        {{ appointment.service_names|service_list }}
                                    </td>

                                    <td>
                                        <span class="status-badge status-badge--{{ appointment.status }}">
                                            {{ trans['status.' ~ appointment.status]|default(appointment.status) }}
                                        </span>
                                    </td>
                                </tr>
                                {% else %}
                                <tr>
                                    <td colspan="4">
                                        {{ trans['client.empty'] }}
                                    </td>
                                </tr>
                                {% endfor %}
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>

</html>
