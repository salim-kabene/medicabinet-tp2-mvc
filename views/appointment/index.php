<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'appointments'
        }) }}

        <div class="app-layout__content">
            <main class="appointments">
                <header class="appointments__header">
                    <div class="appointments__heading">
                        <h1 class="appointments__title">Rendez-vous</h1>

                        <p class="appointments__description">
                            Organisez les consultations du cabinet.
                        </p>
                    </div>

                    <a class="button button--primary" href="{{ base }}/appointment/create">
                        <span class="icon icon--plus" aria-hidden="true"></span>
                        Planifier un rendez-vous
                    </a>
                </header>
                {% if success == 'created' %}
                <p class="alert alert--success" role="status">
                    Le rendez-vous a été enregistré.
                </p>
                {% elseif success == 'updated' %}
                <p class="alert alert--success" role="status">
                    Les modifications ont été enregistrées.
                </p>
                {% elseif success == 'deleted' %}
                <p class="alert alert--success" role="status">
                    Le rendez-vous a été supprimé.
                </p>
                {% endif %}
                <a class="back-link" href="{{ base }}/">
                    <span class="icon icon--arrow-left" aria-hidden="true"></span>
                    Retour à l’accueil
                </a>

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
                <form class="filters" action="{{ base }}/appointments" method="get">
                    <div class="filters__field">
                        <label class="filters__label" for="filter-date">
                            Date
                        </label>

                        <input class="filters__input" type="date" id="filter-date" name="date" value="{{ date }}">
                    </div>

                    <div class="filters__field">
                        <label class="filters__label" for="filter-status">
                            Statut
                        </label>

                        <select class="filters__select" id="filter-status" name="status">
                            <option value="">Tous les statuts</option>

                            {% for value, label in statusLabels %}
                            <option value="{{ value }}" {% if status == value %}selected{% endif %}>
                                {{ label }}
                            </option>
                            {% endfor %}
                        </select>
                    </div>

                    <div class="filters__actions">
                        <button class="button button--primary" type="submit">
                            <span class="icon icon--filter" aria-hidden="true"></span>
                            Filtrer
                        </button>
                        <a class="button button--secondary" href="{{ base }}/appointments">
                            Réinitialiser
                        </a>
                    </div>
                </form>
                <div class="appointments__table-wrapper" role="region" aria-label="Liste des rendez-vous" tabindex="0">
                    <table class="data-table{% if appointments is empty %} data-table--empty{% endif %}">
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
                                    <a class="contact-link"
                                        href="{{ base }}/patient/show?id={{ appointment.patient_id }}">
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
                                        <a class="data-table__action"
                                            href="{{ base }}/appointment/edit?id={{ appointment.id }}"
                                            aria-label="Modifier le rendez-vous numéro {{ appointment.id }}">
                                            Modifier
                                        </a>

                                        <a class="data-table__action data-table__action--danger"
                                            href="{{ base }}/appointment/delete?id={{ appointment.id }}"
                                            aria-label="Supprimer le rendez-vous numéro {{ appointment.id }}">
                                            Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            {% else %}
                            <tr class="data-table__row data-table__row--empty">
                                <td class="data-table__empty-cell" colspan="6">
                                    <div class="data-table__empty">
                                        <span class="data-table__empty-icon" aria-hidden="true">
                                            <span class="icon icon--calendar"></span>
                                        </span>

                                        {% if date or status %}
                                        <p class="data-table__empty-title">
                                            Aucun rendez-vous ne correspond à ces filtres
                                        </p>
                                        <p class="data-table__empty-description">
                                            Modifiez la date ou le statut, ou réinitialisez les filtres pour afficher tous les rendez-vous.
                                        </p>
                                        <a class="button button--secondary" href="{{ base }}/appointments">
                                            <span class="icon icon--arrow-left" aria-hidden="true"></span>
                                            Réinitialiser les filtres
                                        </a>
                                        {% else %}
                                        <p class="data-table__empty-title">
                                            Aucun rendez-vous enregistré
                                        </p>
                                        <p class="data-table__empty-description">
                                            Planifiez un premier rendez-vous en choisissant un patient, un médecin et les services souhaités.
                                        </p>
                                        <a class="button button--primary" href="{{ base }}/appointment/create">
                                            <span class="icon icon--plus" aria-hidden="true"></span>
                                            Planifier un rendez-vous
                                        </a>
                                        {% endif %}
                                    </div>
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
