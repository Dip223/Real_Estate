<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: index.php");
    exit();
}

/* -------------------------
   SEARCH FILTER CONDITIONS
--------------------------*/

$where = [];

if(!empty($_GET['location'])){
    $location = mysqli_real_escape_string($conn, $_GET['location']);
    $where[] = "location LIKE '%$location%'";
}

if(!empty($_GET['property_type'])){
    $ptype = $_GET['property_type'];
    $where[] = "property_type = '$ptype'";
}

if(!empty($_GET['bedrooms'])){
    $bed = $_GET['bedrooms'];
    $where[] = "bedrooms = '$bed'";
}

if(!empty($_GET['min_size'])){
    $min = $_GET['min_size'];
    $where[] = "square_feet >= '$min'";
}

if(!empty($_GET['max_size'])){
    $max = $_GET['max_size'];
    $where[] = "square_feet <= '$max'";
}

if(!empty($_GET['min_price'])){
    $pmin = $_GET['min_price'];
    $where[] = "price >= '$pmin'";
}

if(!empty($_GET['max_price'])){
    $pmax = $_GET['max_price'];
    $where[] = "price <= '$pmax'";
}

if(!empty($_GET['category'])){
    $cat = $_GET['category'];
    $where[] = "category = '$cat'";
}

$sql = "SELECT * FROM property";

// If filters are used -> Add WHERE
if(count($where) > 0){
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Default sort: newest
$sql .= " ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);

/* -------------------------
   AI ANALYSIS DATA
--------------------------*/

// Average price per sq ft (AI baseline)
$avgQuery = mysqli_query($conn,"
    SELECT AVG(price/square_feet) AS avg_pps 
    FROM property
    WHERE square_feet > 0
");
$avgPPS = mysqli_fetch_assoc($avgQuery)['avg_pps'];

// User budget (if given)
$userMin = $_GET['min_price'] ?? 0;
$userMax = $_GET['max_price'] ?? 999999999;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Buyer Dashboard - Find Properties</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f3f5f8; }
        .navbar { width: 100%; background: #0d6efd; padding: 18px; color: white; font-size: 22px; font-weight: bold; text-align: center; letter-spacing: 1px; }
        .container { width: 92%; margin: 30px auto; }

        .filter-box {
            background: white; padding: 18px; border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 12px;
        }
        .filter-box input, .filter-box select {
            padding: 10px; border-radius: 8px; border: 1px solid #ccc; width: 180px;
        }
        .filter-box button {
            padding: 10px 30px; background: #198754; color: white;
            border: none; border-radius: 8px; cursor: pointer;
        }

        .property-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }

        .card {
            background: white; border-radius: 18px; overflow: hidden;
            box-shadow: 0 5px 18px rgba(0,0,0,0.12);
            transition: 0.35s; transform: translateY(0);
        }
        .card:hover { transform: translateY(-8px); box-shadow: 0 12px 26px rgba(0,0,0,0.20); }
        .card img { width: 100%; height: 210px; object-fit: cover; transition: 0.4s; }
        .card:hover img { transform: scale(1.06); }

        .card-content { padding: 18px; }
        .card h3 { margin: 0; color: #333; font-size: 20px; }
        .price { color: #0d6efd; font-weight: bold; font-size: 18px; margin: 10px 0; }
        .info { font-size: 14px; color: #555; margin: 5px 0; }

        .btn {
            display: inline-block; width: 100%; padding: 12px;
            background: #0d6efd; color: white; text-align: center;
            border-radius: 8px; text-decoration: none; margin-top: 12px; font-size: 15px;
        }
        .btn:hover { background: #084298; }
        .logout { background: red !important; margin-top: 20px; }

        /* ================= CHATBOT UI CSS ================= */
        #chatbot-bubble{
          position:fixed; right:18px; bottom:18px;
          width:56px; height:56px; border-radius:50%;
          background:#0d6efd; color:#fff;
          display:flex; align-items:center; justify-content:center;
          font-size:24px; cursor:pointer;
          box-shadow:0 10px 25px rgba(0,0,0,.2);
          z-index:9999;
        }
        #chatbot-panel{
          position:fixed; right:18px; bottom:86px;
          width:330px; height:420px;
          background:#fff; border-radius:14px;
          box-shadow:0 12px 30px rgba(0,0,0,.22);
          display:none; flex-direction:column;
          overflow:hidden; z-index:9999;
        }
        #chatbot-header{
          background:#0d6efd; color:#fff;
          padding:12px 14px;
          display:flex; justify-content:space-between;
          align-items:center; font-weight:600;
        }
        #chatbot-header button{
          background:none; border:0;
          color:#fff; font-size:18px; cursor:pointer;
        }
        #chatbot-messages{
          padding:12px; flex:1;
          overflow-y:auto; background:#f7f9fc;
        }
        .msg{margin:8px 0; display:flex;}
        .msg.user{justify-content:flex-end;}
        .bubble{
          max-width:82%; padding:10px 12px;
          border-radius:12px; font-size:14px;
        }
        .msg.user .bubble{
          background:#0d6efd; color:#fff;
          border-bottom-right-radius:4px;
        }
        .msg.bot .bubble{
          background:#fff; color:#333;
          border:1px solid #e5e8ef;
          border-bottom-left-radius:4px;
        }
        #chatbot-form{
          display:flex; gap:8px;
          padding:10px; border-top:1px solid #e9eef7;
        }
        #chatbot-input{
          flex:1; padding:10px;
          border-radius:10px;
          border:1px solid #cfd6e4;
        }
        #chatbot-form button{
          padding:10px 14px;
          border-radius:10px;
          border:0; background:#198754;
          color:#fff; cursor:pointer;
        }
    </style>
