# API documentation

## URL

L'API se trouve sur l'URL suivante :

- [http://demo.comte.re](http://demo.comte.re)


## Routes

### Créer un utilisateur

`POST` /api/auth/register

La méthode doit recevoir un objet json de la sorte :
```json
{"user" : { "username":"XXX", "firsntame":"XXX", "lastname":"XXX", "email":"XXX@XX.XX", "password":"XXX"  }}
```
Retourne l'utilisateur ajouté

### Authentifier un utilisateur

`POST` /api/auth/login

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

`POST` /api/auth/logout

La méthode doit recevoir un objet json de la sorte :
```json
{"email":"XXX@XX.XX" }
```
