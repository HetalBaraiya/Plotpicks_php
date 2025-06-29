# 📚 PlotPicks – Discover, Review & Recommend Books

PlotPicks is a dynamic and user-friendly book recommendation platform where readers can browse and search for books by genre, submit reviews and star ratings, and suggest new titles to the community. The platform also features a dedicated admin panel for managing users, categories, and books. Built using **HTML, CSS, PHP, JavaScript**, and **MySQL**.

---

## 🔍 Features

📖 Browse and search for books by title or genre  
⭐ Rate and review books with star ratings  
📝 Suggest new books to the platform  
🧑‍💻 Admin panel to manage books, users, and genres  
📂 Book cover upload and display support  
📱 Fully responsive and clean UI  
❌ Form validation and error handling  

---

## 💻 Tech Stack

- HTML5  
- CSS3  
- JavaScript (ES6+)  
- PHP  
- MySQL  

---

## 🚀 Getting Started

Follow these steps to run the app locally:

### 1. Clone the repository

git clone https://github.com/HetalBaraiya/Plotpicks_php.git

### 2. Set up the database
Open phpMyAdmin

Create a new database (e.g., book_db)

Import the book_db.sql file (if available) to create tables like books, genres, reviews, and users

### 3. Configure the database connection
Open includes/db.php

Update the credentials to match your local MySQL configuration:
$conn = mysqli_connect("localhost", "your_username", "your_password", "book_db");

### 4. Run the App
Start Apache and MySQL via XAMPP or similar

Open your browser and go to:
http://localhost/your-folder/index.php

---

### 📁 Project Structure

PlotPicks/
├── admin/                # Admin dashboard files
├── includes/             # Database and functions files
├── images/               # Uploaded book images
├── index.php             # Homepage
├── add_review.php        # Add review logic
├── search.php            # Book search results
├── css/                  # Stylesheets
├── js/                   # JavaScript files
├── book_db.sql           # MySQL database structure (if available)
└── README.md             # Project documentation

---

🙋‍♀️ Author
Hetal Baraiya
📧 hetaldbaraiya@gmail.com

---


