<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <main class="appointment-delete">
        <a class="back-link" href="{{ base }}/appointments">
            Retour aux rendez-vous
        </a>

        <section class="confirmation" aria-labelledby="confirmation-title">
            <span class="confirmation__symbol" aria-hidden="true">
                <span class="icon icon--trash"></span>
            </span>

            <h1 class="confirmation__title" id="confirmation-title">
                Supprimer ce rendez-vous ?
            </h1>

            <p class="confirmation__description">
                Cette action est définitive.
            </p>

            <dl class="confirmation__details">
                <div>
                    <dt>Patient</dt>
                    <dd>
                        {{ appointment.patient_first_name }}
                        {{ appointment.patient_last_name }}
                    </dd>
                </div>

                <div>
                    <dt>Médecin</dt>
                    <dd>
                        {{ appointment.doctor_first_name }}
                        {{ appointment.doctor_last_name }}
                    </dd>
                </div>

                <div>
                    <dt>Date et heure</dt>
                    <dd>
                        {{ appointment.starts_at|date('d/m/Y à H:i') }}
                    </dd>
                </div>

                <div>
                    <dt>Services</dt>
                    <dd>
                        {{ appointment.service_names|default('Aucun service') }}
                    </dd>
                </div>
            </dl>

            <p class="confirmation__warning">
                Le patient, le médecin et les services seront conservés.
            </p>

            <form class="confirmation__form" action="{{ base }}/appointment/delete" method="post">
                <input type="hidden" name="id" value="{{ appointment.id }}">

                <input type="hidden" name="csrf_token" value="{{ csrfToken }}">

                <div class="confirmation__actions">
                    <a class="button button--secondary" href="{{ base }}/appointments">
                        Conserver le rendez-vous
                    </a>

                    <button class="button button--danger" type="submit">
                        <span class="icon icon--trash" aria-hidden="true"></span>

                        Supprimer définitivement
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>

</html>