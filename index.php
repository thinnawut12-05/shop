<?php
session_start();

// ===== PRODUCT DATA =====
$products = [
  ['id' => 1, 'name' => 'Velvet Rose Serum', 'name_th' => 'เซรั่มกุหลาบกำมะหยี่', 'price' => 1290, 'orig' => 1890, 'cat' => 'skincare', 'badge' => 'ขายดี', 'badge_class' => 'badge-gold', 'emoji' => '🌹', 'color' => '#E8A0BF', 'desc' => 'บำรุงผิวลึกระดับเซลล์ ด้วยสารสกัดจากกุหลาบดามัสกัส', 'rating' => 4.9, 'reviews' => 2341],
  ['id' => 2, 'name' => 'Lumière Glow Cream', 'name_th' => 'ครีมเลอมิแยร์กลอว์', 'price' => 890, 'orig' => 1290, 'cat' => 'skincare', 'badge' => 'ใหม่', 'badge_class' => 'badge-green', 'emoji' => '✨', 'color' => '#F7D9B0', 'desc' => 'ผิวเรืองแสงทันใจ ด้วยนาโนโกลด์ 24K และวิตามิน C เข้มข้น', 'rating' => 4.7, 'reviews' => 873],
  ['id' => 3, 'name' => 'Midnight Obsidian Mask', 'name_th' => 'มาสก์ออบซิเดียนกลางคืน', 'price' => 650, 'orig' => 950, 'cat' => 'mask', 'badge' => 'ลดราคา', 'badge_class' => 'badge-red', 'emoji' => '🖤', 'color' => '#9B7FC4', 'desc' => 'ดีท็อกซ์ผิวลึกพิเศษ ด้วยคาร์บอนดำจากไม้ไผ่ญี่ปุ่น', 'rating' => 4.8, 'reviews' => 1104],
  ['id' => 4, 'name' => 'Sakura Lip Plump', 'name_th' => 'ลิปพลัมซากุระ', 'price' => 390, 'orig' => 590, 'cat' => 'makeup', 'badge' => 'ฮิต', 'badge_class' => 'badge-orange', 'emoji' => '🌸', 'color' => '#FFB7C5', 'desc' => 'ปากอิ่มเต็มเป็นธรรมชาติ สารสกัดซากุระและเปปเปอร์มินท์', 'rating' => 4.6, 'reviews' => 3299],
  ['id' => 5, 'name' => 'Celestial Eye Elixir', 'name_th' => 'อายอิลิกเซอร์เซเลสเชียล', 'price' => 1590, 'orig' => 2190, 'cat' => 'skincare', 'badge' => 'พรีเมียม', 'badge_class' => 'badge-purple', 'emoji' => '💫', 'color' => '#C0A9F5', 'desc' => 'ลดถุงใต้ตาและริ้วรอย ด้วยเปปไทด์และคาเวียร์สกัด', 'rating' => 5.0, 'reviews' => 421],
  ['id' => 6, 'name' => 'Honey Amber Toner', 'name_th' => 'โทนเนอร์น้ำผึ้งอำพัน', 'price' => 490, 'orig' => 690, 'cat' => 'skincare', 'badge' => 'ยอดนิยม', 'badge_class' => 'badge-blue', 'emoji' => '🍯', 'color' => '#E8B86D', 'desc' => 'ผิวชุ่มชื้นลึก ด้วยน้ำผึ้งป่า 100% และ Hyaluronic Acid 3 ชั้น', 'rating' => 4.5, 'reviews' => 1788],
];

$categories = [
  ['id' => 'all', 'label' => 'ทั้งหมด', 'icon' => '✦'],
  ['id' => 'skincare', 'label' => 'สกินแคร์', 'icon' => '🌿'],
  ['id' => 'makeup', 'label' => 'เมคอัพ', 'icon' => '💄'],
  ['id' => 'mask', 'label' => 'มาสก์', 'icon' => '🫧'],
];

// ===== INIT CART =====
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

// ===== HANDLE ACTIONS =====
$action = $_POST['action'] ?? '';
$order_success = false;
$order_id = '';
$order_info = [];

if ($action === 'add_to_cart') {
  $pid = (int)$_POST['product_id'];
  foreach ($products as $p) {
    if ($p['id'] === $pid) {
      if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid]['qty']++;
      } else {
        $_SESSION['cart'][$pid] = ['id' => $p['id'], 'name' => $p['name'], 'name_th' => $p['name_th'], 'price' => $p['price'], 'emoji' => $p['emoji'], 'qty' => 1];
      }
      break;
    }
  }
  header('Location: ' . $_SERVER['PHP_SELF'] . '?cat=' . ($_POST['cat'] ?? 'all') . '#products');
  exit;
}

if ($action === 'update_qty') {
  $pid = (int)$_POST['product_id'];
  $qty = (int)$_POST['qty'];
  if ($qty <= 0) unset($_SESSION['cart'][$pid]);
  else $_SESSION['cart'][$pid]['qty'] = $qty;
  header('Location: ' . $_SERVER['PHP_SELF'] . '?show_cart=1');
  exit;
}

if ($action === 'remove_item') {
  $pid = (int)$_POST['product_id'];
  unset($_SESSION['cart'][$pid]);
  header('Location: ' . $_SERVER['PHP_SELF'] . '?show_cart=1');
  exit;
}

if ($action === 'toggle_wish') {
  $pid = (int)$_POST['product_id'];
  if (in_array($pid, $_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$pid]));
  } else {
    $_SESSION['wishlist'][] = $pid;
  }
  header('Location: ' . $_SERVER['PHP_SELF'] . '?cat=' . ($_POST['cat'] ?? 'all') . '#products');
  exit;
}

if ($action === 'place_order') {
  $name    = trim($_POST['name'] ?? '');
  $phone   = trim($_POST['phone'] ?? '');
  $address = trim($_POST['address'] ?? '');
  $payment = $_POST['payment'] ?? 'cod';

  if ($name && $phone && $address && !empty($_SESSION['cart'])) {
    $order_id   = 'LMN-' . strtoupper(substr(md5(uniqid()), 0, 8));
    $order_info = ['name' => $name, 'phone' => $phone, 'address' => $address, 'payment' => $payment, 'cart' => $_SESSION['cart']];
    $_SESSION['cart'] = [];
    $order_success = true;
  }
}

// ===== HELPERS =====
function fmt($n)
{
  return number_format($n, 0) . ' ฿';
}

$cart     = $_SESSION['cart'];
$cat_filter = $_GET['cat'] ?? 'all';
$show_cart  = isset($_GET['show_cart']);
$show_checkout = isset($_GET['checkout']);

$cart_total = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
$cart_count = array_sum(array_map(fn($i) => $i['qty'], $cart));
$shipping   = ($cart_total >= 500) ? 0 : 60;
$discount   = ($cart_total >= 1500) ? (int)round($cart_total * 0.1) : 0;
$grand_total = $cart_total - $discount + $shipping;

