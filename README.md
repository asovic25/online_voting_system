# 🗳️ Online Voting System

A web-based online voting system built with PHP and MySQL. The application allows voters to register, view contestants, cast votes, and view election results, while administrators can manage voters, contestants, and election data.

## 🚀 Features

### Voter Features
- Voter registration
- Voter login
- View registered contestants
- Cast votes
- View election results

### Administrator Features
- Administrator authentication
- Admin dashboard
- Manage voters
- Manage contestants
- View voter information
- View contestant information
- Edit and delete voter records
- Edit and delete contestant records
- Monitor election results

## 🛠️ Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap
- XAMPP

## 📂 Project Structure

```text
online_voting_system/
│
├── admin/
│   ├── admin_auth.php
│   ├── admin_dashboard.php
│   ├── admin_login.php
│   ├── admin_logout.php
│   ├── admin_nav.php
│   ├── admin_signup.php
│   ├── admin_style.css
│   ├── delete_contestant.php
│   ├── delete_voter.php
│   ├── edit_contestant.php
│   ├── edit_voter.php
│   ├── view_contestant.php
│   └── view_voter.php
│
├── image/
├── uploads/
│
├── db.php
├── index.php
├── project_participant.php
├── register_contestant.php
├── register_voter.php
├── results.php
├── style.css
├── vote.php
└── online_voting_system.sql

⚙️ Requirements

To run this project locally, you will need:

XAMPP
Apache
MySQL
A modern web browser
💻 Installation
1. Install XAMPP

Download and install XAMPP, then start:

Apache
MySQL
2. Clone or copy the project

Place the project folder inside the XAMPP htdocs directory:

C:\xampp\htdocs\online_voting_system\
3. Create the database

Open:

http://localhost/phpmyadmin/

Create a database named:

online_voting_system
4. Import the database

Select the online_voting_system database and open the Import tab.

Select:

online_voting_system.sql

and import the database.

5. Check the database connection

The project uses the local XAMPP MySQL configuration.

The database connection is configured in:

db.php
6. Run the application

Open your browser and visit:

http://localhost/online_voting_system/
🔐 Administration

The project includes an administrator section for managing voters, contestants, and election information.

The administrator pages are located inside:

/admin/

Use the administrator login provided by the application/database configuration.

🧪 Development Environment

This project was developed and tested locally using:

XAMPP
PHP
MySQL
phpMyAdmin
Visual Studio Code
📌 Future Improvements

Possible future improvements include:

Improved authentication and authorization
Stronger password security
Email verification
Enhanced vote validation
Improved mobile responsiveness
Election audit logs
Additional security measures
Deployment to a production server
👨‍💻 Author

Asogwa Victor Nnamdi

IT Support | Technical Support | PHP Developer | Aspiring Technology Professional

GitHub: asovic25

📄 License

This project is available for educational and portfolio purposes.

---

