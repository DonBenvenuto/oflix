
### Nicolas

```yml
access_control:
        - { path: '^/back', roles: [ROLE_ADMIN, ROLE_MANAGER] }
        - { path: '^/review', roles: [ROLE_USER, ROLE_ADMIN, ROLE_MANAGER]}
```

### William

```yml
access_control:
        - { path: ^/back, roles: [ROLE_ADMIN, ROLE_MANAGER] }
        - { path: ^/new, roles: ROLE_ADMIN }
        - { path: ^/edit, roles: ROLE_ADMIN }
        - { path: ^/delete, roles: ROLE_ADMIN }            
        - { path: ^/front, roles: ROLE_USER }
        - { path: ^/front, roles: PUBLIC_ACCESS }
```

### Jérémy

```yml
    access_control:
        - { path: ^/back, roles: ROLE_MANAGER }
        # - { path: ^/profile, roles: ROLE_USER }

    role_hierarchy:
        ROLE_MANAGER: ROLE_USER
        ROLE_ADMIN: ROLE_MANAGER
```

### Jérémy

```yml
access_control:
    # page édition création
    - { path: ^/back/(.*)/(new|edit), roles: ROLE_ADMIN}
    # suppression que pour les admins
    - { path: ^/back/(.*)/\d+, roles: ROLE_ADMIN, methods: [POST]} 
    # back office pour les managers, uniquement en consultation
    - { path: ^/back/, roles: ROLE_MANAGER }
    # les utilisateurs doivent avoir accès au avis
    - { path: /review/, roles: ROLE_USER }
    
    role_hierarchy:
        ROLE_MANAGER: ROLE_USER
        ROLE_ADMIN: [ROLE_MANAGER, ROLE_USER]

```