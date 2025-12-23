<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'buyer') {
  http_response_code(401);
  echo json_encode(["reply" => "Please login as a buyer first."]);
  exit();
}

/* ------------------ Local info replies (NO API) ------------------ */
function is_greeting($msg) {
  $m = strtolower(trim($msg));
  return in_array($m, ["hi","hello","hey","hlw","hii","assalamualaikum","as-salamu alaikum","salam"]);
}

function local_reply($msg) {
  $m = strtolower(trim($msg));

  if (is_greeting($m)) {
    return "Hello! 👋 Welcome to our Real Estate website.\n\nYou can:\n• Search properties (example: Flat in Sylhet under 60 lakh, 3 bed)\n• View property details\n• Request a visit (📅 Request Visit)\n\nAsk me to search like: “House in Khulna under 1 crore, 4 bed”.";
  }

  if (strpos($m, "website") !== false || strpos($m, "about") !== false) {
    return "This is a Bangladesh Real Estate platform. Buyers can search properties by location, price, size, bedrooms and type (Flat/House/Land/Commercial). You can view details and request visits from listings.";
  }

  if (strpos($m, "visit") !== false || strpos($m, "appointment") !== false) {
    return "To request a visit: open a property → click “📅 Request Visit” → submit the form. The seller/agent will contact you.";
  }

  return "";
}

/* ------------------ Helpers: parse BD money + sizes ------------------ */
function parse_bd_money_to_int($txt) {
  $t = strtolower(trim($txt));
  $t = str_replace([",","৳","tk","taka","bdt"], "", $t);

  // 50lac / 50 lakh / 50 lacs
  if (preg_match('/(\d+(\.\d+)?)\s*(lakh|lac|lacs)/', $t, $m)) {
    return (int) round(floatval($m[1]) * 100000);
  }
  // 1cr / 1 crore
  if (preg_match('/(\d+(\.\d+)?)\s*(crore|cr)/', $t, $m)) {
    return (int) round(floatval($m[1]) * 10000000);
  }
  // plain number
  if (preg_match('/\d+/', $t, $m)) {
    return (int) preg_replace('/[^\d]/', '', $m[0]);
  }
  return null;
}

function normalize_property_type($msgLower) {
  if (strpos($msgLower, "flat") !== false || strpos($msgLower, "apartment") !== false) return "Flat";
  if (strpos($msgLower, "house") !== false || strpos($msgLower, "villa") !== false) return "House";
  if (strpos($msgLower, "land") !== false || strpos($msgLower, "plot") !== false) return "Land";
  if (strpos($msgLower, "commercial") !== false || strpos($msgLower, "shop") !== false || strpos($msgLower, "office") !== false) return "Commercial";
  return null;
}

function normalize_category($msgLower) {
  // If they said commercial explicitly, we can set category Commercial too.
  if (strpos($msgLower, "commercial") !== false) return "Commercial";
  if (strpos($msgLower, "residential") !== false) return "Residential";
  return null;
}

function parse_bedrooms($msgLower) {
  // "4 bed", "3 bedrooms", "2 room"
  if (preg_match('/\b([1-4])\s*(bed|beds|bedroom|bedrooms|room|rooms)\b/', $msgLower, $m)) {
    return (int)$m[1];
  }
  // "4+ bed" or "4 plus bed" -> use 4 (your UI treats 4 as 4+)
  if (preg_match('/\b4\s*(\+|plus)\s*(bed|beds|bedroom|bedrooms|room|rooms)\b/', $msgLower)) {
    return 4;
  }
  return null;
}

function parse_size_range($msgLower) {
  // "2000-3000 sqft"
  if (preg_match('/\b(\d{2,6})\s*-\s*(\d{2,6})\s*(sq\s*ft|sqft|square\s*feet)\b/', $msgLower, $m)) {
    return ["min_size" => (int)$m[1], "max_size" => (int)$m[2]];
  }
  // "under 2000 sqft"
  if (preg_match('/\b(under|below|max)\s*(\d{2,6})\s*(sq\s*ft|sqft|square\s*feet)\b/', $msgLower, $m)) {
    return ["max_size" => (int)$m[2]];
  }
  // "min 1200 sqft"
  if (preg_match('/\b(min|at least)\s*(\d{2,6})\s*(sq\s*ft|sqft|square\s*feet)\b/', $msgLower, $m)) {
    return ["min_size" => (int)$m[2]];
  }
  return [];
}

