# Uber Project Challenge
This is Uber project challenge for Next Departure Time. It's already hosted in Amazon AWS with url: http://ec2-54-69-234-115.us-west-2.compute.amazonaws.com/index.html

Note: For simplicity, this application can only query next bus departure info for sf-muni in San Francisco. This applicaton can work on 
desktops, mobiles, and tablets. If the device doesn't support geolocation or the user doesn't allow geolocation or the user's current 
place is not in San Francisco, the application will default set the center of map to Uber Headquarter in San Francisco.

## For Reviewers
I chose next departure time as my challenge project, which requires :
> Create a service that gives real-time departure time for public transportation (use freely available public API). The app should geolocalize the user.

What I did is exactly what it requires. The App will get the user's current geolocation, find and display bus stops covered by a circle centered at the user's location with radius 500 meters.

## Technical Stack
### Backend
The Backend was written in PHP. I wrote 6 php files in total.<br />
1. Object representation class:<br />
   1.) stop_class.php : represent bus stop<br />
   2.) departure_class.php : represent departure info<br />

2. data fetch and data query class:<br />
  1.) next_bus_class.php : it's an encapsulation of NEXT BUS RESTful service, only provides functionalities that NEXT BUS RESTful API offers<br />
  2.) data_supplier_class.php : it supplies necessary data used in data query, such as geohash and all the bus stops<br />
  3.) data_querier_class.php: it contains all the logic to get data we want(nearby bus stops and departure infos)<br />

3. ajax call handler:ajax_call_handler.php: it's a script file that recieve ajax call from front end and call method defined in data_querier_class then return 
                     to front end. It's just like a bridge between front end and back edn.

The reason I designed like this is because I want to keep clear definition for each class and for each function. One function just 
does one simple thing, one class contains the similar functions. In this way we can keep the code clean and decoupled. If something 
changed for NEXT BUS RESTful API, I just have to change the associate codes in next_bus_class.php, if there any other logic added into 
the data query, I just need to add associate codes to the logic and that's it, we don't have to bother the code from other classes.

PHP Lib files I used:<br />
1.) spatial_distance_class: borrowed from Derick Rethans's example code, to calculate the distance between two geolocations<br />
2.) geohash_class: borrowed from Bruce Chen, geohash used for fast approximatly query nearby geolocation<br />
Thanks to them that wrote good stuff that I don't have to make my own wheels. <br />

### Frontend<br />
I used Backbone.js for Frontend, and used require.js to orgnize all the js files as AMD modules.
Thanks to Google who created such a beatiful and fexlibility javascript api that I can use them very smoothly, easily and fast.

For Frontend I only have two files:<br />
1.) NextBusView class : contain all the business logics<br />
2.) NextBusModel Class : contains all the ajax call and some values.<br />

## Challenges and future work<br />
### Challenges:<br />
This part is my favorate. It's a good way for me to exhibit how I met problems and how I approched to figure them out. At the very 
first glance of the challenge, I thought it was just one day project that just need call NEXT BUS RESTful API then return the results 
to front end. However When I started working on this, I found it's not that easy, the reason is NEXT BUS RESTful API doen't provide functionality 
that with a given location and given radius it returns all the nearby stops covered by the cicle whose center is the given location.
Then I faced two challenges:<br /> 
1.) How to get all the nearby stops within givin circle in other ways?<br />
  Well, it did take me some time to figure out, but my intuition is to use geohash(I just heard of this terminology but never used this before),then 
  I spent some time researching on this, try to understand the algorithem and finally apply this to my problem.
2.) When I thought about the geohash algorightm, here came the second challenge, if I need hash all the bus stops, how can I get all the stops, 
   since the RESTful API doesn't provide neither. <br />
  I thought about two ways: a. Looply excute exsiting RESTful API to build a function which can get all the stops then cache them.<br />
                            b. using php curl concurrent way to send out all many RESTful request at the same time to reduce the waiting time.Then cached them.<br />
  Because of time limit, I only implemented the first way, I really wonder how faster the second method would be.<br />
3.) When talk about cache, here's another problem, PHP is not like Node.js or Java, it's just excute script, php server won't have any 
   status that is to say we cannot cache the bus stops and geohash in memory. Usually, If we want to improve the website performance,we always use memcache or 
   Redis, anytime when we want the data, just to retrieve them in Redis or Memcache. Since it's just a small assignment, I just 
   stored data in stream data and write to disck.<br />

future work<br />
  1. we can changed codes to use curl make concurrent Restful call to improve the speed.<br />
  2. we can creat sockests connection between backend and frontend in which way backend can auto push update to frontend and we don't have 
    to muanlly click the stop to departure info.
                            


  
  
  
