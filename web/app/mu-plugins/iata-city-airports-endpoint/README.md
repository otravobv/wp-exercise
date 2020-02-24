##### Pupulate DB:
- method: PUT
- url: http://otravo.exercise:3333/wp-json/airports/add
- headers: application/json
- body: raw | JSON

##### Get city airports by IATA city code:

- method: GET
- url: http://otravo.exercise:3333/wp-json/airports/IATA_CITY_CODE
- replace IATA_CITY_CODE
- IATA_CITY_CODE can consist of 3 [A-z] symbols

##### Notes:
- Didn't use namespace since WP REST API docs recommend not to do so
- Didn't change WP REST API prefix from 'wp-json' to 'api' although it is possible to do so if needed
- Didn't use prefixes in class/method names for the sake of readability, since it's localized project ant no conflicts should arise with such a basic install
- Currently not checking/handling error for duplicate entries when using PUT