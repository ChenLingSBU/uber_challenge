# Uber Project Challenge
This is Uber project challenge for Next Departure Time. It's already hosted in Amazon AWS with url: http://ec2-54-69-234-115.us-west-2.compute.amazonaws.com/index.html

Note: For simplicity, this application can only query next bus departure info for sf-muni in San Francisco. This applicaton can work on 
desktops, mobiles, and tablets. If the device doesn't support geolocation or the user doesn't allow geolocation or the user's current 
place is not in San Francisco, the application will default set the center of map to Uber Headquarter in San Francisco.

## For Reviewers
I chose next departure time as my challenge project, which requires :
> Create a service that gives real-time departure time for public transportation (use freely available public API). The app should geolocalize the user.

What I did is exactly what it requires. The App will get the user's current geolocation, find and display bus stops covered by a circle centered at the user's location with radius 500 meters. The user can click the bus stops to get the next departure infomation for the clicked bus stop.

## Technical Stack
### Backend
The Backend was written in PHP. I wrote 6 php files in total.<br />

1. Object representation class:<br />
   1.) stop_class.php : represent bus stop<br />
   2.) departure_class.php : represent departure info<br />

2. data supplier and data query class:<br />
  1.) next_bus_class.php : it's an encapsulation of NEXT BUS RESTful service, only provides functionalities that NEXT BUS RESTful API offers<br />
  2.) data_supplier_class.php : it supplies necessary data used in data query, such as geohash and all the bus stops<br />
  3.) data_querier_class.php: it contains all the logic to get data we want(nearby bus stops and departure infos)<br />

3. ajax call handler:ajax_call_handler.php: it's a script file that recieve ajax call from front end and call method defined in data_querier_class then return to front end. It's just like a bridge between front end and back end.

The reason I designed like this is because I want to keep definition clear for each class and for each function. One function just does one simple thing, one class can represnt the same busines logic. In this way we can keep the code clean and decoupled. If something changed for NEXT BUS RESTful API, I just have to change the associate codes in next_bus_class.php, if there any other logic added into the data query, I just need to add associate codes to the logic and that's it, we don't have to bother the code from other classes.

PHP lib files I used:<br />
1.) spatial_distance_class: borrowed from Derick Rethans's example code, to calculate the distance between two geolocations<br />
2.) geohash_class: borrowed from Bruce Chen, geohash used for fast approximatly query nearby geolocation<br />
Thanks to them that wrote good stuff that I don't have to make my own wheels. <br />

### Frontend<br />
I used Backbone.js for Frontend, and used require.js to orgnize all the js files as AMD modules.
Thanks to Google who created googlemaps -- such a beatiful and flexible javascript api that I can use them very smoothly, easily and fast.

For Frontend I only have two files:<br />
1.) NextBusView class : contain all the business logics and render function<br />
2.) NextBusModel Class : contains all the ajax calls.<br />

## Challenges
This part is my favorite. It's a good way for me to exhibit how I met problems and how I approched to figure them out. At the very first glance of the challenge, I thought it was just one day project that just need call NEXT BUS RESTful API then return the results to front end. However When I started working on this, I found it's not that easy, the reason is NEXT BUS RESTful API doesn't provide functionality that with a given location and given radius it returns all the nearby stops covered by the circle whose center is the given location.
Then I faced two challenges:<br /> 
1.) How to get all the nearby stops within givin circle in other ways?<br />
Well, it did take me some time to figure out, but my intuition is to use geohash(I just heard of this terminology but never used this before),then I spent some time researching on this, tried to understand the algorithem and finally applied this to my problem. <br />
The algorithm is: <br />
Use geohash algorithm to build a hashmap, the key of the hashmap is geohash value(the length of geohash I chose is 6), the value of the hashmap is a list of bus stops which are inside the area represnted by the geohash value. When we want to find nearby bus stop with radius 500 meters, we firstly encode the given location into geohash value, then we find the nearby neighborhood geohash values(8 neighborhood areas plus the area containing the given location). After we got 9 geohash values, we can look up the geohash map which built up previsouly to get all the bus stops inside the 9 geohash area. Then we do a iteration to calculate the distance between each bus stop and the given location to find out bus stops less than 500 meters far by using spatial distance algorithm. <br />
We can cache the hashmap that we don't have to calculate it every time.

2.) When I thought about the geohash algorightm, here came the second challenge, I need hash all the bus stops, but how can I get all the stops, since the RESTful API doesn't provide neither. <br />
Since NEXT BUS RESTful API provides functionality to get all the stops for one route, and get all the routes for one agency. I thought about two ways: <br />
a. Looply excute exsiting RESTful API to build a function which can get all the stops for all the routes, then cache them.<br />
b. using php curl concurrent way to send out many RESTful request at the same time to reduce the waiting time, then cache them.<br />
Because of time limit, I only implemented the first way, I really wonder how faster the second method would be.<br />
3.) When talk about cache, here's another problem, PHP is not like Node.js or Java, it's just excute script, php server won't have any status,  that is to say we cannot cache the bus stops and geohash in memory. Usually, If we want to improve the website performance,we always use memcache or Redis, just to retrieve data in Redis or Memcache anytime when we want data.  Since it's just a small assignment, I just stored data in stream data and write to disk.<br />


  
  
  
