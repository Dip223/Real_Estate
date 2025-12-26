
<?php
session_start();
include 'db.php';

/**
 * OSM Nominatim geocode (FREE) + file cache to avoid repeated lookups
 */
function geocode_osm($place) {
    $place = trim((string)$place);
    if ($place === '') return null;

    $cacheFile = __DIR__ . "/geocode_cache.json";
    $cache = [];

    if (file_exists($cacheFile)) {
        $json = file_get_contents($cacheFile);
        $cache = json_decode($json, true);
        if (!is_array($cache)) $cache = [];
    }

    if (isset($cache[$place])) {
        return $cache[$place];
    }

    $query = $place . ", Bangladesh";
    $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=" . urlencode($query);

    $resp = false;

    $opts = [
        "http" => [
            "header" => "User-Agent: real_estate_project/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $resp = @file_get_contents($url, false, $context);

    if ($resp === false && function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: real_estate_project/1.0"]);
        $resp = curl_exec($ch);
        curl_close($ch);
    }

    if ($resp === false) return null;

    $data = json_decode($resp, true);
    if (!is_array($data) || count($data) === 0) return null;

    $coords = [
        "lat" => (float)$data[0]["lat"],
        "lng" => (float)$data[0]["lon"]
    ];

    $cache[$place] = $coords;
    @file_put_contents($cacheFile, json_encode($cache, JSON_PRETTY_PRINT));

    return $coords;
}

// Fetch latest properties
$sql = "SELECT property_id, title, price, location, image
        FROM property
        ORDER BY created_at DESC
        LIMIT 12";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}

$properties = [];
$propertiesForMap = [];
$coordsById = [];

while ($row = mysqli_fetch_assoc($result)) {
    $properties[] = $row;

    $coords = geocode_osm($row['location']);
    if ($coords) {
        $pid = (int)$row["property_id"];
        $coordsById[$pid] = $coords;

        $propertiesForMap[] = [
            "id" => $pid,
            "title" => $row["title"],
            "price" => (float)$row["price"],
            "location" => $row["location"],
            "lat" => $coords["lat"],
            "lng" => $coords["lng"]
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Real Estate Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <style>
    :root{
      --bg:#0b1220;
      --panel: rgba(255,255,255,.06);
      --panel2: rgba(255,255,255,.10);
      --text:#e8eefc;
      --muted:rgba(232,238,252,.7);
      --brand:#ff3b30;
      --brand2:#ff7a00;
      --line:rgba(255,255,255,.12);
      --shadow: 0 18px 50px rgba(0,0,0,.35);
      --radius:18px;
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:'Inter',sans-serif;
      background: radial-gradient(1200px 600px at 20% 0%, rgba(255,59,48,.18), transparent 55%),
                  radial-gradient(900px 600px at 80% 10%, rgba(255,122,0,.14), transparent 55%),
                  radial-gradient(900px 600px at 50% 90%, rgba(0,180,255,.10), transparent 60%),
                  var(--bg);
      color:var(--text);
      overflow-x:hidden;
    }

    /* Animated gradient glow */
    .glow{
      position:fixed; inset:-30%;
      background: conic-gradient(from 90deg, rgba(255,59,48,.18), rgba(255,122,0,.16), rgba(0,180,255,.14), rgba(255,59,48,.18));
      filter: blur(90px);
      animation: spin 18s linear infinite;
      z-index:-2;
      opacity:.6;
    }
    @keyframes spin{to{transform:rotate(360deg)}}

    /* Navbar */
    header{
      position:sticky; top:0; z-index:50;
      backdrop-filter: blur(14px);
      background: rgba(10,16,30,.55);
      border-bottom:1px solid var(--line);
    }
    .navwrap{
      max-width:1180px;
      margin:0 auto;
      padding:14px 18px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:12px;
    }
    .logo{
      display:flex; align-items:center; gap:10px;
      font-weight:800; letter-spacing:.2px;
    }
    .logo-badge{
      width:38px; height:38px;
      border-radius:12px;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 10px 25px rgba(255,59,48,.25);
    }
    nav a{
      color:var(--text);
      text-decoration:none;
      font-weight:600;
      padding:10px 12px;
      border-radius:12px;
      transition:.2s ease;
    }
    nav a:hover{
      background: rgba(255,255,255,.08);
      transform: translateY(-1px);
    }
    .btn{
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 12px 30px rgba(255,59,48,.22);
      border:0;
      color:white !important;
    }

    /* Hero */
    .hero{
      max-width:1180px;
      margin:0 auto;
      padding:40px 18px 18px;
      display:grid;
      grid-template-columns: 1.2fr .8fr;
      gap:18px;
      align-items:stretch;
    }
    @media (max-width: 900px){
      .hero{grid-template-columns:1fr}
    }
    .hero-card{
      border:1px solid var(--line);
      border-radius: var(--radius);
      background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.04));
      box-shadow: var(--shadow);
      padding:26px;
      position:relative;
      overflow:hidden;
    }
    .hero-card::after{
      content:"";
      position:absolute; inset:-60px -80px auto auto;
      width:220px; height:220px;
      background: radial-gradient(circle at 30% 30%, rgba(255,122,0,.35), transparent 60%);
      filter: blur(10px);
      transform: rotate(15deg);
      opacity:.9;
    }
    .hero h1{
      margin:0;
      font-size: clamp(28px, 3.2vw, 44px);
      line-height:1.08;
      letter-spacing:-.6px;
    }
    .hero p{
      margin:12px 0 0;
      color:var(--muted);
      max-width:56ch;
    }

    .search{
      margin-top:18px;
      display:flex;
      gap:12px;
      background: rgba(255,255,255,.06);
      border:1px solid var(--line);
      border-radius: 16px;
      padding:10px;
      align-items:center;
    }
    .search input{
      flex:1;
      background:transparent;
      border:0;
      outline:0;
      padding:10px 12px;
      color:var(--text);
      font-size:15px;
    }
    .search input::placeholder{color: rgba(232,238,252,.55)}
    .search button{
      padding:10px 14px;
      border-radius:14px;
      border:0;
      font-weight:700;
      color:white;
      cursor:pointer;
      background: linear-gradient(135deg, rgba(255,59,48,.95), rgba(255,122,0,.95));
      transition:.2s ease;
    }
    .search button:hover{ transform: translateY(-1px); opacity:.95; }

    .stats{
      display:flex; gap:12px; margin-top:16px; flex-wrap:wrap;
    }
    .pill{
      border:1px solid var(--line);
      background: rgba(255,255,255,.06);
      border-radius:999px;
      padding:10px 12px;
      font-weight:700;
      color: rgba(232,238,252,.88);
      display:flex; gap:8px;
    }
    .dot{
      width:10px; height:10px; border-radius:99px;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      margin-top:4px;
    }

    .side-card{
      border:1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255,255,255,.05);
      box-shadow: var(--shadow);
      padding:18px;
      display:flex;
      flex-direction:column;
      gap:12px;
    }
    .side-card h3{margin:0; font-size:16px}
    .side-actions{
      display:flex; gap:10px; flex-wrap:wrap;
    }
    .mini{
      flex:1;
      text-align:center;
      padding:10px 12px;
      border-radius:14px;
      border:1px solid var(--line);
      background: rgba(255,255,255,.06);
      color:var(--text);
      font-weight:700;
      text-decoration:none;
      transition:.2s ease;
    }
    .mini:hover{transform:translateY(-1px); background:rgba(255,255,255,.09)}

    /* Content */
    .container{
      max-width:1180px;
      margin:0 auto;
      padding:18px 18px 60px;
    }
    .section-title{
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap:10px;
      margin:16px 0 10px;
    }
    .section-title h2{
      margin:0;
      font-size:20px;
      letter-spacing:-.2px;
    }
    .section-title span{
      color:var(--muted);
      font-size:13px;
      font-weight:600;
    }

    .grid{
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap:16px;
      margin-top:14px;
    }

    .card{
      border-radius: var(--radius);
      overflow:hidden;
      border:1px solid var(--line);
      background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.04));
      box-shadow: 0 18px 44px rgba(0,0,0,.28);
      cursor:pointer;
      transform: translateY(8px);
      opacity:0;
      transition: transform .35s ease, opacity .35s ease, box-shadow .25s ease;
      position:relative;
    }
    .card.reveal{
      transform: translateY(0);
      opacity:1;
    }
    .card:hover{
      transform: translateY(-4px) scale(1.01);
      box-shadow: 0 26px 70px rgba(0,0,0,.35);
    }
    .thumb{
      height:190px;
      overflow:hidden;
      position:relative;
    }
    .thumb img{
      width:100%;
      height:100%;
      object-fit:cover;
      transform: scale(1.02);
      transition: transform .5s ease;
      display:block;
    }
    .card:hover .thumb img{ transform: scale(1.10); }

    .tag{
      position:absolute;
      top:12px; left:12px;
      background: rgba(10,16,30,.72);
      border:1px solid rgba(255,255,255,.14);
      padding:8px 10px;
      border-radius:999px;
      font-weight:800;
      font-size:12px;
      letter-spacing:.2px;
      backdrop-filter: blur(10px);
    }
    .card-body{
      padding:14px 14px 16px;
    }
    .price{
      font-size:18px;
      font-weight:900;
      letter-spacing:-.2px;
    }
    .location{
      margin-top:8px;
      color:var(--muted);
      font-weight:600;
      font-size:13px;
    }
    .actions{
      margin-top:12px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
    }
    .details-link{
      color: rgba(190,220,255,.98);
      text-decoration:none;
      font-weight:800;
      padding:10px 12px;
      border-radius:14px;
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.06);
      transition:.2s ease;
      white-space:nowrap;
    }
    .details-link:hover{ background: rgba(255,255,255,.10); transform: translateY(-1px); }

    .map-wrap{
      margin-top:22px;
      border:1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255,255,255,.04);
      box-shadow: var(--shadow);
      padding:14px;
    }
    #map{
      width:100%;
      height:430px;
      border-radius: calc(var(--radius) - 6px);
      overflow:hidden;
    }

    footer{
      border-top:1px solid var(--line);
      background: rgba(10,16,30,.60);
      backdrop-filter: blur(12px);
      padding:18px;
      color: rgba(232,238,252,.75);
      text-align:center;
    }

    /* smooth focus ring */
    .focus-ring{
      outline: 2px solid rgba(255,122,0,.65);
      outline-offset: 2px;
    }
  </style>
