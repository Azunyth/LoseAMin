# API documentation

## URL

L'API se trouve sur l'URL suivante :

- [http://demo.comte.re](http://demo.comte.re)

## Specification des requêtes

Les requêtes authentifiées doivent être composées des **headers** suivants :
```
Accept: application/json
Authorization: Bearer `Votre access_token`
```

## Routes

### Créer un utilisateur

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Non    | `POST`  | **/api/auth/register** |

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

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Non    | `POST`  | **/api/auth/login** |

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

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Non    | `POST`  | **/api/auth/logout** |

La méthode doit recevoir un objet json de la sorte :
```json
{"email":"XXX@XX.XX" }
```

### Obtenir les details de l'utilisateur

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `GET`   | **/api/user/{email}** |

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
    "lastname": "XXXX",
    "stack": XXX,
    "is_connected": 1,
    "last_refill": "YYYY-MM-DD HH:mm:ss"
  }
}
```

### Obtenir la liste des joueurs connectés

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `GET`   | **/api/user/connected** |

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

### Obtenir une recharge de jetons

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `GET`   | **/api/user/{email}/refill** |

La recharge ne peut se faire que toutes les heures

Si l'utilisateur est autorisé à la faire, les details de l'utilisateur sont renvoyées par l'API

Si l'utilisateur n'est pas autorisé, une réponse au format suivant est retournée

```json
{
  "status": 400,
  "error_code": "refill_too_soon",
  "message": "Vous devez attendre X minutes",
  "error": {
    "minutes": X
  }
}
```

### Mettre à jour les données utilisateur

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `PUT`   | **/api/user/{email}** |

Le paramètre `{email}` représente l'email de l'utilisateur connecté

La méthode doit recevoir un objet json de la sorte :
```json
{"firstname":"XXX", "lastname":"XXX", "username":"XXX" }
```
**Tous les paramètres ne sont pas obligatoires dans l'objet json envoyé**

### Supprimer un utilisateur

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `DELETE`| **/api/user/{email}** |

Le paramètre `{email}` représente l'email de l'utilisateur connecté

### Obtenir la liste des tables ouvertes

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `GET`   | **/api/table/opened** |

Retourne la liste des tables ouvertes sous la forme suivante :

```json
{
  "status": 200,
  "message": "Liste des tables ouvertes",
  "tables": [
      {
          ...
      },
      {
          ...
      }
  ]
}
```
