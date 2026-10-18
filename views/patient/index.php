<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <main class="patients">
        <header class="patients__header">
            <div>
                <h1>Patients</h1>

                <p class="patients__description">
                    Retrouvez les coordonnées de vos patients.
                </p>
            </div>

            <span class="count-badge">
                {{ patientCount }}
                {% if patientCount > 1 %}
                patients
                {% else %}
                patient
                {% endif %}
            </span>
        </header>

        <p>
            <a href="{{ base }}/home">Retour à l’accueil</a>
        </p>

        <div class="table-wrapper" role="region" aria-label="Liste des patients" tabindex="0">
            <table class="data-table">
                <caption class="sr-only">
                    Coordonnées des patients
                </caption>

                <thead>
                    <tr>
                        <th scope="col">Patient</th>
                        <th scope="col">Date de naissance</th>
                        <th scope="col">Téléphone</th>
                        <th scope="col">Courriel</th>
                        <th scope="col">Action</th>
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
                            {{ patient.email|default('Non renseigné') }}
                        </td>
                        <td>
                            <a class="data-table__action" href="{{ base }}/patient/show?id={{ patient.id }}"
                                aria-label="Voir la fiche de {{ patient.first_name }} {{ patient.last_name }}">
                                Voir la fiche
                            </a>
                        </td>
                    </tr>
                    {% else %}
                    <tr>
                        <td colspan="5">
                            Aucun patient enregistré.
                        </td>
                    </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>