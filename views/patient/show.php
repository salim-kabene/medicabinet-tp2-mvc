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
        </header>
    </main>
</body>

</html>