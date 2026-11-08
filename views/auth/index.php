<!DOCTYPE html>
<html lang="fr">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'login'
        }) }}

        <div class="app-layout__content">
            <main class="appointment-editor">
                <header class="appointment-editor__header">
                    <h1>Connexion</h1>
                </header>

                {% if errors %}
                <div class="appointment-form__errors" role="alert">
                    <ul>
                        {% for error in errors %}
                        <li>{{ error }}</li>
                        {% endfor %}
                    </ul>
                </div>
                {% endif %}

                <form action="{{ base }}/login" method="post">
                    <input type="hidden" name="csrf_token" value="{{ csrfToken }}">

                    <div class="appointment-form__fields">
                        <div class="form-field">
                            <label for="username">
                                Identifiant de connexion
                            </label>

                            <input class="form-field__control" type="email" id="username" name="username"
                                value="{{ values.username|default('') }}" maxlength="50" autocomplete="username"
                                required>
                        </div>

                        <div class="form-field">
                            <label for="password">Mot de passe</label>

                            <input class="form-field__control" type="password" id="password" name="password"
                                autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="appointment-form__actions">
                        <button class="button button--primary" type="submit">
                            Se connecter
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>