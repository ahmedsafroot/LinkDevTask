# Events Management
This is Task for management Events.

---


## Requirements

---

- PHP >= 8
- MySql >=8.0
- composer
---

## Installation

---
1. clone code from this [repository](https://github.com/ahmedsafroot/LinkDevTask.git)
2. run composer install
3. now you have two choices can get database directly from code files **(events.sql)** or create your database
4. copy default.settings.php and rename it to be **settings.php** then change your database settings
5. create virtual host to your project
6. open your host -> if you create new database just complete steps to install project if not user this username (ahmedrefaat) and password(12345678) to login as admin
7. run **yourhost/update.php** to install custom tables . you can ignore this step if you use database in code files
8. install custom module events management from extend tab . . you can ignore this step if you use database in code files
9. inside configuration page you will find event management link to config event module [admin/config/site-settings]
10. admin list all events via **[admin/listing-events]** . you will all crud here
11. front end user can list and view details event via **[listing-events]**
12. for view custom block for latest 5 events created go to structure then block layout and put **event block** in any place you want.

