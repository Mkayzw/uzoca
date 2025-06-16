# Deploying UZOCA to Render

## Step-by-Step Deployment Guide

### 1. Prerequisites
- GitHub account with your code pushed
- Render account (free tier available)

### 2. Database Setup Options

#### Option A: Render PostgreSQL (Recommended)
1. Go to Render Dashboard → Create → PostgreSQL
2. Name: `uzoca-db`
3. Database Name: `uzoca`
4. User: `uzoca_user`
5. Region: Choose closest to your users
6. Plan: Free tier (limited) or paid

#### Option B: External MySQL Provider
- PlanetScale (free MySQL)
- Railway (free tier available)
- DigitalOcean Managed Database

### 3. Web Service Deployment

#### Automatic (using render.yaml):
1. Push your code to GitHub
2. In Render Dashboard → Create → Web Service
3. Connect your GitHub repository
4. Render will automatically detect `render.yaml`
5. Click "Create Web Service"

#### Manual Configuration:
1. Create → Web Service
2. Connect GitHub repository
3. Configure:
   - **Name**: `uzoca-app`
   - **Environment**: `PHP`
   - **Build Command**: `composer install --no-dev --optimize-autoloader`
   - **Start Command**: `php -S 0.0.0.0:$PORT -t .`
   - **Auto-Deploy**: Yes

### 4. Environment Variables Setup

In your Render web service settings, add these environment variables:

```
DB_HOST=<your-database-host>
DB_USER=<your-database-user>
DB_PASS=<your-database-password>
DB_NAME=uzoca
DB_PORT=5432 (for PostgreSQL) or 3306 (for MySQL)
```

If using Render PostgreSQL, these will be auto-populated when you link the database.

### 5. Database Schema Migration

#### For PostgreSQL:
You'll need to convert your MySQL schema to PostgreSQL. Here are the main differences:

```sql
-- MySQL to PostgreSQL conversions needed:
-- AUTO_INCREMENT → SERIAL
-- ENUM → VARCHAR with CHECK constraint or custom type
-- TIMESTAMP DEFAULT CURRENT_TIMESTAMP → TIMESTAMP DEFAULT NOW()
```

#### For MySQL (external):
Import your existing `database.sql` file directly.

### 6. File Upload Configuration

Create upload directories and set permissions:
```bash
mkdir -p assets/uploads/properties
mkdir -p assets/uploads/profiles
chmod 755 assets/uploads/properties
chmod 755 assets/uploads/profiles
```

### 7. Testing Your Deployment

Once deployed, test these endpoints:
- `/health.php` - Check if app and database are working
- `/env-check.php` - Debug environment configuration
- `/` - Main application

### 8. Custom Domain (Optional)

1. In Render Dashboard → Your Service → Settings
2. Add custom domain
3. Configure DNS records as instructed
4. SSL certificate is automatic

## Important Notes

### Database Conversion Helper
If you need to convert MySQL to PostgreSQL, here's a quick conversion for your main tables:

```sql
-- Users table (PostgreSQL version)
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'landlord' CHECK (role IN ('admin', 'agent', 'landlord')),
    phone VARCHAR(20),
    profile_pic VARCHAR(255) DEFAULT 'profile-pic.jpg',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

### Performance Tips
1. Use Render's CDN for static assets
2. Enable gzip compression
3. Use environment variables for configuration
4. Monitor using `/health.php` endpoint

### Troubleshooting

#### Common Issues:
1. **500 Error**: Check logs in Render Dashboard
2. **Database Connection Failed**: Verify environment variables
3. **File Upload Issues**: Check directory permissions
4. **Slow Performance**: Consider upgrading Render plan

#### Debug Commands:
- Visit `/env-check.php` to see environment configuration
- Check Render logs for detailed error information
- Use `/health.php` for quick status check

### Cost Optimization
- Start with free tier
- Monitor usage and upgrade as needed
- Use external MySQL if PostgreSQL conversion is complex
- Consider static asset optimization

## Next Steps After Deployment

1. Set up monitoring and alerts
2. Configure backup strategy
3. Test all application features
4. Set up custom domain
5. Optimize performance
6. Set up CI/CD pipeline for updates

## Support Resources
- Render Documentation: https://render.com/docs
- PostgreSQL Migration: https://www.postgresql.org/docs/
- PHP on Render: https://render.com/docs/deploy-php
