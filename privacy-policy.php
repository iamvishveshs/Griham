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
.privacy-header {
    background: var(--theme-color);
    color: white;
    padding: 40px 5%;
    text-align: center;
}
.privacy-header h1 {
    font-size: 32px;
    font-weight: 700;
}
.privacy-header .date {
    font-size: 14px;
    opacity: 0.8;
}
/* Section Layout */
.privacy-section {
    padding: 20px 5%;
    max-width: 900px;
    margin: auto;
}
.privacy-section.dark-bg {
    background: var(--theme-color-dark);
    color: white !important;
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
p, ul {
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
.privacy-section.dark-bg p,.privacy-section.dark-bg ul,.date {
    color: white !important;
}
/* Content Boxes */
.privacy-box {
    background: white;
    padding: 20px;
    border-radius: var(--theme-border-radius);
    box-shadow: var(--theme-shadow);
    margin-bottom: 20px;
}
.privacy-box h3 {
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
     <header class="privacy-header">
        <div class="container">
            <h1>Privacy Policy</h1>
            <p class="date">Effective Date: March 2025</p>
        </div>
    </header>
    <!-- Introduction Section -->
    <section class="privacy-section">
        <div class="container">
            <h2>Introduction & Scope</h2>
            <p>
                This Privacy Policy ("Policy") governs the collection, usage, and disclosure of personal and transactional information ("User Data") on the Griham platform. By accessing or using our services, Users consent to the practices outlined herein.
            </p>
        </div>
    </section>
    <!-- Data Collection Section -->
    <section class="privacy-section">
        <div class="container">
            <h2>Data Collection & Usage</h2>
            <p>We collect and process the following types of data:</p>
            <div class="privacy-box">
                <h3>1. Personal Information</h3>
                <p>Includes name, contact details, and location data.</p>
            </div>
            <div class="privacy-box">
                <h3>2. Transactional Data</h3>
                <p>Records financial transactions conducted via the platform.</p>
            </div>
            <div class="privacy-box">
                <h3>3. Usage Analytics</h3>
                <p>Tracks browsing behavior, cookies, and IP address logs.</p>
            </div>
        </div>
    </section>
    <!-- Third-Party Data Sharing -->
    <section class="privacy-section dark-bg">
        <div class="container">
            <h2>Third-Party Data Sharing</h2>
            <p>By using our platform, Users consent to the sharing of their data with:</p>
            <ul>
                <li>Partner businesses, service providers, and advertisers.</li>
                <li>Regulatory bodies and authorities, if legally required.</li>
            </ul>
            <p class="small-text">Users waive any right to object to such disclosures.</p>
        </div>
    </section>
    <!-- Limitation of Liability -->
    <section class="privacy-section">
        <div class="container">
            <h2>Limitation of Liability</h2>
            <p>Griham acts as a **digital intermediary** and does not control or guarantee the quality of third-party services. Users acknowledge:</p>
            <ul>
                <li>We do not own or operate the properties or services listed.</li>
                <li>We are not responsible for service failures, fraud, or disputes.</li>
                <li>Users engage with service providers at their own risk.</li>
            </ul>
            <p class="small-text">No claims or refunds shall be entertained under any circumstances.</p>
        </div>
    </section>
    <!-- Security & Data Retention -->
    <section class="privacy-section dark-bg">
        <div class="container">
            <h2>Data Security & Retention</h2>
            <p>While we implement security measures, we do not guarantee absolute data protection.</p>
            <ul>
                <li>Users accept the inherent risks of online transactions.</li>
                <li>Griham is not liable for unauthorized breaches or cyber threats.</li>
            </ul>
        </div>
    </section>
    <!-- Contact Section -->
    <section class="privacy-section">
        <div class="container">
            <h2>Contact Information</h2>
            <p>If you have questions, reach out to us at:</p>
            <p class="email">grihamproject@gmail.com</p>.com</p>
        </div>
    </section>
    <?php require("./footer.php"); ?>
</body>
</html>