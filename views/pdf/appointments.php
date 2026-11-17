<!DOCTYPE html>
<html lang="{{ locale }}">

<head>
    <meta charset="UTF-8">
    <title>{{ trans['pdf.title'] }}</title>

    <style>
        @page {
            margin: 35px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 11px;
            color: #17324d;
            line-height: 1.5;
        }

        .document-header {
            padding-bottom: 15px;
            border-bottom: 3px solid #008497;
            margin-bottom: 22px;
        }

        .brand {
            margin: 0 0 8px;
            color: #008497;
            font-size: 24px;
            font-weight: bold;
        }

        h1 {
            margin: 0;
            font-size: 19px;
        }

        .patient-info {
            padding: 14px;
            margin-bottom: 22px;
            background-color: #edf7f8;
        }

        .patient-info p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        th,
        td {
            padding: 9px 7px;
            border: 1px solid #d5e4ec;
            text-align: left;
            vertical-align: top;
            overflow-wrap: break-word;
        }

        th {
            background-color: #edf7f8;
        }

        tr {
            page-break-inside: avoid;
        }

        .empty {
            padding: 20px;
            border: 1px solid #d5e4ec;
            text-align: center;
        }

        .document-note {
            margin-top: 20px;
            font-size: 9px;
            color: #526b80;
        }
    </style>
</head>

<body>
    <header class="document-header">
        <p class="brand">MédiCabinet</p>
        <h1>{{ trans['pdf.title'] }}</h1>
    </header>

    <section class="patient-info">
        <p>
            <strong>{{ trans['common.patient'] }} :</strong>
            {{ patient.first_name }} {{ patient.last_name }}
        </p>

        <p>
            <strong>{{ trans['pdf.generated'] }}</strong>
            {{ generatedAt }}
        </p>

        <p>
            <strong>{{ trans['pdf.count'] }}</strong>
            {{ appointments|length }}
        </p>
    </section>

    {% if appointments is empty %}
    <p class="empty">
        {{ trans['patient.empty_appointments'] }}
    </p>
    {% else %}
    <table>
        <thead>
            <tr>
                <th style="width: 22%;">{{ trans['appointment.datetime'] }}</th>
                <th style="width: 25%;">{{ trans['appointment.doctor'] }}</th>
                <th style="width: 33%;">{{ trans['appointment.services'] }}</th>
                <th style="width: 20%;">{{ trans['appointment.status'] }}</th>
            </tr>
        </thead>

        <tbody>
            {% for appointment in appointments %}
            <tr>
                <td>
                    {{ appointment.starts_at|date('d/m/Y') }}
                    <br>
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
                    {{ statusLabels[appointment.status]|default(trans['common.not_provided']) }}
                </td>
            </tr>
            {% endfor %}
        </tbody>
    </table>
    {% endif %}

    <p class="document-note">
        {{ trans['pdf.note'] }}
    </p>
</body>

</html>