</head>

<body>
<div class="glow"></div>

<header>
  <div class="navwrap">
    <div class="logo">
      <div class="logo-badge"></div>
      <div>RealEstate</div>
    </div>

    <nav>
      <a href="index.php">Buy</a>
      <a href="seller_dashboard.php">Sell</a>

      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="btn">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php" class="btn">Sign Up</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<section class="hero">
  <div class="hero-card">
    <h1>Find your next home in minutes — beautiful listings, real locations.</h1>
    <p>Browse the latest properties, explore on the map, and zoom directly from any card. Fast, clean, modern UI.</p>

    <div class="search">
      <input type="text" placeholder="Search city, area, address... (UI only)">
      <button type="button">Search</button>
    </div>

    <div class="stats">
      <div class="pill"><span class="dot"></span> Verified Listings</div>
      <div class="pill"><span class="dot"></span> Map Preview</div>
      <div class="pill"><span class="dot"></span> Smooth Animations</div>
    </div>
  </div>

  <div class="side-card">
    <h3>Quick Actions</h3>
    <div class="side-actions">
      <a class="mini" href="index.php">Buy</a>
      <a class="mini" href="seller_dashboard.php">Sell</a>
      <a class="mini" href="login.php">Login</a>
      <a class="mini btn" href="register.php">Register</a>
    </div>
    <div style="color:rgba(232,238,252,.75); font-weight:600; font-size:13px; line-height:1.4;">
      Tip: Click any property card to zoom the map and open its popup instantly.
    </div>
  </div>
