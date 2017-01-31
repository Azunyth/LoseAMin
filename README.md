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
