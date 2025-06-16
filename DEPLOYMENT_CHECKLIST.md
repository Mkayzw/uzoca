# UZOCA Render Deployment Checklist

## Pre-Deployment Setup

### 1. Environment Configuration
- [x] Updated `config/database.php` to use environment variables
- [x] Created `render.yaml` for Render deployment configuration
- [x] Updated `.gitignore` for PHP-specific files

### 2. Render Configuration Required

#### Database Setup (PostgreSQL recommended for Render)
1. Create a PostgreSQL database in Render dashboard
2. Note: You'll need to convert MySQL schema to PostgreSQL if using PostgreSQL
3. Or use PlanetScale/Railway for MySQL hosting

#### Environment Variables to Set in Render:
- `DB_HOST` - Database host
- `DB_USER` - Database username  
- `DB_PASS` - Database password
- `DB_NAME` - Database name (uzoca)
- `DB_PORT` - Database port (5432 for PostgreSQL, 3306 for MySQL)

### 3. Deployment Steps

1. **Push to GitHub:**
   ```bash
   git add .
   git commit -m "Add Render deployment configuration"
   git push origin main
   ```

2. **Create Render Web Service:**
   - Connect your GitHub repository
   - Use the `render.yaml` configuration
   - Or manually configure:
     - Build Command: `composer install --no-dev --optimize-autoloader`
     - Start Command: `php -S 0.0.0.0:$PORT -t .`

3. **Database Migration:**
   - Import your `database.sql` file into the Render database
   - Run any additional setup scripts

## Server Requirements (Render Provides)
- [x] PHP 8.1+ 
- [x] Web server
- [x] SSL certificate
- [x] Sufficient disk space

## Database Setup
- [ ] Create Render PostgreSQL database OR external MySQL
- [ ] Import database structure from `database.sql`
- [ ] Configure environment variables in Render
- [ ] Test database connection

## Post-Deployment Testing
- [ ] Test user registration
- [ ] Test login system
- [ ] Test admin functionality
- [ ] Test agent features
- [ ] Test landlord features
- [ ] Test payment system
- [ ] Test email notifications

## Post-Deployment
- [ ] Monitor error logs
- [ ] Set up monitoring
- [ ] Configure backups
- [ ] Update DNS settings
- [ ] Test all features
- [ ] Document deployment

## Maintenance
- [ ] Regular backups
- [ ] Security updates
- [ ] Performance monitoring
- [ ] Error logging
- [ ] User support system 