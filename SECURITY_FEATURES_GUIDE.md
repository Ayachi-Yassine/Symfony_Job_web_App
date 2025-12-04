# Security Features Guide for Job Portal

This document outlines security features to add to strengthen the job portal application.

---

## 1. Authentication & Authorization

### Currently Implemented ✓
- User registration with password hashing (bcrypt)
- Login/logout with Symfony Security
- Role-based access control (ROLE_USER, ROLE_ADMIN)
- CSRF token protection on forms

### To Add

**1.1 Password Requirements**
```php
// src/Validator/PasswordValidator.php
- Minimum 8 characters
- At least 1 uppercase letter
- At least 1 number
- At least 1 special character (!@#$%^&*)
```

**1.2 Email Verification**
- Send confirmation email on registration
- User cannot login until email verified
- Resend verification link option

**1.3 Two-Factor Authentication (2FA)**
- TOTP (Time-based One-Time Password) via authenticator app
- SMS-based backup codes
- Recovery codes stored securely

**1.4 Session Security**
- Session timeout after 30 minutes of inactivity
- Remember-me functionality (14 days max)
- Prevent session fixation attacks
- Secure session cookies (HttpOnly, Secure flags)

---

## 2. Data Protection

### To Add

**2.1 Input Validation**
```php
// Validate all user inputs
- Max length checks
- Type checking (email, integer, string)
- SQL injection prevention (already in Doctrine)
- XSS prevention via Twig auto-escaping ✓
```

**2.2 File Upload Security**
- Validate file type (only PDF for CVs)
- Check file size (max 5MB)
- Scan for viruses/malware (ClamAV)
- Store uploads outside webroot
- Rename files to random names (already done ✓)
- Prevent direct file execution

**2.3 SQL Injection Prevention**
- Use parameterized queries (Doctrine ORM) ✓
- Never concatenate SQL strings
- Validate all query inputs

**2.4 Data Encryption**
- Encrypt sensitive data at rest (passwords, CVs)
- Use HTTPS/TLS for data in transit
- Encrypt database backups

---

## 3. API & Request Security

### To Add

**3.1 Rate Limiting**
```yaml
# Prevent brute force attacks
- Login attempts: Max 5 per minute
- API calls: Max 100 per hour per IP
- File uploads: Max 3 per hour
```

**3.2 CORS (Cross-Origin Resource Sharing)**
```php
// Allow only trusted domains
- Restrict which domains can call APIs
- Prevent unauthorized cross-origin requests
```

**3.3 Security Headers**
```yaml
X-Frame-Options: DENY (prevent clickjacking)
X-Content-Type-Options: nosniff (prevent MIME sniffing)
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000 (HTTPS only)
Content-Security-Policy: restrict script sources
Referrer-Policy: no-referrer
```

**3.4 Request Validation**
- Validate Content-Type headers
- Check request size limits
- Validate HTTP methods (GET, POST, etc.)

---

## 4. Access Control

### Currently Implemented ✓
- Role-based access (ROLE_USER, ROLE_ADMIN)
- User can only edit their own profile
- Admins can manage all users

### To Add

**4.1 Implement Attribute-Level Security**
```php
// Check permissions on each action
- User can only view their own applications
- User can only withdraw their own applications
- Admin can view all applications
```

**4.2 IP Whitelist/Blacklist**
- Admin panel: Allow only specific IPs
- Suspicious activity: Auto-block IPs
- Rate limit by IP address

**4.3 Audit Logging**
- Log all admin actions
- Log failed login attempts
- Log file downloads
- Store logs in tamper-proof database

---

## 5. Vulnerability Prevention

### To Add

**5.1 SQL Injection**
- Already protected by Doctrine ORM ✓
- Never use raw SQL queries

**5.2 Cross-Site Scripting (XSS)**
- Twig auto-escapes by default ✓
- Use `|raw` filter only for trusted content
- Sanitize user input on output

**5.3 Cross-Site Request Forgery (CSRF)**
- Already implemented on all forms ✓
- Validate CSRF tokens on state-changing requests
- Use SameSite cookie attribute

**5.4 Insecure Direct Object References (IDOR)**
- Validate user owns resource before allowing access
- Example: User A cannot access User B's applications
```php
// Check ownership
if ($application->getUser()->getId() !== $this->getUser()->getId()) {
    throw $this->createAccessDeniedException();
}
```

**5.5 Security Misconfiguration**
- Disable debug mode in production
- Hide version info (Symfony, PHP)
- Remove default routes
- Secure .env file (never commit to git)
- Use environment variables for secrets

---

## 6. Password & Credential Security

### To Add

**6.1 Password Storage**
```php
// Already using bcrypt ✓
// Add:
- Password history (prevent reuse)
- Password expiration (every 90 days)
- Force password reset on first login
```

**6.2 Forgot Password Flow**
- Send secure token (expires in 1 hour)
- Cannot reuse old passwords
- Email confirmation required

**6.3 Credential Rotation**
- API keys/tokens expire after 1 year
- OAuth tokens refresh regularly
- SSH keys rotation

---

## 7. Database Security

### To Add

**7.1 Database Access**
- Use strong database password
- Limit database user permissions (principle of least privilege)
- Use SSL for database connections
- Never expose database credentials

