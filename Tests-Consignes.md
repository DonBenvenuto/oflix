# Tests sur O'flix

## Front

- Ajouter une critique en POST
    - Avec erreurs du form (Validation)
    - Sans erreurs (redirection vers le film)
- Ma liste
    - Ajout
    - Suppression
    - Vider

## Back

- Anonyme, routes en POST
- idem pour
    - ROLE_USER
    - ROLE_MANAGER => GET ok, POST pas ok
    - ROLE_ADMIN => GET + POST et analyse du retour selon l'action (DELETE => 302)

## API (Bonus)

- Test JWT ?? voir la doc de Lexik