</head>
<body>

<div class="navbar">
    🔍 Find Your Perfect Property
</div>

<div class="container">

    <!-- FILTER UI -->
    <form method="GET" class="filter-box">
        <input type="text" name="location" placeholder="City / Area">
        <select name="property_type">
            <option value="">Any Type</option>
            <option value="Flat">Flat</option>
            <option value="Land">Land</option>
            <option value="House">House</option>
            <option value="Commercial">Commercial</option>
        </select>

        <select name="bedrooms">
            <option value="">Rooms</option>
            <option value="1">1 Room</option>
            <option value="2">2 Rooms</option>
            <option value="3">3 Rooms</option>
            <option value="4">4+ Rooms</option>
        </select>

        <input type="number" name="min_size" placeholder="Min Sq Ft">
        <input type="number" name="max_size" placeholder="Max Sq Ft">
        <input type="number" name="min_price" placeholder="Min Price">
        <input type="number" name="max_price" placeholder="Max Price">

        <select name="category">
            <option value="">Any Category</option>
            <option value="Residential">Residential</option>
            <option value="Commercial">Commercial</option>
        </select>

        <button type="submit">Search</button>
    </form>

    <!-- PROPERTY GRID -->
    <div class="property-grid">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="card">
                <img src="uploads/<?php echo $row['image']; ?>" onerror="this.src='no-image.jpg'">
                <div class="card-content">
                    <h3><?php echo $row['title']; ?></h3>
                    <div class="price">৳ <?php echo number_format($row['price']); ?></div>
                    <div class="info">📍 <?php echo $row['location']; ?></div>
                    <div class="info">🏠 <?php echo $row['property_type']; ?> — <?php echo $row['bedrooms']; ?> Beds</div>
                    <div class="info">📌 <?php echo $row['square_feet']; ?> Sq Ft</div>

                    <a href="property_details.php?id=<?php echo $row['property_id']; ?>" class="btn">View Details</a>
                    <a href="request_appointment.php?property_id=<?php echo $row['property_id']; ?>" class="btn">📅 Request Visit</a>
                </div>
            </div>
        <?php } ?>
    </div>

    <a href="logout.php" class="btn logout">Logout</a>
</div>

<!-- ================= CHATBOT UI START ================= -->
<div id="chatbot-bubble">💬</div>

<div id="chatbot-panel">
  <div id="chatbot-header">
    <div>Property Assistant</div>
    <button id="chatbot-close">✕</button>
  </div>

  <div id="chatbot-messages"></div>

  <form id="chatbot-form">
    <input id="chatbot-input" type="text"
           placeholder="Example: Flat in Sylhet under 60 lakh, 3 bed"
           autocomplete="off" />
    <button type="submit">Send</button>
  </form>
</div>

<script>
const bubble = document.getElementById('chatbot-bubble');
const panel  = document.getElementById('chatbot-panel');
const closeB = document.getElementById('chatbot-close');
const form   = document.getElementById('chatbot-form');
const input  = document.getElementById('chatbot-input');
const msgs   = document.getElementById('chatbot-messages');

function addMsg(text, who){
  const row = document.createElement('div');
  row.className = 'msg ' + who;
  const b = document.createElement('div');
  b.className = 'bubble';
  b.textContent = text;
  row.appendChild(b);
  msgs.appendChild(row);
  msgs.scrollTop = msgs.scrollHeight;
}

bubble.onclick = () => {
  panel.style.display = 'flex';
  bubble.style.display = 'none';
  if(msgs.children.length === 0){
    addMsg("Hi! Type like: Flat in Sylhet under 60 lakh, 3 bed", "bot");
  }
};

closeB.onclick = () => {
  panel.style.display = 'none';
  bubble.style.display = 'flex';
};

form.onsubmit = async (e) => {
  e.preventDefault();
  const text = input.value.trim();
  if(!text) return;

  addMsg(text, "user");
  input.value = "";

  addMsg("Typing...", "bot");
  const typing = msgs.lastChild;

  try{
    const res = await fetch("chatbot.php", {
      method: "POST",
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify({message:text})
    });

    const data = await res.json();

    // ✅ AUTO REDIRECT TO APPLY FILTERS
    if (data.link) {
      //typing.querySelector(".bubble").textContent = "✅ Applying your search…";
      window.location.href = data.link;
    } else {
      typing.querySelector(".bubble").textContent = data.reply || "No reply.";
    }

  }catch{
    typing.querySelector(".bubble").textContent = "Server error.";
  }
};
</script>
<!-- ================= CHATBOT UI END ================= -->

</body>
</html>