function parse_price_range($msgLower) {
  $out = [];

  // "between 50 lakh and 80 lakh"
  if (preg_match('/\bbetween\s+(.+?)\s+and\s+(.+?)\b/', $msgLower, $m)) {
    $min = parse_bd_money_to_int($m[1]);
    $max = parse_bd_money_to_int($m[2]);
    if ($min !== null) $out["min_price"] = $min;
    if ($max !== null) $out["max_price"] = $max;
    return $out;
  }

  // "under 60 lakh", "below 1 crore", "max 70 lac"
  if (preg_match('/\b(under|below|max)\s+([0-9\.]+\s*(lakh|lac|lacs|crore|cr)|\d{4,})\b/', $msgLower, $m)) {
    $max = parse_bd_money_to_int($m[2]);
    if ($max !== null) $out["max_price"] = $max;
    return $out;
  }

  // "min 30 lakh"
  if (preg_match('/\b(min|at least)\s+([0-9\.]+\s*(lakh|lac|lacs|crore|cr)|\d{4,})\b/', $msgLower, $m)) {
    $min = parse_bd_money_to_int($m[2]);
    if ($min !== null) $out["min_price"] = $min;
    return $out;
  }

  // fallback: if they mention a money value without under/min, ignore (avoid wrong filters)
  return $out;
}

function parse_location($msgLower) {
  // supports: "in sylhet", "at bashundhara", "location: sylhet"
  if (preg_match('/\b(in|at|near)\s+([a-z0-9\'\.\-\s]{2,40})\b/', $msgLower, $m)) {
    $loc = trim($m[2]);
    // stopwords to trim
    $loc = preg_replace('/\b(under|below|max|min|bed|beds|bedroom|bedrooms|room|rooms|sqft|square|feet|price|tk|taka|lakh|lac|crore|cr)\b.*/', '', $loc);
    $loc = trim($loc, " ,.");
    if ($loc !== "") return ucwords($loc);
  }

  // supports: "Flat Bashundhara under 60 lakh" (no "in")
  if (preg_match('/\b(flat|house|land|commercial)\s+in?\s*([a-z0-9\'\.\-\s]{2,40})\b/', $msgLower, $m)) {
    $loc = trim($m[2]);
    $loc = preg_replace('/\b(under|below|max|min|bed|beds|bedroom|bedrooms|room|rooms|sqft|square|feet|price|tk|taka|lakh|lac|crore|cr)\b.*/', '', $loc);
    $loc = trim($loc, " ,.");
    if ($loc !== "") return ucwords($loc);
  }

  return null;
}

/* ------------------ Main ------------------ */
$raw = file_get_contents("php://input");
$body = json_decode($raw, true);
$userMsg = trim($body['message'] ?? '');

if ($userMsg === '') {
  echo json_encode(["reply" => "Please type a message."]);
  exit();
}

$quick = local_reply($userMsg);
if ($quick !== "") {
  echo json_encode(["reply" => $quick]);
  exit();
}

$msgLower = strtolower($userMsg);

$filters = [];

$ptype = normalize_property_type($msgLower);
if ($ptype) $filters["property_type"] = $ptype;

$cat = normalize_category($msgLower);
if ($cat) $filters["category"] = $cat;

$bed = parse_bedrooms($msgLower);
if ($bed) $filters["bedrooms"] = $bed;

$size = parse_size_range($msgLower);
$filters = array_merge($filters, $size);

$price = parse_price_range($msgLower);
$filters = array_merge($filters, $price);

$loc = parse_location($msgLower);
if ($loc) $filters["location"] = $loc;

/* If no filters detected, help user */
if (count($filters) === 0) {
  echo json_encode([
    "reply" => "I can help you search! Try:\n• Flat in Sylhet under 60 lakh, 3 bed\n• House in Khulna under 1 crore, 4 bed\n• Land in Cox's Bazar under 40 lakh"
  ]);
  exit();
}

/* Build dashboard link */
$query = http_build_query($filters);
$link = "buyer_dashboard.php" . ($query ? ("?" . $query) : "");

echo json_encode([
  "reply" => "✅ Applying your search…",
  "link"  => $link
]);
