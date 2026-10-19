<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <main class="appointments">
        <header class="appointments__header">
            <div class="appointments__heading">
                <h1 class="appointments__title">Rendez-vous</h1>

                <p class="appointments__description">
                    Organisez les consultations du cabinet.
                </p>
            </div>

            <button class="button button--primary" type="button" disabled>
                <span class="icon icon--plus" aria-hidden="true"></span>

                Planifier un rendez-vous
            </button>
        </header>

        <p>
            <a href="{{ base }}/home">Retour à l’accueil</a>
        </p>

        <section class="stats" aria-label="Statistiques du cabinet">
            <div class="stats__card">
                <span class="stats__icon" aria-hidden="true">
                    <span class="icon icon--calendar"></span>
                </span>

                <h2 class="stats__label">Aujourd’hui</h2>
                <p class="stats__value">{{ todayCount }}</p>
            </div>

            <div class="stats__card">
                <span class="stats__icon" aria-hidden="true">
                    <span class="icon icon--clock"></span>
                </span>

                <h2 class="stats__label">À venir</h2>
                <p class="stats__value">{{ upcomingCount }}</p>
            </div>

            <div class="stats__card">
                <span class="stats__icon" aria-hidden="true">
                    <span class="icon icon--users"></span>
                </span>

                <h2 class="stats__label">Patients</h2>
                <p class="stats__value">{{ patientCount }}</p>
            </div>
        </section>

        <div class="appointments__table-wrapper" role="region" aria-label="Liste des rendez-vous" tabindex="0">
            <table class="data-table">
                <caption class="data-table__caption">
                    Liste des rendez-vous
                </caption>

                <thead class="data-table__head">
                    <tr class="data-table__row">
                        <th scope="col">Date et heure</th>
                        <th scope="col">Patient</th>
                        <th scope="col">Médecin</th>
                        <th scope="col">Services</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>

                <tbody class="data-table__body">
                    {% for appointment in appointments %}
                    <tr class="data-table__row">
                        <td class="data-table__cell">
                            {{ appointment.starts_at|date('d/m/Y à H:i') }}
                        </td>

                        <td class="data-table__cell">
                            <a class="contact-link" href="{{ base }}/patient/show?id={{ appointment.patient_id }}">
                                {{ appointment.patient_first_name }}
                                {{ appointment.patient_last_name }}
                            </a>
                        </td>

                        <td class="data-table__cell">
                            {{ appointment.doctor_first_name }}
                            {{ appointment.doctor_last_name }}
                        </td>

                        <td class="data-table__cell">
                            {{ appointment.service_names|default('Aucun service') }}
                        </td>

                        <td class="data-table__cell">
                            <span class="status-badge status-badge--{{ appointment.status }}">
                                {{ statusLabels[appointment.status]|default(appointment.status) }}
                            </span>
                        </td>

                        <td class="data-table__cell">
                            <div class="data-table__actions">
                                <button class="data-table__action" type="button" disabled>
                                    Modifier
                                </button>

                                <button class="data-table__action data-table__action--danger" type="button" disabled>
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                    {% else %}
                    <tr>
                        <td colspan="6">
                            Aucun rendez-vous enregistré.
                        </td>
                    </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>