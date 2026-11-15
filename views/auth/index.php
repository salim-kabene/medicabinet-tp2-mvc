<!DOCTYPE html>
<html lang="{{ locale }}">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'login'
        }) }}

        <div class="app-layout__content">
            <div class="account-bar">
                {% include 'layouts/language.php' %}
            </div>
            <main class="appointment-editor">
                <header class="appointment-editor__header">
                    <h1>{{ trans['auth.title'] }}</h1>
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
                                {{ trans['auth.username'] }}
                            </label>

                            <input class="form-field__control" type="email" id="username" name="username"
                                value="{{ values.username|default('') }}" maxlength="50" autocomplete="username"
                                required>
                        </div>

                        <div class="form-field">
                            <label for="password">{{ trans['auth.password'] }}</label>

                            <input class="form-field__control" type="password" id="password" name="password"
                                autocomplete="current-password" required>
                        </div>
                    </div>

                    <div class="appointment-form__actions">
                        <button class="button button--primary" type="submit">
                            {{ trans['account.login'] }}
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>

</html>