</section>

<div class="container">
  <div class="section-title">
    <h2>Latest Properties</h2>
    <span>Click a card → zoom on map</span>
  </div>

  <div class="grid">
    <?php if(count($properties) > 0): ?>
      <?php foreach($properties as $row): ?>
        <?php
          $pid = (int)$row['property_id'];
          $img = (!empty($row['image'])) ? "uploads/" . $row['image'] : "uploads/no-image.jpg";

          $lat = '';
          $lng = '';
          if (isset($coordsById[$pid])) {
            $lat = $coordsById[$pid]['lat'];
            $lng = $coordsById[$pid]['lng'];
          }
        ?>
        <div class="card property-card"
             data-id="<?= $pid ?>"
             data-lat="<?= htmlspecialchars($lat) ?>"
             data-lng="<?= htmlspecialchars($lng) ?>">
          <div class="thumb">
            <img src="<?= htmlspecialchars($img) ?>" alt="Property">
            <div class="tag">Featured</div>
          </div>
          <div class="card-body">
            <div class="price">৳<?= number_format((float)$row['price']); ?></div>
            <div class="location"><?= htmlspecialchars($row['location']); ?></div>
            <div class="actions">
              <a class="details-link" href="property_details.php?id=<?= $pid; ?>">View Details</a>
              <span style="color:rgba(232,238,252,.55); font-weight:700; font-size:12px;">#<?= $pid ?></span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No properties found.</p>
    <?php endif; ?>
  </div>

  <div class="section-title" style="margin-top:24px;">
    <h2>Explore on Map</h2>
    <span>Markers are loaded from your database</span>
  </div>

  <div class="map-wrap">
    <div id="map"></div>
  </div>
