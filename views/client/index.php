<!DOCTYPE html>
<html lang="fr">

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
                        Bienvenue, {{ session.user_name }}
                    </h1>

                    <p class="home__description">
                        Espace client — consultez vos rendez-vous.
                    </p>
                </header>

                <section aria-labelledby="appointments-title">
                    <h2 id="appointments-title">Mes rendez-vous</h2>

                    <div class="table-wrapper">
                        <table class="data-table">
                            <caption class="sr-only">
                                Rendez-vous de {{ patient.first_name }}
                                {{ patient.last_name }}
                            </caption>

                            <thead>
                                <tr>
                                    <th scope="col">Date et heure</th>
                                    <th scope="col">Médecin</th>
                                    <th scope="col">Services</th>
                                    <th scope="col">Statut</th>
                                </tr>
                            </thead>

                            <tbody>
                                {% for appointment in appointments %}
                                <tr>
                                    <td>
                                        {{ appointment.starts_at|date('d/m/Y à H:i') }}
                                    </td>
                                    <td>
                                        {{ appointment.doctor_first_name }}
                                        {{ appointment.doctor_last_name }}
                                    </td>
                                    <td>
                                        {{ appointment.service_names|default('Aucun service') }}
                                    </td>
                                    <td>
                                        <span class="status-badge status-badge--{{ appointment.status }}">
                                            {{ statusLabels[appointment.status]|default(appointment.status) }}
                                        </span>
                                    </td>
                                </tr>
                                {% else %}
                                <tr>
                                    <td colspan="4">
                                        Vous n’avez aucun rendez-vous enregistré.
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