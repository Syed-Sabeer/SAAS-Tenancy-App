Hi, To Run the application
Please run migrations along with seeders 
you can do it through this command (php artisan migrate --seed)
then run this command to open the application  (php artisan serve --host=localhost --port=8000)
http://localhost:8000/
after that as per I set in seeders the email and password for login are:
Email: admin@saas.local
Password: Password@123

After login you can create company please make sure you remember the admin email and password 
once you create a company then pleaae click on trigeer provision to migrate the tables and seerders for tenant 
just like the admin panel will be wokring on http://localhost:8000/login
so to login into company / tenant dashboard you must need to access this url 

http://{tenant subdomain}localhost:8000/login
and use the login credentials which you added in that company to view the dashboard 

in short :
Central login: http://localhost:8000/login
Tenant login: http://{subdomain}.localhost:8000/login (example: http://test123.localhost:8000/login)


Thank you

