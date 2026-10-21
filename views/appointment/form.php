<form class="appointment-form" action="{{ formAction }}" method="post">
    <input type="hidden" name="csrf_token" value="{{ csrfToken }}">

    {% if errors %}
    <div class="appointment-form__errors" role="alert">
        <p>Veuillez corriger les erreurs suivantes :</p>

        <ul>
            {% for error in errors %}
            <li>{{ error }}</li>
            {% endfor %}
        </ul>
    </div>
    {% endif %}

    <div class="appointment-form__layout">
        <div class="appointment-form__fields">
            <p class="appointment-form__notice">
                Les champs marqués d’un * sont obligatoires.
            </p>

            <div class="appointment-form__row appointment-form__row--people">
                <div class="form-field">
                    <label class="form-field__label" for="patient-id">
                        Patient <span aria-hidden="true">*</span>
                    </label>

                    <select class="form-field__control" id="patient-id" name="patient_id" required
                        aria-invalid="{{ errors.patient_id is defined ? 'true' : 'false' }}"
                        {% if errors.patient_id is defined %} aria-describedby="patient-id-error" {% endif %}>
                        <option value="">Choisir un patient</option>

                        {% for patient in patients %}
                        <option value="{{ patient.id }}" {% if values.patient_id == patient.id %}selected{% endif %}>
                            {{ patient.first_name }} {{ patient.last_name }}
                        </option>
                        {% endfor %}
                    </select>

                    {% if errors.patient_id is defined %}
                    <p class="form-field__error" id="patient-id-error">
                        {{ errors.patient_id }}
                    </p>
                    {% endif %}
                </div>

                <div class="form-field">
                    <label class="form-field__label" for="doctor-id">
                        Médecin <span aria-hidden="true">*</span>
                    </label>

                    <select class="form-field__control" id="doctor-id" name="doctor_id" required
                        aria-invalid="{{ errors.doctor_id is defined ? 'true' : 'false' }}"
                        {% if errors.doctor_id is defined %} aria-describedby="doctor-id-error" {% endif %}>
                        <option value="">Choisir un médecin</option>

                        {% for doctor in doctors %}
                        <option value="{{ doctor.id }}" {% if values.doctor_id == doctor.id %}selected{% endif %}>
                            {{ doctor.first_name }} {{ doctor.last_name }}
                        </option>
                        {% endfor %}
                    </select>

                    {% if errors.doctor_id is defined %}
                    <p class="form-field__error" id="doctor-id-error">
                        {{ errors.doctor_id }}
                    </p>
                    {% endif %}
                </div>
            </div>

            <div class="appointment-form__row appointment-form__row--schedule">
                <div class="form-field">
                    <label class="form-field__label" for="appointment-date">
                        Date <span aria-hidden="true">*</span>
                    </label>

                    <input class="form-field__control" type="date" id="appointment-date" name="date"
                        value="{{ values.date }}" min="1000-01-01" max="9999-12-31" required
                        aria-invalid="{{ errors.date is defined ? 'true' : 'false' }}" {% if errors.date is defined %}
                        aria-describedby="appointment-date-error" {% endif %}>

                    {% if errors.date is defined %}
                    <p class="form-field__error" id="appointment-date-error">
                        {{ errors.date }}
                    </p>
                    {% endif %}
                </div>

                <div class="form-field">
                    <label class="form-field__label" for="appointment-time">
                        Heure <span aria-hidden="true">*</span>
                    </label>

                    <input class="form-field__control" type="time" id="appointment-time" name="time"
                        value="{{ values.time }}" required
                        aria-invalid="{{ errors.time is defined ? 'true' : 'false' }}" {% if errors.time is defined %}
                        aria-describedby="appointment-time-error" {% endif %}>

                    {% if errors.time is defined %}
                    <p class="form-field__error" id="appointment-time-error">
                        {{ errors.time }}
                    </p>
                    {% endif %}
                </div>

                <div class="form-field">
                    <label class="form-field__label" for="status">
                        Statut <span aria-hidden="true">*</span>
                    </label>

                    <select class="form-field__control" id="status" name="status" required
                        aria-invalid="{{ errors.status is defined ? 'true' : 'false' }}"
                        {% if errors.status is defined %} aria-describedby="status-error" {% endif %}>
                        <option value="">Choisir un statut</option>

                        {% for value, label in statusLabels %}
                        <option value="{{ value }}" {% if values.status == value %}selected{% endif %}>
                            {{ label }}
                        </option>
                        {% endfor %}
                    </select>

                    {% if errors.status is defined %}
                    <p class="form-field__error" id="status-error">
                        {{ errors.status }}
                    </p>
                    {% endif %}
                </div>
            </div>

            <div class="form-field">
                <label class="form-field__label" for="reason">
                    Motif
                </label>

                <textarea class="form-field__control" id="reason" name="reason" rows="4" maxlength="500"
                    aria-invalid="{{ errors.reason is defined ? 'true' : 'false' }}" {% if errors.reason is defined %}
                    aria-describedby="reason-error" {% endif %}>{{ values.reason }}</textarea>

                {% if errors.reason is defined %}
                <p class="form-field__error" id="reason-error">
                    {{ errors.reason }}
                </p>
                {% endif %}
            </div>

            <fieldset class="appointment-form__services"
                aria-describedby="services-help{% if errors.services is defined %} services-error{% endif %}"
                aria-invalid="{{ errors.services is defined ? 'true' : 'false' }}">
                <legend class="appointment-form__legend">
                    Services <span aria-hidden="true">*</span>
                </legend>

                <p class="appointment-form__help" id="services-help">
                    Sélectionnez au moins un service.
                </p>

                {% for service in services %}
                <label class="service-option">
                    <input class="service-option__input" type="checkbox" name="services[]" value="{{ service.id }}"
                        {% if service.id in selectedServices %}checked{% endif %}>

                    <span class="service-option__label">
                        {{ service.name }}
                        ({{ service.duration_minutes }} min)
                    </span>
                </label>
                {% endfor %}

                {% if errors.services is defined %}
                <p class="form-field__error" id="services-error">
                    {{ errors.services }}
                </p>
                {% endif %}
            </fieldset>
        </div>

        <div class="appointment-form__side">
            <div class="appointment-form__actions">
                <a class="button button--secondary" href="{{ base }}/appointments">
                    <span class="icon icon--arrow-left" aria-hidden="true"></span>
                    Annuler
                </a>

                <button class="button button--primary" type="submit" {% if not canSubmit %}disabled{% endif %}>
                    {{ submitLabel }}
                </button>
            </div>
        </div>
    </div>
</form>