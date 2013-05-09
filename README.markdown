# Network Status Page         
## Intro
This repo contains a lightweight network status page written in PHP with a SQLite3 backend. It has support for sending twitter messages when incidents are added and updated using twitteroauth (https://github.com/abraham/twitteroauth). jQuery and jQuery UI are also used extensively throughout.

You can view this code in production at http://status.lstn.net/

## Prerequisites

* SQLite3 PHP extension
* Smarty template engine (http://www.smarty.net/) available in your PHP include path
* Twitter OAuth (https://github.com/abraham/twitteroauth) available in your PHP include path

## Installation
* Clone this repo: ```git clone git://github.com/limestonenetworks/statuspage.git```
* Configure settings in ```includes/config.php```
* Create a virtual host with the document root set to ```statuspage/public```, or simply symlink to the public directory from your docroot
* Make sure your web server can write to the ```statuspage/cache``` directory.
* After these steps are complete, navigate to the root of the status site and it will initialize the database.
* Once the database has been initialized, you may visit ```statuspage/login.php``` and use the username and password admin/admin to log in. Once logged in you should immediately change the admin user's password in the user page, or create a new user and delete the admin username.

* Optionally, logos may be placed in ```public/templates/default/images/```
	* The logo in the header should be named ```logo.gif```. We use a 175x46 transparent gif.
	* In the footer, ```logo_square.jpg``` is displayed if present. We use a 47x46 jpg.

## Twitter Settings
* In ```includes/config.php``` we have a section for configuring the twitter integration. If you plan to utilize the integrated twitter functions, you must configure all ```twitter_``` settings in the configuration.
* To generate twitter API authentication credentials, follow these instructions:
	1. Register a twitter account at http://www.twitter.com where the tweets will be sent to.
	2. Navigate to https://dev.twitter.com/apps and log in using your twitter credentials.
	3. Click ```Create a new application```
	4. Fill out all of the required fields and create your application.
	5. Navigate to the ```Details``` tab and scroll to the bottom. Click ```Create my access token```
	6. Copy the consumer key and access token information to the twitter settings in ```includes/config.php```
	7. In the ```Settings``` tab for your dev.twitter.com application, change the application type to ```Read and Write```

## Usage
* When logged in as an admin, a "Report Incident" button will appear in the page header. Use this to create incidents either in the present (unplanned outage) or future (scheduled maintenance). When the timestamp of the incident is set in the future, the page will assume that the incident is a planned maintenance event.
* A scheduled maintenance event will move to the left (current incident) column when the future timestamp is reached. Logged in users will then be able to provide updates regarding the maintenance window like any other incident.
* Once the incident is created, updates can easily be provided by filling out the text box below each incident.
* Click a service while logged in to change its working status.

## FAQ
* If you receive the error ```Fatal error: Class 'SQLite3' not found```, then your PHP instance does not have the SQLite3 module loaded.
* How do I add services to a facility
	1. SSH into your server and go to the directory "cache" that has "status.db" in it.
	2. To get facility IDs run: ```sqlite3 status.db "SELECT * FROM `facilities`"```
	3. Then run: ```sqlite3 status.db "INSERT INTO facilities_services (facilities_id, friendly_name, status) VALUES (2, 'Web Server', 'online')"```
	4. I used "2" as the ID in my example, and "Web Server" as the service. You would simply run that command again making changes to those variables to add more services.

## License

This project is distributed under the GNU GPL v3 license which can be found in COPYING.
