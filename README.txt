SALES & INVENTORY PHP PROJECT

Requirements:
- Windows + WAMP/XAMPP
- PHP 8+
- MySQL/MariaDB

Setup:
1. Copy the folder sales_inventory_php to your WAMP www folder.
   Example: C:\wamp64\www\sales_inventory_php

2. Start WAMP and make sure the tray icon is green.

3. Open phpMyAdmin.
   Create/import the database using:
   database/sales_inventory.sql

4. Check database connection in:
   config/database.php

   Default settings:
   host: localhost
   database: sales_inventory_db
   username: root
   password: blank

5. Open the system in your browser:
   http://localhost/sales_inventory_php/login.php

Demo Login:
Username: admin
Password: password

Included Modules:
- Login authentication
- Dashboard totals and summary tables
- Order list with status tabs
- Order creation with customer selection/new customer
- Multiple product selection with quantity
- Discount, shipping fee, payment method, and partial payment support
- Automatic stock deduction after confirmed order
- Payment-based order status updates: Unpaid, Partial, Paid, Overdue
- Customer CRUD and customer order history
- Category CRUD
- Product CRUD with image upload and low-stock indicator
- Sales report with filters, print, and CSV export
- Inventory report with summary cards, filters, print, and CSV download

Notes:
- This is a clean starter project matching the activity requirements.
- UI is responsive but simple, so you can customize it based on the Figma design.
- Uploaded product photos are stored in the uploads folder.
