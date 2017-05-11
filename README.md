# Challenge Project 2015
Challenge: Build a photo sharing site in PHP without using a Framework. This was built for a job interview a few years ago.

## Dependencies:
	PDO Enabled
	GD (Image Manipulation)

## Configure
	Open app/config/myAppConfig.php and set your database settings, you don't need to touch anything else.
	User should have all privileges
	
### Flow of things:
	- App Engine Starts
		* Configuration is read
		* User required files checked for syntax and loaded into the environment
		* Test database connection
		* URI is checked for action. If it's controller start controller otherwise send to URI path
		* If is controller check for child of Restful
		* Check if it's controller action, if not send to error page
		* Run logic in controller
		* Append layout files
		* Render page
		* Bye bye :)
