<!DOCTYPE html>
<html lang="{{ locale }}">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'appointments'
        }) }}

        <div class="app-layout__content">
            {{ include('layouts/account.php') }}
            <main class="appointments">
                <header class="appointments__header">
                    <div class="appointments__heading">
                        <h1 class="appointments__title">{{ trans['nav.appointments'] }}</h1>

                        <p class="appointments__description">
                            {{ trans['appointment.description'] }}
                        </p>
                    </div>

                    <a class="button button--primary" href="{{ base }}/appointment/create">
                        <span class="icon icon--plus" aria-hidden="true"></span>
                        {{ trans['appointment.create'] }}
                    </a>
                </header>
                {% if success == 'created' %}
                <p class="alert alert--success" role="status">
                    {{ trans['appointment.created'] }}
                </p>
                {% elseif success == 'updated' %}
                <p class="alert alert--success" role="status">
                    {{ trans['appointment.updated'] }}
                </p>
                {% elseif success == 'deleted' %}
                <p class="alert alert--success" role="status">
                    {{ trans['appointment.deleted'] }}
                </p>
                {% endif %}

                <section class="stats" aria-label="{{ trans['appointment.stats'] }}">
                    <div class="stats__card">
                        <span class="stats__icon" aria-hidden="true">
                            <span class="icon icon--calendar"></span>
                        </span>

                        <h2 class="stats__label">{{ trans['appointment.today'] }}</h2>
                        <p class="stats__value">{{ todayCount }}</p>
                    </div>

                    <div class="stats__card">
                        <span class="stats__icon" aria-hidden="true">
                            <span class="icon icon--clock"></span>
                        </span>

                        <h2 class="stats__label">{{ trans['appointment.upcoming'] }}</h2>
                        <p class="stats__value">{{ upcomingCount }}</p>
                    </div>

                    <div class="stats__card">
                        <span class="stats__icon" aria-hidden="true">
                            <span class="icon icon--users"></span>
                        </span>

                        <h2 class="stats__label">{{ trans['nav.patients'] }}</h2>
                        <p class="stats__value">{{ patientCount }}</p>
                    </div>
                </section>
                <form class="filters" action="{{ base }}/appointments" method="get">
                    <div class="filters__field">
                        <label class="filters__label" for="filter-date">
                            {{ trans['common.date'] }}
                        </label>

                        <input class="filters__input" type="date" id="filter-date" name="date" value="{{ date }}">
                    </div>

                    <div class="filters__field">
                        <label class="filters__label" for="filter-status">
                            {{ trans['appointment.status'] }}
                        </label>

                        <select class="filters__select" id="filter-status" name="status">
                            <option value="">{{ trans['appointment.all_statuses'] }}</option>

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
                            {{ trans['common.filter'] }}
                        </button>
                        <a class="button button--secondary" href="{{ base }}/appointments">
                            {{ trans['common.reset'] }}
                        </a>
                    </div>
                </form>
                <div class="appointments__table-wrapper" role="region" aria-label="{{ trans['appointment.list'] }}" tabindex="0">
                    <table class="data-table{% if appointments is empty %} data-table--empty{% endif %}">
                        <caption class="data-table__caption">
                            {{ trans['appointment.list'] }}
                        </caption>

                        <thead class="data-table__head">
                            <tr class="data-table__row">
                                <th scope="col">{{ trans['appointment.datetime'] }}</th>
                                <th scope="col">{{ trans['common.patient'] }}</th>
                                <th scope="col">{{ trans['appointment.doctor'] }}</th>
                                <th scope="col">{{ trans['appointment.services'] }}</th>
                                <th scope="col">{{ trans['appointment.status'] }}</th>
                                <th scope="col">{{ trans['common.actions'] }}</th>
                            </tr>
                        </thead>

                        <tbody class="data-table__body">
                            {% for appointment in appointments %}
                            <tr class="data-table__row">
                                <td class="data-table__cell">
                                    {{ appointment.starts_at|date('d/m/Y') }} {{ trans['common.at'] }} {{ appointment.starts_at|date('H:i') }}
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
                                    {{ appointment.service_names|service_list }}
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
                            <tr class="data-table__row data-table__row--empty">
                                <td class="data-table__empty-cell" colspan="6">
                                    <div class="data-table__empty">
                                        <span class="data-table__empty-icon" aria-hidden="true">
                                            <span class="icon icon--calendar"></span>
                                        </span>

                                        {% if date or status %}
                                        <p class="data-table__empty-title">
                                            {{ trans['appointment.no_matches'] }}
                                        </p>
                                        <p class="data-table__empty-description">
                                            {{ trans['appointment.filter_help'] }}
                                        </p>
                                        <a class="button button--secondary" href="{{ base }}/appointments">
                                            <span class="icon icon--arrow-left" aria-hidden="true"></span>
                                            {{ trans['common.reset_filters'] }}
                                        </a>
                                        {% else %}
                                        <p class="data-table__empty-title">
                                            {{ trans['appointment.empty'] }}
                                        </p>
                                        <p class="data-table__empty-description">
                                            {{ trans['appointment.empty_help'] }}
                                        </p>
                                        <a class="button button--primary" href="{{ base }}/appointment/create">
                                            <span class="icon icon--plus" aria-hidden="true"></span>
                                            {{ trans['appointment.create'] }}
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
