# Griham – Find Your Perfect Stay 🏠

**Griham** simplifies your search for accommodation and essential services. From accomodation listings to food , Gym and laundry, everything is a call away — designed for students and professionals.


🌐 **Live Demo**: [https://griham.thevshub.in](https://griham.thevshub.in)
 
---

## 🖼 Preview

### Homepage
![Homepage](https://github.com/iamvishveshs/iamvishveshs.github.io/blob/main/assets/png/griham-homepage.png)

### Meal Listings Page
![Meal Listings](https://github.com/iamvishveshs/iamvishveshs.github.io/blob/main/assets/png/griham-services.png)

---
---

## ✨ Features

### 🏡 Effortless Room Finder
- Verified flats, PGs, and shared homes
- Smart filters: location, room type
- One-click call button for instant contact

### 👯 Roommate Matching System
- Filter profiles by gender, lifestyle, and habits
- Criteria: smoking, drinking, pets, food preferences, etc.

### 🍱 Homestyle Tiffin Delivery
- View local tiffin and mess providers with photos
- Cuisine types and service names displayed

### 🧺 Laundry & Housekeeping Services
- Contact laundry and cleaning providers near you
- Fast access via direct call buttons

### 🚨 Emergency & Utility Contacts
- Quick-dial access to essential services: Hospitals, Fire Stations etc.

### 📧 Gmail Email Integration (PHPMailer)
- Registration confirmation emails
- Password reset via secure Gmail SMTP using app password

### 💻 Frontend & UX
- Built with HTML5, CSS3, JavaScript, jQuery
- Responsive and mobile-friendly design

---

## 🛠️ Tech Stack

| Category   | Tools / Languages            |
|------------|------------------------------|
| Frontend   | HTML, CSS, JavaScript, jQuery|
| Backend    | PHP (Procedural)             |
| Database   | MySQL                        |
| Email      | PHPMailer + Gmail SMTP       |

---

## 🧩 Project Setup

### ✅ Requirements
- PHP 7.4 +
- MySQL
- Apache server (XAMPP/LAMP/WAMP recommended)
 
---

## ⚙️ Setup & Installation Guide

### Clone the Repository

```bash
git clone https://github.com/iamvishveshs/griham.git
cd griham
```
### Database Configuration

#### Config areas
change these files to run the website smoothly

`./database.php`
`./admin/database.php`
`./owner/database.php`
`./user/database.php`
`./libs/database.php`


| Key | value     | 
| :-------- | :------- | 
| `$servername` | `MySQL Hostname e.g. localhost` | 
| `$username` | `MySQL username e.g root` |
| `$password` | `MySQL password` |
| `$dbname` | `griham_project` |


### User Authentication

##### Demo Accounts for Local user


| Role | Email     | Password                |
| :-------- | :------- | :------------------------- |
| `Admin` | `admin@gmail.com` |  `Demo@1234`|
| `Owner` | `owner@gmail.com` |  `Demo@1234`|
| `user` | `user@gmail.com` |  `Demo@1234`|

##### Note:

To use the `griham.thevshub.in` register and use your account 



### SMTP setup
Also change the credentials in 
change these files to run the website smoothly

`./libs/accoun_verification_success.php`
`./libs/otp-resend.php`
`./libs/reset-password-otp.php`
`./libs/send_email_otp.php`
`./libs/send-support-response-email.php`

| Key | Value     | 
| :-------- | :------- |
| `$mail->Username` | `Your Email Address` | 
| `$mail->Password` | `generate app password from google account` |
| `$mail->setFrom('your_email', 'Griham')`| `Your Email Address`|

`go to gmail and generate app password here`
`https://myaccount.google.com/apppasswords` 


## Deveopment Team 
| Name                                                   | Role                   | Profile                                             |
| ------------------------------------------------------ | ---------------------- | --------------------------------------------------- |
| [Vishvesh Shivam](https://github.com/iamvishveshs)      | Full Stack | [LinkedIn](www.linkedin.com/in/iamvishveshs)           |
| [Akshay Kumar](https://github.com/ak-11bhardwaj) | Full Stack    | [LinkedIn](https://www.linkedin.com/in/) |
| [Aayush Chauhan](#)        | Frontend      | [-](#)          |
| [Ayush Sharma](https://github.com/Ayusharma24)   | Frontend    | [LinkedIn](https://www.linkedin.com/in/ayush-sharma-student)  |
| [Srishti Sharma](https://github.com/SrishtiSharma645)   | Frontend    | [LinkedIn](https://www.linkedin.com/in/srishti-sharma-1593452b2)  |
| [Mohd. Bilal](#)   | Data Collection, Frontend   | [LinkedIn](https://www.linkedin.com/in/mohd-bilal-264831339)  |
| [Sujal Mehra](https://github.com/Sujal-Hawkeye)   | Data Collection, Presentation  | [LinkedIn](https://www.linkedin.com/in/sujal-mehra--)  |
| [Arushi Sood](#)   | Data Collection, Presentation  | [LinkedIn](https://www.linkedin.com/in/arushi-sood-975aa2269)  |
| [Rizul Thakur](#)   | Data Collection,Report Writting   | [LinkedIn](https://www.linkedin.com/in/rizul-thakur-a5bb57289)  |
| [Akshika Kapil](#)   | Data Collection,Report Writting   | [LinkedIn](https://www.linkedin.com/in/akshika-kapil-a62b09289)  |
