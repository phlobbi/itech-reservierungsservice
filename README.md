# Reservierungsservice

Ein mit Symfony implementiertes Backend für den Reservierungsservice.

Dieses Projekt wurde im Rahmen des Moduls "Internet-Technologien" an der htw saar erstellt.

## Dienste

### Reservierung
Um einen Tisch zu reservieren, muss der Benutzer zuerst die verfügbaren Zeiten für ein bestimmtes Datum abrufen.
Dazu wird die Route `/api/availabletimes` verwendet. Hierbei sendet der Benutzer das Datum, die Anzahl der Gäste und ob der Tisch drinnen oder draußen sein soll.
Der Service antwortet mit den verfügbaren Zeiten.

Der Benutzer kann dann mit den erhaltenen Zeiten eine Reservierung erstellen. Dazu wird die Route `/api/reservation` verwendet. Hierbei sendet der Benutzer das Datum, die Zeit, die Anzahl der Gäste, ob der Tisch drinnen oder draußen sein soll, spezielle Wünsche, seinen Namen und seine E-Mail-Adresse.
Der Service antwortet mit einer Bestätigung und sendet eine E-Mail an die vom Nutzer angegebene E-Mail-Adresse.

Der Service ordnet dabei automatisch einen Tisch zu, der den Anforderungen des Benutzers entspricht.

Über das Admin-Interface können alle Reservierungen für einen bestimmten Tag abgerufen werden.

### Bewertungen
Benutzer können Bewertungen abgeben. Dazu wird die Route `/api/ratings` verwendet. Hierbei sendet der Benutzer den Bewertungscode, den Text und die Anzahl der Sterne.
Der Bewertungscode dient als Nachweis, dass der Benutzer tatsächlich in dem Restaurant war.

Über das Admin-Interface können alle Bewertungen abgerufen werden. Hierbei können Bewertungen auch beantwortet werden.
Um aber das System vor Verfälschungen zu schützen, können Bewertungen nicht gelöscht werden.

Außerdem werden alle Bewertungen öffentlich angezeigt.

### Admin-Interface
Über das Admin-Interface können alle Reservierungen für einen bestimmten Tag abgerufen werden, sowie Bewertungen beantwortet werden.

Um auf das Admin-Interface zuzugreifen, muss sich der Benutzer einloggen. Dazu wird die Route `/api/login` verwendet. Hierbei sendet der Benutzer seinen Benutzernamen und sein Passwort.
Der Service antwortet mit einem Sessiontoken, der für alle weiteren Anfragen im X-Authorization-Header verwendet werden muss.

Die Session läuft nach Login nach 10 Minuten ab. Sie wird mit jeder Anfrage um 10 Minuten verlängert.
Der Benutzer kann sich auch ausloggen. Dazu wird die Route `/api/logout` verwendet.

Versucht ein Benutzer auf das Admin-Interface zuzugreifen, ohne eingeloggt zu sein, erhält er eine Fehlermeldung.

### CRON
Der Service verfügt über zwei CRON-Jobs. Diese können jeweils über die Route `/cron` aufgerufen werden.

Der erste Job löscht alle Reservierungen, die in der Vergangenheit liegen. Der zweite Job generiert neue Reservierungszeiten für bis zu 14 Tage im Voraus.

## API

### GET /api/availabletimes?date=[Datum]&guests=[Anzahl Gäste]&isOutside=[Draußen oder Drinnen (true/false)]
Ruft die verfügbaren Zeiten für ein bestimmtes Datum unter Berücksichtigung von Gästen und drinnen/draußen ab.
Antwort vom Service:
```json
{
  "date": string,
  "times": [string]
}
```

### POST /api/reservation
Erstellt eine Reservierung.
Der Requestbody sollte beinhalten:
```json
{
  "date": string,
  "time": string,
  "guests": int,
  "isOutside": bool,
  "specialWishes": string,
  "name": string,
  "email": string
}
```

### GET /api/ratings
Ruft alle Bewertungen ab.
Antwort vom Service:
```json
[
  {
    "id": int,
    "text": string,
    "stars": int,
    "date": string,
    "response": string
  }
]
```

### POST /api/ratings
Erstellt eine neue Bewertung.
Der Requestbody sollte beinhalten:
```json
{
  "code": string,
  "text": string,
  "stars": int
}
```

### POST /api/login
Loggt einen Benutzer ein.
Der Requestbody sollte beinhalten:
```json
{
  "username": string,
  "password": string
}
```
Antwort vom Service:
```json
{
  "user": string,
  "token": string
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
    "id": int,
    "guests": int,
    "specialWishes": string,
    "name": string,
    "email": string,
    "date": string,
    "time": string,
    "table": string
  }
]
```

### PATCH /api/admin/ratings/{id}
Fügt einer Bewertung eine Antwort hinzu.
Erfordert ein Sessiontoken im X-Authorization-Header.
Der Requestbody sollte beinhalten:
```json
{
  "response": string
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
Antwort vom Service:
```json
[string]
```