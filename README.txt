
=========================
📘 ONLINE VOTING SYSTEM
=========================
👤 Developer: [Asogwa Victor]
💻 Project Folder: online_voting_system
🗃️ Database File: online_voting_db.sql
🧩 Server: XAMPP (PHP + MySQL)
📅 Version: October 2025
----------------------------------------
🧱 1. REQUIREMENTS
----------------------------------------
- Install XAMPP on your system
  (https://www.apachefriends.org/download.html)
- Make sure Apache and MySQL are running in XAMPP Control Panel.
----------------------------------------
📂 2. PROJECT FILE SETUP
----------------------------------------
1. Extract the folder named **online_voting_system** into:
   C:\xampp\htdocs\

   So your path should look like this:
   C:\xampp\htdocs\online_voting_system\

2. Inside that folder, you should see files like:
   - index.php
   - db.php
   - vote.php
   - admin/
   - css/, js/, images/, etc.

----------------------------------------
💾 3. DATABASE SETUP
----------------------------------------
1. Open your browser and go to:
   http://localhost/phpmyadmin/

2. Click **New** on the left panel to create a new database.
   Name it:
   **online_voting_db**

3. Click on the new database, then go to the **Import** tab.

4. Click **Choose File**, and select:
   **online_voting_db.sql**

5. Click **Go** to import the database.

✅ You should now see all tables such as:
   - voters
   - contestants
   - votes
   - admins

----------------------------------------
🚀 4. RUN THE PROJECT
----------------------------------------
After setup:
1. Open your browser
2. Type this into the address bar:
   👉 http://localhost/online_voting_system/

The main site should load successfully.

To access the admin dashboard, visit:
   👉 http://localhost/online_voting_system/admin/

----------------------------------------
🔑 5. DEFAULT ADMIN LOGIN (if included)
----------------------------------------
Email: admin@example.com
Password: admin123

(If you don’t have one, create a new admin in the database manually.)

----------------------------------------
💡 6. TROUBLESHOOTING
----------------------------------------
- If you see a “Not Found” error, make sure the folder name is correct and placed inside `htdocs`.
- If database errors appear, confirm that your `db.php` connection settings match:
  
  $servername = "localhost";
  $username   = "root";
  $password   = "";
  $dbname     = "online_voting_db";

- Always start **Apache** and **MySQL** in XAMPP before running the project.

----------------------------------------
🎯 7. PROJECT DESCRIPTION
----------------------------------------
The **Online Voting System** allows users to:
- Register or log in as voters
- View contestants
- Cast votes (one vote per voter)
- See results in real time
- Admins can manage voters, contestants, and votes securely

Built with:
- PHP (backend)
- MySQL (database)
- HTML, CSS, JavaScript (frontend)
- Bootstrap (for responsive design)

----------------------------------------
📩 8. CONTACT
----------------------------------------
If you need help setting it up, contact:
[Asogwa Victor]
[asovic2016@gmail.com]

----------------------------------------
END OF README
----------------------------------------