</div>

<footer>
  © <?= date('Y'); ?> Real Estate Portal — Built with Leaflet + OpenStreetMap
</footer>

<script>
document.addEventListener("DOMContentLoaded", function () {

  // Reveal animation for cards on load
  const cards = document.querySelectorAll(".card");
  cards.forEach((c, i) => setTimeout(() => c.classList.add("reveal"), 70 * i));

  // Leaflet map
  var map = L.map('map').setView([23.8103, 90.4125], 11);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(map);

  var properties = <?php echo json_encode($propertiesForMap, JSON_UNESCAPED_UNICODE); ?>;

  // marker registry
  var markerMap = {};

  if (properties.length > 0) {
    var bounds = [];

    properties.forEach(function(p) {
      var marker = L.marker([p.lat, p.lng]).addTo(map);

      marker.bindPopup(`
        <div style="font-family:Inter,sans-serif; min-width:180px">
          <div style="font-weight:900; font-size:14px; margin-bottom:4px;">${p.title}</div>
          <div style="font-weight:800; color:#ff3b30; margin-bottom:4px;">৳${Number(p.price).toLocaleString()}</div>
          <div style="opacity:.85; font-weight:600; margin-bottom:8px;">${p.location}</div>
          <a href="property_details.php?id=${p.id}" style="font-weight:900; text-decoration:none; color:#2aa3ff;">
            View Details →
          </a>
        </div>
      `);

      markerMap[String(p.id)] = marker;
      bounds.push([p.lat, p.lng]);
    });

    map.fitBounds(bounds, { padding: [30, 30] });
  }

  // stop card click when clicking link
  document.querySelectorAll(".details-link").forEach(function(a){
    a.addEventListener("click", function(e){ e.stopPropagation(); });
  });

  // card click -> zoom + open popup
  document.querySelectorAll(".property-card").forEach(function(card){
    card.addEventListener("click", function(){
      var id  = this.dataset.id;
      var lat = this.dataset.lat;
      var lng = this.dataset.lng;

      if (!lat || !lng || !markerMap[String(id)]) return;

      // highlight selected card
      document.querySelectorAll(".property-card").forEach(c => c.classList.remove("focus-ring"));
      this.classList.add("focus-ring");

      map.setView([parseFloat(lat), parseFloat(lng)], 16, { animate: true });

      setTimeout(function(){
        markerMap[String(id)].openPopup();
      }, 200);
    });
  });

});
</script>

</body>
</html>

