<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'users'
        }) }}

        <div class="app-layout__content">
            {{ include('layouts/account.php') }}
            <main class="appointment-editor">
                <header class="appointment-editor__header">
                    <h1>Créer un utilisateur</h1>
                </header>
                {% if success %}
                <p class="alert alert--success" role="status">
                    {{ success }}
                </p>
                {% endif %}
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

                <form action="{{ base }}/user/create" method="post">
                    <input type="hidden" name="csrf_token" value="{{ csrfToken }}">

                    <div class="appointment-form__fields">
                        <div class="form-field">
                            <label for="name">Nom complet</label>
                            <input class="form-field__control" type="text" id="name" name="name"
                                value="{{ values.name|default('') }}" minlength="2" maxlength="50" required>
                        </div>

                        <div class="form-field">
                            <label for="username">
                                Identifiant de connexion (courriel)
                            </label>
                            <input class="form-field__control" type="email" id="username" name="username"
                                value="{{ values.username|default('') }}" maxlength="50" autocomplete="username"
                                required>
                        </div>

                        <div class="form-field">
                            <label for="email">Courriel de contact</label>
                            <input class="form-field__control" type="email" id="email" name="email"
                                value="{{ values.email|default('') }}" maxlength="100" required>
                        </div>

                        <div class="form-field">
                            <label for="password">Mot de passe</label>
                            <input class="form-field__control" type="password" id="password" name="password"
                                autocomplete="new-password" minlength="8" required>
                        </div>

                        <div class="form-field">
                            <label for="privilege-id">Privilège</label>
                            <select class="form-field__control" id="privilege-id" name="privilege_id" required>
                                <option value="">Choisir un privilège</option>

                                {% for privilege in privileges %}
                                <option value="{{ privilege.id }}"
                                    {% if values.privilege_id|default('') == privilege.id %} selected {% endif %}>
                                    {{ privilege.privilege }}
                                </option>
                                {% endfor %}
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="patient-id">Patient associé</label>
                            <select class="form-field__control" id="patient-id" name="patient_id"
                                aria-describedby="patient-help">
                                <option value="">Aucun patient</option>

                                {% for patient in patients %}
                                <option value="{{ patient.id }}" {% if values.patient_id|default('') == patient.id %}
                                    selected {% endif %}>
                                    {{ patient.first_name }}
                                    {{ patient.last_name }}
                                </option>
                                {% endfor %}
                            </select>

                            <p id="patient-help">
                                Obligatoire pour un client.
                                Pour un administrateur, choisir « Aucun patient ».
                            </p>
                        </div>
                    </div>

                    <div class="appointment-form__actions">
                        <a class="button button--secondary" href="{{ base }}/">
                            Annuler
                        </a>

                        <button class="button button--primary" type="submit">
                            Créer le compte
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>