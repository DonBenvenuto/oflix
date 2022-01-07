### Nos routes

| Endpoint           | Méthode HTTP | Description                                                                                   | Retour                          |
| ------------------ | ------------ | --------------------------------------------------------------------------------------------- | ------------------------------- |
| `/api/movies`      | `GET`        | Récupération de tous les films                                                                | 200                             |
| `/api/movies/{id}` | `GET`        | Récupération du film dont l'id est fourni                                                     | 200 ou 404                      |
| `/api/movies`      | `POST`       | Ajout d'un film _+ la donnée JSON qui représente le nouveau film_                             | 201 + Location: /movies/{newID} |
| `/api/movies/{id}` | `PUT`        | Modification d'un film dont l'id est fourni _+ la donnée JSON qui représente le film modifié_ | 200, 204 ou 404                 |
| `/api/movies/{id}` | `DELETE`     | Suppression d'un film dont l'id est fourni                                                    | 200 ou 404                      |
