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
            <main class="patient-profile">
                <a class="back-link" href="{{ base }}/patients">
                    <span class="icon icon--arrow-left" aria-hidden="true"></span>
                    {{ trans['patient.back'] }}
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
                                        {{ trans['patient.birth_date'] }} :
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
                                    {{ trans['patient.no_email'] }}
                                    {% endif %}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a class="button button--secondary" href="{{ base }}/pdf/appointments?id={{ patient.id }}">
                        {{ trans['patient.download_pdf'] }}
                    </a>
                    <a class="button button--primary" href="{{ base }}/appointment/create?patient_id={{ patient.id }}">
                        <span class="icon icon--plus" aria-hidden="true"></span>
                        {{ trans['appointment.create'] }}
                    </a>
                </header>
                <section class="patient-profile__history" aria-labelledby="history-title">
                    <h2 id="history-title">
                        {{ trans['patient.history'] }} ({{ appointments|length }})
                    </h2>

                    <div class="table-wrapper" role="region" aria-label="{{ trans['patient.history_label'] }}"
                        tabindex="0">
                        <table class="data-table">
                            <caption class="sr-only">
                                {{ trans['client.appointments_for'] }}
                                {{ patient.first_name }}
                                {{ patient.last_name }}
                            </caption>

                            <thead>
                                <tr>
                                    <th scope="col">{{ trans['appointment.datetime'] }}</th>
                                    <th scope="col">{{ trans['appointment.doctor'] }}</th>
                                    <th scope="col">{{ trans['appointment.services'] }}</th>
                                    <th scope="col">{{ trans['appointment.status'] }}</th>
                                    <th scope="col">{{ trans['common.actions'] }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                {% for appointment in appointments %}
                                <tr>
                                    <td>
                                        {{ appointment.starts_at|date('d/m/Y') }} {{ trans['common.at'] }} {{ appointment.starts_at|date('H:i') }}
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
                                            {{ statusLabels[appointment.status]|default(appointment.status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="data-table__actions">
                                            <a class="data-table__action"
                                                href="{{ base }}/appointment/edit?id={{ appointment.id }}"
                                                aria-label="{{ trans['appointment.edit_number'] }} {{ appointment.id }}">
                                                {{ trans['common.edit'] }}
                                            </a>

                                            <a class="data-table__action data-table__action--danger"
                                                href="{{ base }}/appointment/delete?id={{ appointment.id }}"
                                                aria-label="{{ trans['appointment.delete_number'] }} {{ appointment.id }}">
                                                {{ trans['common.delete'] }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                {% else %}
                                <tr>
                                    <td colspan="5">
                                        {{ trans['patient.empty_appointments'] }}
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
