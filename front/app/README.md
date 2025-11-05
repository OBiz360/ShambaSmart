# 🌱 ShambaSmart - Kenyan Agribusiness Learning Hub

A comprehensive web application connecting Kenyan farmers, students, and agribusiness enthusiasts through knowledge sharing, Q&A forums, and agricultural resources.

## 📋 Features

- ✅ **User Authentication** - Register/Login with role-based access
- ✅ **Farming Guides** - Submit, browse, and search agricultural guides
- ✅ **Q&A Community** - Ask questions and get expert answers
- ✅ **Market Prices** - Real-time crop price updates
- ✅ **Admin Panel** - Manage content, users, and approvals
- ✅ **Partner Network** - Connect with NGOs and organizations
- ✅ **Responsive Design** - Works on desktop, tablet, and mobile

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Server:** Apache (XAMPP/WAMP)

## 📦 Installation

### Prerequisites

- XAMPP or WAMP installed
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser

### Step 1: Clone/Download Project

```bash
# Download the project files to your htdocs folder
# For XAMPP: C:/xampp/htdocs/shambasmart
# For WAMP: C:/wamp64/www/shambasmart
```

### Step 2: Create Database

1. Open **MySQL Workbench** or **phpMyAdmin**
2. Create a new database named `shambasmart`
3. Import the `database.sql` file:
   - In Workbench: File > Run SQL Script > Select `database.sql`
   - In phpMyAdmin: Import tab > Choose file > Go

### Step 3: Configure Database Connection

Edit `includes/config.php` and update these lines if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Change if you have a password
define('DB_NAME', 'shambasmart');
```

### Step 4: Create Uploads Folder

Create an `uploads` folder in the root directory and make it writable:

```
/shambasmart/
  └── uploads/  (create this folder)
```

### Step 5: Start Server

1. Start **Apache** and **MySQL** in XAMPP/WAMP
2. Open browser and navigate to: `http://localhost/shambasmart`

## 🔑 Default Login Credentials

**Admin Account:**
- Email: `admin@shambasmart.com`
- Password: `admin123`

## 📁 Project Structure

```
shambasmart/
├── index.php                 # Landing page
├── login.php                 # User login
├── register.php              # User registration
├── logout.php                # Logout handler
├── dashboard.php             # User dashboard
├── submit-guide.php          # Submit farming guide
├── ask-question.php          # Ask a question
├── view-guides.php           # Browse all guides
├── view-questions.php        # Browse questions
├── admin-panel.php           # Admin dashboard
├── partners.php              # Partner organizations
├── database.sql              # Database schema
│
├── includes/
│   ├── config.php            # Database connection & functions
│   ├── header.php            # Reusable header (optional)
│   └── footer.php            # Reusable footer (optional)
│
├── css/
│   └── style.css             # Main stylesheet
│
├── js/
│   └── main.js               # JavaScript functions
│
├── uploads/                  # User uploaded files
└── images/                   # Static images
```

## 🎨 Customization

### Change Colors

Edit `css/style.css` and modify the CSS variables:

```css
:root {
    --primary-green: #228B22;    /* Main green */
    --accent-yellow: #FFD700;    /* Accent yellow */
    --secondary-brown: #8B4513;  /* Brown headers */
}
```

### Add New Features

1. Create new PHP file in root directory
2. Include `config.php` at the top
3. Use existing functions from config
4. Follow the design patterns in existing files

## 🔒 Security Notes

- Passwords are hashed using `password_hash()`
- SQL injection prevented with prepared statements (PDO)
- Input sanitization with `clean()` function
- Session-based authentication
- File upload validation

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config.php`
- Ensure database exists

### Upload Folder Error
- Create `uploads` folder
- Set write permissions (777 on Linux)
- Check `UPLOAD_DIR` path in config

### CSS Not Loading
- Clear browser cache
- Check file paths
- Verify Apache is serving CSS files

### Login Not Working
- Check if session is started
- Verify database has users table
- Clear cookies and try again

## 📱 Mobile Responsiveness

The app is fully responsive and works on:
- ✅ Desktop (1200px+)
- ✅ Tablet (768px - 1199px)
- ✅ Mobile (< 768px)

## 🚀 Future Enhancements

- [ ] Email verification
- [ ] Password reset functionality
- [ ] Real-time notifications
- [ ] Chat/messaging system
- [ ] Weather API integration
- [ ] SMS alerts for market prices
- [ ] PDF export of guides
- [ ] Multi-language support (Swahili)

## 👥 User Roles

1. **Farmer** - Submit guides, ask questions, view resources
2. **Student** - Access learning materials, participate in Q&A
3. **Enthusiast** - Explore trends, share innovations
4. **Admin** - Manage all content and users

## 📊 Database Tables

- `users` - User accounts and profiles
- `guides` - Farming guides and articles
- `questions` - Community questions
- `answers` - Answers to questions
- `market_prices` - Crop prices
- `notifications` - User notifications
- `partners` - Partner organizations
- `guide_comments` - Comments on guides

## 🤝 Contributing

To add new features:

1. Create feature branch
2. Follow existing code structure
3. Test thoroughly
4. Update documentation

## 📞 Support

For issues or questions:
- Email: info@shambasmart.com
- Phone: +254 700 000 000

## 📄 License

Built for educational and community purposes.

---

**Built with ❤️ for Kenyan Farmers 🇰🇪**

*Empowering agriculture through technology and knowledge sharing*