<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Energy Monitoring System</title>
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
            border-left: 5px solid #48bb78;
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
            border-left: 5px solid #4299e1;
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
            display: flex;
            align-items: center;
            gap: 10px;
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
            border-left: 4px solid #48bb78;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .data-table th {
            background: #4299e1;
            color: white;
            text-align: left;
            padding: 15px;
            font-weight: 600;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:nth-child(even) {
            background: #f7fafc;
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

        .privacy-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #48bb78;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 10px;
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
            
            .data-table {
                display: block;
                overflow-x: auto;
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
                <i class="fas fa-user-shield"></i>
            </div>
            <h1>Privacy Policy</h1>
            <p>How we protect and handle your data</p>
            <div class="privacy-badge">
                <i class="fas fa-lock"></i> GDPR Compliant
            </div>
        </div>

        <div class="content-card">
            <div class="last-updated">
                <i class="fas fa-history"></i> Last Updated: January 15, 2024
            </div>

            <div class="highlight-box">
                <p><strong>Your Privacy Matters:</strong> At Energy Monitoring System, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our Service.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-info-circle"></i> 1. Information We Collect</h2>
                <p>We collect several types of information for various purposes to provide and improve our Service to you:</p>
                
                <h3 style="color: #4a5568; margin: 15px 0 10px 0;">1.1 Personal Data</h3>
                <p>While using our Service, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you:</p>
                <table class="data-table">
                    <tr>
                        <th>Data Type</th>
                        <th>Examples</th>
                        <th>Purpose</th>
                    </tr>
                    <tr>
                        <td>Account Information</td>
                        <td>Name, email, username, password</td>
                        <td>Account creation and authentication</td>
                    </tr>
                    <tr>
                        <td>Contact Information</td>
                        <td>Email address, phone number</td>
                        <td>Communication and support</td>
                    </tr>
                    <tr>
                        <td>Profile Information</td>
                        <td>Profile picture, preferences</td>
                        <td>Personalization of service</td>
                    </tr>
                </table>

                <h3 style="color: #4a5568; margin: 15px 0 10px 0;">1.2 Energy Usage Data</h3>
                <p>We collect data from your connected energy monitoring devices:</p>
                <ul>
                    <li>Real-time energy consumption readings</li>
                    <li>Historical energy usage patterns</li>
                    <li>Device status and performance metrics</li>
                    <li>Energy cost calculations</li>
                </ul>

                <h3 style="color: #4a5568; margin: 15px 0 10px 0;">1.3 Usage Data</h3>
                <p>We automatically collect information on how the Service is accessed and used:</p>
                <ul>
                    <li>Browser type and version</li>
                    <li>Pages visited and time spent</li>
                    <li>Device information (IP address, device type)</li>
                    <li>Cookies and similar tracking technologies</li>
                </ul>
            </div>

            <div class="section">
                <h2><i class="fas fa-cogs"></i> 2. How We Use Your Information</h2>
                <p>We use the collected data for various purposes:</p>
                <ul>
                    <li><strong>To provide and maintain our Service:</strong> Ensure proper functionality and access</li>
                    <li><strong>To notify you about changes:</strong> Inform you about updates or modifications</li>
                    <li><strong>To provide customer support:</strong> Respond to your inquiries and requests</li>
                    <li><strong>To gather analysis:</strong> Improve our Service and develop new features</li>
                    <li><strong>To monitor usage:</strong> Detect and prevent technical issues</li>
                    <li><strong>To provide energy insights:</strong> Generate reports and recommendations</li>
                    <li><strong>To send notifications:</strong> Alert you about abnormal energy consumption</li>
                </ul>
            </div>

            <div class="section">
                <h2><i class="fas fa-share-alt"></i> 3. Data Sharing and Disclosure</h2>
                <p>We do not sell your personal information. We may share your data in the following circumstances:</p>
                
                <h3 style="color: #4a5568; margin: 15px 0 10px 0;">3.1 Service Providers</h3>
                <p>We employ third-party companies and individuals to facilitate our Service, provide the Service on our behalf, perform Service-related services, or assist us in analyzing how our Service is used.</p>

                <h3 style="color: #4a5568; margin: 15px 0 10px 0;">3.2 Legal Requirements</h3>
                <p>We may disclose your information if required to do so by law or in response to valid requests by public authorities.</p>

                <h3 style="color: #4a5568; margin: 15px 0 10px 0;">3.3 Business Transfers</h3>
                <p>In connection with any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.</p>

                <h3 style="color: #4a5568; margin: 15px 0 10px 0;">3.4 With Your Consent</h3>
                <p>We may disclose your personal information for any other purpose with your consent.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-cookie-bite"></i> 4. Cookies and Tracking</h2>
                <p>We use cookies and similar tracking technologies to track activity on our Service and hold certain information.</p>
                <p>Cookies are files with small amount of data which may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>
                
                <div class="highlight-box">
                    <p><strong>Types of cookies we use:</strong></p>
                    <ul>
                        <li><strong>Essential Cookies:</strong> Necessary for the Service to function</li>
                        <li><strong>Performance Cookies:</strong> Help us understand how users interact with our Service</li>
                        <li><strong>Functionality Cookies:</strong> Remember your preferences and settings</li>
                        <li><strong>Analytics Cookies:</strong> Collect information about your usage patterns</li>
                    </ul>
                </div>
            </div>

            <div class="section">
                <h2><i class="fas fa-shield-alt"></i> 5. Data Security</h2>
                <p>The security of your data is important to us. We implement appropriate technical and organizational measures to protect your personal information against unauthorized or unlawful processing, accidental loss, destruction, or damage.</p>
                <p>Our security measures include:</p>
                <ul>
                    <li>Encryption of sensitive data in transit and at rest</li>
                    <li>Regular security assessments and testing</li>
                    <li>Access controls and authentication mechanisms</li>
                    <li>Secure data backup and disaster recovery procedures</li>
                    <li>Employee training on data protection</li>
                </ul>
                <p>However, no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your personal information, we cannot guarantee its absolute security.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-globe-europe"></i> 6. Your Data Protection Rights</h2>
                <p>Depending on your location, you may have the following data protection rights:</p>
                
                <table class="data-table">
                    <tr>
                        <th>Right</th>
                        <th>Description</th>
                    </tr>
                    <tr>
                        <td>Access</td>
                        <td>Request copies of your personal data</td>
                    </tr>
                    <tr>
                        <td>Rectification</td>
                        <td>Request correction of inaccurate data</td>
                    </tr>
                    <tr>
                        <td>Erasure</td>
                        <td>Request deletion of your personal data</td>
                    </tr>
                    <tr>
                        <td>Restriction</td>
                        <td>Request restriction of processing your data</td>
                    </tr>
                    <tr>
                        <td>Portability</td>
                        <td>Request transfer of your data to another organization</td>
                    </tr>
                    <tr>
                        <td>Objection</td>
                        <td>Object to processing of your personal data</td>
                    </tr>
                </table>
                
                <p>To exercise these rights, please contact us using the information in the "Contact Us" section.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-database"></i> 7. Data Retention</h2>
                <p>We retain your personal information only for as long as necessary for the purposes set out in this Privacy Policy. We will retain and use your information to the extent necessary to comply with our legal obligations, resolve disputes, and enforce our policies.</p>
                <p>Energy usage data is retained for 5 years to provide historical analysis and comply with regulatory requirements. Account information is retained while your account is active and for 2 years after account closure for legal and business purposes.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-external-link-alt"></i> 8. Third-Party Links</h2>
                <p>Our Service may contain links to other sites that are not operated by us. If you click on a third-party link, you will be directed to that third party's site. We strongly advise you to review the Privacy Policy of every site you visit.</p>
                <p>We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-child"></i> 9. Children's Privacy</h2>
                <p>Our Service is not intended for use by children under the age of 18 ("Children").</p>
                <p>We do not knowingly collect personally identifiable information from children under 18. If you are a parent or guardian and you are aware that your Child has provided us with Personal Data, please contact us. If we become aware that we have collected Personal Data from children without verification of parental consent, we take steps to remove that information from our servers.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-sync-alt"></i> 10. Changes to This Privacy Policy</h2>
                <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
                <p>We will let you know via email and/or a prominent notice on our Service, prior to the change becoming effective and update the "effective date" at the top of this Privacy Policy.</p>
                <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
            </div>

            <div class="section">
                <h2><i class="fas fa-envelope"></i> 11. Contact Us</h2>
                <p>If you have any questions about this Privacy Policy, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> privacy@energymonitor.com</li>
                    <li><strong>Data Protection Officer:</strong> dpo@energymonitor.com</li>
                    <li><strong>Address:</strong> 123 Energy Street, Green City, EC 12345</li>
                    <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                </ul>
                <p>For GDPR-related inquiries from EU residents, you can also contact our EU representative at gdpr@energymonitor.eu.</p>
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