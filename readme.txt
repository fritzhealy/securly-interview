Hello, and thank you for the opportunity to reach thus far in the interview process. 

I'll just jump right in.
The build uses MVC paradigms and OOP to complete it's tasks.
The frontend was designed as a SPA with Angular JS. 
The backend is a simple api that fufills all the complex tasks.
The index.php file initializes all the necessary classes and renders the view.
The View class takes care of parsing out api and importer requests from frontend requests.
The default route is to angular which is initialized by the home.php file.
The convention "home" was used to refer to most angular files.
The Controller class is user facing portion of the api. It handles incoming request paths,
manages json encoding, and restricts unauthorized access.
Sessions are managed through the default php sessions, although JWT would be my choice in a more detailed build.
The Db class is used as the storage and processing portion of the build.
It handles all the database calls initialization and processing.
The css, templates and js folders hold the assets used by angular.

From a broader perspective I used mysql and apache on docker.
The mysql database is split into 8 tables.
There are 3 main tables that store information regarding the schools, clubs, and kids.
Those are respectively school, club, and kid. 
These tables are linked to show relation by the clubKidLink, schoolClubLink, and schoolKidLink tables.
The queries table holds previous queries so they can be rerun. 
SQL is not stored in this table, just parameters so angular can remake the api calls.
Finally the connectedClubs table holds all the clubs that are connected. 
This was essential for fast processing for step 3.

Feel free to test everything out. 
If I was alloted more time I would improve the project by adding status codes to the api calls.
Currently the api passes a status variable in the json object back and forth on routes that can fail based on user input.
I would also style the site more with more time.

Feel free to contact me with any other questions.
Best,
Frederick (Fritz) Healy