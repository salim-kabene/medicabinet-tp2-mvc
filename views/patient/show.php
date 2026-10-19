<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <main class="patient-profile">
        <a class="back-link" href="{{ base }}/patients">
            Retour aux patients
        </a>

        <header class="patient-profile__header">
            <div class="patient-profile__identity">
                <div>
                    <h1>
                        {{ patient.first_name }}
                        {{ patient.last_name }}
                    </h1>

                    <ul class="patient-profile__contacts">
                        <li>
                            <span class="icon icon--calendar" aria-hidden="true"></span>

                            <span>
                                Date de naissance :
                                {{ patient.birth_date|date('d/m/Y') }}
                            </span>
                        </li>

                        <li>
                            <span class="icon icon--phone" aria-hidden="true"></span>

                            <a class="contact-link" href="tel:{{ patient.phone }}">
                                {{ patient.phone }}
                            </a>
                        </li>

                        <li>
                            <span class="icon icon--mail" aria-hidden="true"></span>

                            {% if patient.email %}
                            <a class="contact-link" href="mailto:{{ patient.email }}">
                                {{ patient.email }}
                            </a>
                            {% else %}
                            Courriel non renseigné
                            {% endif %}
                        </li>
                    </ul>
                </div>
            </div>
            <a class="button button--primary" href="{{ base }}/appointment/create?patient_id={{ patient.id }}">
                <span class="icon icon--plus" aria-hidden="true"></span>
                Planifier un rendez-vous
            </a>
        </header>
        <section class="patient-profile__history" aria-labelledby="history-title">
            <h2 id="history-title">
                Historique des rendez-vous
                ({{ appointments|length }})
            </h2>

            <div class="table-wrapper" role="region" aria-label="Historique des rendez-vous du patient" tabindex="0">
                <table class="data-table">
                    <caption class="sr-only">
                        Rendez-vous de
                        {{ patient.first_name }}
                        {{ patient.last_name }}
                    </caption>

                    <thead>
                        <tr>
                            <th scope="col">Date et heure</th>
                            <th scope="col">Médecin</th>
                            <th scope="col">Services</th>
                            <th scope="col">Statut</th>
                            <th scope="col">Actions</th>
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
                            <td>
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
                        <tr>
                            <td colspan="5">
                                Aucun rendez-vous enregistré pour ce patient.
                            </td>
                        </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>

</html>