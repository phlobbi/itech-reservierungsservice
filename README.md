# Reservierungsservice

Ein mit Symfony implementiertes Backend für den Reservierungsservice.

## API

### GET /api/availabletimes?date=[Datum]&guests=[Anzahl Gäste]&isOutside=[Draußen oder Drinnen (true/false)]
Ruft die verfügbaren Zeiten für ein bestimmtes Datum unter Berücksichtigung von Gästen und drinnen/draußen ab.
Antwort vom Service:
```json
{
  'date': string,
  'times: [string]
}
```

### POST /api/reservation
Erstellt eine Reservierung.
Der Requestbody sollte beinhalten:
```json
{
  'date': string,
  'time': string,
  'guests': int,
  'isOutside': bool,
  'specialWishes': string,
  'name': string,
  'email': string
}
```

### GET /api/ratings
Ruft alle Bewertungen ab.
Antwort vom Service:
```json
[
  {
    'id': int,
    'text': string,
    'stars': int,
    'date': string,
    'response': string
  }
]
```

### POST /api/ratings
Erstellt eine neue Bewertung.
Der Requestbody sollte beinhalten:
```json
{
  'code': string,
  'text': string,
  'stars': int
}
```

### POST /api/login
Loggt einen Benutzer ein.
Der Requestbody sollte beinhalten:
```json
{
  'username': string,
  'password': string
}
```
Antwort vom Service:
```json
{
  'user': string,
  'token': string
}
```

### POST /api/logout
Loggt einen Benutzer aus.
Erfordert ein Sessiontoken im X-Authorization-Header.

### GET /api/admin/reservations?date=[Datum]
Ruft alle Reservierungen für einen bestimmten Tag ab.
Erfordert ein Sessiontoken im X-Authorization-Header.
Antwort vom Service:
```json
[
  {
    'id': int,
    'guests': int,
    'specialWishes': string,
    'name': string,
    'email': string,
    'date': string,
    'time': string,
    'table': string
  }
]
```

### PATCH /api/admin/ratings/{id}
Fügt einer Bewertung eine Antwort hinzu.
Erfordert ein Sessiontoken im X-Authorization-Header.
Der Requestbody sollte beinhalten:
```json
{
  'response': string
}
```

### GET /api/admin/ratingcodes
Ruft alle verfügbaren Bewertungscodes ab.
Erfordert ein Sessiontoken im X-Authorization-Header.
Antwort vom Service:
```json
[string]
```

### POST /api/admin/ratingcodes?amount=[Anzahl]
Erstellt die angegebene Anzahl an Ratingcodes.
Erfordert ein Sessiontoken im X-Authorization-Header.
