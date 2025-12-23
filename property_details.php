<?php
session_start();
include("db.php");

if(!isset($_GET['id'])){
    die("Property ID missing");
}

$pid = (int)$_GET['id'];

// fetch property + seller name
$property_sql = "SELECT p.*, u.name AS seller_name 
                 FROM property p
                 LEFT JOIN users u ON p.seller_id = u.user_id
                 WHERE p.property_id = $pid";
$prop_res = mysqli_query($conn, $property_sql);
if(mysqli_num_rows($prop_res) == 0){
    die("Property not found");
}
$property = mysqli_fetch_assoc($prop_res);

// average rating
$avg_sql = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE property_id = $pid";
$avg_res = mysqli_query($conn, $avg_sql);
$avg_row = mysqli_fetch_assoc($avg_res);
$avg_rating = $avg_row['avg_rating'] ? round($avg_row['avg_rating'],1) : 0;
$total_reviews = (int)$avg_row['total_reviews'];

// fetch reviews with buyer name
$reviews_sql = "SELECT r.*, u.name AS buyer_name FROM reviews r
                LEFT JOIN users u ON r.buyer_id = u.user_id
                WHERE r.property_id = $pid
                ORDER BY r.created_at DESC";
$reviews_res = mysqli_query($conn, $reviews_sql);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($property['title']); ?> — Property Details</title>
    <style>
        /* Glassmorphism + modern layout (Style E) */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        :root{
            --glass-bg: rgba(255,255,255,0.08);
            --glass-border: rgba(255,255,255,0.14);
            --accent: #6c5ce7;
            --muted: #94a3b8;
            --page-bg: linear-gradient(135deg,#0f172a 0%, #0b1220 100%);
        }
        html,body{ height:100%; margin:0; font-family: 'Inter', sans-serif; background:var(--page-bg); color:#e6eef8; }

        .wrap{ width:92%; margin:30px auto; display:grid; grid-template-columns: 1fr 390px; gap:30px; align-items:start; }

        .glass{
            background: rgba(255,255,255,0.06);
            border-radius: 14px;
            padding:18px;
            backdrop-filter: blur(8px) saturate(140%);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 8px 30px rgba(2,6,23,0.6);
        }

        .gallery{
            display:grid;
            grid-template-columns: 1fr 120px;
            gap:12px;
            margin-bottom:12px;
        }

        .main-img{
            height:360px;
            border-radius:12px;
            overflow:hidden;
        }
        .main-img img{
            width:100%;
            height:100%;
            object-fit:cover;
            display:block;
        }

        .thumbs{
            display:flex;
            flex-direction:column;
            gap:10px;
        }
        .thumbs img{
            width:100%;
            height:110px;
            object-fit:cover;
            border-radius:8px;
            cursor:pointer;
            border:2px solid transparent;
            transition:transform .18s, border-color .18s;
        }
        .thumbs img:hover{ transform:scale(1.03); border-color:rgba(255,255,255,0.12); }

        .title-row{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:6px; }
        .title-row h1{ margin:0; font-size:22px; color:#fff; }
        .seller{ color:var(--muted); font-size:14px; }

        .price{ color:#7ee787; font-weight:700; font-size:20px; margin-top:8px; }

        .badges{ margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; }
        .badge{
            background: linear-gradient(90deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
            padding:8px 12px;
            border-radius:999px;
            border:1px solid rgba(255,255,255,0.04);
            color:#e6eef8;
            font-size:13px;
        }

        .desc{ margin-top:14px; color:#dbeafe; line-height:1.6; }

        /* right side */
        .side{
            display:flex;
            flex-direction:column;
            gap:14px;
        }

        .card-side{
            background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
            border-radius:12px;
            padding:14px;
            border:1px solid rgba(255,255,255,0.06);
        }

        .stars{ font-size:18px; color:#ffd166; font-weight:700; }

        .contact-btn{
            background: linear-gradient(90deg,#6c5ce7,#5a55f5);
            color:white;
            padding:12px;
            border-radius:10px;
            text-decoration:none;
            text-align:center;
            display:block;
            font-weight:700;
            margin-top:10px;
        }

        .reviews-list{ margin-top:8px; max-height:380px; overflow:auto; padding-right:8px; }

        .review{
            background: rgba(255,255,255,0.02);
            border-radius:10px;
            padding:10px;
            margin-bottom:10px;
            border:1px solid rgba(255,255,255,0.03);
        }

        .review .meta{ font-size:13px; color:var(--muted); margin-bottom:6px; }

        .add-review{
            display:block;
            padding:10px;
            text-align:center;
            border-radius:8px;
            color:#c7d2fe;
            border:1px dashed rgba(199,210,254,0.12);
            margin-top:8px;
            text-decoration:none;
        }

    </style>
    <script>
        function swapMain(src){
            document.getElementById('mainImage').src = src;
        }
    </script>
</head>
<body>

<div style="width:92%; margin:22px auto;">
    <a href="buyer_dashboard.php" style="color:#dbeafe; text-decoration:none;">← Back to listings</a>
</div>

<div class="wrap">

    <!-- LEFT PANEL -->
    <div class="glass">

        <!-- IMAGE GALLERY -->
        <div class="gallery">
            <div class="main-img">

                <?php 
                // FIXED IMAGE PATH
                $img = $property['image'] 
                    ? 'uploads/'.htmlspecialchars($property['image']) 
                    : 'uploads/no-image.jpg'; 
                ?>

                <img id="mainImage" src="<?php echo $img; ?>" alt="main">
            </div>

            <div class="thumbs">
                <img src="<?php echo $img; ?>" onclick="swapMain('<?php echo $img; ?>')">
                <img src="<?php echo $img; ?>" onclick="swapMain('<?php echo $img; ?>')">
                <img src="<?php echo $img; ?>" onclick="swapMain('<?php echo $img; ?>')">
            </div>
        </div>

        <!-- TITLE -->
        <div class="title-row">
            <div>
                <h1><?php echo htmlspecialchars($property['title']); ?></h1>
                <div class="seller">By: <?php echo htmlspecialchars($property['seller_name'] ?: 'Seller'); ?></div>
            </div>
            <div style="text-align:right;">
                <div class="price">৳ <?php echo number_format($property['price']); ?></div>
                <div style="color:var(--muted); font-size:13px;"><?php echo htmlspecialchars($property['location']); ?></div>
            </div>
        </div>

        <!-- BADGES -->
        <div class="badges">
            <div class="badge"><?php echo htmlspecialchars($property['property_type']); ?></div>
            <div class="badge"><?php echo ($property['bedrooms'] ? $property['bedrooms'].' Beds' : 'Land'); ?></div>
            <div class="badge"><?php echo ($property['square_feet'] ? $property['square_feet'].' Sq Ft' : ''); ?></div>
            <div class="badge"><?php echo htmlspecialchars($property['category']); ?></div>
        </div>

        <!-- DESCRIPTION -->
        <div class="desc">
            <?php echo nl2br(htmlspecialchars($property['description'])); ?>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="side">

        <div class="card-side">
            <div style="display:flex; justify-content:space-between;">
                <div>
                    <div style="font-size:13px; color:var(--muted);">AVERAGE RATING</div>
                    <div style="font-size:20px; font-weight:700;"><?php echo $avg_rating; ?> / 5</div>
                    <div style="color:var(--muted); font-size:13px;"><?php echo $total_reviews; ?> reviews</div>
                </div>

                <div class="stars">
                    <?php echo str_repeat('★', round($avg_rating)); ?>
                    <?php echo str_repeat('☆', 5 - round($avg_rating)); ?>
                </div>
            </div>

            <a class="contact-btn" href="mailto:<?php echo htmlspecialchars($property['seller_name'].'@example.com'); ?>">
                Contact Seller
            </a>

            <?php if(isset($_SESSION['role']) && $_SESSION['role']=='buyer'){ ?>
                <a href="add_review.php?property_id=<?php echo $pid; ?>" class="add-review">✍️ Add a review</a>
            <?php } else { ?>
                <div style="margin-top:10px; color:var(--muted); font-size:13px;">Login as buyer to add a review</div>
            <?php } ?>
        </div>

        <div class="card-side">
            <h3 style="margin:0 0 8px 0;">Reviews</h3>
            <div class="reviews-list">
                <?php if(mysqli_num_rows($reviews_res) == 0){ ?>
                    <div style="color:var(--muted);">No reviews yet.</div>
                <?php } else {
                    while($rv = mysqli_fetch_assoc($reviews_res)){ ?>
                        <div class="review">
                            <div class="meta">
                                <strong><?php echo htmlspecialchars($rv['buyer_name']); ?></strong>
                                · <?php echo $rv['rating']; ?> ★
                                · <span style="color:var(--muted); font-size:12px;"><?php echo $rv['created_at']; ?></span>
                            </div>

                            <div><?php echo nl2br(htmlspecialchars($rv['review_text'])); ?></div>

                            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id']==$rv['buyer_id']){ ?>
                                <div style="margin-top:8px;">
                                    <a href="edit_review.php?id=<?php echo $rv['review_id']; ?>" style="color:#9fb6ff;">✏ Edit</a>
                                    <a href="delete_review.php?id=<?php echo $rv['review_id']; ?>" style="color:#ff7b7b;" onclick="return confirm('Delete review?')">🗑 Delete</a>
                                </div>
                            <?php } ?>
                        </div>
                <?php }} ?>
            </div>
        </div>

    </div>
</div>

</body>
</html>
