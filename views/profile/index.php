<!DOCTYPE html>
<html lang="fr">

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
            <main class="home">
                <header>
                    <h1 class="home__title">Mon profil</h1>
                    <p>Personnalisez votre photo de profil.</p>
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
                    <h2>Photo de profil</h2>

                    {% if session.user_photo|default('') %}
                    <img class="profile-photo__preview" src="{{ base }}/profile/photo" alt="Votre photo de profil"
                        width="120" height="120">
                    {% else %}
                    <p>Vous n’avez pas encore ajouté de photo.</p>
                    {% endif %}

                    <form action="{{ base }}/profile/photo" method="post" enctype="multipart/form-data"
                        class="profile-photo__form">

                        <input type="hidden" name="csrf_token" value="{{ session.csrf_token }}">

                        <label for="photo">Choisir une photo</label>

                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png"
                            aria-describedby="photo-help" required>

                        <p id="photo-help">
                            JPEG ou PNG, 2 Mo maximum.
                            Dimensions maximales : 3 000 × 3 000 pixels.
                        </p>

                        <button class="button button--primary" type="submit">
                            Enregistrer la photo
                        </button>
                    </form>
                </section>
            </main>
        </div>
    </div>
</body>

</html>