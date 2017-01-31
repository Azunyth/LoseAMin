# API documentation

## URL

L'API se trouve sur l'URL suivante :

- [http://demo.comte.re](http://demo.comte.re)


## Routes

### Créer un utilisateur

`POST` **/api/auth/register**

La méthode doit recevoir un objet json de la sorte :
```json
{"user" : {
    "username":"XXX",
    "firsntame":"XXX",
    "lastname":"XXX",
    "email":"XXX@XX.XX",
    "password":"XXX"  
    }
}
```
Retourne l'utilisateur ajouté

### Authentifier un utilisateur

`POST` **/api/auth/login**

La méthode doit recevoir un objet json de la sorte :
```json
{"email":"XXX@XX.XX", "password":"XXX", "secret":"XXX" }
```
Le paramètre `secret` doit être un md5 du mot de passe en basse 64

Retourne la réponse suivante si valide :
```json
    {
       "status":"XXX",
       "tokens": {
         "token_type" : "XXXX",
         "expires_in" : "XXXX",
         "access_token" : "XXXX",
         "refresh_token" : "XXXX"
        },
        "user" : {
            ...
        }
     }
```
La propriété `user` représente l'utilisateur connecté.
La propriété `tokens` représente les jetons de l'utilisateur.
**La sous propriété `access_token` est très importante, elle représente le jeton d'authentification à utilier pour les requêtes authentifiées.
Cette propriété est donc à conserver.**

### Deconnecter l'utilisateur

`POST` **/api/auth/logout**

La méthode doit recevoir un objet json de la sorte :
```json
{"email":"XXX@XX.XX" }
```

### Obtenir les details de l'utilisateur

`GET` **/api/user/{email}**

Le paramètre `{email}` représente l'email de l'utilisateur connecté

Retourne les details de l'utilisateur connecté sous la forme suivante

```json
{
  "status": 200,
  "message": "D&eacute;tail utilisateur",
  "user": {
    "id": X,
    "username": "XXXX",
    "email": "XXXX@XXX.XXX",
    "created_at": "YYYY-MM-DD HH:mm:ss",
    "updated_at": "YYYY-MM-DD HH:mm:ss",
    "firstname": "XXXX",
    "lastname": "VERNOT",
    "stack": 200,
    "is_connected": 1,
    "last_refill": "YYYY-MM-DD HH:mm:ss"
  }
}
```

### Obtenir la liste des joueurs connectés

`GET` **/api/user/connected**

Retourne la liste des utilisateurs sous la forme suivante :

```json
{
  "status": 200,
  "message": "Liste des utilisateurs connect&eacute;s",
  "users": [
    {
      ...
    },
    {
      ...
    }
  ]
}
```