**7.2 Backups**
- Encrypt database backups
- Store backups in secure location
- Test restore procedures regularly
- Keep multiple backup versions

**7.3 Data Masking**
- Mask sensitive data in logs/backups
- Show only last 4 digits of phone numbers
- Don't log passwords or credit cards

---

## 8. Infrastructure Security

### To Add

**8.1 HTTPS/TLS**
- Force HTTPS redirect
- Use SSL certificate (Let's Encrypt - free)
- Set HSTS header (HTTP Strict Transport Security)

**8.2 Firewall Rules**
- Allow only necessary ports (80, 443)
- Block unnecessary services
- Implement Web Application Firewall (WAF)

**8.3 DDoS Protection**
- Use CDN (Cloudflare - free tier)
- Rate limiting
- Traffic filtering

**8.4 Regular Updates**
- Update PHP regularly
- Update Symfony and dependencies
- Security patches within 24 hours

---

## 9. Monitoring & Logging

### To Add

**9.1 Security Logging**
```php
// Log these events:
- Login attempts (success/failure)
- Permission denials
- Admin actions
- File uploads/downloads
- Database changes
- API calls
```

**9.2 Real-time Alerts**
- Alert on multiple failed logins
- Alert on admin actions
- Alert on file downloads
- Alert on database changes

**9.3 Log Analysis**
- Store logs separately from app
- Analyze logs for suspicious patterns
- Keep logs for at least 30 days

---

## 10. Third-Party & Dependency Security

### To Add

**10.1 Dependency Scanning**
```bash
# Check for vulnerabilities in dependencies
composer audit
symfony check:security
```

**10.2 Keep Dependencies Updated**
```bash
# Check for updates
composer outdated

# Update safely
composer update
```

**10.3 Code Review**
- Review all code before production
- Use code scanning tools (SonarQube)
- Security testing

---

## Priority Implementation Order

### **Phase 1 (Critical - Do First)**
1. Email verification for registration
2. Password requirements validation
3. Rate limiting on login
4. HTTPS/TLS enforcement
5. Security headers
6. Input validation
7. IDOR prevention (access control)

### **Phase 2 (Important - Do Second)**
1. 2FA (Two-Factor Authentication)
2. Session timeout
3. Audit logging
4. File upload security hardening
5. Database security improvements
6. Dependency scanning

### **Phase 3 (Nice to Have - Do Later)**
1. IP whitelisting for admin
2. DDoS protection (Cloudflare)
3. Advanced logging/monitoring
4. Incident response procedures
5. Security training
6. Penetration testing

---

## Quick Implementation Examples

### Email Verification (Phase 1)
```php
// Generate token on registration
$token = bin2hex(random_bytes(32));
$user->setEmailVerificationToken($token);
$user->setEmailVerified(false);

// Send email with link: /verify-email?token=xxx

// Verify endpoint
public function verifyEmail(string $token) {
    $user = $this->userRepository->findByVerificationToken($token);
    if (!$user || $user->getTokenExpiresAt() < new DateTime()) {
        throw new \Exception('Invalid or expired token');
    }
    $user->setEmailVerified(true);
    $this->entityManager->flush();
}
```

### Password Requirements (Phase 1)
```php
// Add validator constraint
#[Assert\Regex(
    pattern: '/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
    message: 'Password must contain 8+ chars, 1 uppercase, 1 number, 1 special char'
)]
private string $password;
```

### Rate Limiting (Phase 1)
```php
// config/packages/rate_limiter.yaml
rate_limiters:
    login:
        policy: 'sliding_window'
        limit: 5
        interval: '1 minute'
```

### Security Headers (Phase 1)
```yaml
# config/packages/security_headers.yaml
security_headers:
    x_frame_options: 'DENY'
    x_content_type_options: 'nosniff'
    x_xss_protection: '1; mode=block'
```

---

## Testing Security

```bash
# Check for known vulnerabilities
composer audit

# Run security tests
php bin/phpunit tests/Security/

# Check OWASP compliance
# Use tools like: OWASP ZAP, Burp Suite Community

# Manual testing
1. Test SQL injection: ' OR '1'='1
2. Test XSS: <script>alert('XSS')</script>
3. Test CSRF: Remove CSRF token
4. Test IDOR: Change user ID in URL
5. Test authentication bypass
```

---

## Resources

- **OWASP Top 10**: https://owasp.org/www-project-top-ten/
- **Symfony Security**: https://symfony.com/doc/current/security.html
- **CWE Top 25**: https://cwe.mitre.org/top25/
- **NIST Cybersecurity Framework**: https://www.nist.gov/
- **PHP Security**: https://www.php.net/manual/en/security.php

---

## Summary

| Feature | Priority | Effort | Impact |
|---------|----------|--------|--------|
| Email verification | High | Low | High |
| Password requirements | High | Low | High |
| Rate limiting | High | Medium | High |
| HTTPS/SSL | High | Low | High |
| Security headers | High | Low | Medium |
| 2FA | Medium | High | High |
| Audit logging | Medium | Medium | Medium |
| IDOR prevention | High | Low | Critical |
| File upload security | High | Medium | High |
| DDoS protection | Low | High | Medium |

---

**Start with Phase 1 for maximum security improvement with minimum effort!**
