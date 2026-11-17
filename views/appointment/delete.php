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
            <main class="appointment-delete">
                <a class="back-link" href="{{ base }}/appointments">
                    <span class="icon icon--arrow-left" aria-hidden="true"></span>
                    {{ trans['appointment.back'] }}
                </a>
                <section class="confirmation" aria-labelledby="confirmation-title">
                    <span class="confirmation__symbol" aria-hidden="true">
                        <span class="icon icon--trash"></span>
                    </span>

                    <h1 class="confirmation__title" id="confirmation-title">
                        {{ trans['appointment.delete_question'] }}
                    </h1>

                    <p class="confirmation__description">
                        {{ trans['appointment.delete_warning'] }}
                    </p>

                    <dl class="confirmation__details">
                        <div>
                            <dt>{{ trans['common.patient'] }}</dt>
                            <dd>
                                {{ appointment.patient_first_name }}
                                {{ appointment.patient_last_name }}
                            </dd>
                        </div>

                        <div>
                            <dt>{{ trans['appointment.doctor'] }}</dt>
                            <dd>
                                {{ appointment.doctor_first_name }}
                                {{ appointment.doctor_last_name }}
                            </dd>
                        </div>

                        <div>
                            <dt>{{ trans['appointment.datetime'] }}</dt>
                            <dd>
                                {{ appointment.starts_at|date('d/m/Y') }} {{ trans['common.at'] }} {{ appointment.starts_at|date('H:i') }}
                            </dd>
                        </div>

                        <div>
                            <dt>{{ trans['appointment.services'] }}</dt>
                            <dd>
                                {{ appointment.service_names|service_list }}
                            </dd>
                        </div>
                    </dl>

                    <p class="confirmation__warning">
                        {{ trans['appointment.delete_kept'] }}
                    </p>

                    <form class="confirmation__form" action="{{ base }}/appointment/delete" method="post">
                        <input type="hidden" name="id" value="{{ appointment.id }}">

                        <input type="hidden" name="csrf_token" value="{{ csrfToken }}">

                        <div class="confirmation__actions">
                            <a class="button button--secondary" href="{{ base }}/appointments">
                                {{ trans['appointment.keep'] }}
                            </a>

                            <button class="button button--danger" type="submit">
                                <span class="icon icon--trash" aria-hidden="true"></span>

                                {{ trans['appointment.delete_confirm'] }}
                            </button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>
</body>

</html>