$payment_labels = ['cod' => 'เก็บเงินปลายทาง', 'transfer' => 'โอนเงิน', 'promptpay' => 'พร้อมเพย์'];
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LUMINA — Luxury Beauty</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0A0706;
      --bg2: #120E0C;
      --bg3: #1C1511;
      --surface: #1E1812;
      --surface2: #251E16;
      --gold: #C9A85C;
      --gold2: #E8C97A;
      --gold3: #F5E0A0;
      --rose: #C47A7A;
      --rose2: #E8A0A0;
      --cream: #F5EDE0;
      --cream2: #EDD8BC;
      --text: #F0E8DC;
      --text2: #C4B49A;
      --text3: #8A7A68;
      --text4: #5A4A38;
      --border: rgba(201, 168, 92, 0.15);
      --border2: rgba(201, 168, 92, 0.08);
      --green: #78DCA0;
      --shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
      --t: 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0
    }

    html {
      scroll-behavior: smooth
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
      pointer-events: none;
      z-index: 9999;
      opacity: .35
    }

    ::-webkit-scrollbar {
      width: 3px
    }

    ::-webkit-scrollbar-track {
      background: var(--bg)
    }

    ::-webkit-scrollbar-thumb {
      background: var(--gold);
      border-radius: 2px
    }

    a {
      text-decoration: none;
      color: inherit
    }

    button,
    input,
    textarea,
    select {
      font-family: 'DM Sans', sans-serif
    }

    /* ===== HEADER ===== */
    header {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(10, 7, 6, .9);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border2);
      padding: 0 5vw
    }

    .hdr {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 68px;
      max-width: 1300px;
      margin: 0 auto
    }

    .logo {
      display: flex;
      flex-direction: column;
      line-height: 1
    }

    .logo-main {
      font-family: 'Cormorant Garamond', serif;
      font-size: 26px;
      font-weight: 300;
      letter-spacing: .3em;
      color: var(--gold2)
    }

    .logo-sub {
      font-size: 9px;
      letter-spacing: .5em;
      color: var(--text4);
      text-transform: uppercase;
      margin-top: 2px
    }

    nav {
      display: flex;
      gap: 28px
    }

    nav a {
      font-size: 11px;
      letter-spacing: .15em;
      text-transform: uppercase;
      color: var(--text3);
      transition: color var(--t);
      position: relative
    }

    nav a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 1px;
      background: var(--gold);
      transition: width var(--t)
    }

    nav a:hover {
      color: var(--gold2)
    }

    nav a:hover::after {
      width: 100%
    }

    .btn-cart {
      display: flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, var(--gold), var(--gold2));
      color: var(--bg);
      border: none;
      padding: 10px 20px;
      font-size: 11px;
      letter-spacing: .15em;
      text-transform: uppercase;
      cursor: pointer;
      font-weight: 500;
      transition: all var(--t);
      border-radius: 2px
    }

    .btn-cart:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(201, 168, 92, .3)
    }

    .cart-badge {
      background: var(--bg);
      color: var(--gold);
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700
    }

    /* ===== HERO ===== */
    .hero {
      position: relative;
      min-height: 80vh;
      display: flex;
      align-items: center;
      padding: 60px 5vw;
      overflow: hidden
    }

    .hero-bg {
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse 70% 60% at 65% 50%, rgba(196, 122, 122, .07) 0%, transparent 60%), radial-gradient(ellipse 50% 70% at 25% 30%, rgba(201, 168, 92, .05) 0%, transparent 60%)
    }

    .hero-grid {
      position: absolute;
      inset: 0;
      background-image: linear-gradient(rgba(201, 168, 92, .04) 1px, transparent 1px), linear-gradient(90deg, rgba(201, 168, 92, .04) 1px, transparent 1px);
      background-size: 64px 64px;
      mask-image: radial-gradient(ellipse at center, black 0%, transparent 70%)
    }

    .hero-inner {
      max-width: 1300px;
      margin: 0 auto;
      width: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
      position: relative
    }

    .eyebrow {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px
    }

    .eyebrow-line {
      width: 36px;
      height: 1px;
      background: var(--gold)
    }

    .eyebrow-txt {
      font-size: 10px;
      letter-spacing: .4em;
      text-transform: uppercase;
      color: var(--gold)
    }

    .hero-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(48px, 5.5vw, 82px);
      font-weight: 300;
      line-height: 1.05;
      color: var(--cream);
      margin-bottom: 10px
    }

    .hero-title em {
      font-style: italic;
      color: var(--gold2)
    }

    .hero-subtitle {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(18px, 2vw, 26px);
      font-weight: 300;
      color: var(--text3);
      margin-bottom: 24px;
      display: block
    }

    .hero-desc {
      font-size: 14px;
      line-height: 1.8;
      color: var(--text2);
      max-width: 420px;
      margin-bottom: 40px
    }

    .hero-btns {
      display: flex;
      gap: 14px;
      flex-wrap: wrap
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--gold), var(--gold2));
      color: var(--bg);
      border: none;
      padding: 14px 32px;
      font-size: 11px;
      letter-spacing: .2em;
      text-transform: uppercase;
      cursor: pointer;
      font-weight: 500;
      transition: all var(--t);
      border-radius: 2px;
      display: inline-block
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 30px rgba(201, 168, 92, .35)
    }

    .btn-secondary {
      background: transparent;
      color: var(--text2);
      border: 1px solid var(--border);
      padding: 13px 28px;
      font-size: 11px;
      letter-spacing: .2em;
      text-transform: uppercase;
      cursor: pointer;
      transition: all var(--t);
      border-radius: 2px;
      display: inline-block
    }

    .btn-secondary:hover {
      color: var(--gold);
      border-color: var(--gold);
      background: rgba(201, 168, 92, .05)
    }

    .hero-stats {
      display: flex;
      gap: 36px;
      margin-top: 40px;
      padding-top: 36px;
      border-top: 1px solid var(--border2);
      flex-wrap: wrap
    }

    .stat-num {
      font-family: 'Cormorant Garamond', serif;
      font-size: 28px;
      font-weight: 300;
      color: var(--gold2)
    }

    .stat-label {
      font-size: 10px;
      letter-spacing: .15em;
      text-transform: uppercase;
      color: var(--text4)
    }

    .hero-visual {
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative
    }

    .ring {
      position: absolute;
      border-radius: 50%;
      border: 1px solid var(--border2)
    }

    .ring1 {
      width: 360px;
      height: 360px;
      animation: spin 20s linear infinite
    }

    .ring2 {
      width: 290px;
      height: 290px;
      border-style: dashed;
      opacity: .4;
      animation: spin 30s linear infinite reverse
    }

    .hero-card {
      position: relative;
      width: 260px;
      height: 330px;
      background: linear-gradient(145deg, var(--bg3), var(--bg2));
      border: 1px solid var(--border);
      border-radius: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 12px;
      box-shadow: var(--shadow);
      z-index: 1;
      overflow: hidden;
      animation: float 6s ease-in-out infinite
    }

    .hero-card::before {
      content: '';
      position: absolute;
      top: -60px;
      left: -60px;
      width: 180px;
      height: 180px;
      border-radius: 50%;
      background: rgba(196, 122, 122, .08);
      filter: blur(40px)
    }

    .hero-card-badge {
      position: absolute;
      top: 10px;
      right: 12px;
      background: linear-gradient(135deg, var(--rose), var(--rose2));
      color: #fff;
      font-size: 9px;
      font-weight: 500;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: 5px 12px;
      border-radius: 20px
    }

    .hero-emoji {
      font-size: 70px;
      filter: drop-shadow(0 8px 20px rgba(196, 122, 122, .3));
      position: relative;
      z-index: 1
    }

    .hero-pname {
      font-family: 'Cormorant Garamond', serif;
      font-size: 19px;
      font-weight: 300;
      color: var(--cream);
      text-align: center;
      padding: 0 16px
    }

    .hero-pprice {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      font-weight: 300;
      color: var(--gold2)
    }

    .fdot {
      position: absolute;
      border-radius: 50%;
      animation: float 4s ease-in-out infinite
    }

    /* ===== MARQUEE ===== */
    .marquee-bar {
      border-top: 1px solid var(--border2);
      border-bottom: 1px solid var(--border2);
      padding: 12px 0;
      overflow: hidden;
      background: var(--bg2)
    }

    .marquee-track {
      display: flex;
      animation: marquee 25s linear infinite;
      white-space: nowrap
    }

    .marquee-track:hover {
      animation-play-state: paused
    }

    .mitem {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 0 28px;
      font-size: 10px;
      letter-spacing: .3em;
      text-transform: uppercase;
      color: var(--text4)
    }

    .mdot {
      width: 4px;
      height: 4px;
      border-radius: 50%;
      background: var(--gold);
      flex-shrink: 0
    }

    /* ===== FEATURES ===== */
    .features {
      padding: 70px 5vw;
      background: var(--bg2);
      border-bottom: 1px solid var(--border2)
    }

    .feat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 24px;
      max-width: 1300px;
      margin: 0 auto
    }

    .feat-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 14px;
      padding: 28px 20px;
      background: var(--bg3);
      border: 1px solid var(--border2);
      border-radius: 14px;
      transition: all var(--t)
    }

    .feat-item:hover {
      border-color: var(--border);
      transform: translateY(-3px)
    }

    .feat-icon {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      background: rgba(201, 168, 92, .08);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px
    }

    .feat-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 17px;
      font-weight: 400;
      color: var(--cream)
    }

    .feat-desc {
      font-size: 12px;
      color: var(--text3);
      line-height: 1.6
    }

    /* ===== PRODUCTS SECTION ===== */
    .products-section {
      padding: 80px 5vw
    }

    .sec-inner {
      max-width: 1300px;
      margin: 0 auto
    }

    .sec-header {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 36px;
      flex-wrap: wrap;
      gap: 20px
    }

    .sec-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(30px, 3.5vw, 46px);
      font-weight: 300;
      color: var(--cream);
      line-height: 1.1
    }

    .sec-title em {
      font-style: italic;
      color: var(--gold2)
    }

    .cats {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 28px
    }

    .cat-btn {
      display: flex;
      align-items: center;
      gap: 7px;
      padding: 9px 20px;
      background: var(--bg3);
      border: 1px solid var(--border2);
      color: var(--text3);
      font-size: 12px;
      cursor: pointer;
      border-radius: 30px;
      transition: all var(--t)
    }

    .cat-btn.active,
    .cat-btn:hover {
      background: rgba(201, 168, 92, .1);
      border-color: var(--gold);
      color: var(--gold2)
    }

    .promo-bar {
      background: linear-gradient(135deg, rgba(201, 168, 92, .07), rgba(196, 122, 122, .04));
      border: 1px solid rgba(201, 168, 92, .12);
      border-radius: 10px;
      padding: 11px 20px;
      margin-bottom: 28px;
      font-size: 13px;
      color: var(--text2);
      display: flex;
      align-items: center;
      gap: 10px
    }

    .promo-bar strong {
      color: var(--gold2)
    }

    .prod-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px
    }

    /* ===== PRODUCT CARD ===== */
    .pcard {
      background: var(--bg3);
      border: 1px solid var(--border2);
      border-radius: 16px;
      overflow: hidden;
      transition: all var(--t);
      position: relative
    }

    .pcard:hover {
      transform: translateY(-5px);
      border-color: var(--border);
      box-shadow: 0 20px 50px rgba(0, 0, 0, .5)
    }

    .pcard-visual {
      height: 190px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden
    }

    .pcard-glow {
      position: absolute;
      width: 130px;
      height: 130px;
      border-radius: 50%;
      opacity: .12;
      filter: blur(28px);
      pointer-events: none
    }

    .pcard-emoji {
      font-size: 64px;
      position: relative;
      z-index: 1;
      filter: drop-shadow(0 6px 14px rgba(0, 0, 0, .3));
      transition: transform var(--t)
    }

    .pcard:hover .pcard-emoji {
      transform: scale(1.08) translateY(-4px)
    }

    .pbadge {
      position: absolute;
      top: 12px;
      left: 12px;
      font-size: 10px;
      font-weight: 500;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 20px
    }

    .badge-gold {
      background: rgba(201, 168, 92, .18);
      color: var(--gold2);
      border: 1px solid rgba(201, 168, 92, .3)
    }

    .badge-green {
      background: rgba(100, 180, 120, .18);
      color: #78DCA0;
      border: 1px solid rgba(100, 180, 120, .3)
    }

    .badge-red {
      background: rgba(220, 80, 80, .18);
      color: #F08080;
      border: 1px solid rgba(220, 80, 80, .3)
    }

    .badge-orange {
      background: rgba(220, 140, 60, .18);
      color: #F0A070;
      border: 1px solid rgba(220, 140, 60, .3)
    }

    .badge-purple {
      background: rgba(180, 130, 240, .18);
      color: #C8A0F5;
      border: 1px solid rgba(180, 130, 240, .3)
    }

    .badge-blue {
      background: rgba(100, 160, 220, .18);
      color: #80B8F0;
      border: 1px solid rgba(100, 160, 220, .3)
    }

    .wish-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: rgba(10, 7, 6, .75);
      border: 1px solid var(--border2);
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all .2s;
      backdrop-filter: blur(8px)
    }

    .wish-btn:hover,
    .wish-btn.wished {
      color: var(--rose2);
      border-color: rgba(196, 122, 122, .4)
    }

    .pcard-info {
      padding: 16px 16px 20px
    }

    .pname {
      font-family: 'Cormorant Garamond', serif;
      font-size: 19px;
      font-weight: 400;
      color: var(--cream);
      margin-bottom: 3px;
      line-height: 1.2
    }

    .pname-th {
      font-size: 12px;
      color: var(--text4);
      margin-bottom: 8px
    }

    .pdesc {
      font-size: 12px;
      color: var(--text4);
      line-height: 1.6;
      margin-bottom: 10px
    }

    .prating {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 10px
    }

    .stars {
      color: var(--gold);
      font-size: 12px
    }

    .rnum {
      font-size: 12px;
      font-weight: 500;
      color: var(--text2)
    }

    .rcnt {
      font-size: 11px;
      color: var(--text4)
    }

    .ppricing {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
      flex-wrap: wrap
    }

    .price-now {
      font-family: 'Cormorant Garamond', serif;
      font-size: 24px;
      font-weight: 300;
      color: var(--gold2)
    }

    .price-orig {
      font-size: 13px;
      color: var(--text4);
      text-decoration: line-through
    }

    .price-disc {
      font-size: 10px;
      background: rgba(220, 80, 80, .14);
      color: #F08080;
      border: 1px solid rgba(220, 80, 80, .25);
      padding: 3px 8px;
      border-radius: 20px
    }

    /* qty control */
    .qty-ctrl {
      display: flex;
      align-items: center;
      height: 42px;
      background: rgba(201, 168, 92, .07);
      border: 1px solid rgba(201, 168, 92, .18);
      border-radius: 8px;
      overflow: hidden
    }

    .qty-btn {
      flex: 0 0 42px;
      height: 100%;
      background: transparent;
      border: none;
      color: var(--gold);
      font-size: 20px;
      cursor: pointer;
      transition: background .2s
    }

    .qty-btn:hover {
      background: rgba(201, 168, 92, .12)
    }

    .qty-val {
      flex: 1;
      text-align: center;
      font-family: 'Cormorant Garamond', serif;
      font-size: 20px;
      color: var(--gold2)
    }

    .btn-add {
      width: 100%;
      background: linear-gradient(135deg, var(--gold), var(--gold2));
      color: var(--bg);
      border: none;
      padding: 13px;
      font-size: 11px;
      letter-spacing: .15em;
      text-transform: uppercase;
      cursor: pointer;
      font-weight: 500;
      border-radius: 8px;
      transition: all var(--t)
    }

    .btn-add:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 20px rgba(201, 168, 92, .28)
    }

    /* ===== CART PANEL ===== */
    .cart-overlay {
      position: fixed;
      inset: 0;
      z-index: 300;
      display: flex;
      visibility: hidden;
      opacity: 0;
      transition: opacity .35s, visibility .35s
    }

    .cart-overlay.open {
      visibility: visible;
      opacity: 1
    }

    .cart-backdrop {
      flex: 1;
      background: rgba(0, 0, 0, .7);
      backdrop-filter: blur(4px)
    }

    .cart-drawer {
      width: 420px;
      background: var(--bg2);
      border-left: 1px solid var(--border2);
      display: flex;
      flex-direction: column;
      transform: translateX(100%);
      transition: transform .38s cubic-bezier(.25, .46, .45, .94);
      box-shadow: -20px 0 60px rgba(0, 0, 0, .5);
      max-height: 100vh;
      overflow-y: auto
    }

    .cart-overlay.open .cart-drawer {
      transform: translateX(0)
    }

    .cart-hdr {
      padding: 18px 22px;
      border-bottom: 1px solid var(--border2);
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      background: var(--bg2);
      z-index: 1
    }

    .cart-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 24px;
      font-weight: 300;
      color: var(--cream)
    }

    .cart-sub {
      font-size: 11px;
      color: var(--text4);
      margin-top: 2px
    }

    .icon-btn {
      background: rgba(201, 168, 92, .07);
      border: 1px solid var(--border2);
      color: var(--text3);
      width: 34px;
      height: 34px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 15px;
      transition: all .2s;
      display: flex;
      align-items: center;
      justify-content: center
    }

    .icon-btn:hover {
      color: var(--gold2);
      border-color: var(--gold)
    }

    .cart-empty {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 14px;
      padding: 40px;
      color: var(--text4);
      text-align: center
    }

    .cart-empty-icon {
      font-size: 56px;
      opacity: .5
    }

    .cart-empty-txt {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      color: var(--text3)
    }

    .cart-items {
      flex: 1;
      padding: 16px 20px;
      display: flex;
      flex-direction: column;
      gap: 12px
    }

    .cart-item {
      display: flex;
      gap: 12px;
      align-items: center;
      padding: 13px;
      background: var(--bg3);
      border: 1px solid var(--border2);
      border-radius: 12px
    }

    .ci-thumb {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      background: var(--surface2);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      flex-shrink: 0
    }

    .ci-info {
      flex: 1;
      min-width: 0
    }

    .ci-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 15px;
      color: var(--cream);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin-bottom: 2px
    }

    .ci-name-th {
      font-size: 11px;
      color: var(--text4);
      margin-bottom: 5px
    }

    .ci-price {
      font-family: 'Cormorant Garamond', serif;
      font-size: 16px;
      color: var(--gold2)
    }

    .ci-qty {
      display: flex;
      align-items: center;
      gap: 0;
      background: rgba(201, 168, 92, .08);
      border: 1px solid rgba(201, 168, 92, .15);
      border-radius: 7px;
      overflow: hidden;
      flex-shrink: 0
    }

    .cq-btn {
      width: 28px;
      height: 28px;
      background: transparent;
      border: none;
      color: var(--gold);
      font-size: 16px;
      cursor: pointer;
      transition: background .2s
    }

    .cq-btn:hover {
      background: rgba(201, 168, 92, .12)
    }

    .cq-val {
      width: 26px;
      text-align: center;
      font-size: 13px;
      color: var(--gold2);
      font-family: 'Cormorant Garamond', serif;
      font-size: 17px
    }

    .cart-footer {
      padding: 16px 20px;
      border-top: 1px solid var(--border2);
      background: var(--bg);
      position: sticky;
      bottom: 0
    }

    .cart-summary {
      display: flex;
      flex-direction: column;
      gap: 7px;
      margin-bottom: 14px;
      font-size: 13px;
      color: var(--text3)
    }

    .cs-row {
      display: flex;
      justify-content: space-between
    }

    .cs-total {
      font-size: 15px;
      color: var(--text);
      font-weight: 500
    }

    .cs-amount {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      color: var(--gold2);
      font-weight: 300
    }

    .cs-green {
      color: var(--green)
    }

    .promo-hint {
      background: rgba(201, 168, 92, .06);
      border: 1px solid rgba(201, 168, 92, .1);
      border-radius: 8px;
      padding: 8px 12px;
      font-size: 11px;
      color: var(--text4);
      margin-top: 2px
    }

    /* ===== MODAL ===== */
    .modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 400;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      visibility: hidden;
      opacity: 0;
      transition: opacity .35s, visibility .35s
    }

    .modal-overlay.open {
      visibility: visible;
      opacity: 1
    }

    .modal-bg {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, .82);
      backdrop-filter: blur(6px)
    }

    .modal {
      position: relative;
      width: 100%;
      max-width: 560px;
      max-height: 90vh;
      overflow-y: auto;
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 20px;
      box-shadow: 0 30px 80px rgba(0, 0, 0, .7);
      animation: fadeUp .4s ease
    }

    .modal-hdr {
      padding: 22px 26px;
      border-bottom: 1px solid var(--border2);
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      background: var(--bg2);
      z-index: 1;
      border-radius: 20px 20px 0 0
    }

    .modal-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 26px;
      font-weight: 300;
      color: var(--cream)
    }

    .modal-sub {
      font-size: 11px;
      color: var(--text4);
      margin-top: 2px
    }

    .modal-body {
      padding: 24px 26px;
      display: flex;
      flex-direction: column;
      gap: 18px
    }

    /* Order summary in modal */
    .order-summary {
      background: var(--bg3);
      border: 1px solid var(--border2);
      border-radius: 12px;
      padding: 16px
    }

    .os-label {
      font-size: 10px;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 12px
    }

    .os-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid var(--border2)
    }

    .os-item:last-child {
      border-bottom: none
    }

    .os-left {
      display: flex;
      gap: 10px;
      align-items: center
    }

    .os-emoji {
      font-size: 22px
    }

    .os-iname {
      font-family: 'Cormorant Garamond', serif;
      font-size: 15px;
      color: var(--cream)
    }

    .os-isub {
      font-size: 11px;
      color: var(--text4)
    }

    .os-iprice {
      font-family: 'Cormorant Garamond', serif;
      font-size: 16px;
      color: var(--gold2)
    }

    .os-rows {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px solid var(--border2);
      font-size: 13px
    }

    .os-row {
      display: flex;
      justify-content: space-between;
      color: var(--text3)
    }

    .os-row.total {
      border-top: 1px solid var(--border2);
      padding-top: 10px;
      margin-top: 4px;
      color: var(--text);
      font-weight: 500
    }

    .os-total-val {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      color: var(--gold2);
      font-weight: 300
    }

    /* Form */
    .field {
      display: flex;
      flex-direction: column;
      gap: 7px
    }

    .field label {
      font-size: 10px;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--text3)
    }

    .field input,
    .field textarea {
      background: var(--bg3);
      border: 1px solid var(--border2);
      border-radius: 8px;
      padding: 13px 15px;
      color: var(--text);
      font-size: 14px;
      outline: none;
      transition: border-color .3s;
      width: 100%
    }

    .field input:focus,
    .field textarea:focus {
      border-color: rgba(201, 168, 92, .35)
    }

    .field textarea {
      resize: none
    }

    .field input::placeholder,
    .field textarea::placeholder {
      color: var(--text4)
    }

    /* Payment options */
    .pay-opts {
      display: flex;
      flex-direction: column;
      gap: 10px
    }

    .pay-opt {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 13px 15px;
      background: var(--bg3);
      border: 1px solid var(--border2);
      border-radius: 10px;
      cursor: pointer;
      transition: all .25s
    }

    .pay-opt.selected,
    .pay-opt:hover {
      background: rgba(201, 168, 92, .07);
      border-color: rgba(201, 168, 92, .28)
    }

    .pay-icon {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      background: rgba(201, 168, 92, .06);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 17px;
      flex-shrink: 0
    }

    .pay-label {
      flex: 1;
      font-size: 14px;
      color: var(--text)
    }

    .pay-sub {
      font-size: 11px;
      color: var(--text4);
      margin-top: 2px
    }

    .pay-radio {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      border: 2px solid rgba(201, 168, 92, .2);
      flex-shrink: 0;
      transition: all .2s
    }

    .pay-opt.selected .pay-radio,
    .pay-opt input:checked~.pay-radio {
      border-color: var(--gold);
      background: var(--gold)
    }

    /* ===== SUCCESS ===== */
    .success-overlay {
      position: fixed;
      inset: 0;
      z-index: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px
    }

    .success-bg {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, .9);
      backdrop-filter: blur(10px)
    }

    .success-card {
      position: relative;
      width: 100%;
      max-width: 440px;
      background: linear-gradient(145deg, var(--bg3), var(--bg2));
      border: 1px solid rgba(201, 168, 92, .2);
      border-radius: 24px;
      padding: 44px 36px;
      text-align: center;
      box-shadow: 0 30px 80px rgba(0, 0, 0, .7), 0 0 0 1px var(--border2);
      animation: fadeUp .5s ease
    }

    .success-icon {
      width: 76px;
      height: 76px;
      border-radius: 50%;
      background: linear-gradient(135deg, rgba(120, 220, 160, .12), rgba(100, 200, 140, .04));
      border: 1px solid rgba(120, 220, 160, .28);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 22px;
      font-size: 32px
    }

    .success-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 38px;
      font-weight: 300;
      color: var(--cream);
      margin-bottom: 10px
    }

    .success-desc {
      font-size: 14px;
      color: var(--text3);
      line-height: 1.7;
      margin-bottom: 6px
    }

    .success-brand {
      color: var(--gold2);
      font-family: 'Cormorant Garamond', serif;
      font-size: 18px
    }

    .success-note {
      font-size: 13px;
      color: var(--text4);
      margin-bottom: 28px
    }

    .order-box {
      background: rgba(201, 168, 92, .06);
      border: 1px solid var(--border2);
      border-radius: 12px;
      padding: 14px 18px;
      margin-bottom: 24px;
      text-align: left
    }

    .ob-label {
      font-size: 10px;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 8px
    }

    .ob-id {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      color: var(--gold2);
      letter-spacing: .1em
    }

    .ob-info {
      font-size: 12px;
      color: var(--text4);
      margin-top: 4px
    }

    /* ===== TESTIMONIALS ===== */
    .testimonials {
      padding: 80px 5vw;
      background: var(--bg2);
      border-top: 1px solid var(--border2)
    }

    .test-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 18px;
      max-width: 1300px;
      margin: 0 auto
    }

    .tcard {
      background: var(--bg3);
      border: 1px solid var(--border2);
      border-radius: 14px;
      padding: 24px;
      transition: all var(--t)
    }

    .tcard:hover {
      border-color: var(--border)
    }

    .tstars {
      color: var(--gold);
      margin-bottom: 12px;
      font-size: 13px
    }

    .ttext {
      font-family: 'Cormorant Garamond', serif;
      font-style: italic;
      font-size: 16px;
      color: var(--text);
      line-height: 1.7;
      margin-bottom: 18px
    }

    .tauthor {
      display: flex;
      align-items: center;
      gap: 10px
    }

    .tavatar {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      font-weight: 500;
      color: #fff;
      flex-shrink: 0
    }

    .taname {
      font-size: 13px;
      font-weight: 500;
      color: var(--text2)
    }

    .taprod {
      font-size: 11px;
      color: var(--text4)
    }

    /* ===== NEWSLETTER ===== */
    .newsletter {
      padding: 90px 5vw;
      position: relative;
      overflow: hidden;
      background: var(--bg2);
      border-top: 1px solid var(--border2)
    }

    .newsletter::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse 60% 80% at 50% 50%, rgba(201, 168, 92, .04) 0%, transparent 70%)
    }

    .nl-inner {
      max-width: 580px;
      margin: 0 auto;
      text-align: center;
      position: relative
    }

    .nl-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(32px, 4vw, 50px);
      font-weight: 300;
      color: var(--cream);
      margin-bottom: 14px
    }

    .nl-desc {
      font-size: 14px;
      color: var(--text2);
      line-height: 1.7;
      margin-bottom: 36px
    }

    .nl-form {
      display: flex;
      border: 1px solid var(--border);
      border-radius: 4px;
      overflow: hidden
    }

    .nl-input {
      flex: 1;
      background: var(--bg3);
      border: none;
      padding: 15px 18px;
      font-size: 14px;
      color: var(--text);
      outline: none
    }

    .nl-input::placeholder {
      color: var(--text4)
    }

    .nl-btn {
      background: linear-gradient(135deg, var(--gold), var(--gold2));
      color: var(--bg);
      border: none;
      padding: 15px 26px;
      font-size: 11px;
      letter-spacing: .15em;
      text-transform: uppercase;
      cursor: pointer;
      font-weight: 500;
      transition: all var(--t);
      white-space: nowrap
    }

    .nl-btn:hover {
      opacity: .9
    }

    .nl-note {
      margin-top: 14px;
      font-size: 12px;
      color: var(--text4)
    }

    /* ===== FOOTER ===== */
    footer {
      background: var(--bg2);
      border-top: 1px solid var(--border2);
      padding: 56px 5vw 28px
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 44px;
      max-width: 1300px;
      margin: 0 auto;
      padding-bottom: 44px;
      border-bottom: 1px solid var(--border2)
    }

    .footer-logo {
      font-family: 'Cormorant Garamond', serif;
      font-size: 26px;
      font-weight: 300;
      letter-spacing: .3em;
      color: var(--gold2);
      margin-bottom: 14px
    }

    .footer-tagline {
      font-size: 13px;
      color: var(--text4);
      line-height: 1.7;
      max-width: 260px;
      margin-bottom: 22px
    }

    .social-links {
      display: flex;
      gap: 10px
    }

    .social-link {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      border: 1px solid var(--border2);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text4);
      font-size: 13px;
      transition: all var(--t)
    }

    .social-link:hover {
      border-color: var(--gold);
      color: var(--gold)
    }

    .fcol-title {
      font-size: 10px;
      letter-spacing: .3em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 18px
    }

    .flinks {
      display: flex;
      flex-direction: column;
      gap: 11px
    }

    .flink {
      font-size: 13px;
      color: var(--text4);
      transition: color var(--t)
    }

    .flink:hover {
      color: var(--text2)
    }

    .footer-bottom {
      max-width: 1300px;
      margin: 0 auto;
      padding-top: 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 12px;
      color: var(--text4);
      flex-wrap: wrap;
      gap: 12px
    }

    .footer-legal {
      display: flex;
      gap: 18px
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(24px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0)
      }

      50% {
        transform: translateY(-10px)
      }
    }

    @keyframes spin {
      from {
        transform: rotate(0)
      }

      to {
        transform: rotate(360deg)
      }
    }

    @keyframes marquee {
      from {
        transform: translateX(0)
      }

      to {
        transform: translateX(-50%)
      }
    }

    /* ===== RESPONSIVE ===== */
    @media(max-width:900px) {
      .hero-inner {
        grid-template-columns: 1fr;
        gap: 44px
      }

      .hero-visual {
        order: -1
      }

      .ring1 {
        width: 280px;
        height: 280px
      }

      .ring2 {
        width: 220px;
        height: 220px
      }

      .hero-card {
        width: 220px;
        height: 280px
      }

      nav {
        display: none
      }

      .footer-grid {
        grid-template-columns: 1fr 1fr
      }

      .cart-drawer {
        width: 100%;
        max-width: 420px
      }
    }

    @media(max-width:600px) {
      .footer-grid {
        grid-template-columns: 1fr
      }

      .hero-stats {
        gap: 20px
      }

      .nl-form {
        flex-direction: column
      }

      .modal {
        border-radius: 16px
      }
    }
  </style>
