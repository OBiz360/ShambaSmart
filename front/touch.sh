#!/bin/bash

# Project root directory (optional: you can change the name)
PROJECT_DIR="app"

# Create main project directory
mkdir -p $PROJECT_DIR

# Navigate into project directory
cd $PROJECT_DIR

# Create main files
touch index.html login.php register.php dashboard.php submit-guide.php ask-question.php admin-panel.php partners.php

# Create directories
mkdir -p css js images uploads includes

# Create files inside includes/
cd includes
touch header.php footer.php db_connect.php
cd ..

# Optional: Add a success message
echo "✅ Project structure created successfully in $(pwd)"
tree .
