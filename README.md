# Route finder using Open Street Maps & Open Route Service  
  ![Demo](https://raw.githubusercontent.com/rsgcata/osmFindRoute/master/demoImages/osm-app.gif)
 A simple web app to showcase how Google Maps route finder feature can be implemented using open source tools and services (Open Street Maps & Open Route Service). You can set waypoints for multiple routes on a map by just clicking on it. After you've set all the waypoints, you can get all the details about the your routes, paths, segments you want to go through (ie. go to straight ahead on street x for 15 meters, turn left onto boulevard St. James, etc...). You will get the shortest path and the time it takes to get there.
 Usage:
 1) Install [PHP](https://www.php.net/) and [composer](https://getcomposer.org) (PHP 7.1 was used for development but any PHP 7.x should work)
 2) Clone repository and change directory to cloned project folder
 3) Run composer install
 4) Setup [Open Route Service](https://openrouteservice.org/) API key in proxy.php
 5) Run php -S localhost:8080 proxy.php (this will start PHP's built in server)
 6) Open localhost:8080 in any browser and play with the app

