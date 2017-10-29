
8.10.2017
* softaa muutettu ja asennettu "tuotantoon" uusilla antureilla
* sensorin paikka jää muistiin, ja sen voi vaihtaa conf- tiedostossa
* Sensorien lisääminen on mahdollista ilman, että vanhojen sensorien järjestys muuttuu
* ONGELMA: uusi sensor aiheuttaa virheen getTemp() - funktiossa. 
** QUICKFIX: käynnistä softa useaan kertaan. kun lopulta kaikki sensorit on tunnistettu ( 1/per käynnistys), softa toimii.

21.10.2017
* softa tekee virheen ja palvelu lakkaa välillä. Ennen muutosta tätä ei tapahtunut.
* softan läpikäyntiä
