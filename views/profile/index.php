<!DOCTYPE html>
<html lang="{{ locale }}">

<head>
    {{ include('layouts/head.php') }}
</head>

<body>
    <div class="app-layout">
        {{ include('layouts/sidebar.php', {
            activePage: 'profile'
        }) }}

        <div class="app-layout__content">
            {{ include('layouts/account.php') }}
            <p class="language-switch__notice">{{ trans['language.reload_notice'] }}</p>
            <main class="home">
                <header>
                    <h1 class="home__title"> {{ trans['account.profile'] }}</h1>
                    <p>{{ trans['profile.description'] }}</p>
                </header>

                {% if success %}
                <p role="status">{{ success }}</p>
                {% endif %}

                {% if errors %}
                <div role="alert">
                    <ul>
                        {% for error in errors %}
                        <li>{{ error }}</li>
                        {% endfor %}
                    </ul>
                </div>
                {% endif %}

                <section class="profile-photo">
                    <h2> {{ trans['profile.photo'] }}</h2>

                    {% if session.user_photo|default('') %}
                    <img class="profile-photo__preview" src="{{ base }}/profile/photo"
                        alt="{{ trans['profile.photo_alt'] }}" width="120" height="120">
                    {% else %}
                    <p>{{ trans['profile.no_photo'] }}</p>
                    {% endif %}

                    <form action="{{ base }}/profile/photo" method="post" enctype="multipart/form-data"
                        class="profile-photo__form">

                        <input type="hidden" name="csrf_token" value="{{ session.csrf_token }}">

                        <label for="photo">{{ trans['profile.choose_photo'] }}</label>

                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png"
                            aria-describedby="photo-help" required>

                        <p id="photo-help">
                            {{ trans['profile.photo_help'] }}
                        </p>

                        <button class="button button--primary" type="submit">
                            {{ trans['profile.save_photo'] }}
                        </button>
                    </form>
                    {% if session.user_photo|default('') %}
                    <form action="{{ base }}/profile/photo/delete" method="post" class="profile-photo__delete">

                        <input type="hidden" name="csrf_token" value="{{ session.csrf_token }}">

                        <button class="button button--secondary" type="submit">
                            {{ trans['profile.delete_photo'] }}
                        </button>
                    </form>
                    {% endif %}
                </section>
            </main>
        </div>
    </div>
</body>

</html>
