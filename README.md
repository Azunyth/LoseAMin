# API documentation

## URL

L'API se trouve sur l'URL suivante :

- [http://demo.comte.re](http://demo.comte.re)

## Specification des requêtes

Les requêtes authentifiées doivent être composées des **headers** suivants :
```
Accept: application/json
Authorization: Bearer 'Votre access_token'
```

Une requête authentifiée avec les mauvais identifiants retourne l'erreur suivante :
```json
{"error": "Unauthenticated"}
```

Les autres retour d'erreur sont sous la forme suivante :
```json
{
    "status" : XXX,
    "error_code" : "XXX",
    "error" : "XXX",
    "message" : "XXX"
}
```
| Paramètre   | Description   |
| ------ | ------- |
| error_code    | Chaine de caractère qui représente un code erreur  |
| error    | Chaine de caractère qui représente l'erreur  |
| message    | Message de l'exception  |

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

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------- |
| register_data_empty    | Données manquantes  | 400 |
| register_data_fails    | Données non conformes aux attentes  | 400 |
| register_insert_fail    | Erreur SQL  | 400 |


### Authentifier un utilisateur

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Non    | `POST`  | **/api/auth/login** |

La méthode doit recevoir un objet json de la sorte :
```json
{"email":"XXX@XX.XX", "password":"XXX", "secret":"XXX" }
```
Le paramètre `secret` doit être un md5 du mot de passe en base 64.

Par exemple : Le mot de passe `test123` après un hash md5 encoder en base64 donne le résultat : `Y2MwM2U3NDdhNmFmYmJjYmY4YmU3NjY4YWNmZWJlZTU=`

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

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------- |
| login_data_fails    | Données non conformes aux attentes  | 400 |
| bad_credentials    | Mauvais identifiants  | 401 |
| login_no_result    | Pas d'utilisateur trouvé  | 404 |

### Deconnecter l'utilisateur

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Non    | `POST`  | **/api/auth/logout** |

La méthode doit recevoir un objet json de la sorte :
```json
{"email":"XXX@XX.XX" }
```

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------ |
| logout_data_fails    | Données non conformes aux attentes  | 400 |
| logout_no_result    | Pas d'utilisateur trouvé  | 404 |


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

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------ |
| user_not_found  | Pas d'utilisateur trouvé  | 404 |

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


Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------- |
| user_connected_fail    | Erreur SQL  | 400 |

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
Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------ |
| refill_too_soon    | Appel à la méthode trop tot  | 400 |
| user_not_found    | Utilisateur non trouvé  | 404 |
| refill_fail    | Erreur SQL  | 400 |

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

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------- |
| user_not_found    | Utilisateur non trouvé  | 404 |
| user_update_fail    | Erreur SQL  | 400 |


### Mettre à jour le stack de l'utilisateur

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `GET`   | **/api/user/{email}/stack/{amount}** |

Le paramètre `{email}` représente l'email de l'utilisateur connecté
Le paramètre `{amount}` représente le montant à **ajouter** au stack

Retourne un objet json contenant les détails de l'utilisateur

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ----- |
| stack_not_number    | Le montant n'est pas un nombre  | 400 |
| user_not_found    | Utilisateur non trouvé  | 404 |
| user_update_fail    | Erreur SQL  | 400 |

### Supprimer un utilisateur

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `DELETE`| **/api/user/{email}** |

Le paramètre `{email}` représente l'email de l'utilisateur connecté

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------ |
| user_not_found    | Utilisateur non trouvé  | 404 |
| delete_fail    | Erreur SQL  | 400 |

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

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------ |
| tables_open_fail    | Erreur SQL  | 400 |

### S'assoir à une table

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `GET`   | **/api/user/{email}/table/{id}/sit** |

Le paramètre `{email}` représente l'email de l'utilisateur connecté
Le paramètre `{id}` représente l'id de la table


Retourne un objet json sous la forme suivante :

```json
{
  "status": 200,
  "message": "Place à la table autorisée",
  "table": {
          ...
      },
  "user": {
          ...
      }  
}
```

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------- |
| table_closed    | Table fermée  | 400 |
| table_full    | Table pleine  | 400 |
| user_already_on_table    | Utilisateur déjà présent à la table  | 400 |
| table_not_found    | Table non trouvée  | 404 |
| user_not_found   | Utilisateur non trouvé  | 404 |

### Quitter une table

| Auth   | Verbe   | URL   |
| ------ | ------- | ----- |
| Oui    | `GET`   | **/api/user/{email}/table/{id}/leave** |

Le paramètre `{email}` représente l'email de l'utilisateur connecté
Le paramètre `{id}` représente l'id de la table


Retourne un objet json sous la forme suivante :

```json
{
  "status": 200,
  "message": "Place à la table restaurée",
  "table": {
          ...
      },
  "user": {
          ...
      }  
}
```

Erreurs :

| error_code   | Description   | Status |
| ------ | ------- | ------- |
| user_not_on_table    | Utilisateur non présent à cette table  | 400 |
| table_not_found    | Table non trouvée  | 404 |
| user_not_found   | Utilisateur non trouvé | 404 |
