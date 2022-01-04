# Routes de l'application

| URL             | Méthode HTTP | Contrôleur            | Méthode          | Titre HTML                     | Commentaire                                       |
| --------------- | ------------ | --------------------- | ---------------- | ------------------------------ | ------------------------------------------------- |
| `/`             | `GET`        | `MainController`      | `home`           | Bienvenue sur O'flix           | Page d'accueil                                    |
| `/movie/{id}`   | `GET`        | `MainController`      | `movieShow`      | Titre du film/série            | Page détail d'un film/série                       |
| `/list`         | `GET`        | `MainController`      | `list`           | Tous les films et séries       | Liste de tous les films ou résultats de recherche |
| `/favorites`    | `GET`        | `FavoritesController` | `favorites`      | Ma liste                       | Les favoris de l'utilisateur courant              |
| `/api/movies`   | `GET`        | `ApiController`       | `movies_get`     | -                              | Le JSON de la liste des films                     |
| `/api/movies`   | `GET`        | `ApiController`       | `movies_get`     | -                              | Le JSON de la liste des films                     |
| `/theme/toggle` | `GET`        | `MainController`      | `theme_switcher` | - (pas de Twig => redirection) | Page qui permet de changer de thème               |
