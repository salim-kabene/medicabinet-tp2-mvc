<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Récapitulatif des rendez-vous</title>

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
        <h1>Récapitulatif des rendez-vous</h1>
    </header>

    <section class="patient-info">
        <p>
            <strong>Patient :</strong>
            {{ patient.first_name }} {{ patient.last_name }}
        </p>

        <p>
            <strong>Document généré le :</strong>
            {{ generatedAt }}
        </p>

        <p>
            <strong>Nombre de rendez-vous :</strong>
            {{ appointments|length }}
        </p>
    </section>

    {% if appointments is empty %}
    <p class="empty">
        Aucun rendez-vous enregistré pour ce patient.
    </p>
    {% else %}
    <table>
        <thead>
            <tr>
                <th style="width: 22%;">Date et heure</th>
                <th style="width: 25%;">Médecin</th>
                <th style="width: 33%;">Services</th>
                <th style="width: 20%;">Statut</th>
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
                    {{ appointment.service_names|default('Non renseigné') }}
                </td>

                <td>
                    {{ statusLabels[appointment.status]|default('Non renseigné') }}
                </td>
            </tr>
            {% endfor %}
        </tbody>
    </table>
    {% endif %}

    <p class="document-note">
        Ce document présente les rendez-vous enregistrés au moment
        de sa génération.
    </p>
</body>

</html>