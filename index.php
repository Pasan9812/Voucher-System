<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Voucher System</title>
  <link rel="stylesheet" href="index.css" />
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="user-panel">
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert-success">
            <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif ?>

    <?php if (isset($_SESSION['username'])) : ?>
        <span class="welcome-text">
            Welcome, <strong><?php echo $_SESSION['username']; ?></strong>
        </span>
        <a href="index.php?logout='1'" class="logout-btn">Logout</a>
    <?php endif ?>
</div>


<!-- Top Selling Products -->
<section class="top-products">
  <h2>Our Products</h2>
  <div class="products-grid">
    <div class="product-card">
      <h3>Automated Attendance Management</h3>
    </div>
    <div class="product-card">
      <h3>Payroll Management</h3>
    </div>
    <div class="product-card">
      <h3>Fingerprint Machines with Software</h3>
    </div>
    <div class="product-card">
      <h3>Report Generation and Graphical Data Representation</h3>
    </div>
  </div>
</section>

<!-- Trusted By Section -->
<section class="trusted-institutions">
  <h2>Our Valubale Customers</h2>

  <!-- Government Institutions -->
  <div class="institution-category">
    <h3>01 - Government Institutions</h3>
    <p>Sri Lankan Government trusts EKRAIN’s integrity and reliability. Institutions like Sri Lanka Post, Co-operative Department, Irrigation Department, and Lanka Sathosa use EKRAIN.</p>
    <div class="logo-grid">
      <img src="https://slpost.gov.lk/wp-content/uploads/2019/10/DOP_header.png" alt="Sri Lanka Post" />
      <img src="https://www.coop.gov.lk/assets/img/app/logo_txt_black.png" alt="Co-operative Department" />
      <img src="https://www.agrarian.lk/img/link/link02.png" alt="Agrarian Service Centers" />
      <img src="https://rda.gov.lk/templates/theme2021/images/new/logo.png" alt="RDA" />
    </div>
    <a href="https://ekrain.lk/" class="view-more">View More</a>
  </div>

  <!-- Hotels and Resorts -->
  <div class="institution-category">
    <h3>02 - Hotels and Resorts</h3>
    <p>Hotels such as Kandy City Hotel, Earl’s Regent Kandy, Mandarina Colombo trust EKRAIN.</p>
    <div class="logo-grid">
      <img src="http://store.galadarihotel.lk/wp-content/uploads/2024/04/logo-galadari-store-2024-6-copy.svg" alt="Galadagi" />
      <img src="https://s.shangri-la.com/sl-fe-public/imgs/logo/header_logo_sh_en.png" alt="shangri la" />
      <img src="https://www.hilton.com/modules/assets/svgs/logos/WW.svg" alt="Hilton" />
      <img src="https://stafftravel.voyage/_assets/SndFVnBrRzI2blVHcXhlYllLY2NHdz09" alt="cinnaman" />
    </div>
    <a href="https://ekrain.lk/" class="view-more">View More</a>
  </div>

  <!-- Hospitals and Factories -->
  <div class="institution-category">
    <h3>03 - Hospitals and Factories</h3>
    <p>Clients like Ceylon Biscuits Limited, Aloka Hospitals, and Hela Garments manage large enterprises using EKRAIN.</p>
    <div class="logo-grid">
      <img src="https://cbllk.com/white/img/logo.png" alt="CBL" />
      <img src="https://masholdings.com/wp-content/themes/mas-holdings/assets/images/mas_new.png" alt="MAS" />
      <img src="https://www.helaclothing.com/wp-content/uploads/2021/11/hela-logo-header-9-black.svg" alt="Hela Garments" />
      <img src="https://sathuta.net/images/sathuta_logo_web.png" alt="Sathuta Builders" />
    </div>
    <a href="https://ekrain.lk/" class="view-more">View More</a>
  </div>

  <!-- Other Private Institutions -->
  <div class="institution-category">
    <h3>04 - Other Private Institutions</h3>
    <p>Companies like Thilakawardana Textiles, eZone, Tree of Life, and Coco Green have trusted EKRAIN for years.</p>
    <div class="logo-grid">
      <img src="https://sobacaterers.lk/img/logo.png" alt="Soba Caters" />
      <img src="http://www.norfolk.lk/images/logo2.png" alt="norfolk" />
      <img src="https://hoteltreeoflife.com/wp-content/uploads/2021/09/TOL-Logo-1.png" alt="Tree of Life" />
      <img src="https://cocogreen.com/wp-content/uploads/2024/10/dbc686a8-4096-4da4-9317-9afd4e96cf94.png" alt="Coco Green" />
    </div>
    <a href="https://ekrain.lk/" class="view-more">View More</a>
  </div>
</section>


<footer>
  @Design and Developed by Pasan Aberathna (+94 777919198 / cloudforce98@gmail.com)
  <div class="datetime" id="datetime"></div>
</footer>

<script>
  function updateDateTime() {
    const now = new Date();
    const options = { 
      weekday: 'long', year: 'numeric', month: 'long', 
      day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' 
    };
    document.getElementById('datetime').innerText = now.toLocaleDateString('en-US', options);
  }

  updateDateTime();
  setInterval(updateDateTime, 1000); // Update every second
</script>

</body>
</html>
