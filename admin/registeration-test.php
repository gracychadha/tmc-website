<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NZAPSCON 2026 — Registration</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* ===== RESET & BASE ===== */
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f2f5;
      color: #1a1a2e;
      line-height: 1.6;
    }

    /* ===== HEADER / BRANDING ===== */
    .header {
      background: linear-gradient(135deg, #0d1b3e 0%, #1a2a5e 40%, #0d1b3e 100%);
      padding: 0;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .header-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 40px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .logo-area {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .logo-area img {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: #fff;
      padding: 4px;
      object-fit: contain;
    }
    .logo-text h1 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem;
      color: #f0c040;
      letter-spacing: 2px;
      line-height: 1.2;
    }
    .logo-text p {
      font-size: 0.72rem;
      color: rgba(255,255,255,0.7);
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    /* ===== NAV MENU (right side) ===== */
    .nav-menu {
      display: flex;
      gap: 8px;
      align-items: center;
    }
    .nav-menu a {
      text-decoration: none;
      color: #fff;
      font-size: 0.88rem;
      font-weight: 500;
      padding: 10px 22px;
      border-radius: 30px;
      transition: all 0.3s ease;
      border: 1.5px solid rgba(255,255,255,0.2);
      position: relative;
    }
    .nav-menu a:hover,
    .nav-menu a.active {
      background: #f0c040;
      color: #0d1b3e;
      border-color: #f0c040;
      font-weight: 600;
    }
    .nav-menu a::before {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 50%;
      transform: translateX(-50%);
      width: 0;
      height: 2px;
      background: #f0c040;
      transition: width 0.3s;
    }

    /* ===== HERO BANNER ===== */
    .hero-banner {
      background: linear-gradient(135deg, #0d1b3e 0%, #1a2a5e 50%, #0d1b3e 100%);
      padding: 60px 40px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .hero-banner::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 50%, rgba(240,192,64,0.06) 0%, transparent 50%);
      animation: shimmer 8s ease-in-out infinite;
    }
    @keyframes shimmer {
      0%, 100% { transform: translate(0, 0); }
      50% { transform: translate(5%, 3%); }
    }
    .hero-banner h2 {
      font-family: 'Playfair Display', serif;
      font-size: 3rem;
      color: #f0c040;
      letter-spacing: 4px;
      position: relative;
    }
    .hero-banner .subtitle {
      font-size: 1.1rem;
      color: rgba(255,255,255,0.85);
      margin-top: 8px;
      font-weight: 300;
      position: relative;
    }
    .hero-banner .event-info {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 24px;
      flex-wrap: wrap;
      position: relative;
    }
    .hero-banner .event-info span {
      color: #fff;
      font-size: 0.95rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .hero-banner .event-info .icon {
      width: 36px;
      height: 36px;
      background: rgba(240,192,64,0.15);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
    }

    /* ===== MAIN CONTENT ===== */
    .main-content {
      max-width: 1100px;
      margin: 0 auto;
      padding: 40px 20px 60px;
    }

    /* ===== SECTION TITLES ===== */
    .section-title {
      text-align: center;
      margin-bottom: 36px;
    }
    .section-title h2 {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      color: #0d1b3e;
      position: relative;
      display: inline-block;
    }
    .section-title h2::after {
      content: '';
      display: block;
      width: 60px;
      height: 3px;
      background: #f0c040;
      margin: 10px auto 0;
      border-radius: 2px;
    }
    .section-title p {
      color: #666;
      font-size: 0.92rem;
      margin-top: 8px;
    }

    /* ===== REGISTRATION STRUCTURE TABLE ===== */
    #registration-structure {
      scroll-margin-top: 100px;
    }
    .structure-card {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.06);
      margin-bottom: 50px;
    }
    .structure-header {
      background: linear-gradient(135deg, #f0c040, #e6a817);
      padding: 18px 30px;
      text-align: center;
    }
    .structure-header h3 {
      font-size: 1.4rem;
      color: #0d1b3e;
      font-weight: 700;
      letter-spacing: 1px;
    }
    .structure-header p {
      font-size: 0.82rem;
      color: rgba(13,27,62,0.7);
      margin-top: 2px;
    }
    .structure-table {
      width: 100%;
      border-collapse: collapse;
    }
    .structure-table thead th {
      background: #0d1b3e;
      color: #fff;
      padding: 16px 20px;
      font-size: 0.88rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .structure-table thead th:first-child {
      text-align: left;
      border-radius: 0 0 0 0;
    }
    .structure-table tbody td {
      padding: 16px 20px;
      text-align: center;
      font-size: 0.95rem;
      border-bottom: 1px solid #eee;
      transition: background 0.2s;
    }
    .structure-table tbody td:first-child {
      text-align: left;
      font-weight: 600;
      color: #0d1b3e;
    }
    .structure-table tbody tr:hover {
      background: #f8f6ff;
    }
    .structure-table tbody tr:last-child td {
      border-bottom: none;
    }
    .price-tag {
      font-weight: 700;
      color: #1a2a5e;
      font-size: 1.05rem;
    }
    .early-bird { color: #27ae60; }
    .regular { color: #2980b9; }
    .spot { color: #e74c3c; }

    .gst-note {
      text-align: center;
      padding: 14px;
      background: #f8f9fa;
      font-size: 0.82rem;
      color: #888;
      font-style: italic;
    }

    /* ===== REGISTRATION FORM ===== */
    #registration-form {
      scroll-margin-top: 100px;
    }
    .form-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.06);
      overflow: hidden;
    }
    .form-header {
      background: linear-gradient(135deg, #0d1b3e, #1a2a5e);
      padding: 24px 36px;
      display: flex;
      align-items: center;
      gap: 14px;
    }
    .form-header .form-icon {
      width: 44px;
      height: 44px;
      background: rgba(240,192,64,0.2);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
    }
    .form-header h3 {
      color: #fff;
      font-size: 1.3rem;
      font-weight: 600;
    }
    .form-header p {
      color: rgba(255,255,255,0.6);
      font-size: 0.82rem;
    }

    .form-body {
      padding: 36px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
    }
    .form-group.full-width {
      grid-column: 1 / -1;
    }
    .form-group label {
      font-size: 0.85rem;
      font-weight: 600;
      color: #0d1b3e;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .form-group label .required {
      color: #e74c3c;
      font-size: 0.9rem;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 0.92rem;
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
      background: #fafafa;
      color: #333;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #f0c040;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(240,192,64,0.15);
    }
    .form-group input::placeholder,
    .form-group textarea::placeholder {
      color: #aaa;
    }
    .form-group textarea {
      resize: vertical;
      min-height: 80px;
    }

    /* Radio / Checkbox Group */
    .radio-group {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 4px;
    }
    .radio-group label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 400;
      font-size: 0.88rem;
      cursor: pointer;
      padding: 8px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      transition: all 0.2s;
      background: #fafafa;
    }
    .radio-group label:hover {
      border-color: #f0c040;
      background: #fffef5;
    }
    .radio-group input[type="radio"] {
      accent-color: #f0c040;
      width: 16px;
      height: 16px;
    }
    .radio-group input[type="radio"]:checked + span {
      font-weight: 600;
      color: #0d1b3e;
    }

    /* Category Select Styling */
    .category-select {
      position: relative;
    }
    .category-select select {
      appearance: none;
      cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 16px center;
      padding-right: 40px;
    }

    /* Submit Button */
    .form-actions {
      grid-column: 1 / -1;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 12px;
      padding-top: 24px;
      border-top: 1px solid #eee;
    }
    .btn-reset {
      padding: 12px 28px;
      border: 2px solid #ddd;
      border-radius: 10px;
      background: #fff;
      color: #666;
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s;
    }
    .btn-reset:hover {
      border-color: #e74c3c;
      color: #e74c3c;
    }
    .btn-submit {
      padding: 14px 48px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg, #f0c040, #e6a817);
      color: #0d1b3e;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      font-family: 'Poppins', sans-serif;
      letter-spacing: 0.5px;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(240,192,64,0.3);
    }
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 25px rgba(240,192,64,0.45);
    }
    .btn-submit:active {
      transform: translateY(0);
    }

    /* ===== FOOTER ===== */
    .footer {
      background: #0d1b3e;
      color: rgba(255,255,255,0.6);
      text-align: center;
      padding: 24px;
      font-size: 0.82rem;
    }
    .footer strong {
      color: #f0c040;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .header-top {
        flex-direction: column;
        gap: 12px;
        padding: 14px 20px;
      }
      .nav-menu {
        flex-wrap: wrap;
        justify-content: center;
      }
      .hero-banner h2 { font-size: 2rem; }
      .form-grid { grid-template-columns: 1fr; }
      .form-body { padding: 24px 20px; }
      .structure-table thead th,
      .structure-table tbody td {
        padding: 12px 10px;
        font-size: 0.8rem;
      }
      .form-actions {
        flex-direction: column;
        gap: 12px;
      }
      .btn-submit, .btn-reset { width: 100%; text-align: center; }
    }
  </style>
</head>
<body>

  <!-- ===== HEADER WITH LOGO & NAV ===== -->
  <header class="header">
    <div class="header-top">
      <div class="logo-area">
        <!-- NZAPS Logo -->
        <img src="https://i.imgur.com/nzaps-logo.png" alt="NZAPS Logo"
             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2245%22 fill=%22%23fff%22/%3E%3Ctext x=%2250%22 y=%2255%22 text-anchor=%22middle%22 font-size=%2218%22 font-weight=%22bold%22 fill=%22%230d1b3e%22%3ENZAPS%3C/text%3E%3C/svg%3E'" />
        <div class="logo-text">
          <h1>NZAPSCON 2026</h1>
          <p>North Zone Association of Plastic Surgeons</p>
        </div>
      </div>
      <nav class="nav-menu">
        <a href="#registration-structure" id="nav-structure">📋 Registration Structure</a>
        <a href="#registration-form" id="nav-form">📝 Registration Form</a>
      </nav>
    </div>
  </header>

  <!-- ===== HERO BANNER ===== -->
  <section class="hero-banner">
    <h2>NZAPSCON 2026</h2>
    <p class="subtitle">21st Annual Conference — Kasauli</p>
    <div class="event-info">
      <span><span class="icon">📅</span> 13th – 15th November, 2026</span>
      <span><span class="icon">📍</span> Glenview Resort, Kasauli</span>
    </div>
  </section>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="main-content">

    <!-- ===== REGISTRATION STRUCTURE SECTION ===== -->
    <section id="registration-structure">
      <div class="section-title">
        <h2>Registration Structure</h2>
        <p>Choose the category that best suits you</p>
      </div>

      <div class="structure-card">
        <div class="structure-header">
          <h3>Conference Registration</h3>
          <p>All prices inclusive of GST @ 18%</p>
        </div>
        <table class="structure-table">
          <thead>
            <tr>
              <th>Category</th>
              <th>Early Bird<br><small style="font-weight:400;opacity:0.8">(Till Aug 31, 2026)</small></th>
              <th>Regular<br><small style="font-weight:400;opacity:0.8">(Till Oct 15, 2026)</small></th>
              <th>Spot Registration<br><small style="font-weight:400;opacity:0.8">(Oct 16, 2026 Onwards)</small></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Member</td>
              <td><span class="price-tag early-bird">₹ 6,000</span></td>
              <td><span class="price-tag regular">₹ 6,500</span></td>
              <td><span class="price-tag spot">₹ 7,500</span></td>
            </tr>
            <tr>
              <td>Non Member</td>
              <td><span class="price-tag early-bird">₹ 6,500</span></td>
              <td><span class="price-tag regular">₹ 7,000</span></td>
              <td><span class="price-tag spot">₹ 8,000</span></td>
            </tr>
            <tr>
              <td>Residents</td>
              <td><span class="price-tag early-bird">₹ 4,000</span></td>
              <td><span class="price-tag regular">₹ 4,500</span></td>
              <td><span class="price-tag spot">₹ 6,000</span></td>
            </tr>
            <tr>
              <td>Accompanying Person</td>
              <td><span class="price-tag early-bird">₹ 4,000</span></td>
              <td><span class="price-tag regular"> 4,500</span></td>
              <td><span class="price-tag spot">₹ 6,000</span></td>
            </tr>
          </tbody>
        </table>
        <div class="gst-note">
          * All amounts are inclusive of GST @ 18%. Registration fees are non-refundable.
        </div>
      </div>
    </section>

    <!-- ===== REGISTRATION FORM SECTION ===== -->
    <section id="registration-form">
      <div class="section-title">
        <h2>Registration Form</h2>
        <p>Fill in your details to complete your registration</p>
      </div>

      <div class="form-card">
        <div class="form-header">
          <div class="form-icon">📝</div>
          <div>
            <h3>Participant Details</h3>
            <p>Fields marked with * are mandatory</p>
          </div>
        </div>

        <form class="form-body" id="regForm" onsubmit="handleSubmit(event)">
          <div class="form-grid">

            <!-- Name -->
            <div class="form-group">
              <label>Full Name <span class="required">*</span></label>
              <input type="text" name="name" placeholder="Enter your full name" required />
            </div>

            <!-- Age -->
            <div class="form-group">
              <label>Age <span class="required">*</span></label>
              <input type="number" name="age" placeholder="Your age" min="1" max="120" required />
            </div>

            <!-- Gender -->
            <div class="form-group full-width">
              <label>Gender <span class="required">*</span></label>
              <div class="radio-group">
                <label><input type="radio" name="gender" value="Male" required /> <span>Male</span></label>
                <label><input type="radio" name="gender" value="Female" /> <span>Female</span></label>
                <label><input type="radio" name="gender" value="Prefer not to say" /> <span>Prefer not to say</span></label>
                <label><input type="radio" name="gender" value="Other" /> <span>Other</span></label>
              </div>
            </div>

            <!-- Affiliation -->
            <div class="form-group full-width">
              <label>Affiliation / Institution <span class="required">*</span></label>
              <input type="text" name="affiliation" placeholder="Your hospital / institution name" required />
            </div>

            <!-- Mobile Number -->
            <div class="form-group">
              <label>Mobile Number <span class="required">*</span></label>
              <input type="tel" name="mobile" placeholder="+91 XXXXX XXXXX" pattern="[0-9+\s]{10,15}" required />
            </div>

            <!-- Email -->
            <div class="form-group">
              <label>Email Address <span class="required">*</span></label>
              <input type="email" name="email" placeholder="your@email.com" required />
            </div>

            <!-- Registration Category -->
            <div class="form-group category-select">
              <label>Registration Category <span class="required">*</span></label>
              <select name="category" required>
                <option value="" disabled selected>Select category</option>
                <option value="Member">Member — ₹ 6,000</option>
                <option value="Non Member">Non Member — ₹ 6,500</option>
                <option value="Resident">Resident — ₹ 4,000</option>
                <option value="Accompanying Person">Accompanying Person — ₹ 4,000</option>
              </select>
            </div>

            <!-- Registration Type -->
            <div class="form-group category-select">
              <label>Registration Type <span class="required">*</span></label>
              <select name="regType" required>
                <option value="" disabled selected>Select type</option>
                <option value="Early Bird">Early Bird (Till Aug 31, 2026)</option>
                <option value="Regular">Regular (Till Oct 15, 2026)</option>
                <option value="Spot">Spot Registration (Oct 16, 2026 Onwards)</option>
              </select>
            </div>

            <!-- Address -->
            <div class="form-group full-width">
              <label>Address</label>
              <textarea name="address" placeholder="Your full address (optional)"></textarea>
            </div>

            <!-- Actions -->
            <div class="form-actions">
              <button type="reset" class="btn-reset"> Reset Form</button>
              <button type="submit" class="btn-submit">Submit Registration →</button>
            </div>

          </div>
        </form>
      </div>
    </section>

  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="footer">
    © 2026 <strong>NZAPSCON</strong> — North Zone Association of Plastic Surgeons. All rights reserved.
  </footer>

  <!-- ===== JAVASCRIPT ===== -->
  <script>
    // ID-wise active menu highlighting on scroll
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-menu a');

    function updateActiveNav() {
      const scrollPos = window.scrollY + 150;
      sections.forEach(section => {
        const top = section.offsetTop;
        const height = section.offsetHeight;
        const id = section.getAttribute('id');
        if (scrollPos >= top && scrollPos < top + height) {
          navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + id) {
              link.classList.add('active');
            }
          });
        }
      });
    }

    window.addEventListener('scroll', updateActiveNav);
    window.addEventListener('load', updateActiveNav);

    // Form submission handler
    function handleSubmit(e) {
      e.preventDefault();
      const form = e.target;
      const data = new FormData(form);
      const obj = {};
      data.forEach((val, key) => obj[key] = val);

      console.log('Registration Data:', obj);

      // Show success feedback
      const btn = form.querySelector('.btn-submit');
      const originalText = btn.textContent;
      btn.textContent = '✅ Registered Successfully!';
      btn.style.background = 'linear-gradient(135deg, #27ae60, #2ecc71)';
      btn.style.color = '#fff';

      setTimeout(() => {
        btn.textContent = originalText;
        btn.style.background = '';
        btn.style.color = '';
        form.reset();
      }, 3000);
    }
  </script>

</body>
</html>