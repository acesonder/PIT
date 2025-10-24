# Quick Start Guide - PIT Count Application

## For First-Time Users

### What You Need
- Web server with PHP 7.4+ and MySQL 5.7+
- 10 minutes for setup

### Installation (3 Steps)

1. **Upload Files**
   - Upload all files to your web server directory

2. **Run Setup**
   ```bash
   cd /path/to/PIT
   ./setup.sh
   ```
   - Enter your MySQL credentials when prompted
   - Script creates database and imports schema

3. **Access Application**
   - Open: `http://yourserver.com/PIT/`
   - Test: `http://yourserver.com/PIT/test.php`

That's it! ✓

## Using the Application

### For Outreach Staff

**Starting a New Assessment:**
1. Go to home page → Click "New Assessment"
2. Select your name from the dropdown
3. Enter client's name (and DOB if available)
4. System checks for duplicates
5. Client chooses consent type (full or partial)
6. Client selects assessment length (SHORT/MEDIUM/HARD)
7. Complete assessment (all questions are optional!)
8. Submit when done

**Tips:**
- Use "Skip" button on any question
- Assessment auto-saves every 2 minutes
- Progress bar shows completion percentage
- Can save and return later

### For Administrators

**Access Admin Portal:**
1. Go to: `http://yourserver.com/PIT/admin-login.html`
2. Enter passcode: `079777` (change this in production!)
3. You're in!

**Admin Dashboard Shows:**
- Total assessments count
- Unique clients
- Assessments today/week/month
- Breakdown by assessment type

**Admin Can:**
- **View All Assessments**: See, edit, or delete any assessment
- **Manage Widgets**: Toggle which statistics show on landing page
- **Manage Staff**: Add or remove outreach workers
- **Export Data**: Download all data as CSV

**Managing Widgets:**
1. Admin → Widgets
2. Check box in top-right of any widget to show it on landing page
3. Uncheck to hide it
4. Changes appear immediately on home page

## Default Configuration

### Default Staff Members
- Jordan
- London
- Chance
- Deb
- Jenni

*Add more in Admin → Staff*

### Default Admin Passcode
- `079777`

**⚠️ IMPORTANT**: Change this in production!
- Edit: `config/config.php`
- Change: `define('ADMIN_PASSCODE', 'your_secure_code');`

### Default Widgets Visible on Landing Page
1. Total Assessments
2. Unique Individuals  
3. Assessments Today

*Customize in Admin → Widgets*

## Understanding Assessment Types

### SHORT (6-10 minutes)
- ~25 questions
- Basic demographics and location
- Best for: Quick counts

### MEDIUM (12-20 minutes)
- 50-70 questions
- Includes health, income, barriers
- Best for: Service planning

### HARD (25-40 minutes)
- 100-120 questions
- Comprehensive assessment
- Best for: Individual case planning

## Consent Options Explained

### Full Consent
- Client agrees to share name and DOB with Northumberland County
- Allows personalized follow-up and service connection
- All data shared

### Partial Consent (Anonymous)
- Only demographics shared (age, gender)
- No identifying information to county
- Still counted in total
- OUTSINC maintains full record

**Every client chooses their consent level**

## Data Privacy

✓ All participation is voluntary  
✓ Every question can be skipped  
✓ Data stored securely  
✓ Shared only per consent given  
✓ Compliant with Canadian privacy laws  

## Common Questions

**Q: Can someone take the assessment twice?**  
A: System checks for duplicates by name and DOB. Staff are alerted if client already assessed.

**Q: What if someone doesn't want to answer a question?**  
A: Every question has "Skip" or "Prefer not to say" option.

**Q: How do I see the total count?**  
A: Displayed prominently on the home page, updates in real-time.

**Q: Can I edit an assessment after submission?**  
A: Yes, admins can edit or delete any assessment from the admin portal.

**Q: How do I backup the data?**  
A: See DEPLOYMENT.md for automated backup script.

## Getting Help

### Check System Status
- Visit: `http://yourserver.com/PIT/test.php`
- All items should show green checkmarks

### Common Issues

**Can't login to admin?**
- Verify passcode is correct (default: 079777)
- Check browser console for errors
- Clear browser cache/cookies

**Database connection failed?**
- Check credentials in `config/config.php`
- Verify MySQL is running
- Ensure database was imported

**Widgets not showing data?**
- Need at least one completed assessment
- Check widget is enabled in Admin → Widgets
- Verify database queries are working in test.php

### Still Need Help?
- Review README.md for detailed setup
- Check DEPLOYMENT.md for production issues
- Review test.php for diagnostics
- Contact: info@outsinc.ca

## Next Steps

1. **Change admin passcode** (in config/config.php)
2. **Add real images** (see images/IMAGE-REQUIREMENTS.md)
3. **Train staff** on using the system
4. **Test with sample data** before going live
5. **Set up backups** (see DEPLOYMENT.md)
6. **Enable HTTPS** for production

## Important Files

- `README.md` - Full documentation
- `DEPLOYMENT.md` - Production deployment guide
- `test.php` - System diagnostics
- `config/config.php` - Database and settings
- `database/schema.sql` - Database structure
- `images/IMAGE-REQUIREMENTS.md` - Image specifications

## Project Info

**Organization**: OUTSINC (Outreach Services, Inc.)  
**Purpose**: Point-in-Time count for 2026 warming room planning  
**Coverage**: Cobourg, Ontario and Northumberland County  
**Questions**: info@outsinc.ca  

---

**Ready to Start!** 🚀

Access your application at: `http://yourserver.com/PIT/`
