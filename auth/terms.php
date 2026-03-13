<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Energy Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            opacity: 0.1;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            left: 80%;
            animation-delay: -5s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: -10s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 85%;
            animation-delay: -15s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            33% {
                transform: translateY(-20px) rotate(120deg);
            }
            66% {
                transform: translateY(20px) rotate(240deg);
            }
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #667eea;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .logo i {
            font-size: 24px;
            color: white;
        }

        .header h1 {
            color: #2d3748;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header p {
            color: #718096;
            font-size: 16px;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            border-left: 5px solid #48bb78;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #2d3748;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section p {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .section ul, .section ol {
            margin-left: 20px;
            margin-bottom: 15px;
            color: #4a5568;
        }

        .section li {
            margin-bottom: 8px;
            line-height: 1.6;
        }

        .highlight-box {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .back-button:active {
            transform: translateY(0);
        }

        .footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            padding: 20px;
            margin-top: 40px;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
            text-decoration: underline;
        }

        .last-updated {
            text-align: right;
            color: #718096;
            font-size: 14px;
            font-style: italic;
            margin-bottom: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .content-card {
                padding: 25px;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .section h2 {
                font-size: 22px;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 15px;
            }
            
            .content-card {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-bolt"></i>
            </div>
            <h1>Terms of Service</h1>
            <p>Energy Monitoring System - Last Updated: January 2024</p>
        </div>

        <div class="content-card">
            <div class="last-updated">
                <i class="fas fa-history"></i> Last Updated: January 15, 2024
            </div>

            <div class="highlight-box">
                <p><strong>Important:</strong> Please read these Terms of Service ("Terms") carefully before using the Energy Monitoring System. By accessing or using our service, you agree to be bound by these Terms.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-file-contract"></i> 1. Acceptance of Terms</h2>
                <p>By registering for and/or using the Energy Monitoring System ("Service") in any manner, you agree to these Terms of Service and all other operating rules, policies and procedures that may be published from time to time on the site by Energy Monitoring System, each of which is incorporated by reference and each of which may be updated from time to time without notice to you.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-user-check"></i> 2. Eligibility</h2>
                <p>You must be at least 18 years old to use this Service. By agreeing to these Terms, you represent and warrant that:</p>
                <ul>
                    <li>You are at least 18 years old</li>
                    <li>You have the legal capacity to enter into a binding contract</li>
                    <li>You are not prohibited from receiving our services under applicable law</li>
                    <li>You will provide accurate and complete registration information</li>
                </ul>
            </div>

            <div class="section">
                <h2><i class="fas fa-user-shield"></i> 3. Account Registration and Security</h2>
                <p>To access certain features of the Service, you must register for an account. When you register, you must provide accurate and complete information. You are solely responsible for:</p>
                <ul>
                    <li>Maintaining the confidentiality of your account and password</li>
                    <li>All activities that occur under your account</li>
                    <li>Restricting access to your computer or device</li>
                    <li>Immediately notifying us of any unauthorized use of your account</li>
                </ul>
                <p>We reserve the right to refuse service, terminate accounts, or remove content at our sole discretion.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-chart-line"></i> 4. Service Description</h2>
                <p>The Energy Monitoring System provides:</p>
                <ul>
                    <li>Real-time monitoring of energy consumption</li>
                    <li>Historical data analysis and reporting</li>
                    <li>Predictive analytics for energy usage</li>
                    <li>Alerts and notifications for abnormal consumption</li>
                    <li>Energy efficiency recommendations</li>
                </ul>
                <p>We reserve the right to modify, suspend, or discontinue the Service (or any part thereof) at any time with or without notice.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-database"></i> 5. Data Collection and Use</h2>
                <p>By using our Service, you agree to our data practices as described in these Terms and our Privacy Policy. We collect:</p>
                <ul>
                    <li>Energy consumption data from your connected devices</li>
                    <li>Account information and preferences</li>
                    <li>Usage statistics and analytics</li>
                    <li>Device information and configuration data</li>
                </ul>
                <p>We use this data to provide, maintain, and improve our Service, and to develop new features and services.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-ban"></i> 6. Prohibited Conduct</h2>
                <p>You agree not to engage in any of the following prohibited activities:</p>
                <ul>
                    <li>Using the Service for any illegal purpose or in violation of any laws</li>
                    <li>Attempting to interfere with, compromise the system integrity or security of the Service</li>
                    <li>Accessing content or data not intended for you</li>
                    <li>Impersonating others or providing false information</li>
                    <li>Distributing viruses, worms, or other malicious code</li>
                    <li>Attempting to reverse engineer or extract source code from the Service</li>
                    <li>Sharing your account credentials with others</li>
                </ul>
            </div>

            <div class="section">
                <h2><i class="fas fa-copyright"></i> 7. Intellectual Property Rights</h2>
                <p>The Service and its original content, features, and functionality are owned by Energy Monitoring System and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
                <p>You may not copy, modify, create derivative works of, publicly display, publicly perform, republish, or transmit any of the material obtained through the Service, except as expressly permitted by these Terms.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-exclamation-triangle"></i> 8. Disclaimer of Warranties</h2>
                <p>The Service is provided "as is" and "as available" without any warranties of any kind, either express or implied. We do not warrant that:</p>
                <ul>
                    <li>The Service will meet your specific requirements</li>
                    <li>The Service will be uninterrupted, timely, secure, or error-free</li>
                    <li>The results that may be obtained from the use of the Service will be accurate or reliable</li>
                    <li>The quality of any products, services, information, or other material obtained by you through the Service will meet your expectations</li>
                </ul>
            </div>

            <div class="section">
                <h2><i class="fas fa-balance-scale"></i> 9. Limitation of Liability</h2>
                <p>To the maximum extent permitted by applicable law, Energy Monitoring System shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses, resulting from:</p>
                <ul>
                    <li>Your access to or use of or inability to access or use the Service</li>
                    <li>Any conduct or content of any third party on the Service</li>
                    <li>Any content obtained from the Service</li>
                    <li>Unauthorized access, use, or alteration of your transmissions or content</li>
                </ul>
            </div>

            <div class="section">
                <h2><i class="fas fa-exchange-alt"></i> 10. Changes to Terms</h2>
                <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>
                <p>By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the Service.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-gavel"></i> 11. Governing Law</h2>
                <p>These Terms shall be governed and construed in accordance with the laws of [Your Country/State], without regard to its conflict of law provisions.</p>
                <p>Any dispute arising from or relating to the subject matter of these Terms shall be subject to the exclusive jurisdiction of the courts located in [Your City, Country].</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-envelope"></i> 12. Contact Information</h2>
                <p>If you have any questions about these Terms, please contact us at:</p>
                <ul>
                    <li>Email: legal@energymonitor.com</li>
                    <li>Address: 123 Energy Street, Green City, EC 12345</li>
                    <li>Phone: +1 (555) 123-4567</li>
                </ul>
            </div>

            <a href="register.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Registration
            </a>
        </div>

        <div class="footer">
            © 2024 <a href="#">Energy Monitoring System</a>. All rights reserved.
            <br>
            <span style="font-size: 12px; opacity: 0.7;">
                Version 5.0 | Made with <i class="fas fa-heart" style="color: #ff6b6b;"></i> for a sustainable future
            </span>
        </div>
    </div>
</body>
</html>