</head>

<body>

  <!-- ===== HEADER ===== -->
  <header>
    <div class="hdr">
      <a href="index.php" class="logo">
        <span class="logo-main">LUMINA</span>
        <span class="logo-sub">Luxury Beauty</span>
      </a>
      <nav>
        <a href="index.php">หน้าหลัก</a>
        <a href="index.php?cat=skincare#products">สกินแคร์</a>
        <a href="index.php?cat=makeup#products">เมคอัพ</a>
        <a href="index.php?cat=mask#products">มาสก์</a>
        <a href="#">โปรโมชั่น</a>
      </nav>
      <button class="btn-cart" onclick="openCart()">
        🛍 ตะกร้า
        <?php if ($cart_count > 0): ?>
          <span class="cart-badge"><?= $cart_count ?></span>
        <?php endif; ?>
      </button>
    </div>
  </header>

  <!-- ===== HERO ===== -->
  <section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-inner">
      <div style="animation:fadeUp .9s ease both">
        <div class="eyebrow">
          <div class="eyebrow-line"></div>
          <span class="eyebrow-txt">Premium Beauty Collection 2025</span>
        </div>
        <h1 class="hero-title">Reveal Your<br><em>Inner Glow</em></h1>
        <span class="hero-subtitle">เปิดเผยความงามที่แท้จริงในตัวคุณ</span>
        <p class="hero-desc">สัมผัสประสบการณ์ความงามระดับลักชูรี่ ด้วยผลิตภัณฑ์คัดสรรจากธรรมชาติ ผสมผสานนวัตกรรมล้ำสมัยเพื่อผิวที่เปล่งประกายอย่างเป็นธรรมชาติ</p>
        <div class="hero-btns">
          <a href="#products" class="btn-primary">ช้อปเลย ✦</a>
          <a href="#" class="btn-secondary">ดูคอลเลกชั่น</a>
        </div>
        <div class="hero-stats">
          <div>
            <div class="stat-num">50K+</div>
            <div class="stat-label">ลูกค้าพึงพอใจ</div>
          </div>
          <div>
            <div class="stat-num">4.9★</div>
            <div class="stat-label">คะแนนเฉลี่ย</div>
          </div>
          <div>
            <div class="stat-num">100%</div>
            <div class="stat-label">วัตถุดิบธรรมชาติ</div>
          </div>
        </div>
      </div>
      <div class="hero-visual" style="animation:fadeUp .9s .2s ease both">
        <div class="ring ring1"></div>
        <div class="ring ring2"></div>
        <div class="hero-card">
          <div class="hero-card-badge">✦ ขายดี</div>
          <div class="hero-emoji">🌹</div>
          <div class="hero-pname">Velvet Rose Serum</div>
          <div class="hero-pprice">1,290 ฿</div>
          <form method="post">
            <input type="hidden" name="action" value="add_to_cart">
            <input type="hidden" name="product_id" value="1">
            <input type="hidden" name="cat" value="<?= htmlspecialchars($cat_filter) ?>">
            <button type="submit" class="btn-primary" style="margin-top:4px;padding:10px 22px;font-size:10px">+ เพิ่มในตะกร้า</button>
          </form>
        </div>
        <div class="fdot" style="width:10px;height:10px;background:var(--gold);opacity:.55;top:22%;left:12%;animation-delay:0s"></div>
        <div class="fdot" style="width:7px;height:7px;background:var(--rose);opacity:.38;top:68%;left:8%;animation-delay:1s"></div>
        <div class="fdot" style="width:14px;height:14px;background:var(--cream2);opacity:.13;top:32%;right:6%;animation-delay:2s"></div>
      </div>
    </div>
  </section>

  <!-- ===== MARQUEE ===== -->
  <div class="marquee-bar">
    <div class="marquee-track">
      <?php for ($i = 0; $i < 5; $i++): ?>
        <span class="mitem"><span class="mdot"></span>ส่งฟรีทั่วไทย เมื่อซื้อครบ 500฿</span>
        <span class="mitem"><span class="mdot"></span>วัตถุดิบธรรมชาติ 100% · Cruelty Free</span>
        <span class="mitem"><span class="mdot"></span>ลด 10% เมื่อซื้อครบ 1,500฿</span>
        <span class="mitem"><span class="mdot"></span>รับประกันคุณภาพ 30 วัน</span>
        <span class="mitem"><span class="mdot"></span>มาตรฐาน GMP ผลิตในไทย</span>
      <?php endfor; ?>
    </div>
  </div>

  <!-- ===== FEATURES ===== -->
  <div class="features">
    <div class="feat-grid">
      <div class="feat-item">
        <div class="feat-icon">🌿</div>
        <div class="feat-title">Natural Ingredients</div>
        <div class="feat-desc">สารสกัดจากธรรมชาติ ปราศจากสารเคมีอันตราย</div>
      </div>
      <div class="feat-item">
        <div class="feat-icon">🧪</div>
        <div class="feat-title">Dermatologist Tested</div>
        <div class="feat-desc">ผ่านการทดสอบโดยแพทย์ผิวหนัง เหมาะทุกสภาพผิว</div>
      </div>
      <div class="feat-item">
        <div class="feat-icon">🚚</div>
        <div class="feat-title">Free Shipping</div>
        <div class="feat-desc">จัดส่งฟรีทั่วไทย เมื่อซื้อครบ 500 บาทขึ้นไป</div>
      </div>
      <div class="feat-item">
        <div class="feat-icon">♻️</div>
        <div class="feat-title">Eco Packaging</div>
        <div class="feat-desc">บรรจุภัณฑ์รีไซเคิล ใส่ใจสิ่งแวดล้อม</div>
      </div>
    </div>
  </div>

  <!-- ===== PRODUCTS ===== -->
  <section class="products-section" id="products">
    <div class="sec-inner">
      <div class="sec-header">
        <div>
          <div class="eyebrow" style="margin-bottom:10px">
            <div class="eyebrow-line"></div><span class="eyebrow-txt">Our Collection</span>
          </div>
          <h2 class="sec-title">ผลิตภัณฑ์<em> แนะนำ</em></h2>
        </div>
        <a href="index.php?cat=all#products" class="btn-secondary">ดูทั้งหมด →</a>
      </div>

      <!-- Categories -->
      <div class="cats">
        <?php foreach ($categories as $c): ?>
          <a href="index.php?cat=<?= $c['id'] ?>#products" class="cat-btn <?= $cat_filter === $c['id'] ? 'active' : '' ?>"><?= $c['icon'] ?> <?= $c['label'] ?></a>
        <?php endforeach; ?>
      </div>

      <!-- Promo bar -->
      <div class="promo-bar">
        🎁 <span><strong>ลด 10%</strong> เมื่อซื้อครบ 1,500 ฿ &nbsp;|&nbsp; 🚚 <strong>ส่งฟรี</strong> เมื่อซื้อครบ 500 ฿</span>
      </div>

      <!-- Product grid -->
      <div class="prod-grid">
        <?php
        foreach ($products as $p):
          if ($cat_filter !== 'all' && $p['cat'] !== $cat_filter) continue;
          $disc = round((1 - $p['price'] / $p['orig']) * 100);
          $in_cart = isset($cart[$p['id']]) ? $cart[$p['id']]['qty'] : 0;
          $wished = in_array($p['id'], $_SESSION['wishlist']);
        ?>
          <div class="pcard">
            <div class="pcard-visual">
              <div class="pcard-glow" style="background:<?= $p['color'] ?>"></div>
              <div class="pcard-emoji"><?= $p['emoji'] ?></div>
              <span class="pbadge <?= $p['badge_class'] ?>"><?= $p['badge'] ?></span>
              <form method="post" style="position:absolute;top:10px;right:10px">
                <input type="hidden" name="action" value="toggle_wish">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="cat" value="<?= htmlspecialchars($cat_filter) ?>">
                <button type="submit" class="wish-btn <?= $wished ? 'wished' : '' ?>" title="Wishlist"><?= $wished ? '❤' : '♡' ?></button>
              </form>
            </div>
            <div class="pcard-info">
              <div class="pname"><?= $p['name'] ?></div>
              <div class="pname-th"><?= $p['name_th'] ?></div>
              <div class="pdesc"><?= $p['desc'] ?></div>
              <div class="prating">
                <span class="stars"><?= str_repeat('★', (int)$p['rating']) ?></span>
                <span class="rnum"><?= $p['rating'] ?></span>
                <span class="rcnt">(<?= number_format($p['reviews']) ?> รีวิว)</span>
              </div>
              <div class="ppricing">
                <span class="price-now"><?= fmt($p['price']) ?></span>
                <span class="price-orig"><?= fmt($p['orig']) ?></span>
                <span class="price-disc">-<?= $disc ?>%</span>
              </div>

              <?php if ($in_cart > 0): ?>
                <div class="qty-ctrl">
                  <form method="post" style="display:contents">
                    <input type="hidden" name="action" value="update_qty">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="qty" value="<?= $in_cart - 1 ?>">
                    <button type="submit" class="qty-btn">−</button>
                  </form>
                  <span class="qty-val"><?= $in_cart ?></span>
                  <form method="post" style="display:contents">
                    <input type="hidden" name="action" value="update_qty">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="qty" value="<?= $in_cart + 1 ?>">
                    <button type="submit" class="qty-btn">+</button>
                  </form>
                </div>
              <?php else: ?>
                <form method="post">
                  <input type="hidden" name="action" value="add_to_cart">
                  <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                  <input type="hidden" name="cat" value="<?= htmlspecialchars($cat_filter) ?>">
                  <button type="submit" class="btn-add">+ เพิ่มในตะกร้า</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===== CART DRAWER ===== -->
  <div class="cart-overlay" id="cartOverlay">
    <div class="cart-backdrop" onclick="closeCart()"></div>
    <div class="cart-drawer">
      <div class="cart-hdr">
        <div>
          <div class="cart-title">ตะกร้าสินค้า</div>
          <div class="cart-sub"><?= $cart_count ?> รายการ</div>
        </div>
        <button class="icon-btn" onclick="closeCart()">✕</button>
      </div>

      <?php if (empty($cart)): ?>
        <div class="cart-empty">
          <div class="cart-empty-icon">🛍</div>
          <div class="cart-empty-txt">ตะกร้าว่างเปล่า</div>
          <p style="font-size:13px;color:var(--text4)">เลือกสินค้าที่คุณชื่นชอบได้เลย</p>
          <button class="btn-primary" onclick="closeCart()" style="margin-top:8px">เลือกสินค้า</button>
        </div>
      <?php else: ?>
        <div class="cart-items">
          <?php foreach ($cart as $item): ?>
            <div class="cart-item">
              <div class="ci-thumb"><?= $item['emoji'] ?></div>
              <div class="ci-info">
                <div class="ci-name"><?= htmlspecialchars($item['name']) ?></div>
                <div class="ci-name-th"><?= htmlspecialchars($item['name_th']) ?></div>
                <div class="ci-price"><?= fmt($item['price']) ?></div>
              </div>
              <div class="ci-qty">
                <form method="post" style="display:contents">
                  <input type="hidden" name="action" value="update_qty">
                  <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                  <input type="hidden" name="qty" value="<?= $item['qty'] - 1 ?>">
                  <button type="submit" class="cq-btn">−</button>
                </form>
                <span class="cq-val"><?= $item['qty'] ?></span>
                <form method="post" style="display:contents">
                  <input type="hidden" name="action" value="update_qty">
                  <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                  <input type="hidden" name="qty" value="<?= $item['qty'] + 1 ?>">
                  <button type="submit" class="cq-btn">+</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="cart-footer">
          <div class="cart-summary">
            <div class="cs-row"><span>ยอดรวม</span><span style="color:var(--text2)"><?= fmt($cart_total) ?></span></div>
            <?php if ($discount > 0): ?>
              <div class="cs-row"><span class="cs-green">ส่วนลด 10%</span><span class="cs-green">-<?= fmt($discount) ?></span></div>
            <?php endif; ?>
            <div class="cs-row">
              <span>ค่าจัดส่ง</span>
              <span <?= $shipping === 0 ? 'class="cs-green"' : '' ?>><?= $shipping === 0 ? 'ฟรี!' : fmt($shipping) ?></span>
            </div>
            <?php if ($cart_total < 500): ?>
              <div class="promo-hint">🚚 ซื้อเพิ่มอีก <?= fmt(500 - $cart_total) ?> รับส่งฟรี!</div>
            <?php elseif ($cart_total < 1500): ?>
              <div class="promo-hint">🎁 ซื้อเพิ่มอีก <?= fmt(1500 - $cart_total) ?> รับส่วนลด 10%!</div>
            <?php endif; ?>
            <div class="cs-row" style="margin-top:6px;padding-top:10px;border-top:1px solid var(--border2)">
              <span class="cs-total">รวมสุทธิ</span>
              <span class="cs-amount"><?= fmt($grand_total) ?></span>
            </div>
          </div>
          <a href="index.php?checkout=1" onclick="closeCart()" class="btn-primary" style="display:block;text-align:center;padding:15px;border-radius:8px;font-size:12px">สั่งซื้อเลย →</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- ===== CHECKOUT MODAL ===== -->
  <?php if ($show_checkout && !$order_success && !empty($cart)): ?>
    <div class="modal-overlay open" id="checkoutModal">
      <div class="modal-bg" onclick="window.location='index.php'"></div>
      <div class="modal">
        <div class="modal-hdr">
          <div>
            <div class="modal-title">ยืนยันการสั่งซื้อ</div>
            <div class="modal-sub">กรอกข้อมูลจัดส่งให้ครบถ้วน</div>
          </div>
          <a href="index.php" class="icon-btn">✕</a>
        </div>
        <div class="modal-body">

          <!-- Order summary -->
          <div class="order-summary">
            <div class="os-label">สรุปออเดอร์</div>
            <?php foreach ($cart as $item): ?>
              <div class="os-item">
                <div class="os-left">
                  <span class="os-emoji"><?= $item['emoji'] ?></span>
                  <div>
                    <div class="os-iname"><?= htmlspecialchars($item['name']) ?></div>
                    <div class="os-isub">จำนวน <?= $item['qty'] ?> ชิ้น</div>
                  </div>
                </div>
                <span class="os-iprice"><?= fmt($item['price'] * $item['qty']) ?></span>
              </div>
            <?php endforeach; ?>
            <div class="os-rows">
              <?php if ($discount > 0): ?>
                <div class="os-row" style="color:var(--green)"><span>ส่วนลด 10%</span><span>-<?= fmt($discount) ?></span></div>
              <?php endif; ?>
              <div class="os-row"><span>ค่าจัดส่ง</span><span <?= $shipping === 0 ? 'style="color:var(--green)"' : '' ?>><?= $shipping === 0 ? 'ฟรี!' : fmt($shipping) ?></span></div>
              <div class="os-row total"><span>รวมสุทธิ</span><span class="os-total-val"><?= fmt($grand_total) ?></span></div>
            </div>
          </div>

          <!-- Form -->
          <form method="post" id="checkoutForm">
            <input type="hidden" name="action" value="place_order">
            <div style="display:flex;flex-direction:column;gap:16px">
              <div class="field">
                <label>ชื่อ-นามสกุล *</label>
                <input type="text" name="name" placeholder="กรอกชื่อ-นามสกุล" required>
              </div>
              <div class="field">
                <label>เบอร์โทรศัพท์ *</label>
                <input type="tel" name="phone" placeholder="0xx-xxx-xxxx" required>
              </div>
              <div class="field">
                <label>ที่อยู่จัดส่ง *</label>
                <textarea name="address" rows="3" placeholder="บ้านเลขที่ ถนน แขวง/ตำบล เขต/อำเภอ จังหวัด รหัสไปรษณีย์" required></textarea>
              </div>

              <!-- Payment -->
              <div class="field">
                <label>ช่องทางชำระเงิน</label>
                <div class="pay-opts">
                  <?php
                  $payopts = [
                    'cod'       => ['💵', 'เก็บเงินปลายทาง', 'ชำระเมื่อได้รับสินค้า'],
                    'transfer'  => ['🏦', 'โอนเงิน', 'ธนาคารกสิกร / SCB / กรุงเทพ'],
                    'promptpay' => ['📱', 'พร้อมเพย์', 'ชำระผ่าน QR Code ทันที'],
                  ];
                  foreach ($payopts as $pid => $po):
                  ?>
                    <label class="pay-opt <?= $pid === 'cod' ? 'selected' : '' ?>" id="pay_<?= $pid ?>">
                      <input type="radio" name="payment" value="<?= $pid ?>" <?= $pid === 'cod' ? 'checked' : '' ?> style="display:none" onchange="selectPay('<?= $pid ?>')">
                      <div class="pay-icon"><?= $po[0] ?></div>
                      <div>
                        <div class="pay-label"><?= $po[1] ?></div>
                        <div class="pay-sub"><?= $po[2] ?></div>
                      </div>
                      <div class="pay-radio" id="pr_<?= $pid ?>" <?= $pid === 'cod' ? 'style="border-color:var(--gold);background:var(--gold)"' : '' ?>></div>
                    </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <button type="submit" class="btn-primary" style="padding:15px;font-size:12px;border-radius:8px;letter-spacing:.18em;width:100%">✦ ยืนยันและสั่งซื้อเลย</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php elseif ($show_checkout && empty($cart) && !$order_success): ?>
    <script>
      window.location = 'index.php';
    </script>
  <?php endif; ?>

  <!-- ===== ORDER SUCCESS ===== -->
  <?php if ($order_success): ?>
    <div class="success-overlay">
      <div class="success-bg"></div>
      <div class="success-card">
        <div class="success-icon">✓</div>
        <h2 class="success-title">สั่งซื้อสำเร็จ!</h2>
        <p class="success-desc">ขอบคุณที่เลือกใช้ <span class="success-brand">LUMINA</span></p>
        <p class="success-note">เราจะจัดส่งสินค้าให้คุณภายใน 1-3 วันทำการ 📦</p>
        <div class="order-box">
          <div class="ob-label">หมายเลขออเดอร์</div>
          <div class="ob-id"><?= $order_id ?></div>
          <div class="ob-info">
            ชื่อ: <?= htmlspecialchars($order_info['name']) ?> | โทร: <?= htmlspecialchars($order_info['phone']) ?><br>
            ชำระ: <?= $payment_labels[$order_info['payment']] ?? 'เก็บเงินปลายทาง' ?>
          </div>
        </div>
        <a href="index.php" class="btn-primary" style="display:block;padding:15px;font-size:12px;border-radius:8px;letter-spacing:.18em">✦ ช้อปต่อ</a>
      </div>
    </div>
  <?php endif; ?>

  <!-- ===== TESTIMONIALS ===== -->
  <section class="testimonials">
    <div class="sec-inner" style="max-width:1300px;margin:0 auto">
      <div class="sec-header" style="margin-bottom:32px">
        <div>
          <div class="eyebrow" style="margin-bottom:10px">
            <div class="eyebrow-line"></div><span class="eyebrow-txt">Reviews</span>
          </div>
          <h2 class="sec-title">เสียง<em>จากลูกค้า</em></h2>
        </div>
      </div>
      <div class="test-grid">
        <?php
        $tests = [
          ['น', 'linear-gradient(135deg,#C9A85C,#A87840)', 'นภัสสร พงษ์ไพบูลย์', 'Velvet Rose Serum', 'ใช้ Velvet Rose Serum มา 2 เดือน ผิวดีขึ้นมากจริงๆ รูขุมขนกระชับ ผิวเรียบเนียน ประทับใจมากเลยค่ะ'],
          ['ป', 'linear-gradient(135deg,#C47A7A,#E8A0A0)', 'ปรียา อินทร์สุข', 'Midnight Obsidian Mask', 'Midnight Mask เป็นสุดยอดมาสก์เลย ผิวผ่องขาวขึ้นหลังจากใช้คืนแรก กลิ่นหอมมากและไม่แสบผิวเลยค่ะ'],
          ['ก', 'linear-gradient(135deg,#7A8AE8,#C0A9F5)', 'กมลพร รัตนกุล', 'Lumière Glow Cream', 'Glow Cream เนื้อครีมเบาสบาย ซึมเร็วมาก ผิวดูสุขภาพดีและมีออร่าตลอดวัน คุ้มค่ากับราคามากค่ะ'],
        ];
        foreach ($tests as [$init, $grad, $name, $prod, $text]):
        ?>
          <div class="tcard">
            <div class="tstars">★★★★★</div>
            <div class="ttext">"<?= $text ?>"</div>
            <div class="tauthor">
              <div class="tavatar" style="background:<?= $grad ?>"><?= $init ?></div>
              <div>
                <div class="taname"><?= $name ?></div>
                <div class="taprod"><?= $prod ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===== NEWSLETTER ===== -->
  <div class="newsletter">
    <div class="nl-inner">
      <div class="eyebrow" style="justify-content:center;margin-bottom:14px">
        <div class="eyebrow-line"></div><span class="eyebrow-txt">Newsletter</span>
        <div class="eyebrow-line"></div>
      </div>
      <h2 class="nl-title">รับข่าวสาร<br><em style="font-style:italic;color:var(--gold2)">ก่อนใคร</em></h2>
      <p class="nl-desc">สมัครรับจดหมายข่าว รับส่วนลด 15% สำหรับการสั่งซื้อครั้งแรก และอัพเดตโปรโมชั่นพิเศษก่อนใคร</p>
      <div class="nl-form">
        <input type="email" class="nl-input" placeholder="กรอกอีเมลของคุณ...">
        <button class="nl-btn" onclick="this.textContent='✓ สมัครแล้ว!';this.style.background='var(--green)';this.style.color='#0A0706'">สมัครเลย</button>
      </div>
      <div class="nl-note">🔒 ข้อมูลของคุณปลอดภัย ไม่มีสแปมแน่นอน</div>
    </div>
  </div>

  <!-- ===== FOOTER ===== -->
  <footer>
    <div class="footer-grid">
      <div>
        <div class="footer-logo">LUMINA</div>
        <div class="footer-tagline">ความงามระดับพรีเมียมที่เข้าถึงได้ทุกคน เราเชื่อว่าทุกคนสมควรได้รับผลิตภัณฑ์บำรุงผิวคุณภาพสูง</div>
        <div class="social-links">
          <a href="#" class="social-link">f</a>
          <a href="#" class="social-link">𝕏</a>
          <a href="#" class="social-link">📸</a>
          <a href="#" class="social-link">▶</a>
        </div>
      </div>
      <div>
        <div class="fcol-title">สินค้า</div>
        <div class="flinks">
          <a href="?cat=skincare#products" class="flink">สกินแคร์</a>
          <a href="?cat=makeup#products" class="flink">เมคอัพ</a>
          <a href="?cat=mask#products" class="flink">มาสก์</a>
          <a href="#" class="flink">เซรั่ม</a>
          <a href="#" class="flink">ครีมกันแดด</a>
        </div>
      </div>
      <div>
        <div class="fcol-title">บริษัท</div>
        <div class="flinks">
          <a href="#" class="flink">เกี่ยวกับเรา</a>
          <a href="#" class="flink">บล็อก</a>
          <a href="#" class="flink">ร่วมงานกับเรา</a>
          <a href="#" class="flink">ความยั่งยืน</a>
        </div>
      </div>
      <div>
        <div class="fcol-title">ช่วยเหลือ</div>
        <div class="flinks">
          <a href="#" class="flink">ติดต่อเรา</a>
          <a href="#" class="flink">FAQ</a>
          <a href="#" class="flink">นโยบายคืนสินค้า</a>
          <a href="#" class="flink">ติดตามออเดอร์</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div>© 2025 LUMINA Beauty. All rights reserved.</div>
      <div class="footer-legal">
        <a href="#" class="flink">นโยบายความเป็นส่วนตัว</a>
        <a href="#" class="flink">เงื่อนไขการใช้งาน</a>
      </div>
    </div>
  </footer>

  <script>
    // Cart drawer
    function openCart() {
      document.getElementById('cartOverlay').classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function closeCart() {
      document.getElementById('cartOverlay').classList.remove('open');
      document.body.style.overflow = '';
    }

    // Payment option selector
    function selectPay(id) {
      ['cod', 'transfer', 'promptpay'].forEach(p => {
        const el = document.getElementById('pay_' + p);
        const radio = document.getElementById('pr_' + p);
        if (p === id) {
          el.classList.add('selected');
          radio.style.borderColor = 'var(--gold)';
          radio.style.background = 'var(--gold)';
        } else {
          el.classList.remove('selected');
          radio.style.borderColor = 'rgba(201,168,92,0.2)';
          radio.style.background = 'transparent';
        }
      });
    }

    // Auto-open cart if ?show_cart
    <?php if ($show_cart): ?>
      window.addEventListener('DOMContentLoaded', openCart);
    <?php endif; ?>

    // Scroll reveal
    const ro = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.style.animation = 'fadeUp 0.55s ease both';
          ro.unobserve(e.target);
        }
      });
    }, {
      threshold: 0.08
    });
    document.querySelectorAll('.pcard,.feat-item,.tcard').forEach(el => ro.observe(el));
  </script>
</body>

</html>