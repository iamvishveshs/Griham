<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Griham</title>
    <link rel="stylesheet" href="./assets/css/nav.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Header */
        .terms-header {
            background: var(--theme-color);
            color: white;
            padding: 40px 5%;
            text-align: center;
        }

        .terms-header h1 {
            font-size: 32px;
            font-weight: 700;
        }

        .terms-header .date {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Section Layout */
        .terms-section {
            padding: 20px 5%;
            max-width: 900px;
            margin: auto;
        }

        .terms-section.dark-bg {
            background: var(--theme-color-dark);
            color: white;
            padding: 60px 5%;
        }

        h2 {
            font-size: 24px;
            font-weight: 600;
            color: var(--theme-accent-color);
            margin-bottom: 15px;
            border-bottom: 2px solid var(--theme-border);
            padding-bottom: 5px;
        }

        p,
        ul {
            font-size: 16px;
            color: var(--theme-secondary-color);
            line-height: 1.8;
        }

        ul {
            padding-left: 20px;
        }

        ul li {
            margin-bottom: 10px;
        }

        .terms-section.dark-bg p,
        .terms-section.dark-bg ul,
        .date {
            color: white !important;
        }

        /* Content Boxes */
        .terms-box {
            background: white;
            padding: 20px;
            border-radius: var(--theme-border-radius);
            box-shadow: var(--theme-shadow);
            margin-bottom: 20px;
        }

        .terms-box h3 {
            font-size: 18px;
            color: var(--theme-color);
            margin-bottom: 10px;
        }

        /* Small Font for Legal Notices */
        .small-text {
            font-size: 14px;
            color: rgba(0, 0, 0, 0.6);
            font-style: italic;
            margin-top: 10px;
        }

        /* Contact Email */
        .email {
            font-size: 18px;
            font-weight: bold;
            color: var(--theme-color);
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <?php require("navbar.php"); ?>
    <!-- Header Section -->
    <header class="terms-header">
        <div class="container">
            <h1>Terms & Conditions</h1>
            <p class="date">Effective Date: March 2025</p>
        </div>
    </header>

    <!-- Introduction Section -->
    <section class="terms-section">
        <div class="container">
            <h2>1. Agreement to Terms</h2>
            <p>By accessing or using the Griham platform, Users acknowledge and agree to be bound by these Terms & Conditions. Continued use of the platform constitutes acceptance of all provisions set forth herein.</p>
        </div>
    </section>

    <!-- User Responsibilities -->
    <section class="terms-section">
        <div class="container">
            <h2>2. User Obligations</h2>
            <p>Users agree to the following conditions:</p>
            <div class="terms-box">
                <h3>1. Compliance with Laws</h3>
                <p>Users must adhere to all applicable laws when using the platform.</p>
            </div>
            <div class="terms-box">
                <h3>2. Accurate Information</h3>
                <p>Users must provide truthful and accurate information during registration.</p>
            </div>
            <div class="terms-box">
                <h3>3. No Unauthorized Use</h3>
                <p>Users shall not attempt to hack, exploit, or manipulate the platform.</p>
            </div>
        </div>
    </section>

    <!-- Third-Party Liability -->
    <section class="terms-section dark-bg">
        <div class="container">
            <h2>3. Third-Party Services</h2>
            <p>Griham operates as an **intermediary platform** connecting Users with independent service providers. Griham does not own, manage, or control these services, and is not liable for:</p>
            <ul>
                <li>Property conditions, disputes, or service failures.</li>
                <li>Delays, fraud, misrepresentation, or unauthorized charges.</li>
                <li>Any losses, damages, or inconveniences caused by third-party providers.</li>
            </ul>
            <p class="small-text">Users engage with service providers at their own risk.</p>
        </div>
    </section>

    <!-- Account & Termination -->
    <section class="terms-section">
        <div class="container">
            <h2>4. Account Suspension & Termination</h2>
            <p>Griham reserves the right to suspend, restrict, or terminate User accounts under the following conditions:</p>
            <ul>
                <li>Violation of platform policies or terms.</li>
                <li>Suspicious, fraudulent, or illegal activities.</li>
                <li>Failure to comply with applicable laws.</li>
            </ul>
            <p class="small-text">Account suspensions may occur without prior notice.</p>
        </div>
    </section>

    <!-- Indemnification Clause -->
    <section class="terms-section dark-bg">
        <div class="container">
            <h2>5. Indemnification & Liability Waiver</h2>
            <p>Users agree to indemnify and hold harmless Griham, its affiliates, employees, and partners from any claims, liabilities, damages, or expenses arising from:</p>
            <ul>
                <li>User interactions with third-party service providers.</li>
                <li>Violation of these Terms & Conditions.</li>
                <li>Unauthorized use of the platform.</li>
            </ul>
            <p class="small-text">Griham is not liable for any direct or indirect damages, including but not limited to loss of profits, data, or service failures.</p>
        </div>
    </section>

    <!-- Amendments & Updates -->
    <section class="terms-section">
        <div class="container">
            <h2>6. Modifications & Updates</h2>
            <p>Griham reserves the right to modify, update, or replace these Terms & Conditions at any time without prior notice. Continued use of the platform constitutes acceptance of the latest version.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="terms-section">
        <div class="container">
            <h2>7. Contact Information</h2>
            <p>For any questions regarding these Terms & Conditions, contact:</p>
            <p class="email">grihamproject@gmail.com</p>
        </div>
    </section>
    <?php require("./footer.php"); ?>
</body>

</html>