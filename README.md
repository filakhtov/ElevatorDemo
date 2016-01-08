Summary
========

This is a real-time PHP emulator for elevator.
Implementation consists of two parts: back-end service and front-end web interface.
To start using it, just put contents inside any virtualhost of webserver with PHP support.
WARNING: "data" folder must have a writable permissions for the PHP process.
Web-interface entry point is index.html file.


Front-end
==========

Frontend part is developed using AngularJS, Twitter Bootstrap and HTML5 Polyglot markup.
Frontend is responsive, that is, it's adapted to the screen size of the device.
For the demonstrational purposes, there are only two layouts: smartphone and pc.
There are 15 floors prefilled (configurable from backend side).
Frontend show all elevator movements/actions.

You can choose source and target floors and press "Send request" button to start emulation.
You can send multiple signals to the elevator:

  1. Open door - opens elevator door if it is in the "standing" state, or delays closing door for 3 seconds if it is already opened;
  2. Close door - closes the door if it is open, otherwise does nothing.
  3. Alarm - set elevator in alarm state. After pressing this button elevator stops receiving any signal or commands.
  4. Reset - resets elevator state (including alarm) and removes log file.

Blue bar indicating current elevator status.

History is recorded on every command/signal and is displayed at the bottom of the screen, below the buttons and controls. History is not permanent and is lost after browser window refresh. State is maintained even after the page refresh.

AngularJS is used on frontend for communicating with backend api.

Back-end
=========

Backend is implemented in pure PHP with various design patterns applied whenever possible (for demo purposes).
API has a single endpoint /api.php which handles all required requests.
In real-world situation rewrite rule should be implemented to remove api.php from urls.
API is JSON based. Requests and response is transferred using JSON format.

Known commands:

  GET /api.php/elevator - returns current elevator status, it's location and so on;
  POST /api.php/elevator {"from":sourceFloorNumber,"to":targetFloorNumber}
    Makes a new request for elevator moving from "sourceFloorNumber" to "targetFloorNumber";
  GET /api.php/floors - returns floors configuration from backend (how many floors, which ones are under maintenance);
  POST /api.php/signal - {"signal":signalCode}
    Send signal "signalCode" to elevator;
  POST /api.php/reset - reset simulation status. Remove log file and state file.

Response is sent in JSON format. HTTP status code indicates status. In addition to that, JSON is transported as a result, indicating "sucess" or "error" reason.

State is stored in "data/state" file as a serialized "Elevator" class state.
In real conditions DB should be used, but for the demo purposes I've used just simple flat file.

PHP files are implemented in PSR-2 compatible way.
During implementation I've tried to use multiple design and enterprise programming patterns.
Best practices, like SOLID, Law of Demeter, Dependency Injection are followed in this project.
Autoloader is using PSR-4 file layout. Namespaces and class names corresponds to file paths.

File Info
==========

This section will shortly describe important project files:

  index.html - HTML document, entry-point for web-interface. Bootstrap is included for responsive layout implementation, elements decoration and status icons displaying.
  
  pub/style.css - customized CSS styles, used just to improve look and feel of web-interface

  pub/elevator.js - frontend functionality implementation. Consists of single "lender411" module. Module includes:
      elevatorService - AngularJS factory used for communicating with backend, parsing responses, setting callbacks;
      elevatorController - Implementation for all the functionality. Actively uses elevatorService under the hood.

  data/ - folder MUST be writable for PHP, as log and state files will be stored here.

  data/state - Persistant storage for elevator state. Flat file with serialized data inside.

  data/api.log - Logging every API request and response
  
  autoloader.php - Simple, primitive autoloader implementation. Follows PSR-4 implementation: Vendor\SubNamespace\ClassName.
  
  api.php - API entry point. This is a simplified version of front controller with built-in routing system. This is very primitive implementation, but sufficient for pattern revealing. Front controller is also responsible for handling different kinds of errors that can appear during communication and also logging. Front controller is delegating actual work to various MVC-style controllers. As this is a test task only, project does not include complete MVC or MVP model, but could be easily extended.
  
  Lender411/ - Folder for vendor based files according to PSR-4

  Lender411/Controller/ - MVC-based controllers folder and namespace

  Lender411/Exception/ - Custom exceptions implementations are stored here

  Lender411/Exception/BadRequestException.php - This exception just have a default error code 400, which is used in front controller to set status to "Bad request" if no any other status code were provided.

  Lender411/Controller/*Controller.php - MVC-style controllers. Every controller is responsible for single set of related operations. Every operation is implemented in terms of public method with "*Action" name. Front controller decides to which controller/action pair to route the request.
  
  Lender411/Elevator.php - Main class, which is entry point for all requests. Aggregation in conjunction with delegation is used to route elevator requests, manage floors and handle signals. STATE_DURATION class constant that reflects how long every action takes to complete, for example: how long elevator moves from one floor to the next one, or how long door stays open. More constants are provided - reflecting possible states. Elevator has a queue of requests inside and business logic to process it internaly.
  
  Lender411/SignalProcessor.php, Lender411/AlarmSignalProcessor.php, Lender411/DoorCloseSignalProcessor.php, Lender411/DoorOpenSignalProcessor.php - family of signal processor classes. Chain of responsibility pattern is used in these classes. SignalProcessor class is abstract base class with delegation logic implementation. Concrete implementations are responsible for their own "known" signals. Unknown signal reaches end of chain and triggers na exception, which is later catched and reported by front controller. Used by elevator to process signals.
  
  Lender411/ElevatorInfo.php - Helper class, describes in human-friendly way what happens to elevator right now.
  
  Lender411/ElevatorRepository.php - This is a factory class, and also DDD-style repository. It is responsible for checking for previous saved state and restoring it or creating a new "clean" state. Floor configuration is hardcoded in this file and can be easily changed. Changes to this configuration will automatically propagete to the front end after reset. In real-world project this configuration will be isolated and separated, possibly in configuration file or database with specific configuration helper class implementation.
  
  Lender411/ElevatorRequest.php - This is a simple value object which is used to transport information about elevator request, basically target and source floors. This class is responsible also for input data validation and does not accept anything except integer values.
  
  Lender411/FloorService.php - This class is responsible for storing and checking floor configuration. As requested, floors have two possible states: working and maintenance. This class also does input validation. In addition to that, class checks if boundaries were crossed and throw an exception.
  
  Lender411/Logger.php - Simple logging class implementation. Has some log-file status validation routine in the constructor. In real-size project more complex logging solution will be used ofcourse, but for demo purpose this one should suffice.
  
  Lender411/QueueService.php - Most interesting part of the system, which actually does queue organization and requests routing. It looks for the best place to insert source and target floor requests so elevator will make less unnecessary movements. Due to a bit higher complexity this class has some hints inside comments. Elevator uses this class to manage it's queue.
  
I had a lot of fun writing this code. So I hope you will have a lot of fun playing with it!
